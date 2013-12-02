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

/** @class files 
 *
 *  - Set of file utilities to copy, move files and folders.
 *  - Some of the functions can be used directly without the new to instance
 *  an object.
 */

//param constants
define('PX_FILES_OVERWRITE_TRUE',     1);
define('PX_FILES_OVERWRITE_FALSE',    2);
define('PX_FILES_OVERWRITE_IF_NEWER', 3);

//success codes > 0
define('PX_FILES_SUCCESS',            1);
define('PX_FILES_SUCCESS_NO_CHANGE',  2);

//error codes <= 0
define('PX_FILES_ERROR',              0);
define('PX_FILES_ERROR_NO_RIGHTS',   -1);
define('PX_FILES_ERROR_NO_SOURCE',   -2);
define('PX_FILES_ERROR_BAD_DEST',    -3);
define('PX_FILES_ERROR_CHMOD',       -4);

define('PX_OS_SEP',                  '/');
define('PX_CUR_OS', (strtoupper(substr(PHP_OS,0,3) == 'WIN')) ? 'Win' : 'Nix');


class files
{
    var $job = array();
    var $log = array();
    /**
     *  Add a job.
     */

    /**
     * Copy the content of one folder into another. 
     * The folder is copied recursively.
     * By default the files are not overwritten in the destination folder. 
     * The destination folder must exist.
     * If storage is asked, the function must be called through an object, 
     * else the $log array is not available!
     * The completely empty folders are not created.
     *
     * @param string Source folder
     * @param string Destination folder
     * @param int    Overwrite (PX_FILES_OVERWRITE_FALSE)
     * @param octal  Default chmod for the files (0666)
     * @param octal  Default chmod for the folders (0777)
     * @param bool   Store the status of each file in the log (false)
     * @return int   Global error success code
     */
    function copyfolder($src, $dest, $overwrite=PX_FILES_OVERWRITE_FALSE, 
                        $chmodfiles=0666, $chmodfolders=0777, $log=false)
    {
        if (!file_exists($src) or !@is_dir($src)) 
            return PX_FILES_ERROR_NO_SOURCE;
        if (!file_exists($dest) or !@is_dir($dest)) 
            return PX_FILES_ERROR_BAD_DEST;
        $src = files::real_path($src);
        $dest = files::real_path($dest);
        $folders = array(); //will get the list of created folders
        $files = array();
        $r = PX_FILES_SUCCESS;
        files::listfiles($src, $files);
        reset($files);
        foreach ($files as $file) {
            $folder = dirname($dest.substr($file, strlen($src)));
            if (empty($folders[$folder])) {
                $folders[$folder] = files::createfolder($folder, $chmodfolders);
            }
            $res = files::copyfile($file, $dest.substr($file, strlen($src)), 
                                   $chmodfiles, $overwrite);
            if ($log) {
                $this->log[$file] = $res;
            }
            if (!files::is_success($res))
                $r = PX_FILES_ERROR;
        }
        return $r;
    }

    /**
     * List all the files of a folder recursively by default.
     *
     * @param string Folder
     * @param array Reference to the array to contain the files
     * @param string Regex to catch only some files, comparison only on the file name ('')
     * @param bool (true) do the recursive listing.
     * @return int Error/success code
     */
    public static function listfiles($folder, &$files, $regex='', $rec=true)
    {
        if (!@file_exists($folder) or !@is_dir($folder)) 
            return PX_FILES_ERROR_NO_SOURCE;
        $r = PX_FILES_SUCCESS;
        if ($dirstream = @opendir($folder)) {
            while (false !== ($file = readdir($dirstream))) {
                if ($file != '.' && $file != '..') {
                    if (is_file($folder.'/'.$file) 
                        && (empty($regex) or preg_match($regex, $file))) {
                        $files[] = files::unifypath($folder.'/'.$file);
                    }
                    if ($rec && @is_dir($folder.'/'.$file))
                        $r = files::listfiles($folder.'/'.$file, $files, 
                                              $regex, $rec);
                }
            }
        }
        return $r;
    }

    /**
     * Copy and chmod a file. Can overwrite the file if asked.
     * No try to create the needed folders if not available.
     *
     * @param string Source file
     * @param string Destination file
     * @param int (octal) chmod the file (0666)
     * @param int Overwrite the file (PX_FILES_OVERWRITE_FALSE)
     * @return int Success/error code
     */
    public static function copyfile($src, $dest, $chmod=0666, 
                      $overwrite=PX_FILES_OVERWRITE_FALSE)
    {
        $src_exists = @file_exists($src);
        $dest_exists = @file_exists($dest);

        if (!$src_exists or @is_dir($src)) 
            return PX_FILES_ERROR_NO_SOURCE;
        if ($dest_exists && @is_dir($dest)) 
            return PX_FILES_ERROR_BAD_DEST;
        if ($dest_exists && $overwrite == PX_FILES_OVERWRITE_FALSE) 
            return PX_FILES_SUCCESS_NO_CHANGE;
        //Do not overwrite if dest newer and PX_FILES_OVERWRITE_IF_NEWER asked
        if ($dest_exists && $overwrite == PX_FILES_OVERWRITE_IF_NEWER 
            && (@filemtime($src) < @filemtime($dest)))
            return PX_FILES_SUCCESS_NO_CHANGE;
        $success = @copy($src, $dest);
        if (!$success) 
            return PX_FILES_ERROR;
        $success = @chmod($dest, $chmod);
        if (!$success) 
            return PX_FILES_ERROR_CHMOD;
        return PX_FILES_SUCCESS;
    }
        
    /**
     * Create folder recursively if needed.
     *          
     * @param string Folder to create absolute path needed
     * @param int (octal) Chmod for the newly created folders (0777)
     * @return int Success/error code
     */
    public static function createfolder($folder, $chmod=0777)
    {
        $folder = files::real_path($folder);
        //windows cleaning
        if (preg_match('#^[A-Za-z]+\:/#', $folder)) {
            list($_startPoint, $_path) = explode(':', $folder, 2);
            $folder = $_path;
        }
        // First pop from the top to the bottom "/" to see when a directory
        // exist. Then start to create them from this point.
        // This is to avoid problems in safe_mod.
        $dirs = explode('/', $folder);
        if (@is_dir($folder)) {
            return PX_FILES_SUCCESS;
        }
        $offset = 1;
        for ($i=count($dirs); $i>-1; $i--) {
            $path = implode('/', array_slice($dirs, 0, $i));
            if (@is_dir($path)) {
                $offset = $i;
                break;
            }
        }
        $path .= '/';
        for ($i=$offset; $i<count($dirs); $i++) {
            $path .= $dirs[$i];
            if (!@is_dir($path)) {
                @mkdir($path, $chmod);
                @chmod($path, $chmod);
            }
            $path.='/';
        }
        if (@is_dir($folder)) {
            return PX_FILES_SUCCESS;
        } else {
            return PX_FILES_ERROR;
        }
    }

    /**
     * Convert a success/error code into a BOOL
     *
     * @param int error/success code
     * @return BOOL error or success
     */
    public static function is_success($code)
    {
        return ($code > 0);
    }


    public static function is_pathrelative($dir)
    {
        if (strcmp('Win', PX_CUR_OS) == 0) {
            return (preg_match('/^\w+:/', $dir) <= 0);
        } else {
            return (preg_match('/^\//', $dir) <= 0);
        }
    }

    public static function unifypath($path)
    {
        if (strcmp('Win', PX_CUR_OS) == 0) {
            return str_replace('\\', PX_OS_SEP, $path);
        }
        return $path;
    }

    public static function real_path($path)
    {
        $_path = files::unifypath($path);
        if (files::is_pathrelative($path)) {
            $_curdir = files::unifypath(realpath('.').PX_OS_SEP);
            $_arrayDir = explode('/',$_curdir);
            if (preg_match('/[A-Z]{1}\:/', $_arrayDir[0])) {
            	// win dir : disk letter
				// remove  it
            	array_shift($_arrayDir);
            	$_curdir = '/'. implode('/',$_arrayDir);
            } else {
            	$_path = $_curdir.$_path;
            }
            
        }
        $_startPoint = '';
        /*
        if (strcmp('Win', PX_CUR_OS) == 0) {
            list($_startPoint, $_path) = explode(':', $_path, 2);
            $_startPoint .= ':';
        }
        */
        // From now processing is the same for WIndows and Unix, 
        // and hopefully for others.
        $_realparts = array();
        $_parts = explode(PX_OS_SEP, $_path);
        for ($i=0; $i<count($_parts); $i++) {
            if (strlen($_parts[$i]) == 0 || $_parts[$i] == '.') {
                continue;
            }
            if ($_parts[$i] == '..') {
                if (count($_realparts) > 0) {
                    array_pop($_realparts);
                }
            } else {
                array_push($_realparts, $_parts[$i]);
            }
        }
        return $_startPoint.PX_OS_SEP.implode(PX_OS_SEP, $_realparts);
    }

}
?>
