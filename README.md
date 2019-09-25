# php-dotenv

This library is a simple and standalone DotEnv files parser for PHP 5.6+

## Installation

Simply install the library using composer:

```
composer require bgaze/php-dotenv
```

## Usage

To quickly parse a Dotenv file, use the `load` helper.  
An `UnexpectedValueException` will be raised if the file is invalid. 

```php
use Bgaze\Dotenv\Parser as Dotenv;

try {
    var_dump(Dotenv::load('path/to/dotenv/file')->toArray());
} catch (\Exception $e) {
    echo "<pre>{$e}</pre>";
}
```

You can use fluently most a available methods to manipulate your configuration:

```php
Dotenv::load('path/to/dotenv/file')   // Load a dotenv file
    ->defaults([ /* ... */ ])         // Set some default values
    ->trim()                          // Remove empty vars.
    ->define();                       // Create PHP constants.
```

If you want to get parsing errors, instanciate the parser manually:

```php
$dotenv = new Dotenv('path/to/dotenv/file');

if ($dotenv->valid()) {
    var_dump($dotenv->errors());
} else {
    var_dump($dotenv->toArray());
}
```

## Available methods

**load:**

Instantiate a Dotenv parser, parse provided file and throw an exception if invalid.

```php
/**
 * @param type $path
 * @return \Bgaze\Dotenv\Parser
 * @throws \InvalidArgumentException
 * @throws \UnexpectedValueException
 */
public static function load($path); 
```

**toArray:**

Get parsed content as an array.

```php
/**
 * @return array
 */
public function toArray();
```

**toJson:**

Get parsed content encoded to json.  
See [json_encode](http://php.net/manual/en/function.json-encode.php) PHP function for flags usage.

```php
/**
 * @param integer $flags Option flags for the json_encode function  
 * @return string Returns a JSON encoded string on success or FALSE on failure
 */
public function toJson($flags = 0);
```

**define:**

Define all vars into Dotenv file as PHP constants.

```php
/**
 * @return $this
 */
public function define();
```

**trim:**

Unset all empty constant except if value === false.

```php
/**
 * @return $this
 */
public function trim();
```

**defaults:**

Set default values for missing keys or non false empty values.

```php
/**
 * @param array $defaults The array of defaults values
 * @return $this
 */
public function defaults(array $defaults);
```

**valid:**

Check if errors occurs while parsing the Dotenv file

```php
/**
 * @return boolean
 */
public function valid();
```

**errors:**

Get doentenv file parsing errors

```php
/**
 * @return array
 */
public function errors();
```

**parse:**

Reset parser then parse provided Dotenv file.

```php
/**
 * @param string $path Path oh the file to parse
 */
public function parse($path);
```
