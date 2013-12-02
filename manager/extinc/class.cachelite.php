<?php
/**
* Fast, light and safe Cache Class
*
* CacheLite is a fast, light and safe cache system. It's optimized
* for file containers. It is fast and safe (because it uses file
* locking and/or anti-corruption tests).
*
* This a simplified version of the Cache_Lite class available in PEAR
*
* Based on version $ Id: Lite.php,v 1.11 2003/02/23 16:18:53 fab Exp $ of Cache_Lite
* Original author Fabien MARTY <fab@php.net>
*
*/

class CacheLite
{

    // --- Private properties ---

    /**
    * Directory where to put the cache files
    * (make sure to add a trailing slash)
    *
    * @var string $_cacheDir
    */
    var $_cacheDir = '/tmp/';

    /**
    * Enable / disable caching
    *
    * (can be very usefull for the debug of cached scripts)
    *
    * @var boolean $_caching
    */
    var $_caching = true;

    /**
    * Enable / disable fileLocking
    *
    * (can avoid cache corruption under bad circumstances)
    *
    * @var boolean $_fileLocking
    */
    var $_fileLocking = true;

    /**
    * Timestamp of the last valid cache
    *
    * @var int $_refreshTime
    */
    var $_refreshTime;

    /**
    * File name (with path)
    *
    * @var string $_file
    */
    var $_file;

    /**
    * Enable / disable write control (the cache is read just after writing to detect corrupt entries)
    *
    * Enable write control will lightly slow the cache writing but not the cache reading
    * Write control can detect some corrupt cache files but maybe it's not a perfect control
    *
    * @var boolean $_writeControl
    */
    var $_writeControl = true;

    /**
    * Enable / disable read control
    *
    * If enabled, a control key is embeded in cache file and this key is compared with the one
    * calculated after the reading.
    *
    * @var boolean $_writeControl
    */
    var $_readControl = false;

    /**
    * Current cache id
    *
    * @var string $_id
    */
    var $_id;

    // --- Public methods ---

    /**
    * Constructor
    *
    * $options is an assoc. Available options are :
    * $options = array(
    *     'cacheDir' => directory where to put the cache files (string),
    *     'caching' => enable / disable caching (boolean),
    *     'lifeTime' => cache lifetime in seconds (int),
    *     'fileLocking' => enable / disable fileLocking (boolean),
    *     'writeControl' => enable / disable write control (boolean),
    *     'readControl' => enable / disable read control (boolean),
    * );
    *
    * @param array $options options
    * @access public
    */
    function CacheLite($options = array(NULL))
    {
        $availableOptions = '{cacheDir}{caching}{lifeTime}{fileLocking}{writeControl}{readControl}';
        while (list($key, $value) = each($options)) {
            if (strpos('>'.$availableOptions, '{'.$key.'}')) {
                $property = '_'.$key;
                $this->$property = $value;
            }
        }
        $this->_refreshTime = time() - $this->_lifeTime;
    }

    /**
    * Test if a cache is available and (if yes) return it
    *
    * @param string $id cache id
    * @param boolean $doNotTestCacheValidity if set to true, the cache validity won't be tested
    * @return string data of the cache (or false if no cache available)
    * @access public
    */
    function get($id, $doNotTestCacheValidity = false)
    {
        $this->_id = $id;
        $data = false;
        if ($this->_caching) {
            $this->_setFileName($id);
            if ($doNotTestCacheValidity) {
                if (file_exists($this->_file)) {
                    $data = $this->_read();
                }
            } else {
                if (@filemtime($this->_file) > $this->_refreshTime) {
                    $data = $this->_read();
                }
            }
            return $data;
        }
        return false;
    }

    /**
    * Save some data in a cache file
    *
    * @param string $data data to put in cache
    * @param string $id cache id
    * @return boolean true if no problem
    * @access public
    */
    function save($data, $id = NULL)
    {
        if ($this->_caching) {
            if (isset($id)) {
                $this->_setFileName($id);
            }
            if ($this->_writeControl) {
                if (!$this->_writeAndControl($data)) {
                    @touch($this->_file, time() - 2*abs($this->_lifeTime));
                    return false;
                } else {
                    return true;
                }
            } else {
                return $this->_write($data);
            }
        }
        return false;
    }

    /**
    * Remove a cache file
    *
    * @param string $id cache id
    * @return boolean true if no problem
    * @access public
    */
    function remove($id)
    {
        $this->_setFileName($id);
        return @unlink($this->_file);
    }

    /**
    * Clean the cache
    *
    * if no group is specified all cache files will be destroyed
    * else only cache files of the specified group will be destroyed
    *
    * @param string $group name of the cache group
    * @return boolean true if no problem
    * @access public
    */
    function clean()
    {
        $motif = 'cache_';
        if (!($dh = opendir($this->_cacheDir))) {
            return false;
        }
        while ($file = readdir($dh)) {
            if (($file != '.') && ($file != '..')) {
                $file = $this->_cacheDir . $file;
                if (is_file($file)) {
                    if (strpos($file, $motif, 0)) {
                        if (!@unlink($file)) {
                            return false;
                        }
                    }
                }
            }
        }
        return true;
    }

    /**
    * Set a new life time
    *
    * @param int $newLifeTime new life time (in seconds)
    * @access public
    */
    function setLifeTime($newLifeTime)
    {
        $this->_lifeTime = $newLifeTime;
        $this->_refreshTime = time() - $newLifeTime;
    }


    // --- Private methods ---

    /**
    * Make a file name (with path)
    *
    * @param string $id cache id
    * @access private
    */
    function _setFileName($id)
    {
		$this->_file = $this->_cacheDir.'cache_'.$id;
    }

    /**
    * Read the cache file and return the content
    *
    * @return string content of the cache file
    * @access private
    */
    function _read()
    {
        $fp = @fopen($this->_file, "r");
        if ($this->_fileLocking) @flock($fp, LOCK_SH);
        if ($fp) {
            clearstatcache(); // because the filesize can be cached by PHP itself...
            $length = @filesize($this->_file);
            $mqr = get_magic_quotes_runtime();
            set_magic_quotes_runtime(0);
            if ($this->_readControl) {
                $hashControl = @fread($fp, 32);
                $length = $length - 32;
            }
            $data = @fread($fp, $length);
            set_magic_quotes_runtime($mqr);
            if ($this->_fileLocking) @flock($fp, LOCK_UN);
            @fclose($fp);
            if ($this->_readControl) {
                $hashData = $this->_hash($data, $this->_readControlType);
                if ($hashData != $hashControl) {
                    @touch($this->_file, time() - 2*abs($this->_lifeTime));
                    return false;
                }
            }
            return $data;
        }
        return false;
    }

    /**
    * Write the given data in the cache file
    *
    * @param string $data data to put in cache
    * @return boolean true if ok
    * @access private
    */
    function _write($data)
    {
        $fp = @fopen($this->_file, "w");
        if ($fp) {
            if ($this->_fileLocking) @flock($fp, LOCK_EX);
            if ($this->_readControl) {
                @fwrite($fp, $this->_hash($data, $this->_readControlType), 32);
            }
            $len = strlen($data);
            @fwrite($fp, $data, $len);
            if ($this->_fileLocking) @flock($fp, LOCK_UN);
            @fclose($fp);
            return true;
        }
        return false;
    }

    /**
    * Write the given data in the cache file and control it just after to avoir corrupted cache entries
    *
    * @param string $data data to put in cache
    * @return boolean true if the test is ok
    * @access private
    */
    function _writeAndControl($data)
    {
        $this->_write($data);
        $dataRead = $this->_read($data);
        return ($dataRead==$data);
    }

    /**
    * Make a control key with the string containing datas
    *
    * @param string $data data
    * @return string control key
    * @access private
    */
    function _hash($data, $controlType)
    {
        return sprintf('% 32d', crc32($data));
    }
    
}
?>
