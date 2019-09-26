# php-dotenv

This library is a simple and standalone DotEnv parser for PHP 5.6+

## Installation

Simply install the library using composer:

```
composer require bgaze/php-dotenv
```

## Usage

To quickly parse Dotenv, use helper functions from the `Dotenv` class:  

```php
use \Bgaze\Dotenv\Dotenv;

try {
    var_dump(Dotenv::fromString('a dotenv string', [ /* some default values */ ]));
} catch (\Exception $e) {
    echo "<pre>{$e}</pre>";
}

try {
    var_dump(Dotenv::fromFile('path/to/dotenv/file', [ /* some default values */ ]));
} catch (\Exception $e) {
    echo "<pre>{$e}</pre>";
}
```

You can also use directly the `Parser` class:

```php
use \Bgaze\Dotenv\Parser;

$parser = new Parser();

if ($parser->parseString('a dotenv string')) {
    var_dump($parser->get());
} else {
    var_dump($parser->errors());
}

if ($parser->parseFile('path/to/dotenv/file')) {
    var_dump($parser->get());
} else {
    var_dump($parser->errors());
}
```

## Documentation

### Dotenv class

**fromString:**

Parse provided string, throw an exception if invalid, otherwise return parsed content as a key-value array.

```php
/**
 * @param string $string The string to parse
 * @param array $defaults An array of defaults values
 * @return array The parsed content
 * 
 * @throws \UnexpectedValueException
 */
public static function fromString($string, array $defaults = []);
```

**fromFile:**

Parse provided file, throw an exception if invalid, otherwise return parsed content as a key-value array.

```php
/**
 * @param string $path The file to parse
 * @param array $defaults An array of defaults values
 * @return array The parsed content
 * 
 * @throws \InvalidArgumentException
 * @throws \UnexpectedValueException
 */
public static function fromFile($path, array $defaults = []);
```

### Parser class

**parseString:**

Reset parser then parse provided string.

```php
/**
 * @param string $string The string to parse
 * @return boolean
 */
public function parseString($string);
```

**parseFile:**

Reset parser then parse provided file.

```php
/**
 * @param string $path Path oh the file to parse
 * @return boolean
 */
public function parseFile($path);
```

**get:**

Get parsed content array.

```php
/**
 * @return array
 */
public function get();
```

**errors:**

Get parsing errors array.

```php
/**
 * @return array
 */
public function errors();
```