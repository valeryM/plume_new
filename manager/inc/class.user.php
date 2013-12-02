<?php
/* -*- tab-width: 4; indent-tabs-mode: nil; c-basic-offset: 4 -*- */
/*
# ***** BEGIN LICENSE BLOCK *****
# This file is part of Plume CMS, a website management application.
# Copyright (C) 2001-2005 Loic d'Anterroches and contributors.
#
# Plume CMS is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.
#
# Plume CMS is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
#
# ***** END LICENSE BLOCK ***** */

/*=============================================================================
 Class User
=============================================================================*/
define('PX_USER_NOINIT', 0);
define('PX_USER_INIT',   1);
define('PX_USER_SYNCHRO_FROM_SESSION', 2);
define('PX_USER_SYNCHRO_TO_SESSION',   3);

require_once dirname(__FILE__).'/../extinc/class.recordset.php';

/**
 * The User class stores the user data when doing manipulation on the user.
 * Through RecordSet the User class extends the CError class. When a call
 * to a method is not successfull it is always possible to get the reason
 * by accessing the error message through the methods provided by CError.
 */
class User extends RecordSet
{
    var $prefs   = array();
    var $webs    = array();
    var $wdata   = array();
    var $website = ''; //current website
    var $lang    = ''; //current lang (can change with the website)
	var $cats = null; // list of the category;
    var $con     = null;
    var $path_media = '';

    /**
     * Constructor. 
     * The user object is initialized from the id (integer) or 
     * username (string). If no id or username is given, an empty
     * User object is created.
     *
     * @param mixed Id or username ('')
     */
    function User($user='')
    {
        if (!empty($user) and !is_array($user)) {
            $this->con =& pxDBConnect();
            $this->load($user);
        } elseif (is_array($user)) {
            parent::recordset($user);
        }
    }

    /**
     * Load user data from the database.
     * Given an id (integer) or a username (string) the data from the
     * corresponding user are loaded from the database.
     * If no id or username are given, try to load from the user_id 
     * taken from $this->f('user_id')
     *
     * @param mixed Id or username of the user ('')
     * @return bool Success
     */
    function load($user='')
    {
        if (empty($user)) {
            $user = $this->f('user_id');
            if (empty($user)) {
                $this->setError('Error: No user_id or username given, impossible to load the user.');
                return false;
            }
        }
        $user_id = 0;
        if (!preg_match('/[^0-9]/', $user)) {
            //Only digits, this is the user id
            $user_id = $user;
        }

        $this->con =& pxDBConnect();
        if ($user_id === 0) {
            $req = 'SELECT * FROM '.$this->con->pfx.'users 
                    WHERE user_username
                    LIKE \''.$this->con->escapeStr($user).'\' LIMIT 1';
        } else {
            //The previous check ensures that $user is a safe string
            //with only digits.
            $req = 'SELECT * FROM '.$this->con->pfx.'users 
                    WHERE user_id='.$user.' LIMIT 1';
        }
        if (($rs = $this->con->select($req)) !== false) {
            //init the internal array with the user data
            parent::recordset($rs->getData()); 

            if (false === $this->loadPrefs() ||
                false === $this->loadWebsites() ||
				false === $this->loadCategories() ) {
                return false;
            }
        } else {
            $this->setError('MySQL: '. $this->con->error(), 500);
            return false;
        }
        return true;
    }


    /**
     * Return the user id.
     *
     * @return int User id
     */
    function getId()
    {
        return $this->f('user_id');
    }

    /**
     * Get the list of resources created by the user.
     *
     * @param string Website id, all the websites if empty ('')
     * @return mixed Recordset object with the resources or false
     *
     * @todo Should return a ResourceSet object.
     */
    function getListResources($website='')
    {
        $userid = $this->f('user_id');
        $this->con =& pxDBConnect();

        $r = 'SELECT * FROM '.$this->con->pfx.'resources
              WHERE  user_id = \''.$this->con->escapeStr($userid).'\'';
        if (!empty($website))
            $r .= ' AND website_id = \''.$this->con->escapeStr($website).'\'';
    
        if (($rs = $this->con->select($r)) !== false) {
            return $rs;
        } else {
            $this->setError('MySQL: ' . $this->con->error(), 500);
            return false;
        }
    }

    /**
     * Get the rights on a given website.
     *
     * @param string Website id
     * @return int Level
     */
    function getWebsiteLevel($website)
    {
        $levels = $this->getWebsiteLevels();
        if (isset($levels[$website])) {
            return $levels[$website];
        }
        return 0;
    }

    /**
     * Get the levels for the websites.
     *
     * If no user id is given, uses the current one.
     *
     * @param int User id ('')
     * @return array Associative array array('websiteid' => level, ...);
     */
    function getWebsiteLevels($user_id='')
    {
        if ('' == $user_id) {
            $user_id = $this->f('user_id');
        }
        $res = array();
        $this->con =& pxDBConnect();

        $req = SQL::getWebsiteLevels($user_id);
        if (($rs = $this->con->select($req)) !== false) {
            while (!$rs->EOF()) {
                $res[$rs->f('website_id')] = $rs->f('level');
                $rs->moveNext();
            }
        } else {
            $this->setError('MySQL: ' . $this->con->error(), 500);
            return false;
        }
        return $res;
    }



    /**
     * Clean the current user object.
     * This is a basic cleaning, some state variables from the recordset
     * and CError parents are not cleaned.
     *
     * @return void
     */
    function clear()
    {
        $this->prefs = array();
        $this->webs = array();
        $this->wdata = array();
        $this->arry_data = array();
        $this->website ='';
        $this->lang = '';
    }
       
    /**
     * Clean the current user object and remove it from the session.
     * The session is closed and destroyed.
     *
     * @return void
     */ 
    function logout()
    {
        $this->clear();
        session_unset();
        session_destroy();
    }

    /**
     * Check the login/password of a user.
     * This method can be used as a static method. Thus it is
     * not possible to get directly the error message from
     * the Connection object if an error occured in the DB query.
     * To get the message, use pxDBConnect() to get a Connection object and
     * check the last error message.
     *
     * @param string Username
     * @param string Password
     * @return bool The pair is valid or not
     */
    public static function checkUser($user, $pswd)
    {
        if (0 == strlen($user) || 0 == strlen($pswd)) return false;
        $con =& pxDBConnect();
        $r = 'SELECT user_username, user_password 
             FROM '.$con->pfx.'users WHERE user_username
             LIKE \''.$con->escapeStr($user).'\' LIMIT 1';
        if (($rs = $con->select($r)) !== false) {
            $md5pass = $rs->f('user_password');
            return (md5($pswd) == $md5pass);
        } else {
            return false;
        }
    }
        

    /** 
     * Load user preferences.
     * The preferences are stored in the $this->prefs member variable.
     *
     * @return bool Success
     */
    function loadPrefs()
    {
        $user = $this->f('user_id');
        $this->prefs = array();
        $this->con =& pxDBConnect();
		
        if ((int)$user > 0) {
            $req = 'SELECT * FROM '.$this->con->pfx.'userprefs 
                  WHERE user_id LIKE \''.$this->con->escapeStr($user).'\'';
            if (($rs = $this->con->select($req)) !== false) {
                $d = $rs->getData();
                while (list( , $val) = each($d)) {
                    $this->prefs[$val['keyname']][$val['website_id']] = $val['data'];
                }
                $this->lang = $this->getPref('lang', $this->website);
            } else {
                $this->setError('MySQL: ' . $this->con->error(), 500);
                return false;
            }
            $this->prefs['xmedia_current_dir'][$this->website] = '';
            $req = 'SELECT user_id,user_path_media FROM '.$this->con->pfx.'users WHERE user_id = \''.$this->con->escapeStr($user).'\'';
            //echo $req;
            if (($rs = $this->con->select($req)) !== false)  {
            	if (!$rs->EOF()) {
            		$this->prefs['xmedia_current_dir'][$this->website] = $rs->f('user_path_media');
            		$this->path_media = $rs->f('user_path_media');
            	}
            }
            //echo $this->prefs['xmedia_current_dir'][$this->website];
        } else {
            return false;
        }
        return true;
    }

    /** 
     * Get one preference.
     *
     * @return string preference or empty string if no pref
     * @param string key
     * @param string website id
     */
    function getPref($key, $websiteid = '')
    {
        //echo $key .':' . $this->prefs[$key]['#all#'];
    	if (strlen($key) == 0)
            return '';
        if (strlen($websiteid) == 0)
            $websiteid = $this->website;
        //echo $key . '/'.$websiteid .' : ' . $this->prefs[$key][$websiteid];
        if (!empty($this->prefs[$key][$websiteid]))
            return $this->prefs[$key][$websiteid];
        if (!empty($this->prefs[$key]['#all#']))
            return $this->prefs[$key]['#all#'];
        if (!empty($GLOBALS['_PX_config'][$key])) 
            return $GLOBALS['_PX_config'][$key];
        return '';
        
    }

    /** 
     * Load user website grants and data.
     * The website grants are saved in $this->webs
     * The website data are saved in $this->wdata
     * 
     * @return bool Success
     */
    function loadWebsites()
    {
        $user = $this->f('user_id');
        $this->webs = array();
        $this->wdata = array();
        $this->con =& pxDBConnect();
		
        if ((int)$user > 0) {
            $req = SQL::getWebsiteLevels($user);

            if (($rs = $this->con->select($req)) !== false) {
                while (!$rs->EOF()) {
                    $this->webs[$rs->f('website_id')]  = $rs->f('level');
                    $this->wdata[$rs->f('website_id')]['website_name']  = $rs->f('website_name');
                    $this->wdata[$rs->f('website_id')]['website_url']   = $rs->f('website_url');
                    $this->wdata[$rs->f('website_id')]['website_reurl'] = $rs->f('website_reurl');
                    $this->wdata[$rs->f('website_id')]['website_xmedia_path'] = $rs->f('website_xmedia_path');
                    $rs->moveNext();
                }

            } else {
                $this->setError('MySQL: ' . $this->con->error(), 500);
                return false;
            }
        } else {
            return false;
        }
        return true;
    }

	
	function loadCategories()  
	{
		$user = $this->f('user_id');
		$this->con =& pxDBConnect();
		$req = SQL::getCategoryForUser($user);
		if ( ($cats=$this->con->select($req) ) !== false ) {
			return true;
		} else {
			return false;
		}
	}
	
	function loadArrayCategoriesFromId($id)  
	{
		$this->con =& pxDBConnect();
		$req = SQL::getCategoryForUser($id);
		if ( ($rs=$this->con->select($req) ) !== false ) {
			$array_cats= array();
			while (!$rs->EOF())  {
				$array_cats[$rs->f('category_id')]= $rs->f('category_name') . ' (' . $rs->f('category_path') .')';
				$rs->moveNext();
			}
			return $array_cats;
		} else {
			return false;
		}
	}
	
    /**
     * Set the current website.
     * It loads the lang preference of the website and try to set the
     * locale.
     *
     * @param string Website id
     * @return bool True
     */
    function setWebsite($website)
    {
        $this->website = $website;
        $this->lang = $this->getPref('lang', $website);
        if (false === @setlocale(LC_ALL, $this->lang)) {
            @setlocale(LC_ALL, $this->lang.'_'.strtoupper($this->lang));
        }
        return true;
    }

    /** 
     * Remove a pref for a user in the database, and in the
     * user object.
     *
     * @return bool success
     * @param string Key
     * @param string Website id ('#all#')
     * @param bool Remove in all the websites (false)
     * @param bool Remove only in the session (false)
     */
    function removePref($key, $website='#all#', $all=false, $sessiononly=false)
    {
        if (strlen($key) == 0) return false;

        if (!empty($_SESSION['prefs'][$key][$website]))
            unset($_SESSION['prefs'][$key][$website]);
        if (!empty($this->prefs[$key][$website]))
            unset($this->prefs[$key][$website]);

        if (false == $sessiononly) {
            $this->con =& pxDBConnect();
            $extra = '';
            if (false === $all) 
                $extra = 'website_id=\''.$this->con->escapeStr($website).'\' 
                          AND ';
            $r = 'DELETE FROM '.$this->con->pfx.'userprefs
                  WHERE user_id=\''.$this->f('user_id').'\' AND
                  '. $extra .' keyname=\''.$this->con->escapeStr($key).'\'';
            return $this->con->execute($r);
        }
        return true;
    }

    /**
     * Save a user preference.
     * The user preference can be saved only in the session or in the session
     * and the database.
     *
     * @param string Key of the preference
     * @param mixed Value of the preference
     * @param string Website id ('') Set as current is none given
     * @param bool Save only in the session (false)
     */
    function savePref($key, $value, $website='', $sessiononly=false)
    {
        if (strlen($website) == 0) {
            $website = $this->website;
        }

        if (false === ($error = $this->removePref($key, $website, false, $sessiononly))) {
            return $this->con->error();
        }

        if (strlen($value) > 0) {
            if (false === $sessiononly) {
                $req = 'INSERT INTO '.$this->con->pfx.'userprefs  SET
                      user_id=\''.$this->con->escapeStr($this->f('user_id')).'\',
                      website_id=\''.$this->con->escapeStr($website).'\',
                      keyname =\''.$this->con->escapeStr($key).'\',
                      data =\''.$this->con->escapeStr($value).'\'';

                if (false === $this->con->execute($req)) {
                    return $this->con->error();
                }
            }
            $_SESSION['prefs'][$key][$website] = $value;
            $this->prefs[$key][$website] = $value;
        }
        return true;
    }

    /**
     * Synchronize the User object from or to the session.
     * The User object is used in the manager to store the data of the
     * currently logged user. To avoid a set of queries against the database
     * for each page in the manager, the object can be saved in the session
     * and restored from the session. This is the purpose of this method.
     *
     * @param int Direction of the synchro (PX_USER_SYNCHRO_FROM_SESSION)
     * @return bool Success
     */
    function synchronize($dir=PX_USER_SYNCHRO_FROM_SESSION)
    {
        if (PX_USER_SYNCHRO_FROM_SESSION == $dir) {
            //Init the User object from data in the session
            parent::recordset($_SESSION['user']);
            $this->prefs   = $_SESSION['prefs'];
            $this->webs    = $_SESSION['webs'];
            $this->wdata   = $_SESSION['wdata'];
            $this->website = $_SESSION['website_id'];
            $this->lang    = $this->getPref('lang', $this->website);

            // set the locale
            if (false === @setlocale(LC_ALL, $this->lang)) {
                @setlocale(LC_ALL, $this->lang.'_'.strtoupper($this->lang));
            }
        } else {
            $_SESSION['user_id']    = $this->f('user_id');
            $_SESSION['prefs']      = $this->prefs;
            $_SESSION['webs']       = $this->webs;
            $_SESSION['wdata']      = $this->wdata;
            $_SESSION['website_id'] = $this->website;
            $_SESSION['user']       = $this->getData();
            $_SESSION['lang']       = $this->getPref('lang', $this->website);
            setcookie('lang',       $_SESSION['lang'],       time()+31536000);
            setcookie('website_id', $_SESSION['website_id'], time()+31536000);
        }
        return true;
    }


    /* ========================================================================
     * Set of utility methods.
     * ===================================================================== */
    
    /**
     * Increase the size of the textarea preference.
     *
     * @param string Textarea to increase the size
     * @return int New size, max value 100
     */
    function increase($area)
    {
        $p = $this->getPref($area);
        $p = $p + 5;
        if ($p > 100) $p = 100;
        $this->savePref($area, $p);
        return $p;
    }

    /**
     * Decrease the size of the textarea preference.
     *
     * @param string Textarea to decrease the size
     * @return int New size, min value 5
     */
    function decrease($area)
    {
        $p = $this->getPref($area);
        $p = $p - 5;
        if ($p < 5) $p = 5;
        $this->savePref($area, $p);
        return $p;
    }

    /**
     * Get the current "main" theme for the user. 
     * It can be used for the path to the images and css.
     * If the user's theme is not available anymore or if no
     * theme is defined yet, 'default' is returned.
     *
     * @return string Theme id.
     */
    function getTheme()
    {
        $theme = $this->getPref('theme');
        // check if the path exists else reset to 'default'
        if (strlen($theme) > 0 
            && file_exists($GLOBALS['_PX_config']['manager_path'].'/themes/'.$theme.'/')) {
            return $theme;
        } else {
            return 'default';
        }
    }

    /**
     * Get the current "plugin" theme for the user. 
     * It can be used for the path to the images and css of the current plugin,
     * as the "main" theme may not be available for the plugin.
     *
     * @param string Id of the plugin (plugin folder)
     * @return string Theme id
     */
    function getPluginTheme($plugin)
    {
        $theme = $this->getPref('theme');
        // check if the path exists else reset to 'default'
        if (strlen($theme) > 0 
            && file_exists(config::f('manager_path').'/tools/'.$plugin.'/themes/'.$theme.'/')) {
            return $theme;
        } else {
            return 'default';
        }
    }


}
?>
