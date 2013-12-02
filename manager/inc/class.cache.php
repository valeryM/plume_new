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
# Copyright (C) 2003 KDO / VWT (The Virtual Web Team)
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
 * Cache: Gestion de cache pages dynamiques
 *
 * Utilise l'output buffering: fonctions type ob_xxx de PHP
 * Sample usage-1:
 *   $cc = new Cache('cacheFileName', 300);
 *   if($cc->processPage()) {
 *      .... // Ce que vous voulez cacher
 *      $cc->endCache();
 *   }
 * Sample usage-2:
 *   $cc = new Cache('basfilename');
 *   if($cc->processSegment(200)) {
 *      .... // Ce que vous voulez cacher
 *      $cc->endCache();
 *   }
 *   .... // Suite non cachée
 *   if($cc->processSegment(600)) {
 *      .... // Autre partie à cacher
 *      $cc->endCache();
 *   }
 *
 */
class Cache 
{
    var $data; /**< Data to cache. */
    var $cacheDir = '/tmp/'; /**< Cache folder. */
    var $file; /**< Cache file. */
    var $Fname; /**< Cache file name. */
    var $FnameOrig; /**< Original cache file name, use for segmented cache. */
    var $segmentId = 1; /**< Segment id, used to generate the cache file. */
    var $refreshTime; /**< Cache validity timestamp. */
    var $needUpdate = 0; /**< Need to update the cache. */
    var $isActive = false; /**< Are we in a processcache/processsegment loop */
    var $isSegment = false; /**< In a segment or not. */
    var $timeout; /**< Validity of a cache in seconds. */
    var $headers = array(); /**< Extra headers to send. */
    var $debug = false; /**< Debug mode (no caching). */

    /**
     * Init the cache
     *
     * @param string Cache file name
     * @param int Timeout in minutes (180)
     */
    function Cache($fname, $timeout=180) 
    {
        $fname = md5($fname);
        $this->Fname = $fname;
        $this->FnameOrig = $fname;
        $this->file = $this->cacheDir.$fname;
        $this->setTimeout($timeout);
    }

    /**
     * Set the cache directory.
     *
     * @param string Full path to cache directory with trailing slash
     */
    function setCacheDirectory($dir) 
    {
        $this->cacheDir = $dir;
        $this->file = $dir.$this->Fname;
    }

    /**
     * Set the timeout.
     *
     * @param int Timeout in minutes (180)
     */
    function setTimeout($timeout=180)
    {
        $this->refreshTime = time() - ($timeout * 60);
        $this->timeout = $timeout * 60;
    }

    /**
     * Set extra header to send.
     *
     * @param string Extra header.
     */
    function setHeader($header) {
        if (0 != strlen($header)) {
            $this->headers[] = $header;
        }
    }
    
    /**
     * Send the extra headers.
     */
    function sendHeaders() 
    {
        foreach ($this->headers as $header) {
            header($header);
        }
    }

    /**
     * Process the cache
     *
     * @param int Timeout in minutes (false) if false use the one at construct
     * @return boolean false if cache content is valid
     */
    function processPage($timeout=false) 
    {
        if ($timeout !== false) {
            $this->setTimeout($timeout);
        }
        if(!$this->isActive ) {
            $this->isActive = true;
            ob_start();
            ob_implicit_flush(0);
            if (!$this->debug 
                && file_exists($this->file) 
                && (@filemtime(config::getMassUpdateFile())-10 < @filemtime($this->file))
                && (@filemtime($this->file) > $this->refreshTime)
                ) {
                if (!$this->isSegment) {
                    if (isset($_SERVER["HTTP_IF_MODIFIED_SINCE"])) {
                        $gmtime = gmdate('D, d M Y H:i:s', filemtime($this->file)).' GMT';
                        $usertime = substr($_SERVER["HTTP_IF_MODIFIED_SINCE"], 0, strpos($_SERVER["HTTP_IF_MODIFIED_SINCE"], 'GMT') + 3);
                        if ($usertime == $gmtime) {
                            header("HTTP/1.1 304 Not Modified");
                            ob_end_flush();
                            exit;
                        }
                    }
                }
                $this->read();
                if (empty($gmtime)) 
                    $gmtime = gmdate('D, d M Y H:i:s', 
                                     filemtime($this->file)).' GMT';
                if (!$this->isSegment) 
                    header("Last-Modified: ".$gmtime);
                echo $this->data;
                ob_end_flush();
                $this->isActive = false;
                return false;

            } else {
                if (!$this->isSegment) {
                    header('Cache-Control:  no-cache');
                    header('Pragma:  no-cache');
                    $time = gmdate('D, d M Y H:i:s', 123456).' GMT';
                    header("Last-Modified: ".$time);
                }
                $this->needUpdate = 1;
                return true;
            }
        }
    }

    /**
     * Réalise le cache pour un segment donné
     *
     * @since  2002-02-21
     * @return boolean false si le contenu  du cache est valide
     * @access public
     */
    function processSegment($timeout=180) 
    {
        if (!$this->isActive) {
            $this->isSegment = true;
            $this->Fname = $this->FnameOrig.'%%'.$this->segmentId;
            $this->segmentId += 1;
            $this->file = $this->cacheDir.$this->Fname;
            return $this->processPage($timeout);
        }
        return true;
    }

    /**
     * End of cache block.
     */
    function endCache() 
    {
        $this->isActive = false;
        if ($this->needUpdate) {
            $this->data = ob_get_contents();
            $this->write();
            if (!$this->isSegment) {
                clearstatcache();
                $FT = @filemtime($this->file);
                $gnow = gmdate('D, d M Y H:i:s', $FT+$this->timeout).' GMT';
                if ($this->timeout != -1)
                    header('Expires: '.$gnow);
                $gnow = gmdate('D, d M Y H:i:s', $FT).' GMT';
                header('Last-Modified: '.$gnow);
            }
            ob_end_flush();
            $this->isSegment = false;
        }
    }

    /**
     * Ecrit les données dans le fichier de cache
     *
     * @return true si l'on a pu ecrire dans le fichier, false sinon
     * @access private
     */
    function write() 
    {
        // mode "a" pour ne pas tronquer avant d'avoir le lock
        $fp = @fopen($this->file, 'a'); 
        if ($fp) {
            // lock exclusif pour l'écriture
            flock($fp, LOCK_EX); 
            // maintenant qu'on a le seul pointeur on tronque
            ftruncate($fp,0); 
            // on se replace, comme si on avait ouvert en +w 
            rewind($fp); 
            fwrite($fp, $this->data, strlen($this->data));
            // lock relaché, on peut acceder en lecture 
            flock($fp, LOCK_UN);  
            fclose($fp);
            @chmod($this->file, 0666);
            return true;
        }
        return false;
    }

    /**
     * Lit les données dans le cache
     *
     * @return boolean - true si l'on a pu lire le cache, false sinon
     * @access private
     */
    function read() 
    {
        $fp = @fopen($this->file, 'r');
        if ($fp) {
            flock($fp, LOCK_SH);
            $this->data = '';
            while ($tmp=fread($fp, 4096)) {
                $this->data .= $tmp;
            }
            // un ralachement du lock, même si normalement ca marche sans 
            flock($fp, LOCK_UN);
            fclose($fp);
            return true;
        }
        return false;
    }
    
    // --- Static methods ---
    
    /**
     *
     */
    public static function clean($website_id, $theme)
    {
        include_once config::f('manager_path').'/tools/info/lib.dir.php';
        recursiveDelete(config::f('manager_path').'/cache/'.$website_id.'/');
        clearstatcache();
        @mkdir(config::f('manager_path').'/cache/'.$website_id.'/', 0777);
        @touch(config::f('manager_path').'/cache/'.$website_id.'/MASS_UPDATE', time());
    }
}
?>
