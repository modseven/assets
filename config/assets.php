<?php
/**
 * Assets configuration.
 *
 * @copyright   (c) since 2020 Tobias Oitzinger
 * @license     http://koseven.dev/license
 */

return [
    'js_minify' => false,
    'css_minify' => false,
    'js_path' => PUBPATH . 'js' . DIRECTORY_SEPARATOR,
    'css_path' => PUBPATH . 'css' . DIRECTORY_SEPARATOR,
    'js_minified' => PUBPATH . 'js' . DIRECTORY_SEPARATOR . 'app.js',
    'css_minified' => PUBPATH . 'css' . DIRECTORY_SEPARATOR . 'app.css',
    'lifetime' => 86400
];