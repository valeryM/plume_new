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

/**
 * A Category is a basic class to store the info of a category or
 * a list of categories.
 * It is similar to the Resource class.
 */
class Category extends recordset 
{
    var $con = null; /**< Connection object. */
    var $res = null; /**< Current resources in the category. */
    var $isModified = False; /**< is update of the DB needed. */    

    /**
     * Constructor.
     */
    function Category($data='')
    {
        parent::recordset($data);
    }

    /**
     * Load a category.
     *
     * If no id is given, try to load from
     * the $this->f('category_id') value.
     *
     * @param int Id of the category ('')
     * @return bool Success
     */
    function load($id='')
    {
        if (empty($id)) {
            $id = $this->f('category_id');
        }
        if (!empty($id)) {
            $this->getConnection();
            if (($rs = $this->con->select(SQL::getCategoryById($id))) !== false) {
                parent::recordset($rs->getData());
            } else {
                $this->setError('MySQL: '.$this->con->error(), 500);
                return false;
            }
        }
        $this->isModified = False;
        return true;
    }

    /**
     * Return the content of the category as a string ready for indexation.
     *   
     * @param string Format of the string ('html')
     * @return string The content of the category as a string
     */
    function getAsString($format='html')
    {
        return '';
    }

    /**
     * Check if the path is in use by another category.
     *
     * @return bool In use or not
     */
    function isPathInUse()
    {
        
        $this->getConnection();
        $r = SQL::getCategoryByPath($this->f('category_path'),
                                    $this->f('website_id'));
        if (($rs = $this->con->select($r)) === false) {
            $this->setError('MySQL: '.$this->con->error(), 500);
            return true; // safe approach
        }
        if ($rs->isEmpty()) {
            return false;
        } else if ($rs->nbRow() == 1
                   && $rs->f('category_id') == $this->f('category_id')) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Get the path to the category.
     * The function is context aware. It means that depending of
     * the context it will return a full path or not, with nice
     * urls or the simple format.
     *
     * @param string Force type of path ('')
     * @param bool Get the feed path (false)
     * @return string The path
     */
    function getPath($type='', $feed=false)
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
        if ($feed) {
            $base .= '/feed';
        }
        return $base.$this->f('category_path');
    }


    /**
     * Set the default values for the category.
     *
     * @param object User object to have the preferences
     * @return bool True
     */
    function setDefaults($user)
    {
        $this->setField('website_id', $user->website);
        $this->setField('category_publicationdate', date::stamp());
        $this->setField('category_creationdate', date::stamp());
        $this->setField('category_enddate', date::EOT());
        $this->setField('category_cachetime', 3600);
        $this->setField('category_description', 
                        '='.$user->getPref('content_format')."\n");
        $this->setField('category_isGhost',0);
        $this->setField('category_template','category_category.php');
        $this->isModified = true;
        return true;
    }

    /**
     * Set the data of a category.
     *
     * @param int Id of the parent category
     * @param string Name
     * @param string Description
     * @param string Format of the description
     * @param string Keywords
     * @param string Path
     * @param string Template
     * @param string Cache time
     * @return bool True
     */
    function set($parentid, $name, $description, $format, $subject, $path,
                 $template, $cachetime)
    {
        $this->setField('category_parentid', $parentid);
        $this->setField('category_name', $name);
        $this->setField('category_description', '='.$format."\n".$description);
        $this->setField('category_keywords', $subject);
        if ($this->f('category_path') != '/') {
            $this->setField('category_path', $path);
        }
        $this->setField('category_template', $template);
        $this->setField('category_cachetime', $cachetime);

        $this->isModified = true;
        return true;
    }

    /**
     * Check the integrity of the category.
     *
     * The error is set if error found.
     *
     * @return bool Success
     */
    function check()
    {
        $path = $this->f('category_path');
        if ((strlen($path) == 0) 
            || (strlen($path) == 1 && $path != '/') 
            || (strlen($path) > 1 && !preg_match('/^\/[A-Za-z0-9\_\-\/]+\/$/',
                                                 $path))
            ) { 
            $this->setError(__('Error: Invalid path.'), 400);   
        }
        if ($this->isPathInUse()) {
            $this->setError(__('This path is already used by another category, please change it.'), 400);   
        }
        if (false !== strpos($this->f('category_template'), '..')) {
            $this->setError(__('The category template seems invalid, please select another one from the form.'), 400);   
        }
        if (strlen($this->f('category_name')) == 0) {
            $this->setError(__('You need to provide a name for the category.'),
                            400);   
        }
        if (false !== $this->error()) {
            return false;
        }
        return true;
    }

    /**
     * Load resources in this category.
     *
     * @return bool Success
     */
    function loadResources()
    {
        include_once config::f('manager_path').'/inc/class.resourceset.php';
        $this->getConnection();
        $r = SQL::getResourcesInCat($this->f('category_id'));
        if (false === ($this->res = $this->con->select($r, 'ResourceSet'))) {
            $this->setError('MySQL: '.$this->con->error(), 500);
            return false;
        }
        return true;
    }


    /**
     * Get content of a field as text.
     * No modification of the content is performed.
     *
     * @param string Field to get
     * @return string Content
     */
    function getTextContent($field)
    {
        return $this->f($field);
    }

    /**
     * Get unformatted content of a field.
     * It removes the content type and returns the content without
     * parsing.
     *
     * @param string Field to get
     * @return string Content
     */
    function getUnformattedContent($field)
    {
        return text::getRawContent($this->f($field));
    }

    /**
     * Get parsed content.
     * If content is wiki, transform it as HTML, etc.
     *
     * @param string Field to get
     * @param string Output format ('html')
     * @return string Formatted content
     */
    function getFormattedContent($field, $format='html')
    {
        return text::parseContent($this->f($field), $format);
    }

    /**
     * Get the format of a content.
     *
     * @param string Field of the content
     * @return string Content format
     */
    function getContentFormat($field)
    {
        return text::getType($this->f($field));
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

    /* ===================================================================== *
     *                                                                       *
     *                Methods for rendering the pages.                       *
     *                                                                       *
     * Note: All standalone methods.                                         *
     * ===================================================================== */

    /**
     * Action to display a category.
     *
     * @param string Server query string
     * @return int Success code
     */
    public static function action($query)
    {
        Hook::register('onInitTemplate', 'Category', 'hookOnInitTemplate');
        $l10n = new l10n(config::f('lang'));
        $l10n->loadTemplate(config::f('lang'), config::f('theme_id'));
        // Easy access
        $GLOBALS['_PX_render']['last'] = '';
        $last =& $GLOBALS['_PX_render']['last']; 
        $GLOBALS['_PX_render']['website'] = '';
        $website =& $GLOBALS['_PX_render']['website']; 
        $GLOBALS['_PX_render']['cat'] = '';
        $GLOBALS['_PX_render']['mcat'] = '';
        $GLOBALS['_PX_render']['mchildcat'] = '';
        $cat =& $GLOBALS['_PX_render']['cat']; 
        $GLOBALS['_PX_render']['res'] = '';
        $res =& $GLOBALS['_PX_render']['res']; 

        config::setVar('query_string', $query);
        
        // Parse query string to find the matching category
        list($path, $page) = Category::parseQueryString($query);
        config::setVar('category_page', $page);

        // Load the matching category
        // If category does not exists, returns error code
        // Will be catched up by the 404 at the end
        $sql = SQL::getCategoryByPath($path, config::f('website_id'));
        $con =& pxDBConnect();
        if (($cat = $con->select($sql, 'Category')) !== false) {
            if (!$cat->isEmpty()) {
                $cat->load();
            } else {
            	config::setVar('query_string_origin', Search::parseQueryString($query));
                return 404;
            }
        } else {
            $GLOBALS['_PX_render']['error']->setError('MySQL: '
                                                      .$con->error(), 500);
            config::setVar('query_string_origin', Search::parseQueryString($query));
            return 404;
        }

        include_once dirname(__FILE__).'/class.cache.php';
        $cache = new Cache(urlencode('cat%%'.$path.'%%'.$page));
        $cache->setCacheDirectory(config::getCacheDir());
        $cache->debug = config::f('debug');

        config::setVar('category_current_id', $cat->f('category_id'));
        header(FrontEnd::getHeader($cat->f('category_template')));
        // Load the template
        include config::f('manager_path').'/templates/'
            .config::f('theme_id').'/'.$cat->f('category_template');
        return 200;
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
        if (config::f('action') == 'Category') {
            $GLOBALS['_PX_render']['website'] = FrontEnd::getWebsite();            
            $arrayPath = explode('/',$GLOBALS['_PX_render']['cat']->f('category_path'));
            // récupère la 1ère catégorie
            if (count($arrayPath)>0) 
            	$GLOBALS['_PX_render']['mcat'] = FrontEnd::getCategory('/'.$arrayPath[1].'/');
            // récupère la 2nde catégorie
            //echo print_r($arrayPath,true);
            if (count($arrayPath)>3) 
            	$GLOBALS['_PX_render']['mchildcat'] = FrontEnd::getCategory('/'.$arrayPath[1].'/'.$arrayPath[2].'/');
            else 
            	$GLOBALS['_PX_render']['mchildcat'] = FrontEnd::getCategories($GLOBALS['_PX_render']['mcat']->f('category_id'),'ORDER BY category_position');
            
            $GLOBALS['_PX_render']['pcat'] = FrontEnd::getCategory($GLOBALS['_PX_render']['cat']->f('category_parentid'));
            $GLOBALS['_PX_render']['childcat'] = FrontEnd::getCategories($GLOBALS['_PX_render']['cat']->f('category_id'));
            
            $limit = config::fint('res_per_page');
            $type = ''; 
            if (config::f('order_res_manual')) {
                $order = 'ORDER BY %sresources.title ASC';
            } else {
                $order = 'ORDER BY %sresources.publicationdate DESC';
            }
            // if init to get resources online in the categorie and sub cat 
            if (config::f('resources_online')==true) {
            	$category = $GLOBALS['_PX_render']['cat']->f('category_path').'%';
            	$GLOBALS['_PX_render']['res'] = FrontEnd::getOnlineResourcesInCat($category,
            											'', 
            											$limit, 
            											$type,
            											config::f('category_page'),
            											'ORDER BY %sresources.path');
            } else {
            	$category = $GLOBALS['_PX_render']['cat']->f('category_id');
            	$GLOBALS['_PX_render']['res'] = FrontEnd::getResources($category,
                                                                   $limit, 
                                                                   $type, 
                                                                   config::f('category_page'),
                                                                   $order);
            }
        }
        return true;
    }

    /**
     * Parse query string.
     *
     * @param string Query string
     * @return array (Category path, page number)
     */
    public static function parseQueryString($query)
    {
        $category = '';
        $page = '';

        if (preg_match('#^(.*/)(index)([0-9]*)$#i', $query, $match)) {
            $category = $match[1];
            $page     = ($match[3]) ? (int) $match[3] : 1;
        } elseif (preg_match('#^(.*/)$#i', $query, $match)) {
            $category = $match[1];
            $page     = 1;
        } elseif (preg_match('#^(.*/)*(&Annee=)[0-9]{4}(&Mois=)[0-9]*$#i', $query, $match)) {
        	$category = $match[1];
        	$page = 1;
        } else {
            $category = '/';
            $page     = 1;
        }

        return array($category, $page);
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
        if (false == $this->check()) {
            return false;
        }
        if (false === $this->isModified) {
            return true;
        }

        $this->getConnection();
        $update = (0 < (int) $this->f('category_id')) ? true : false;

        if ($update) {
            $req = 'UPDATE '.$this->con->pfx.'categories SET
                category_parentid = \''.$this->con->esc($this->f('category_parentid')).'\',
                category_name = \''.$this->con->esc($this->f('category_name')).'\',
                category_description = \''.$this->con->esc($this->f('category_description')).'\',  
                category_keywords = \''.$this->con->esc($this->f('category_keywords')).'\',
                category_path = \''.$this->con->esc($this->f('category_path')).'\',
                category_template = \''.$this->con->esc($this->f('category_template')).'\',
                category_cachetime= \''.$this->con->esc($this->f('category_cachetime')).'\',
                category_isGhost = '.$this->f('category_isGhost').', 
                has_xmedia_folder = '.$this->f('has_xmedia_folder').'
                WHERE category_id = \''.$this->con->esc($this->f('category_id')).'\'';
        } else {
            $req = 'INSERT INTO '.$this->con->pfx.'categories SET
                category_parentid = \''.$this->con->esc($this->f('category_parentid')).'\',
                category_name = \''.$this->con->esc($this->f('category_name')).'\',
                category_description = \''.$this->con->esc($this->f('category_description')).'\',  
                category_keywords = \''.$this->con->esc($this->f('category_keywords')).'\',
                category_path = \''.$this->con->esc($this->f('category_path')).'\',
                category_template = \''.$this->con->esc($this->f('category_template')).'\',
                category_cachetime = \''.$this->con->esc($this->f('category_cachetime')).'\',
                website_id = \''.$this->con->esc($this->f('website_id')).'\',
                category_publicationdate = \''.$this->con->esc($this->f('category_publicationdate')).'\',           
                category_creationdate = \''.$this->con->esc($this->f('category_creationdate')).'\',  
                category_enddate = \''.$this->con->esc($this->f('category_enddate')).'\',
                category_type = \'default\',
                category_isGhost = '.$this->f('category_isGhost').', 
                has_xmedia_folder = '.$this->f('has_xmedia_folder').',
                image_id = \'0\',
                icon_id = \'0\',
                forum_id = \'0\'';
        }
        if (!$this->con->execute($req)) {
            $this->setError('MySQL: '.$this->con->error(), 500);
            return false;
        }
        if (!$update) {
            $this->setField('category_id', $this->con->getLastID());
        }
        $this->load();
        return true;
    }

    /**
     * Remove the category from the database.
     *
     * @return bool Success
     */
    function remove()
    {
        if ($this->f('category_path') == '/') {
            $this->setError(__('Error: A root category cannot be deleted.'),
                            400);
            return false;
        }
        if (false === $this->loadResources()) {
            return false;
        }
        if (!$this->res->isEmpty()) {
            $this->setError(__('Error: A category must be empty to be deleted.'), 400);
            return false;
        }
        
        $delReq = 'DELETE FROM '.$this->con->pfx.'categories 
            WHERE category_id = \''.$this->con->esc($this->f('category_id'))
            .'\'';
        if (!$this->con->execute($delReq)) {
            $this->setError('MySQL: '.$this->con->error(), 500);
            return false;
        }        

        // not necessary but can remove some orphan entries
        $delReq = 'DELETE FROM '.$this->con->pfx.'categoryasso
            WHERE category_id = \''.$this->con->esc($this->f('category_id'))
            .'\'';
        if (!$this->con->execute($delReq)) {
            $this->setError('MySQL: '.$this->con->error(), 500);
            return false;
        } 
        // remove from usercats
        $delReq = 'DELETE FROM '.$this->con->pfx.'usercats 
        	WHERE category_id='.$this->con->esc($this->f('category_id'));
        if (!$this->con->execute($delReq)) {
        	$this->setError('MySQL: '.$this->con->error(), 500);
        	return false;
        }
        return true;
    }


}
?>
