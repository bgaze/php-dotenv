<?php

namespace Bgaze\Dotenv;

/**
 * A simple dotenv parser for PHP.
 *
 * @author Bgaze <benjamin@bgaze.fr>
 */
class Parser {

    /**
     * Parsed content.
     * 
     * @var array 
     */
    protected $content;

    /**
     * Parsing errors.
     * 
     * @var array 
     */
    protected $errors;

    /**
     * Current parsed line number.
     * 
     * @var integer 
     */
    protected $line;

    # INSTANTIATION
    ############################################################################

    /**
     * Instantiate parser and parse a file if provided.
     * 
     * @param mixed $path The path of a file to parse
     */
    public function __construct($path = null) {
        if ($path) {
            $this->parse($path);
        }
    }

    /**
     * Instantiate a dotenv parser, parse provided file and throw an exception if invalid.
     * 
     * @param type $path
     * @return \Bgaze\Dotenv\Parser
     * @throws \InvalidArgumentException
     * @throws \UnexpectedValueException
     */
    public static function load($path) {
        if (!file_exists($path)) {
            throw new \InvalidArgumentException("Dotenv file doesn't exists: $path");
        }

        $dotenv = new Parser($path);

        if (!$dotenv->valid()) {
            $count = count($dotenv->errors());
            $errors = implode(' ', $dotenv->errors());
            throw new \UnexpectedValueException("{$count} error(s) where detected into {$path} dotenv file. {$errors}");
        }

        return $dotenv;
    }

    # CONTENT MANAGEMENT
    ############################################################################

    /**
     * Get parsed content as an array.
     * 
     * @return array
     */
    public function toArray() {
        return $this->content;
    }

    /**
     * Get parsed content encoded to json.
     * 
     * @param integer $flags Option flags for the json_encode function  
     * @return string Returns a JSON encoded string on success or FALSE on failure
     * 
     * @see json_encode()
     * @link http://php.net/manual/en/function.json-encode.php
     */
    public function toJson($flags = 0) {
        return json_encode($this->content, $flags);
    }

    /**
     * Define all vars into Dotenv file as PHP constants.
     * 
     * @return $this
     */
    public function define() {
        foreach ($this->content as $name => $value) {
            define($name, $value);
        }

        return $this;
    }

    /**
     * Unset all empty constant except if value === false.
     * 
     * @return $this
     */
    public function trim() {
        foreach ($this->content as $k => $v) {
            if ($v !== false && empty($v)) {
                unset($this->content[$k]);
            }
        }

        return $this;
    }

    /**
     * Set default values for missing keys or non false empty values.
     * 
     * @param array $defaults The array of defaults values
     * @return $this
     */
    public function defaults(array $defaults) {
        foreach ($defaults as $k => $v) {
            if (!isset($this->content[$k]) || (empty($this->content[$k]) && $v !== false)) {
                $this->content[$k] = $v;
            }
        }

        return $this;
    }

    # ERRORS MANAGEMENT
    ############################################################################

    /**
     * Check if errors occurs while parsing doentenv file
     * 
     * @return boolean
     */
    public function valid() {
        return empty($this->errors);
    }

    /**
     * Get doentenv file parsing errors
     * 
     * @return array
     */
    public function errors() {
        return $this->errors;
    }

    # DOTENV FILE PARSING
    ############################################################################

    /**
     * Reset parser and parse provided dotenv file.
     * 
     * @param string $path Path oh the file to parse
     */
    public function parse($path) {
        // Reset.
        $this->line = 0;
        $this->content = [];
        $this->errors = [];

        // Read the dotenv file line by line.
        $handle = fopen($path, 'r');
        while (($line = fgets($handle))) {
            // Trim line then parse it.
            $this->line++;
            $this->parseLine(trim($line));
        }
        fclose($handle);

        // Expand variables.
        $this->expandVariables();
    }

    /**
     * Parse a dotenv file line
     * 
     * @param string $line The line to parse
     */
    protected function parseLine($line) {
        // Skip empty lines and comments.
        if (empty($line) || preg_match('/^#/', $line)) {
            return;
        }

        // Get key and value segments.
        $kv = explode("=", $line, 2);
        if (count($kv) !== 2) {
            $this->errors[] = "Line #{$this->line} doesn't respect 'KEY=VALUE' syntax.";
            return;
        }
        $key = trim($kv[0]);
        $value = trim($kv[1]);

        // Check that key format is valid.
        if (!preg_match('/^[A-Z][A-Z0-9_]*$/i', $key)) {
            $this->errors[] = "Line #{$this->line}: key can only contain alphanumeric and underscores, and can't start with a number.";
            return;
        }

        // Parse quoted string value.
        if (preg_match('/^[\'"]/', $value)) {
            $this->content[$key] = $this->parseQuotedValue($value);
            return;
        }

        // Remove comment into value if present, then parse value.
        $comment = strpos($value, '#');
        if ($comment) {
            $value = trim(substr($value, 0, $comment));
        }
        $this->content[$key] = $this->parseUnquotedValue($value);
    }

    /**
     * Parse an unquoted dotenv line value.
     * 
     * @param string $value The line value
     * @return mixed 
     */
    protected function parseUnquotedValue($value) {
        // Value is empty or commented or explicitly null.
        if (empty($value) || preg_match('/^#/', $value) || strtolower($value) === 'null') {
            return null;
        }

        // If value contain space, it muyst be quoted.
        if (preg_match('/\s/', $value)) {
            $this->errors[] = "Line #{$this->line}: values containing spaces must be wrapped with quotes.";
            return null;
        }

        // Value is explicitly true.
        if (strtolower($value) === 'true') {
            return true;
        }

        // Value is explicitly false.
        if (strtolower($value) === 'false') {
            return false;
        }

        // Value is numeric.
        if (is_numeric($value)) {
            return (strpos($value, '.') !== false) ? (float) $value : (int) $value;
        }

        // Value is a simple string.
        return $value;
    }

    /**
     * Parse a simple or double quoted  dotenv line value.
     * 
     * @param string $value The string to parse
     * @return string The final string value.
     */
    protected function parseQuotedValue($value) {
        // Check if quotes are closed.
        $quote = substr($value, 0, 1);
        if (!preg_match("/{$quote}((?:[^{$quote}\\\\]*(?:\\\\.)?)*){$quote}(.*)/", $value, $matches)) {
            $this->errors[] = "Line #{$this->line}: missing closing quote.";
            return null;
        }

        // Check trailing content.
        if (!empty($matches[2]) && !preg_match('/^#/', trim($matches[2]))) {
            $this->errors[] = "Line #{$this->line}: invalid content after closing quote.";
            return null;
        }

        // Purify the string.
        return strtr($matches[1], ["\\n" => "\n", "\\\"" => "\"", '\\\'' => "'", '\\t' => "\t"]);
    }

    /**
     * Expand variables into parsed content.
     */
    protected function expandVariables() {
        do {
            $expanded = false;

            foreach ($this->content as $key => $value) {
                if ($this->expandLineVariables($key, $value)) {
                    $expanded = true;
                }
            }
        } while ($expanded);
    }

    /**
     * Check if a line value is expandable, or contain expandable(s) variable(s), and expand if needed.
     * 
     * @param string $key    The line key
     * @param string $value  The line value
     * @return boolean       Has something been expanded
     */
    protected function expandLineVariables($key, $value) {
        // Value is an expandable variable.
        if (preg_match('/^\$\{([A-Z][A-Z0-9_]*)\}$/', $value, $matches)) {
            $this->content[$key] = isset($this->content[$matches[1]]) ? $this->content[$matches[1]] : null;
            return true;
        }

        // Value contains expandable variables.
        if (preg_match_all('/\$\{([A-Z][A-Z0-9_]*)\}/', $value, $matches)) {
            foreach (array_combine($matches[0], $matches[1]) as $pattern => $var) {
                $replace = isset($this->content[$var]) ? $this->content[$var] : '';
                $value = str_replace($pattern, $replace, $value);
            }

            $this->content[$key] = $value;

            return true;
        }

        // No expandable variable into value.
        return false;
    }

}
