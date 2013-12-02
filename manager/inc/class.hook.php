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
 * Hook.
 */
class Hook
{

    /**
     * Run a hook.
     *
     * @param string Hook to be run
     * @param array Parameters
     * @return bool Success
     */
    public static function run($hook, $params=array())
    {
        $success = true;
        if (!empty($GLOBALS['_PX_hook'][$hook])) {
            foreach ($GLOBALS['_PX_hook'][$hook] as $key => $val) {
                //$res = call_user_func(array($val[0], $val[1]), $hook, $params);
                $res = call_user_func($val[0].'::'.$val[1], $hook, array(&$params));
                if ($res === false) {
                    $success = false;
                }
            }
        }
        return $success;
    }


    /**
     * Register a hook.
     *
     * @param string Name of the hook
     * @param string Plugin name 
     * @param string Method of the plugin
     * @return bool Success
     */
    public static function register($hook, $plugin, $method)
    {
        if (!isset($GLOBALS['_PX_hook'])) {
            $GLOBALS['_PX_hook'] = array();
        }
        if (!isset($GLOBALS['_PX_hook'][$hook])) {
            $GLOBALS['_PX_hook'][$hook] = array();
        }
        $GLOBALS['_PX_hook'][$hook][] = array($plugin, $method);
        return true;
    }
}
?>
