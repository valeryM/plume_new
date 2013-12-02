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

define('PX_RESOURCE_CATEGORY_MAIN',  1);
define('PX_RESOURCE_CATEGORY_OTHER', 2);

define('PX_RESOURCE_CREATOR',       1);
define('PX_RESOURCE_CONTRIBUTOR',   2);
define('PX_RESOURCE_TRANSLATOR',    3);

define('PX_RESOURCE_STATUS_VALIDE',         1);
define('PX_RESOURCE_STATUS_OFFLINE',        2);
define('PX_RESOURCE_STATUS_DEPRECATED',     3);
define('PX_RESOURCE_STATUS_INEDITION',      4);
define('PX_RESOURCE_STATUS_TOBEVALIDATED',  5);


require_once dirname(__FILE__).'/../extinc/class.recordset.php';
require_once dirname(__FILE__).'/class.category.php';
require_once dirname(__FILE__).'/class.comment.php';

/**
 * A Resource is the basic class from which the news, article, etc.
 * classes are extended. Take a look both at it and at either
 * the article or news class to understand the use.
 */
class Resource extends RecordSet 
{
    var $con = null; /**< Connection object. */
    var $cats = null; /**< recordset of categories. */
    var $authors = null; /**< recordset of authors. */
    var $comments = null; /**< List of comments. */
    var $ncomments = null; /**< Number of comments (cache). */
    var $isModified = False; /**< is update of the DB needed. */

    /**
     * Constructor.
     */
    function Resource($data='')
    {
        parent::recordset($data);
        $this->cats = new Category();
        $this->authors = new RecordSet();
        $this->comments = new Comment();
        $this->isModified = True;
    }

    /**
     * Set the default values for the resources. 
     * Should be extended by each resource class to set all the default
     * values from the preferences.
     *
     * @param object User object
     * @return bool Success
     */
    function setDefaults($user)
    {
        $this->setField('description', 
                        '='.$user->getPref('content_format')."\n");
        $this->setField('enddate', date::EOT());
        $this->setField('noenddate', 1);
        $this->setField('publicationdate', date::stamp());
        $this->setField('creationdate', date::stamp());
        $this->setField('modifdate', date::stamp());
        $this->setField('user_id', $user->f('user_id'));
        $this->setField('website_id', $user->website);
        return true;
    }

    /**
     * Load all the "associated" data of the resource.
     * When the Resource object is created from a SQL query against the
     * `resources` table, only few of the data is available. For 
     * example $this->auhors and $this->cats are not set. This
     * method do that. A resource object extending Resource should add
     * its own specific data, like pages for articles.
     * If an identifier or an id is given, the corresponding resource
     * is loaded.
     *
     * @param mixed identifier or resource id ('')
     * @return bool Sucess or failure
     */
    function load($id='')
    {
        if (empty($id)) {
            $id = $this->f('resource_id');
        }
        if (!empty($id)) {
            // SQL::getResourceByIdentifier accepts both the id or the 
            // identifier. If category id is empty, the LEFT JOIN is made
            // on the main category.
            $sql = SQL::getResourceByIdentifier($id, $this->f('category_id'));
            $this->getConnection();
            if (($rs = $this->con->select($sql)) !== false) {
                parent::recordset($rs->getData());
            } else {
                $this->setError('MySQL: '.$this->con->error(), 500);
                return false;
            }
        }
        if (false === $this->loadCategories()) {
            return false;
        }
        if (false === $this->loadAuthors()) {
            return false;
        }
        if (false === $this->loadComments()) {
            return false;
        }
        $this->isModified = False;
        return true;
    }

    /**
     * Run the onload hook.
     *
     * Each type of resource extending Resource must call this function
     * before returning true after load()
     */
    function runPostLoadHook()
    {
        Hook::run('onLoadResource', array('res' => &$this));
    }

    /**
     * Get the categories of a resources. 
     * Save the categories as a RecordSet into $this->categories
     *
     * @return bool Success 
     */
    function loadCategories()
    {
        $this->getConnection();
        $r = 'SELECT * FROM '.$this->con->pfx.'categoryasso
            LEFT JOIN '.$this->con->pfx.'categories ON '
				.$this->con->pfx.'categoryasso.category_id='.$this->con->pfx.'categories.category_id 
			LEFT JOIN '.$this->con->pfx.'websites ON '
				.$this->con->pfx.'websites.website_id='.$this->con->pfx.'categories.website_id
            WHERE identifier=\''.$this->f('identifier').'\' 
            ORDER BY categoryasso_type ASC';
			/*
			LEFT JOIN '.$this->con->pfx.'usercats ON '
				.$this->con->pfx.'categories.user_id='. $user->f('user_id') . '
				AND '.$this->con->pfx.'websites.website_id='.$this->con->pfx.'usercats.website_id 
				AND '.$this->con->pfx.'categories.categories_id='.$this->con->pfx.'usercats.category_id 			
			*/
        if (($rs = $this->con->select($r, 'Category')) !== false) {
            $this->cats = $rs;
            return true;
        } else {
            $this->setError('MySQL: '.$this->con->error(), 500);
            return false;
        }
    }
    


    /**
     * Get the path to the resource.
     * The function is context aware. It means that depending of
     * the context it will return a full path or not, with nice
     * urls or the simple format.
     *
     * @param string Force type of path ('')
     * @return string The path
     */
    function getPath($type='')
    {
        // Need to get the context:
        // - 'website': Must return relative path
        // - 'manager': Must return full path
        // - 'external': Must return full path (this is the case for external
        // use of the data, like in an RSS link.)
        $context = config::f('context');
        if ($type == 'fullurl' 
            || $context == 'manager' || $context == 'external') {
            $base = $this->f('website_url');
        } else {
            $base = $this->f('website_reurl');
        }
        
        //format
        if (config::f('url_format') == 'simple') {
            $base .= '/?';
        }
        return $base.$this->f('category_path').$this->f('path');
    }
    
	
    /**
     * Get the authors of the resource. 
     * The authors are in the `users` table. The association author -> resource
     * is done in the `authorasso` table.
     *
     * @return bool Success
     */
    function loadAuthors()
    {
        $this->getConnection();
        $r = 'SELECT * FROM '.$this->con->pfx.'users LEFT JOIN
            '.$this->con->pfx.'authorasso USING (user_id)
            WHERE resource_id=\''.$this->f('resource_id').'\' 
            ORDER BY authorasso_type';
        if (($rs = $this->con->select($r)) !== false) {
            $type[PX_RESOURCE_CREATOR]     = 'creator';
            $type[PX_RESOURCE_CONTRIBUTOR] = 'contributor';
            $type[PX_RESOURCE_TRANSLATOR]  = 'translator';
            while (!$rs->EOF()) {
                $rs->setField('authorasso',$type[$rs->f('authorasso_type')]);
                $rs->moveNext();
            }
            $rs->moveStart();
            $this->authors = $rs;
            return true;
        } else {
            $this->setError('MySQL: '.$this->con->error(), 500);
            return false;
        }
    }

    
    /**
     * Get the comments of the resource.
     *
     * @return bool Success
     */
    function loadComments()
    {
        $this->getConnection();
        $status = '';
        if (config::f('context') != 'manager') {
            $status = PX_RESOURCE_STATUS_VALIDE;
        }
        $r = SQL::getComments('', $this->f('resource_id'), $status);
        if (($rs = $this->con->select($r, 'Comment')) !== false) {
            $this->comments = $rs;
            return true;
        } else {
            $this->setError('MySQL: '.$this->con->error(), 500);
            return false;
        }
    }

    /**
     * Get the comment count of the resource.
     *
     * @return mixed Comment count or false.
     */
    function countComments()
    {
        if ($this->ncomments !== null) {
            return $this->ncomments;
        }
        $this->getConnection();
        $status = '';
        if (config::f('context') != 'manager') {
            $status = PX_RESOURCE_STATUS_VALIDE;
        }
        $r = SQL::countComments('', $this->f('resource_id'), $status);
        if (($rs = $this->con->select($r, 'Comment')) !== false) {
            $this->ncomments = $rs->f('n_comments');
            return $this->ncomments;
        } else {
            $this->setError('MySQL: '.$this->con->error(), 500);
            return false;
        }
    }

    /**
     * Check if a path is in use.
     * 
     * Only the path given by the user when creating an article for
     * example.
     *
     * @param string Path 
     * @param string Website id (The one of the current resource 
     *                           is used if none given)
     * @return mixed False or id of the resource using it
     */
    function isPathInUse($path, $website='')
    {
        if ($website == '') {
            $website = $this->f('website_id');
        }
        $this->getConnection();
        $r = SQL::getResourceByPath($path, $website);
        if (($rs = $this->con->select($r)) !== false) {
            if ($rs->nbRow() > 0) {
                return $rs->f('resource_id');
            } else {
                return false;
            }
        } else {
            $this->setError('MySQL: '.$this->con->error(), 500);
            return -1; //Equivalent to "path used"
        }
    }


    /**
     * Return the content of the resource as a string ready for indexation.
     * Must be overwritten for each type of resource.
     *   
     * @param string Format of the string (html, wiki, text)
     * @return string The content of the news as a string
     */
    function getAsString($format = 'html')
    {
        trigger_error('getAsString() not defined for the current resource object.', E_USER_WARNING); 
        return '';
    }

    /**
     * Get a Connection object for the resource.
     * It reuses the main connexion object. After calling this method
     * a Connection object is available as $this->con 
     * It is safe to call it many times.
     */
    function getConnection()
    {
        if ($this->con === null) $this->con =& pxDBConnect();
    }


    /**
     * Get ids of resources with a prefix or not.
     *
     * @return array ids
     * @param  string prefix for ids ('')
     */
    function getIDs($key,$str='')
    {
        $res = array();
        foreach ($this->arry_data as $k => $v) {
            $res[] = $str.$v['resource_id'];
        }
        return $res;
    }

    /* ===================================================================== *
     *                                                                       *
     *           Methods to get data for display and the forms.              *
     *                                                                       *
     * ===================================================================== */

    /**
     * Get content of a field as text.
     * No modification of the content is performed.
     *
     * @param string Field to get
     * @param string Member variable name ('')
     * @param bool Escape the & character (true)
     * @return string Content
     */
    function getTextContent($field, $var='', $escape=true)
    {
        if ($var == '') {
            if ($escape) {
                return str_replace('&', '&amp;', $this->f($field));
            }
            return $this->f($field);
        } else {
            if ($escape) {
                return str_replace('&', '&amp;', $this->$var->f($field));
            }
            return $this->$var->f($field);
        }
    }

    /**
     * Get unformatted content of a field.
     * It removes the content type and returns the content without
     * parsing.
     *
     * @param string Field to get
     * @param string Member variable name ('')
     * @return string Content
     */
    function getUnformattedContent($field, $var='')
    {
        if ($var == '') {
            return text::getRawContent($this->f($field));
        } else {
            return text::getRawContent($this->$var->f($field));
        }
    }

    /**
     * Get parsed content.
     * If content is wiki, transform it as HTML, etc.
     *
     * @param string Field to get
     * @param string Output format ('Html')
     * @param string Member variable name ('')
     * @return string Formatted content
     */
    function getFormattedContent($field, $format='Html', $var='')
    {
        if ($var == '') {
            return text::parseContent($this->f($field), $format);
        } else {
            return text::parseContent($this->$var->f($field), $format);
        }
    }

    /**
     * Get the format of a content.
     *
     * @param string Field of the content
     * @param string Member variable name ('')
     * @return string Content format
     */
    function getContentFormat($field, $var='')
    {
        if ($var == '') {
            return text::getType($this->f($field));
        } else {
            return text::getType($this->$var->f($field));
        }
    }

    /**
     * Get date as array.
     * Returns a date as an array ready to be used in the form::datetime
     * field.
     * 
     * @param string Date field
     * @param string Member variable name ('')
     * @return array array(h,m,s,M,D,Y)
     */
    function getArrayDate($field, $var='')
    {
        if ($var == '') {
            return date::explode($this->f($field),false);
        } else {
            return date::explode($this->$var->f($field),false);
        }
    }

    /**
     * Returns if a date is at the end of time
     *
     * @param string Date field
     * @param string Member variable name ('')
     * @return bool Date at end of time
     */
    function isDateEOT($field, $var = '')
    {
        if ($var == '') {
            return date::isEOT($this->f($field));
        } else {
            return date::isEOT($this->$var->f($field));
        }
    }

    /* ===================================================================== *
     *                                                                       *
     *                Methods modifying data in the database.                *
     *                                                                       *
     * ===================================================================== */


    /**
     * Save the basic data.
     * The common data are the one available in the `resources` table.
     * Check the 'article' and 'news' class to see practical implementations.
     * It is recommended to have a check() method to do the check
     * and auto initialization of the data.
     *
     * @return bool Success
     */
    function set($title, $subject, $content, $format, $status, $path, $datestart,
                 $dateend, $useenddate, $comment_support, $subtype)
    {
        trigger_error('set() not defined for the current resource object.', 
                      E_USER_WARNING); 
        return false;
    }
    

    /**
     * Check the basic data.
     *
     * @return bool Success
     */
    function check()
    {
        trigger_error('check() not defined for the current resource object.',
                      E_USER_WARNING); 
        return false;
    }

    /**
     * Save the data into the DB. Note that it does not save the category 
     * and author data, as those data are saved immediately.
     *
     * @return bool Success
     */
    function commit()
    {
        trigger_error('commit() not defined for the current resource object.', 
                      E_USER_WARNING); 
        return false;
    }

    /**
     * Is just running the post commit hook.
     */
    function runPostCommitHook()
    {
        Hook::run('onResourcePostCommit', array('res' => &$this));
    }

    /**
     * Associate the resource to a category.
     *
     * @see loadCategories()
     *
     * @param int Category id
     * @param int Type of association (PX_RESOURCE_CATEGORY_MAIN)
     * @return bool Success
     */
    function addToCategory($catid, $type=PX_RESOURCE_CATEGORY_MAIN)
    {
        if (!preg_match('/^\d+$/', $catid)) {
            $this->setError(__('The proposed category is invalid.').' : '.$catid , 400); 
            return false;
        }            
        
        $update = false;
        if (in_array($catid, $this->cats->getIDs('category_id'))) { 
            $update = true;
        }
        
        //The association must ensure to keep one and only one main category.
        $this->getConnection();
        if ($type == PX_RESOURCE_CATEGORY_MAIN) {
            $insReq = 'UPDATE '.$this->con->pfx.'categoryasso SET
                 categoryasso_type=\''.PX_RESOURCE_CATEGORY_OTHER.'\'
                 WHERE identifier=\''.$this->con->escapeStr($this->f('identifier')).'\'';
            if (!$this->con->execute($insReq)) {
                $this->setError('MySQL: '.$this->con->error(), 500);
                return false;
            }
        } elseif ($update && $this->cats->f('category_id') == $catid) {
            //the first category in $cats is the main category
            //try to set the cat as not main, but it is currently the main
            //do nothing.
            return true;
        }
        
        if ($update) {
            $insReq = 'UPDATE '.$this->con->pfx.'categoryasso SET
                 categoryasso_type=\''.$this->con->escapeStr($type).'\'
                 WHERE category_id=\''.$this->con->escapeStr($catid).'\'
                 AND identifier=\''.$this->con->escapeStr($this->f('identifier')).'\'';
        } else {
            $insReq = 'INSERT INTO '.$this->con->pfx.'categoryasso SET
                 category_id=\''.$this->con->escapeStr($catid).'\',
                 identifier=\''.$this->con->escapeStr($this->f('identifier')).'\',
                 categoryasso_type=\''.$this->con->escapeStr($type).'\'';
        }
        
        if (!$this->con->execute($insReq)) {
            $this->setError('MySQL: '.$this->con->error(), 500);
            return false;
        }
        
        //Need to synchronize $this->cats
        $this->loadCategories();
        
        return true;
    }
    
    /** 
     * Remove a resource from a category.
     * The resource cannot be removed from the category if the
     * category is the main category.
     *
     * @param int Category id
     * @return bool Success
     */
    function removeFromCategory($catid)
    {
        if (!preg_match('/^\d+$/', $catid)) {
            $this->setError(__('The proposed category is invalid.'), 400); 
            return false;
        }            
        if (!in_array($catid, $this->cats->getIDs('category_id'))) {
            $this->setError(__('The resource is not in this category it must be removed from.'), 400);
            return false;
        }
        if ($this->cats->f('category_id') == $catid) {
            $this->setError(__('The resource cannot be removed from the main category'), 400);
            return false;
        }
        
        $this->getConnection();
        $delReq = 'DELETE FROM '.$this->con->pfx.'categoryasso 
             WHERE category_id=\''.$this->con->escapeStr($catid).'\'
             AND identifier=\''.$this->con->escapeStr($this->f('identifier')).'\'';
        if (!$this->con->execute($delReq)) {
            $this->setError('MySQL: '.$this->con->error(), 500);
            return false;
        }        
        
        $this->loadCategories(); //synchro
        return true;    
    }
    

    /**
     * Add an author.
     * The author cannot be associated 2 times. It means that an author is
     * either author, contributor or translator but can't be both of them.
     *
     * @param int Author id
     * @param int Author type (PX_RESOURCE_CREATOR)
     * @return bool Success
     */
    function addAuthor($id, $type=PX_RESOURCE_CREATOR)
    {
        if (!preg_match('/^\d+$/', $id)) {
            $this->setError(__('Invalid author.'), 400); 
            return false;
        }            
        
        $update = false;
        if (in_array($id, $this->authors->getIDs('user_id'))) { 
            $update = true;
        }
        
        $this->getConnection();
        
        //Need to find if the author exists
        if (($user = $this->con->select(SQL::getUser($id))) === false) {
            $this->setError('MySQL: '.$this->con->error(), 500);
            return false;
        }
        if ($id != $user->f('user_id')) {
            $this->setError(__('Try to add a non existing author to the resource.'), 400);
            return false;
        }
        
        //update or insert the author
        if ($update) {
            $insReq = 'UPDATE '.$this->con->pfx.'authorasso SET
                authorasso_type=\''.$this->con->escapeStr($type).'\'
                WHERE user_id=\''.$this->con->escapeStr($id).'\' AND
                resource_id=\''.$this->con->escapeStr($this->f('resource_id')).'\'';
        } else {
            $insReq = 'INSERT INTO '.$this->con->pfx.'authorasso SET
                user_id=\''.$this->con->escapeStr($id).'\',
                resource_id=\''.$this->con->escapeStr($this->f('resource_id')).'\',
                authorasso_type=\''.$this->con->escapeStr($type).'\',
                authorasso_date=\''.date::stamp().'\'';
        }   
        if (!$this->con->execute($insReq)) {
            $this->setError('MySQL: '.$this->con->error(), 500);
            return false;
        }
        
        $this->loadAuthors(); //Synchro
        return true;
    }
    
    
    /**
     * Remove an author.
     * A resource need at least one author. If you try to remove the last
     * author will get an error. Add the new author and them remove the old.
     *
     * @param int Author id
     * @return bool Success
     */
    function removeAuthor($id)
    {
        if (!preg_match('/^\d+$/', $id)) {
            $this->setError(__('Invalid author.'), 400); 
            return false;
        }            
        
        if (!in_array($id, $this->authors->getIDs('user_id'))) { 
            $this->setError(__('The author to be removed is not associated to the resource.'), 400);
            return false;
        }
    
        if ($this->authors->nbRow() == 1) {
            $this->setError(__('Impossible to remove the unique author of the resource.'), 400);
            return false;
        }
        
        //Delete the author
        $this->getConnection();
        $delReq = 'DELETE FROM '.$this->con->pfx.'authorasso 
            WHERE user_id=\''.$this->con->escapeStr($id).'\' AND
            resource_id=\''.$this->con->escapeStr($this->f('resource_id')).'\'';
        if (!$this->con->execute($delReq)) {
            $this->setError('MySQL: '.$this->con->error(), 500);
            return false;
        }
        
        $this->loadAuthors(); //Synchro
        return true;
    }
    
    function getNumberOfPages( $cat_id)  {
    	$this->getConnection();
    	$res_id = $this->con->escapeStr($this->f('resource_id'));
    	$r = 'SELECT COUNT('.$this->con->pfx.'articles.page_id) AS nbPage 
    		FROM '.$this->con->pfx.'resources INNER JOIN '.$this->con->pfx.'categoryasso
    				ON '.$this->con->pfx.'resources.identifier='.$this->con->pfx.'categoryasso.identifier 
    				INNER JOIN '.$this->con->pfx.'articles ON '.$this->con->pfx.'resources.resource_id='.$this->con->pfx.'articles.resource_id  
    		WHERE '.$this->con->pfx.'resources.resource_id=\''.$res_id.'\' 
    			AND  '.$this->con->pfx.'categoryasso.category_id=\''.$this->con->escapeStr($cat_id).'\' '; 
/*
        $r = 'SELECT * FROM '.$this->con->pfx.'categoryasso
            LEFT JOIN '.$this->con->pfx.'categories ON '
				.$this->con->pfx.'categoryasso.category_id='.$this->con->pfx.'categories.category_id 
			LEFT JOIN '.$this->con->pfx.'websites ON '
				.$this->con->pfx.'websites.website_id='.$this->con->pfx.'categories.website_id
            WHERE identifier=\''.$this->f('identifier').'\' 
            ORDER BY categoryasso_type ASC';
    	*/

        if (($rs = $this->con->select($r, 'infoRes')) !== false) {
            return $rs->f('nbPage');
        } else {
            $this->setError('MySQL: '.$this->con->error(), 500);
            return false;
        }    	
        
    }
    
}
?>
