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

require_once dirname(__FILE__).'/../extinc/class.recordset.php';

define('PX_COMMENT_TYPE_INTERN', -1);
define('PX_COMMENT_TYPE_NORMAL', 1);
define('PX_COMMENT_TYPE_TRACKBACK', 2);

define('PX_RESOURCE_STATUS_JUNK', 6);

/**
 * Constants used for the status of the comments:
 *
 * define('PX_RESOURCE_STATUS_VALIDE',         1);
 * define('PX_RESOURCE_STATUS_OFFLINE',        2);
 * define('PX_RESOURCE_STATUS_TOBEVALIDATED',  5);
 *
 * Defined in class.resource.php
 */

class Comment extends RecordSet
{
    var $con = null;

    /**
     * Constructor.
     */
    function Comment($data='')
    {
        parent::RecordSet($data);
    }

    /**
     * Load the Comment
     *
     * @param int Comment id ('')
     * @return bool Success
     */
    function load($id='')
    {
        if (empty($id)) {
            $id = $this->f('comment_id');
        }
        if (!empty($id)) {
            $sql = SQL::getCommentById($id, $this->f('resource_id'));
            $this->getConnection();
            if (($rs = $this->con->select($sql)) !== false) {
                parent::RecordSet($rs->getData());
            } else {
                $this->setError('MySQL: '.$this->con->error(), 500);
                return false;
            }
        } else {
            return false;
        }
        return true;
    }


    /**
     * Set the data of a comment.
     *
     * @param string Author name
     * @param string Author email
     * @param string Author website
     * @param string Content
     * @param int Resource id
     * @param string IP address
     * @param int Status (PX_RESOURCE_STATUS_VALIDE)
     * @param int Type of comment (PX_COMMENT_TYPE_NORMAL)
     * @param int User id ('')
     * @return bool Success
     */
    function set($author, $email, $website, $content, $resource_id, $ip,
                 $status=PX_RESOURCE_STATUS_VALIDE, $path='',
                 $type=PX_COMMENT_TYPE_NORMAL, $user_id='')
    {
        $this->setField('comment_author', $author);
        $this->setField('comment_email', $email);
        $this->setField('comment_website', $website);
        $this->setField('comment_content', $content);
        $this->setField('resource_id', $resource_id);
        $this->setField('comment_status', $status);
        $this->setField('comment_type', $type);
        $this->setField('comment_ip', $ip);
        $this->setField('comment_user_id', $user_id);
        if ($this->f('comment_id') == '') {
            $this->setField('comment_creation', date::stamp());
        }
        $this->setField('comment_update', date::stamp());
        return true;
    }

    /**
     * Check the integrity of the comment.
     *
     * The error is set if error found.
     *
     * @return bool Success
     */
    function check()
    {
        if (strlen($this->f('comment_author')) == 0) {
            $this->setError(__('You need to provide your name.'), 400); 
        }
        if (false == Validate::checkEmail($this->f('comment_email'))) {
            $this->setError(__('You need to provide a valid email address.'), 400); 
        }
        $pattern = '/^http:\/\//';
        if (strlen($this->f('comment_website')) > 0 &&
            !preg_match($pattern, $this->f('comment_website'))) {
        //if (strlen($this->f('comment_website')) > 0 && !eregi('^http:\/\/', $this->f('comment_website'))) {
            $this->setError(__('The website address must start with http://.'), 400); 
        }
        if (strlen($this->f('comment_content')) == 0) {
            $this->setError(__('Empty comments are not comments.'), 400); 
        }

        Hook::run('onCheckComment', array('ct' => &$this));

        if (false !== $this->error()) {
            return false;
        }
        return true;
    }


    /**
     * Method to get the content of a comment.
     * 
     * For the moment, replace line breaks with <br /> 
     *
     * @param string Format of the output 'textarea' or ('safe')
     * @return string Safe content of the comment.
     */
    function getContent($format='safe')
    {
        if ($format == 'safe') {
            return nl2br(htmlspecialchars($this->f('comment_content')));
        } else {
            return htmlspecialchars($this->f('comment_content'));
        }

    }
     /**
     * Method to count the content of a comment.
     * 
     * For the moment, replace line breaks with <br /> 
     *
     * @param string Format of the output 'textarea' or ('safe')
     * @return string Safe content of the comment.
     */
    function countContent()
    {
        
        return $this->f('n_comments');
      

    }

    /* ===================================================================== *
     *                                                                       *
     *                Methods for rendering the pages.                       *
     *                                                                       *
     * Note: All standalone methods.                                         *
     * ===================================================================== */

    /**
     * Action to display/update a comment.
     *
     * @param string Server query string
     * @return int Success code
     */
    public static function action($query)
    {
        Hook::register('onInitTemplate', 'Comment', 'hookOnInitTemplate');
        $l10n = new l10n(config::f('lang'));
        $l10n->loadTemplate(config::f('lang'), config::f('theme_id'));
        // Easy access
        $GLOBALS['_PX_render']['last'] = '';
        $last =& $GLOBALS['_PX_render']['last']; 
        $GLOBALS['_PX_render']['website'] = '';
        $website =& $GLOBALS['_PX_render']['website']; 
        $GLOBALS['_PX_render']['res'] = '';
        $res =& $GLOBALS['_PX_render']['res']; 
        $GLOBALS['_PX_render']['ct'] = '';
        $ct =& $GLOBALS['_PX_render']['ct']; 

        // Parse query string to find the matching resource
        $id = Comment::parseQueryString($query);
        if ($id == 0) {
        	config::setVar('query_string_origin', Search::parseQueryString($query));
            return 404;
        }
        // Find if the resource exists
        $con =& pxDBConnect();
        $sql = SQL::getResourceByIdentifier($id, '', config::f('website_id'));
        if (($res = $con->select($sql, 'ResourceSet')) !== false) {
            if ($res->isEmpty()) {
            	config::setVar('query_string_origin', Search::parseQueryString($query));
                return 404;
            }
        } else {
            $GLOBALS['_PX_render']['error']->setError('MySQL: '
                                                      .$con->error(), 500);
            config::setVar('query_string_origin', Search::parseQueryString($query));
            return 404;
        }
        // Load the matching comments if GET, add a comment if POST
        // If comment does not exists, returns error code
        // Will be catched up by the 404 at the end
        $GLOBALS['_PX_render']['res_id'] = $res->f('resource_id');
        if ($_SERVER["REQUEST_METHOD"] == 'POST') {
            include_once dirname(__FILE__).'/lib.form.php';
            $author = form::getPostField('c_author');
            $email = form::getPostField('c_email');
            $website = form::getPostField('c_website');
            $content = form::getPostField('c_content');
            $preview = form::getPostField('c_preview');
            $redirect = form::getPostField('redirect');
            $ct = new Comment();
            $ct->set($author, $email, $website, $content, $id, 
                     $_SERVER["REMOTE_ADDR"], 
                     config::f('comment_default_status'));
            		
            if (strlen($redirect) > 5) {
                $GLOBALS['_PX_redirect'] = $redirect;
                $GLOBALS['_PX_render']['ct_redirect'] = $redirect;
            } else {
                $GLOBALS['_PX_redirect'] = $res->getPath('fullurl');
            }
            if ($preview) {
                $ct->check();
            } elseif ($ct->check()) {
                if ((config::f('comment_support') == 1) 
                    or (config::f('comment_support') == 2 && 
                        $res->f('comment_support') == 1)) {
                    Hook::run('onNewPublicCommentBeforeSave', 
                              array('ct' => &$ct, 'res' => &$res));
                    $ct->commit();
                    Hook::run('onNewPublicCommentAfterSave', 
                              array('ct' => &$ct, 'res' => &$res));
                }
                return 301;
            }
            header(FrontEnd::getHeader('comments_post.php'));
            // Load the template
            include config::f('manager_path').'/templates/'
                .config::f('theme_id').'/comments_post.php';
            return 200;
        } else {
            header(FrontEnd::getHeader('comments_list.php'));
            // Load the template
            include config::f('manager_path').'/templates/'
                .config::f('theme_id').'/comments_list.php';
            return 200;
        }
    }

    /**
     * Hook on the initialization of the templates.
     *
     * @param string Name of the calling hook
     * @param array Default parameters (not used)
     * @return bool Success
     */
    public static function hookOnInitTemplate($hook, $param)
    {
        if (config::f('action') == 'Comment') {
            $GLOBALS['_PX_render']['website'] = FrontEnd::getWebsite();
        }
        return true;
    }

    /**
     * Parse query string.
     *
     * @param string Query string
     * @return int Resource id
     */
    public static function parseQueryString($query)
    {
        $id = 0;
        if (preg_match('#^/comments/(\d+)/*$#i', $query, $match)) {
            $id = (int) $match[1];
        }
        return $id;
    }


    /* ===================================================================== *
     *                                                                       *
     *                Methods modifying data in the database.                *
     *                                                                       *
     * ===================================================================== */

    /**
     * Save the data into the database.
     *
     * @return bool Success
     */
    function commit()
    {
        $this->getConnection();
        $update = (0 < (int) $this->f('comment_id')) ? true : false;

        if ($update) {
            $req = 'UPDATE '.$this->con->pfx.'comments SET
              resource_id = \''.$this->con->esc($this->f('resource_id')).'\',
              comment_user_id = \''.$this->con->esc($this->f('comment_user_id')).'\',
              comment_author = \''.$this->con->esc($this->f('comment_author')).'\',
              comment_email = \''.$this->con->esc($this->f('comment_email')).'\',
              comment_website = \''.$this->con->esc($this->f('comment_website')).'\',
              comment_creation = \''.$this->con->esc($this->f('comment_creation')).'\',
              comment_update = \''.$this->con->esc($this->f('comment_update')).'\',
              comment_status = \''.$this->con->esc($this->f('comment_status')).'\',
              comment_type = \''.$this->con->esc($this->f('comment_type')).'\',
              comment_content = \''.$this->con->esc($this->f('comment_content')).'\',
              comment_ip = \''.$this->con->esc($this->f('comment_ip')).'\'
              WHERE 
              comment_id = \''.$this->con->esc($this->f('comment_id')).'\'';
        } else {
            $req = 'INSERT INTO '.$this->con->pfx.'comments SET
              resource_id = \''.$this->con->esc($this->f('resource_id')).'\',
              comment_user_id = \''.$this->con->esc($this->f('comment_user_id')).'\',
              comment_author = \''.$this->con->esc($this->f('comment_author')).'\',
              comment_email = \''.$this->con->esc($this->f('comment_email')).'\',
              comment_website = \''.$this->con->esc($this->f('comment_website')).'\',
              comment_creation = \''.$this->con->esc($this->f('comment_creation')).'\',
              comment_update = \''.$this->con->esc($this->f('comment_update')).'\',
              comment_status = \''.$this->con->esc($this->f('comment_status')).'\',
              comment_type = \''.$this->con->esc($this->f('comment_type')).'\',
              comment_content = \''.$this->con->esc($this->f('comment_content')).'\',
              comment_ip = \''.$this->con->esc($this->f('comment_ip')).'\'';
        }
        if (!$this->con->execute($req)) {
            $this->setError('MySQL: '.$this->con->error(), 500);
            return false;
        }
        if (!$update) {
            if (false == ($id = $this->con->getLastID())) {
                $this->setError('MySQL: '.$this->con->error(), 500);
                return false;
            }
            $this->setField('comment_id', $id);
        }
        include_once dirname(__FILE__).'/class.manager.php';
        Manager::triggerMassUpdate();
        return true;
    }

    /**
     * Remove a comment from the database.
     *
     * This a hard remove.
     *
     * @return bool Success.
     */
    function remove()
    {
        $this->getConnection();
        $req = 'DELETE FROM '.$this->con->pfx.'comments WHERE '
            .'comment_id=\''.$this->con->esc($this->f('comment_id')).'\'';
        if (!$this->con->execute($req)) {
            $this->setError('MySQL: '.$this->con->error(), 500);
            return false;
        }
        $this->setField('comment_id', '');
        return true;
    }

    /**
     * Get a Connection object for the comment.
     * It reuses the main connexion object. After calling this method
     * a Connection object is available as $this->con 
     * It is safe to call it many times.
     */
    function getConnection()
    {
        if ($this->con === null) $this->con =& pxDBConnect();
    }
    
}
?>
