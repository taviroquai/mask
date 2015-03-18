<?php

/**
 * Mask is a Trait that functions as a basic template engine
 * 
 * PHP version 5
 *
 * @category PHP
 * @package  Taviroquai\Mask
 * @author   Marco Afonso <mafonso333@gmail.com>
 * @license  http://opensource.org/licenses/MIT MIT
 * @link     http://github.com/taviroquai/duality
 * @since    0.1.0
 */

namespace Taviroquai\Mask;

/**
 * Mask is a Trait that functions as a basic template engine
 *
 * @category PHP
 * @package  Taviroquai\Mask
 * @author   Marco Afonso <mafonso333@gmail.com>
 * @license  http://opensource.org/licenses/MIT MIT
 * @link     http://github.com/taviroquai/duality
 * @since    0.1.0
 */
trait Mask
{
    /**
     * Sets the cache path
     * 
     * @var string
     */
    public static $templateCachePath = './cache';
    
    /**
     * Sets the templates path
     * @var string
     */
    public static $templatePath = '.';
    
    private $_template = '';
    private $_data = array();
    private $_patterns = array(
        'for'       => '/\{\{\sfor\s([a-z0-9_]+)\sas\s([a-z0-9_]+)\s\}\}(.*?)\{\{\sendfor\s\}\}/isU',
        'if'        => '/\{\{\sif\s([a-z_]+)\s\}\}(.*?)\{\{\sendif\s\}\}/isU',
        'var'       => '/\{\{\s([a-z0-9_]+?|[a-z0-9_]+?\.[a-z0-9_]+?)\s\}\}/isU',
        'include'   => '/\{\{\sinclude\s([a-z0-9_]+)\s\}\}/isU',
    );
    
    /**
     * Gets what properties can be published
     * 
     * @return array
     */
    public function getTemplateData()
    {
        $data = get_object_vars($this);
        unset($data['template']);
        unset($data['patterns']);
        unset($data['data']);
        return $data;
    }

    /**
     * Gets the rendered template
     * 
     * @param string $filename The templaye filename without extension
     * 
     * @return string
     */
    public function mask($filename)
    {
        // Load default data or data parameter
        $this->data = (array) $this->getTemplateData();
        
        // Compile and return cache file
        $this->_clearCache();
        $path = $this->_resolvePath($filename);
        $cache = $this->_compileCacheFile(
            $path,
            $this->_compileRecursive(file_get_contents($path))
        );
        
        // Get output
        ob_start();
        extract($this->data);
        include $cache;
        return (string) ob_get_clean();
    }
    
    /**
     * Compiles cache file
     * 
     * @param string $filename The template filename
     * @param string $template The final compiled string
     * 
     * @return string The compiled cache filepath
     */
    private function _compileCacheFile($filename, $template)
    {
        $cache = self::$templateCachePath
                . DIRECTORY_SEPARATOR
                . 'templater_'
                . md5(file_get_contents($filename)) . '.php';
        if (!file_exists($cache)) {
            file_put_contents($cache, $template);
        }
        return (string) $cache;
    }
    
    /**
     * Compiles all template matches
     * 
     * @param string $subject The string to match against
     * @param array  $data    The local context variable names
     * 
     * @return string The final compiled string
     */
    private function _compileRecursive($subject, $data = array())
    {
        return (string) $this->_compileVar(
            $this->_compileIf(
                $this->_compileFor(
                    $this->_compileInclude(
                        $subject, $data
                    ), $data
                ), $data
            ), $data
        );
    }
    
    /**
     * Compiles all variables
     * 
     * @param string $subject The string to match against
     * @param array  $data    The local context variable names
     * 
     * @return string The compiled string
     */
    private function _compileVar($subject, $data)
    {
        return (string) preg_replace_callback(
            $this->_patterns['var'],
            function ($match) use ($data) {
                return '<?=' . $this->_matchToVar($match[1], $data) . '?>';
            },
            $subject
        );
    }
    
    /**
     * Compiles all IF matches
     * 
     * @param string $subject The string to match against
     * @param array  $data    The local context variable names
     * 
     * @return string The compiled string
     */
    private function _compileIf($subject, $data = array())
    {
        return preg_replace_callback(
            $this->_patterns['if'],
            function ($match) use ($data) {
                return '<?php if (' 
                    . $this->_matchToVar($match[1], $data, '!empty(%s)') 
                    . ') : ?>'
                    . $this->_compileRecursive($match[2], $data)
                    . '<?php endif; ?>';
            },
            $subject
        );
    }
    
    /**
     * Compiles all Include matches
     * 
     * @param string $subject The string to match against
     * @param array  $data    The local context variable names
     * 
     * @return string The compiled string
     */
    private function _compileInclude($subject, $data = array())
    {
        return preg_replace_callback(
            $this->_patterns['include'],
            function ($match) use ($data) {
                $include = file_get_contents($this->_resolvePath($match[1]));
                return $this->_compileRecursive($include, $data);
            },
            $subject
        );
    }
    
    /**
     * Compiles all FOR matches
     * 
     * @param string $subject The string to match against
     * @param array  $data    The local context variable names
     * 
     * @return string The compiled string
     */
    private function _compileFor($subject, $data = array())
    {
        return preg_replace_callback(
            $this->_patterns['for'],
            function ($match) use ($data) {
                $data[] = $match[2];
                return '<?php foreach('
                    . $this->_matchToVar($match[1], $data) 
                    . ' as $' . $match[2] . ') { ?>'
                    . $this->_compileRecursive($match[3], $data)
                    . '<?php } ?>';
            },
            $subject
        );
    }
    
    /**
     * Resolves the template filename path
     * 
     * @param string $filename The template filename
     * 
     * @return string The resolved file path
     * 
     * @throws \Exception The file not found exception
     */
    private function _resolvePath($filename)
    {
        $filename = self::$templatePath
            . DIRECTORY_SEPARATOR
            . $filename 
            . '.html';
        if (!file_exists($filename) || !is_file($filename)) {
            $msg = 'Templater error: file not found ('
                . $filename . ') for ' 
                . get_class($this);
            throw new \Exception($msg);
        }
        return $filename;
    }
    
    /**
     * Clears cache
     * 
     * @param int $prob   The probability to run < 100
     * @param int $expire The expire time in seconds (defauls 7 days)
     * 
     * @return void
     */
    private function _clearCache($prob = 4, $expire = 604800)
    {
        if (rand(1, 100) <= $prob) {
            foreach (glob(self::$templateCachePath.'/*') as $item) {
                if (time() - filemtime($item) > $expire && !is_dir($item)) {
                    unlink($item);
                }
            }
        }
    }
    
    /**
     * Translates match to varibale/function name
     * 
     * @param array  $match   The preg_match matches
     * @param array  $data    The local variable names
     * @param string $wrapper A wrapper to envolve the final name
     * 
     * @return string The final callable var/function name
     * 
     * @throws \Exception Variable/function name not found exception
     */
    private function _matchToVar($match, $data = array(), $wrapper = '%s')
    {
        $out = '';
        $names = explode('.', $match);
        if (count($names) == 1) {
            if (method_exists($this, $names[0])) {
                $out .= '$this->' . $names[0] .'()';
            } elseif (isset($this->data[$names[0]])) {
                $out .= '$' . $names[0];
            } elseif (in_array($names[0], $data)) {
                $out .= '$' . $names[0];
            } else {
                $msg = 'Templater error: undefined '
                    . $names[0] . ' in ' 
                    . get_class($this);
                throw new \Exception($msg);
            }
        }
        if (isset($names[1])) {
            $out .= '->' . $names[1];
        }
        return (string) sprintf($wrapper, $out);
    }
}
