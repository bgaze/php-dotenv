# php-dotenv

This library is a simple and standalone DotEnv files parse for PHP 5.6+

## Installation

Simply install the library using composer:

```
composer require bgaze/php-dotenv
```

## Basic usage

To quickly parse a Dotenv file, use the provided `load` helper.
An `UnexpectedValueException` will be raised if the file is invalid. 

```php
use Bgaze\Dotenv\Parser as Dotenv;

var_dump(Dotenv::load('path/to/dotenv/file')->toArray());
```

You can also define defaults values and/or create PHP constants for your application configuration:

```php
Dotenv::load('path/to/dotenv/file')
    ->defaults(['MY_VAR' => 'a default value'])
    ->define();
```

## Advanced usage

If you want to get parsing errors, instanciate the parser manually:

```
$dotenv = new Dotenv('path/to/dotenv/file');

if ($dotenv->valid()) {
    var_dump($dotenv->errors());
} else {
    var_dump($dotenv->toArray());
}
```

## Available methods

**load:**

Instantiate a dotenv parser, parse provided file and throw an exception if invalid.

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

Check if errors occurs while parsing doentenv file

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

Reset parser and parse provided dotenv file.

```php
/**
 * @param string $path Path oh the file to parse
 */
public function parse($path);
```
