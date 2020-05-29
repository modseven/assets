<?php

/**
 * Assets Class for Modseven.
 *
 * @copyright   (c) since 2020 Tobias Oitzinger
 * @license     http://koseven.dev/license
 */

namespace Modseven\Assets;

use Modseven\HTML;
use Modseven\Config;
use Modseven\Config\Group;

class Assets
{
    /**
     * Holds the current instance
     * @var	self|null
     */
    protected static ?self $_instance = null;

    /**
     * Holds current css files with their dependencies
     * @var	array
     */
    protected array $_css = [];

    /**
     * Holds current js files with their dependencies
     * @var array
     */
    protected array $_js = [];

    /**
     * Holds the current configuration
     * @var Group
     */
    protected Group $_configuration;

    /**
     * Get current instance
     *
     * @return self
     */
    public static function instance() : self
    {
        if (static::$_instance === NULL)
        {
            static::$_instance = new self();
        }

        return static::$_instance;
    }

    /**
     * Assets constructor.
     * @throws \Modseven\Exception
     */
    public function __construct() {
        $this->_configuration = Config::instance()->load('assets');
    }

    /**
     * Add css file, you can also add dependencies to it.
     *
     * @param string $file           The Filename without extension
     * @param array  $dependencies   Array of dependencies
     */
    public function addCSS(string $file, array $dependencies = []) : void
    {
        $this->_css[$file] = ['file' => $file, 'dependencies' => $dependencies, 'sorted' => false];
    }

    /**
     * Add js file, you can also add dependencies to it.
     *
     * @param string $file           The Filename without extension
     * @param array  $dependencies   Array of dependencies
     */
    public function addJS(string $file, array $dependencies = []) : void
    {
        $this->_js[$file] = ['file' => $file, 'dependencies' => $dependencies, 'sorted' => false];
    }

    /**
     * Returns the html tag for css file inclusion, also responsible for dependency based sorting and minification
     *
     * @return string
     *
     * @throws Exception
     * @throws \Modseven\Exception
     */
    public function renderCSS() : string
    {
        return $this->process($this->_css, 'css');
    }

    /**
     * Returns the html tag for js file inclusion, also responsible for dependency based sorting and minification
     *
     * @return string
     *
     * @throws Exception
     * @throws \Modseven\Exception
     */
    public function renderJS() : string
    {
        return $this->process($this->_js, 'js');
    }

    /**
     * Returns the html tag for file inclusion, also responsible for dependency based sorting and minification
     *
     * @param array  $files Files Array with their dependencies
     * @param string $type  File Type (css or js)
     *
     * @return string The tag/tags
     *
     * @throws Exception
     * @throws \Modseven\Exception
     */
    public function process(array $files, string $type) : string
    {
        // First we check if minification is enabled, or minified file is available
        $doMinify = $this->_configuration->get($type . '_minify');

        if ($doMinify)
        {
            // Get the path to the minification file
            $minificationFile = $this->_configuration->get($type . '_minified');
            $fullPath = PUBPATH . $minificationFile;

            // Minified files only get rebuild every 24 Hours, to ensure higher performance, this is ofc even faster than caching
            // But if source files are modified, this one does not get rebuilt so make sure you turn minification off on development systems
            if (is_readable($fullPath) && (time()-filemtime($fullPath)) < $this->_configuration->get('lifetime', 86400))
            {
                //return HTML::style($minificationFile);
            }

            // If the minified version is to old let's check if that file is writable
            if (!is_writable($fullPath) || (!file_exists($fullPath) && !touch($fullPath)))
            {
                throw new Exception('Target File for minified assets ":file" must be writeable.', [
                    ':file' => $fullPath
                ]);
            }
        }

        // Get the path to source files
        $path = $this->_configuration->get($type . '_path');

        // Now we sort them by dependency
        $sorted = [];
        foreach ($files as $element) {
            $this->sort($element, $files, $sorted, ($doMinify ? PUBPATH. $path : ''), ($doMinify ? ('.' . $type) : ''));
        }

        // If minification is enabled we now minify those files
        if ($doMinify)
        {
            // Build minification class
            $minify = 'MatthiasMullie\\Minify\\' . mb_strtoupper($type);

            // And now minify
            $minify = new $minify($sorted);

            if ($minify->minify($minificationFile) !== false)
            {
                return HTML::style($minificationFile);
            }
        }

        // If minification is not enabled, we return one tag for each file
        $tags = '';

        // Determine render function
        $fn = 'style';
        if ($type === 'js') {
            $fn = 'script';
        }

        foreach ($sorted as $file)
        {
            $tags .= HTML::$fn($path . $file . '.' . $type) . PHP_EOL;
        }

        return $tags;
    }


    /**
     * Universal sort function used for JS and CSS
     * Sorts the files by their dependency
     *
     * @param array  $element  First Element to sort
     * @param array  $parent   The parent array which holds all requested files
     * @param array  $sorted   The referenced element which will contain all sorted items
     * @param string $path     If passed, this is prepended to the filename, generally needed for minification
     * @param string $ext      IF passed, this is appended to the filename, generally needed for minification
     *
     * @throws Exception
     */
    protected function sort(array $element, array $parent, array &$sorted, string $path = '', string $ext = '') : void
    {
        foreach ($element['dependencies'] as $dependency)
        {
            if (isset($parent[$dependency]))
            {
                $this->sort($parent[$dependency], $parent, $sorted, $path, $ext);
            }
            else
            {
                throw new Exception('Unresolved dependency');
            }
        }

        if (!isset($sorted[$element['file']]))
        {
            $sorted[$element['file']] = $path . $element['file'] . $ext;
        }
    }
}