# php-dotenv

A simple and standalone DotEnv parser for PHP 5.6+ wich sticks to a unique goal : parse a Dotenv string/file and restitute it as an array of variables.

## Documentation

Full documentation is available at [https://packages.bgaze.fr/php-dotenv](https://packages.bgaze.fr/php-dotenv)

## Quick start

Simply install the library using composer:

```
composer require bgaze/php-dotenv
```

Use the `Helpers` class to parse DotenEnv file or string:  

```php
use \Bgaze\Dotenv\Helpers as DotEnv;

try {
    var_dump(DotEnv::fromString('a dotenv string', [ /* some default values */ ]));
    var_dump(DotEnv::fromFile('path/to/dotenv/file', [ /* some default values */ ]));
} catch (\Exception $e) {
    echo "<pre>{$e}</pre>";
}
```