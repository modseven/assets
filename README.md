<p align="center"><b>Modseven Assets Module</b></p>

##Installation
`composer require modseven/assets` ..that's it.

##Configuration
Copy the file(s) from vendor/modseven/assets/conf/ to your application/conf folder. Modify them as needed.
The following configuration options are available for each type (js/css)

|    Option      | Description                                                              | Default Value             |
|----------------|--------------------------------------------------------------------------|---------------------------|
|  type_minify   | IF true, then the files will be minified using matthiasmullie's minifier | FALSE                     |
|  type_path     | This is the directory where your source files are located                | public/<type>             |
|  type_minified | Location to the minified file, minfied code will be stored there.        | public/app.<type>         |
|  lifetime      | Lifetime for minified files, set high value on production servers here   | 86400 (24 Hours)          |

##Usage
Here is a example of adding, files with and without dependencies
````
// Basic example
$assets = \Modseven\Assets\Assets::instance();
$assets->addCSS('mycssfilewithoutextension', ['dependency1', 'dependency2']);

// Another example, this will render bootstrap first then navbar
$assets = \Modseven\Assets\Assets::instance();
$assets->addCSS('navbar', ['bootstrap']);
$assets->addCSS('bootstrap');

// ...even this is again somewher in the code it will just render "bootstrap" once
$assets->addCSS('bootstrap');
````

After adding files simply include them in your view. Example:
````
<html lang='en'>
    <head>
        <title>Assets Demo</title>
        <?php echo \Modseven\Assets\Assets::renderCSS(); ?>
    </head>
    <body>
        <!-- YOUR BODY -->
    </body>
</html>
````


## Contributing

Any help is more than welcome! Just fork this repo and do a PR.

## Special Thanks

Special Thanks to all Contributors and the Community!