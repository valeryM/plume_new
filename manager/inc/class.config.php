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


/**
 * Wrapper for the configuration variables. 
 * The website variables are in the same "space" as the general variables.
 * The plugin variables have their own "space".
 * The functions are all static for ease of use.
 */
class config
{

    /**
     * Get a configuration variable.
     *
     * @param string Variable to get
     * @param string Plugin to get the variable from ('plume')
     * @return mixed Return an empty string if variable not set.
     */
    public static function f($var, $plugin='plume')
    {
        if ($plugin != 'plume') {
            // Get the variable from a plugin
            if (isset($GLOBALS['_PX_config_plugins'][$plugin][$var]))
                return $GLOBALS['_PX_config_plugins'][$plugin][$var];
            // It may not have been set, so look now at the other
            // configuration variables.
        }
        if (isset($GLOBALS['_PX_config_runtime'][$var]))
            return $GLOBALS['_PX_config_runtime'][$var];
        if (isset($GLOBALS['_PX_website_config'][$var]))
            return $GLOBALS['_PX_website_config'][$var];
        if (isset($GLOBALS['_PX_config'][$var]))
            return $GLOBALS['_PX_config'][$var];
        return '';
    }

    /**
     * Get a configuration variable as boolean.
     *
     * @param string Variable to get
     * @param string Plugin to get the variable from ('plume')
     * @return bool False if not set
     */
    public static function fbool($var, $plugin='plume')
    {
        return (config::f($var, $plugin)) ? true : false;
    }

    /**
     * Get a configuration variable as integer.
     *
     * @param string Variable to get
     * @param string Plugin to get the variable from ('plume')
     * @return int 0 if not set
     */
    public static function fint($var, $plugin='plume')
    {
        return (int) config::f($var, $plugin);
    }

    /**
     * Get cache dir.
     *
     * @return string Cache dir of the current website.
     */
    public static function getCacheDir()
    {
        return dirname(dirname(__FILE__)).'/cache/'.config::f('website_id').'/';
    }

    /**
     * Get mass update file.
     *
     * @return string Path to the mass update file of the current website.
     */
    public static function getMassUpdateFile()
    {
        return config::getCacheDir().'MASS_UPDATE';
    }

    /**
     * Load a website configuration file.
     * It overwrite the configuration of the website previously loaded. 
     * It reset the configuration to an empty array in case of error.
     *
     * @param string Website id
     * @retrun bool Success or not
     */
    public static function loadWebsite($websiteid)
    {
        global $_PX_website_config;
        $success = true;
        if (preg_match('/[^0-9a-z\-]/i', $websiteid)) $success = false;

        $config = dirname(__FILE__).'/../conf/configweb_'.$websiteid.'.php';
        if ($success && file_exists($config)) {
            include $config;
            return true;
        } else {
            $_PX_website_config = array();
            return false;
        }
    }

    /**
     * Load a plugin configuration file.
     * It reset the configuration of the plugin to an empty array
     * in case of error.
     *
     * @param string Plugin id
     * @return bool Success or not
     */
    public static function loadPlugin($pluginid)
    {
        global $_PX_config_plugins;
        $success = true;
        if (preg_match('/[^0-9a-z\-]/i', $pluginid)) $success = false;

        $config = dirname(__FILE__).'/../conf/configplugin_'.$pluginid.'.php';
        if ($success && file_exists($config)) {
            include $config;
            return true;
        } else {
            $_PX_config_plugins[$pluginid] = array();
            return false;
        }
    }

    /**
     * Set runtime configuration variable.
     * The runtime configuration variables are used mainly in the
     * public part, this is a good way to store the parameters for
     * them to be accessible by all the objects.
     *
     * @param string Variable name
     * @param mixed Value
     * @return bool true
     */
    public static function setVar($var, $value)
    {
        if (empty($GLOBALS['_PX_config_runtime'])) {
            $GLOBALS['_PX_config_runtime'] = array();
        }
        $GLOBALS['_PX_config_runtime'][$var] = $value;
        return true;
    }

    /**
     * Alias of setVar()
     *
     * @param string Variable name
     * @param mixed Value
     * @return bool true
     */
    public static function setField($var, $value)
    {
        return config::setVar($var, $value);
    }

    /**
     * Set the context. 
     * The context is a particular configuration variable to know
     * if the current call is from within the manager or the public
     * website.
     * 4 contexts exist:
     * - manager: When in the manager
     * - website: When in the public website
     * - external: When in the public website but with need of full urls
     * - install: When in the installer
     *
     * @param string Context ('manager')
     * @return bool true
     */
    public static function setContext($c='manager')
    {
        $GLOBALS['_PX_config']['context'] = $c;
        return true;
    }

}
?>
