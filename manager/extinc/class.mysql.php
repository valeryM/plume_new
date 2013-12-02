<?php
/* -*- tab-width: 4; indent-tabs-mode: nil; c-basic-offset: 4 -*- */
/*
# ***** BEGIN LICENSE BLOCK *****
# Version: MPL 1.1/GPL 2.0/LGPL 2.1
#
# The contents of this file are subject to the Mozilla Public License Version
# 1.1 (the "License"); you may not use this file except in compliance with
# the License. You may obtain a copy of the License at
# http://www.mozilla.org/MPL/
#
# Software distributed under the License is distributed on an "AS IS" basis,
# WITHOUT WARRANTY OF ANY KIND, either express or implied. See the License
# for the specific language governing rights and limitations under the
# License.
#
# The Original Code is DotClear Weblog.
#
# The Initial Developer of the Original Code is
# Olivier Meunier.
# Portions created by the Initial Developer are Copyright (C) 2003
# the Initial Developer. All Rights Reserved.
#
# Contributor(s):
#   Loic d'Anterroches
#
# Alternatively, the contents of this file may be used under the terms of
# either the GNU General Public License Version 2 or later (the "GPL"), or
# the GNU Lesser General Public License Version 2.1 or later (the "LGPL"),
# in which case the provisions of the GPL or the LGPL are applicable instead
# of those above. If you wish to allow use of your version of this file only
# under the terms of either the GPL or the LGPL, and not to allow others to
# use your version of this file under the terms of the MPL, indicate your
# decision by deleting the provisions above and replace them with the notice
# and other provisions required by the GPL or the LGPL. If you do not delete
# the provisions above, a recipient may use your version of this file under
# the terms of any one of the MPL, the GPL or the LGPL.
#
# ***** END LICENSE BLOCK ***** */



require_once dirname(__FILE__).'/class.recordset.php';

/**
 * MySQL connection class
 */
class Connection
{
    var $con_id;
    var $error;
    var $errno;
    var $pfx = '';
    var $debug = false;
    /** The last query, set with debug(). Used when an error is returned. */
    var $lastquery = '';

    function Connection($user, $pwd, $alias='', $dbname, $pfx='', $debug=false, $version='4.0')
    {
        $this->error = '';
        $this->con_id = @mysql_connect($alias, $user, $pwd);
        $this->debug = $debug;
        $this->pfx = $pfx;
        $this->debug('* MYSQL CONNECT');
        if (!$this->con_id) {
            $this->setError();
        } else {
            $this->database($dbname);
        }
        if (strlen($version) and version_compare($version, '4.1', '>=')) { 
            $this->execute('SET NAMES \'utf8\'');
        }
    }

    
    function database($dbname)
    {
        $db = @mysql_select_db($dbname);
        $this->debug('* USE DATABASE '.$dbname);
        if (!$db) {
            $this->setError();
            return false;
        } else {
            return true;
        }
    }

    /**
     * Log the queries. Keep track of the last query and if in debug mode
     * keep track of all the queries in 
     * $GLOBALS['_PX_debug_data']['sql_queries']
     *
     * @param string Query to keep track
     * @return bool true
     */
    function debug($query)
    {
        $this->lastquery = $query;
        if (!$this->debug) return true;
        if (!isset($GLOBALS['_PX_debug_data']['sql_queries'])) 
            $GLOBALS['_PX_debug_data']['sql_queries'] = array();
        $GLOBALS['_PX_debug_data']['sql_queries'][] = $query;
        return true;
    }

    /**
     * Get results from the cache
     *
     * @param string Query
     * @return mixed Array or false if not cached
     */
    function getFromCache($query)
    {
        if (!isset($this->cached_res[md5($query)])) {
            return false;
        }
        return $this->cached_res[md5($query)];
    }

    /**
     * Cache the results
     *
     * @param string Query
     * @param array Results
     * @return bool true
     */
    function setInCache($query, $res)
    {
        $this->cached_res[md5($query)] = $res;
        return true;
    }


    function close()
    {
        if ($this->con_id) {
            mysql_close($this->con_id);
            return true;
        } else {
            return false;
        }
    }

    function getTablesList() {
    	
    	$sql = 'SHOW TABLES;';
    	return $this->select($sql);    	
    }
    
    function getTableDefinition($table) {
    	$sql = "SHOW CREATE TABLE `".$table."`;";
    	/*
    	$cur = mysql_unbuffered_query($sql, $this->con_id);
    	if (!$cur) {
    		$this->setError();
    		return false;
    	} else {
    		$i = 0;
    		$arryRes = array();
    		while ($res = mysql_fetch_row($cur)) {
    			for ($j=0; $j<count($res); $j++) {
    				$arryRes[$i][strtolower(mysql_field_name($cur, $j))] = $res[$j];
    			}
    			$i++;
    		}
    		//echo print_r($arryRes,true);
    		return new $class($arryRes);
    	}
    	*/
    	return $this->select($sql);
    }

    function select($query, $class='recordset')
    {
        if (!$this->con_id) {
            return false;
        }

        if ($class == '' || !class_exists($class, true)) {
            $class = 'recordset';
        }
        $this->debug($query);
        $cur = mysql_unbuffered_query($query, $this->con_id);
        if ($cur) {
            // Insertion dans le reccordset
            $i = 0;
            $arryRes = array();
            while ($res = mysql_fetch_row($cur)) {
                for ($j=0; $j<count($res); $j++) {
                    $arryRes[$i][strtolower(mysql_field_name($cur, $j))] = $res[$j];
                }
                $i++;
            }
            if (func_num_args() == 4) {
                $_arg = func_get_arg(2);
                $_arg1 = func_get_arg(3);
                return new $class($arryRes, $_arg, $_arg1);
            }
            return new $class($arryRes);
        } else {
            $this->setError();
            return false;
        }
    }

    function execute($query)
    {
        if (!$this->con_id) {
            return false;
        }
        $this->cached_res = array();
        $this->debug($query);
        $cur = mysql_query($query, $this->con_id);
        if (!$cur) {
            $this->setError();
            return false;
        } else {
            return true;
        }
    }


    function getLastID()
    {
        if ($this->con_id) {
            $this->debug('* GET LAST ID');
            return mysql_insert_id($this->con_id);
        } else {
            return false;
        }
    }

    function setError()
    {
        if ($this->con_id) {
            $this->error = mysql_error($this->con_id).' - '.$this->lastquery;
            $this->errno = mysql_errno($this->con_id);
        } else {
            $this->error = mysql_error().' - '.$this->lastquery;
            $this->errno = mysql_errno();
        }
    }

    function error()
    {
        if ($this->error != '') {
            return $this->errno.' - '.$this->error;
        } else {
            return false;
        }
    }

    function escapeStr($str)
    {
        return mysql_real_escape_string($str);
    }

    function esc($str)
    {
        return mysql_real_escape_string($str);
    }
}
?>
