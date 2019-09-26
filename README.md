# php-dotenv

This library is a simple and standalone DotEnv files parser for PHP 5.6+

## Installation

Simply install the library using composer:

```
composer require bgaze/php-dotenv
```

## Usage

To quickly parse a Dotenv file, use the `load` helper.  
An exception will be raised if the file is invalid. 

```php
use Bgaze\Dotenv\Parser as Dotenv;

try {
    $dotenv = Dotenv::load('path/to/dotenv/file');
    var_dump($dotenv->toArray());
    var_dump($dotenv->get('A_KEY', 'a-default-value'));
} catch (\Exception $e) {
    echo "<pre>{$e}</pre>";
}
```

You can use fluently most of available methods to manipulate your configuration:

```php
echo Dotenv::load('path/to/dotenv/file')   // Load a dotenv file
    ->defaults([ /* ... */ ])              // Set some default values
    ->trim()                               // Remove empty vars.
    ->toJson(JSON_PRETTY_PRINT);           // Encode as prettified JSON.
```

To access to parsing errors, instanciate the parser manually:

```php
$dotenv = new Dotenv('path/to/dotenv/file');

if ($dotenv->valid()) {
    var_dump($dotenv->toArray());
} else {
    var_dump($dotenv->errors());
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

**get:**

Retrieve a value by its key.

```php
/**
* @param string $key The key to find
* @param mixed $default A default value if the key doesn't exists
* @return mixed
 */
public function get($key, $default = null);
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

Get Dotenv file parsing errors

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
