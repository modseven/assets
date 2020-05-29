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
    'js_path' => 'js' . DIRECTORY_SEPARATOR,
    'css_path' => 'css' . DIRECTORY_SEPARATOR,
    'js_minified' => 'js' . DIRECTORY_SEPARATOR . 'app.js',
    'css_minified' => 'css' . DIRECTORY_SEPARATOR . 'app.css',
    'lifetime' => 86400
];