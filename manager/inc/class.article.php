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

require_once dirname(__FILE__).'/class.resource.php';
require_once dirname(__FILE__).'/class.page.php';

define('PX_RESOURCE_MANAGER_ARTICLE','articles');

/**
 * Article class. This is the data storage of an article.
 */
class Article extends Resource
{
    var $pages = null; /**< Recordset of the pages. */


    /**
     * Constructor.
     */
    function Article($data='')
    {
        parent::Resource($data);
        $this->pages = new Page();
    }


    /**
     * Load the article.
     *
     * @param mixed Identifier or resource id ('')
     * @return bool Success
     */
    function load($id='')
    {
        if (false === parent::load($id)) { //categories and authors
            return false;
        }
        if (PX_RESOURCE_MANAGER_ARTICLE != $this->f('type_id'))
            return false;

        if (false === $this->loadPages())
            return false;

        $this->isModified = False;
        $this->runPostLoadHook();
        return true;
    }


    /**
     * Load the pages of an articles.
     * The pages are stored in $this->pages for later reuse.
     *
     * @return bool Success or error
     */
    function loadPages()
    {
        $this->getConnection();
        $r = 'SELECT * FROM '.$this->con->pfx.'articles WHERE resource_id=\''.
            $this->f('resource_id').'\' ORDER BY page_number ASC';
        if (($rs = $this->con->select($r, 'Page')) !== false) {
            $this->pages = $rs;
            return true;
        } else {
            $this->con->setError();
            return false;
        }
    } 
    
    /**
     * Load the pages of an articles.
     * The pages are stored in $this->pages for later reuse.
     *
     * @return bool Success or error
     */
    function loadPagesFrom($res_id,$ar)
    {
        $this->getConnection();
        $r = 'SELECT * FROM '.$this->con->pfx.'articles WHERE resource_id=\''.
            $res_id .'\' ORDER BY page_number ASC';
        if (($rs = $this->con->select($r, 'Page')) !== false) {
        	$this->pages = $rs;
        	$sql = 'INSERT INTO '.$this->con->pfx.'articles  SET 
        		resource_id = \'' . $ar->f('resource_id') . '\' ,
        		page_number = \'' . $this->pages->f('page_number') . '\' , 
        		page_title = \'' . $this->pages->f('page_title') . '\', 
        		page_content = \'' . $this->pages->f('page_content') . '\', 
        		page_creationdate = \'' . $this->pages->f('page_creationdate') . '\', 
        		page_modifdate = \'' . $this->pages->f('page_modifdate') . '\' ';
        	
			$this->con->execute($sql);
			$r = 'SELECT * FROM '.$this->con->pfx.'articles WHERE resource_id=\''.$ar->f('resource_id') .'\' ORDER BY page_number ASC';
            $this->pages = $this->con->select($r, 'Page');
            //$ar = $this;
            return true;
        } else {
            $this->con->setError();
            return false;
        }
    }

    /**
     * Return the content of the article as a string ready for indexation.
     *
     * @param string Format of the string (html, wiki, text)
     * @return string The content of the news as a string
     */
    function getAsString($format='Html')
    {
        $idx = $this->pages->getIndex();
        $this->pages->moveStart();

        $string = str_repeat($this->f('title').' ', 3);
        $string .= ' '.str_repeat($this->f('subject').' ', 2);
        //$string .= ' '.str_repeat($this->f('path').' ', 3);
        $string .= ' '.$this->getFormattedContent('description','Text');
        //$string .= ' '.text::parseWikiToText($this->f('description'));        
        while (!$this->pages->EOF()) {
            $string .= ' '.str_repeat($this->pages->f('page_title').' ', 1);
            $string .= ' '.$this->getFormattedContent('page_content', 'Text', 'pages');
        	//$string .= ' '.text::parseWikiToText($this->f('page_content'));        
            $this->pages->moveNext();
        }
        $this->pages->move($idx);

        return $string;
    }

    /**
     * Set the default values for the article.
     *
     * @param object User object to have the preferences
     * @return bool True
     */
    function setDefaults($user)
    {
        parent::setDefaults($user);
        if ($user->getPref('articles_status')) {
            $this->setField('status', $user->getPref('articles_status'));
        } else {
            $this->setField('status', PX_RESOURCE_STATUS_INEDITION);
        }
        $this->setField('category_id', $user->getPref('articles_category_id'));
        $this->setField('subtype_id', $user->getPref('articles_subtype'));

        $this->isModified = true;
        return true;
    }

    /**
     * Set the basic data of an article.
     * The basic data are the one saved in the table `resources`.
     * There is only a check of the data, use the commit() method
     * to save in the database.
     *
     * @param string Title of the article
     * @param string Subject or keywords
     * @param string Description of the article
     * @param string Format of the description
     * @param int Status
     * @param string Path
     * @param timestamp Date of publication
     * @param timestamp Date of end of publication
     * @param bool Use a date of end of publication
     * @param int Comment support
     * @param int Subtype of the article
     */
    function set($title, $subject, $content, $format, $status, $path,
                 $datestart, $dateend, $useenddate, $comment_support, $subtype)
    {
        // Some cleaning
        $datestart = date::clean($datestart);
        if (!empty($useenddate)) {
            $dateend = date::EOT();
        }
        $dateend = date::clean($dateend);
		//if ($filRouge == '') $filRouge =0;
		
        // Set the values
        $this->setField('subtype_id', $subtype);
        $this->setField('subject', $subject);
        $this->setField('title', $title);
        $this->setField('description', '='.$format."\n".$content);
        $this->setField('path', $path);
        $this->setField('publicationdate', $datestart);
        $this->setField('enddate', $dateend);
        $this->setField('status', $status);
        //$this->setField('filRouge', $filRouge);
        $this->setField('comment_support', (int) $comment_support);

        $this->isModified = true;

        return true;
    }

    /**
     * Set a page.
     * If the page id is empty, a new page is added.
     *
     * @param int Id of the page
     * @param string Title of the page
     * @param string Content
     * @param string Format
     * @param int Number
     * @return bool Success
     */
    function setPage($id, $title, $content, $format, $number)
    {
        if (!empty($id)) {
            if (false === $this->goToPage($id)) {
                $this->setError(__('Error: you are trying to update a non existing page.'), 400);
                return false;
            }
        }

        $this->pages->setField('old_number', $this->pages->f('page_number'));
        $this->pages->setField('page_number', $number);
        $this->pages->setField('page_title', $title);
        $this->pages->setField('page_content', '='.$format."\n".$content);

        $this->isModified = true;

        return true;
    }



    /**
     * Check the consistency of the data of the article.
     *
     * Simply goes through the object and check that the values are not
     * possibly conflicting. The check is only with respect to the type
     * of the data, no integrity check like existence of a matching
     * author in the base is done.
     *
     * @return bool Success, if false the error is set.
     */
    function check()
    {
        if (strlen($this->f('title')) == 0) {
            $this->setError(__('You need to give a title.'), 400);
        }
        if (false === date::clean($this->f('publicationdate'))) {
            $this->setError(__('The publication date is wrong.'), 400);
        }
        if (false === date::clean($this->f('enddate'))) {
            $this->setError(__('The off-line date is wrong.'), 400);
        }
        if (strlen($this->f('subtype_id')) == 0) {
            $this->setError(__('A type of resource is needed.'), 400);
        }

        // Path validation
        if (strlen($this->f('path')) == 0) {
            $this->setError(__('A path for the article must be provided.'),
                            400);
        }
        if (strlen($this->f('path')) > 0
            && !preg_match('/^([A-Za-z]|[A-Za-z][A-Za-z0-9\_\-]*[A-Za-z])$/',
                        $this->f('path'))) {
            $this->setError(__('Illegal characters in the path. Please read the help to know how to choose the right path for your article.'), 400);
        }
        $id = $this->isPathInUse($this->f('path'));
        if ($id !== false && $id != $this->f('resource_id')) {
        	
            //$format = __('An article is already using the path: <em>%s</em>. Please use another one.');
			$this->setField('version',$this->f('version')+1);
            //$this->setError(sprintf($format, $this->f('path')), 400);
        }

        if (false !== $this->error()) {
            return false;
        }
        return true;
    }


    /**
     * Go to the page with a specific id.
     * In case of success the $this->pages recordset is set to the page.
     *
     * @param int id of the page
     * @return bool success to go to the page
     */
    function goToPage($id)
    {
        $this->pages->moveStart();
        $page_id = $this->pages->f('page_id');
        $found = true;
        // get the requested page from the list of pages
        while ($id != $page_id) {
            if (false != $this->pages->moveNext()) {
                $page_id = $this->pages->f('page_id');
            } else {
                $found = false; //went through all the pages without finding it
                break;
            }
        }
        return $found;
    }



    /**
     * Get path to the current article page.
     * It uses the current page in $this->pages
     *
     * @return string The path to the current page
     */
    function getPathToPage()
    {
        $base = $this->getPath();
        if ($this->pages->f('page_number') != 1) {
            $base = $base.$this->pages->f('page_number');
        }
        return $base;
    }


    /**
     * Get an array with the list of the pages.
     *
     * @param bool Auto 'at the end' (true)
     * @return array Ready to use in combo box
     */
    function getArrayPageList($auto=true)
    {
        $list = array();
        $np = $this->pages->nbRowTotal(); // at init, the maybe newly added
                                          // is not taken into account
        if ($auto == true && $this->pages->f('page_id') == '') {
            $list[__('At the end')] = $np + 1;
        }
        for ($i=1; $i<=$np; $i++) {
            $list[(string) $i] = (string) $i;
        }
        return $list;
    }



    /**
     * Check that the data for the page to be added or updated are
     * correct.
     *
     * @return bool Success
     */
    function checkPage()
    {
        if (strlen($this->pages->f('page_title')) == 0) {
            $this->setError(__('The page must have a title.'), 400);
        }
        if (!preg_match('/^\d+$/', $this->pages->f('page_number'))) {
            $this->setError(__('Error: Invalid page number.'), 400);
        }
        if (strlen($this->getUnformattedContent('page_content', 'pages')) == 0) {
            $this->setError(__('The page must have a content.'), 400);
        }
        //Check only if not empty.
        if (strlen($this->pages->f('page_id'))
            && !preg_match('/^\d+$/', $this->pages->f('page_id'))) {
            $this->setError(__('Error: Invalid page id.'), 400);
        }
        if (false !== $this->error()) {
            return false;
        }
        return true;
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
        Hook::register('onInitTemplate', 'Article', 'hookOnInitTemplate');
        $l10n = new l10n(config::f('lang'));
        $l10n->loadTemplate(config::f('lang'), config::f('theme_id'));
        // Easy access
        $GLOBALS['_PX_render']['last'] = '';
        $last =& $GLOBALS['_PX_render']['last']; 
        $GLOBALS['_PX_render']['website'] = '';
        $website =& $GLOBALS['_PX_render']['website']; 
        $GLOBALS['_PX_render']['cat'] = '';
        $cat =& $GLOBALS['_PX_render']['cat']; 
        $GLOBALS['_PX_render']['art'] = '';
        $art =& $GLOBALS['_PX_render']['art']; 
        $GLOBALS['_PX_render']['res'] = '';
        $res =& $GLOBALS['_PX_render']['res']; 
        $GLOBALS['_PX_render']['ct'] = '';
        $ct =& $GLOBALS['_PX_render']['ct']; 

        // Parse query string to find the matching article
        list($path, $page, $category_path) = Article::parseQueryString($query);

        $sql = SQL::getOnlineResourceInCat($path, $category_path, 
                                           config::f('website_id'));
        $con =& pxDBConnect();
        if (($art = $con->select($sql, 'Article')) !== false) {
            if (!$art->isEmpty()) {
                $art->load();
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
        $cache = new Cache(urlencode('articles%%'.$path.'%%'.$page.'%%'.$category_path));
        $cache->setCacheDirectory(config::getCacheDir());
        $cache->debug = config::f('debug');

        $art->pages->move($page - 1);
        $GLOBALS['_PX_render']['ct'] = $art->comments;
        $GLOBALS['_PX_render']['res_id'] = $art->f('resource_id');
        header(FrontEnd::getHeader($art->f('subtype_template')));
        // Load the template
		include config::f('manager_path').'/templates/'
            .config::f('theme_id').'/'.$art->f('subtype_template');
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
        if (config::f('action') == 'Article') {
            $GLOBALS['_PX_render']['cat'] = FrontEnd::getCategory($GLOBALS['_PX_render']['art']->f('category_id'));;
            
            $arrayPath = explode('/',$GLOBALS['_PX_render']['cat']->f('category_path'));
            // récupère la 1ère catégorie
            if (count($arrayPath)>0)
            	$GLOBALS['_PX_render']['mcat'] = FrontEnd::getCategory('/'.$arrayPath[1].'/');
            if (count($arrayPath)>3)
            	$GLOBALS['_PX_render']['mchildcat'] = FrontEnd::getCategory('/'.$arrayPath[1].'/'.$arrayPath[2].'/');
            else
            	$GLOBALS['_PX_render']['mchildcat'] = false;
                        
            $GLOBALS['_PX_render']['website'] = FrontEnd::getWebsite();
            if (config::f('order_res_manual')) {
                $order = 'ORDER BY %sresources.title ASC';
            } else {
                $order = 'ORDER BY %sresources.publicationdate DESC';
            }
            $GLOBALS['_PX_render']['res'] = FrontEnd::getResources($GLOBALS['_PX_render']['art']->f('category_id'),
                                                                   '', '', 1, $order);
            $GLOBALS['_PX_render']['ct_enabled'] = false;
            if ((config::f('comment_support') == 1) 
                or (config::f('comment_support') == 2 && 
                    $GLOBALS['_PX_render']['art']->f('comment_support') == 1)) {
                $GLOBALS['_PX_render']['ct_enabled'] = true;
            }
        }
        return true;
    }


    /**
     * Parse query string.
     *
     * @param string Query string
     * @return array (article path, page number, category path)
     */
    public static function parseQueryString($query)
    {
		$category = '';
        $path = '';
        $page = 1;
        
        if (preg_match('#^(.*/)([a-z]|[a-z][a-z0-9\_\-]*[a-z])(\d*)$#i', 
                       $query, $match)) {
			$category = $match[1];
			$path = $match[2];
			$page = ($match[3]) ? (int)$match[3] : 1;
		}
        return array($path, $page, $category);
    }


    /* ===================================================================== *
     *                                                                       *
     *                Methods modifying data in the database.                *
     *                                                                       *
     * ===================================================================== */


    /**
     * Save the article in the database.
     *
     * Only the basic data of the article are saved. The pages are not
     * saved. See commitPage() to commit a page into the database.
     *
     * @return bool Success
     */
    function commit()
    {
        if (false === $this->isModified) {
            return true;
        }

        if (false === $this->check()) {
            return false;
        }

        //Real INSERT/UPDATE into the tables
        $this->getConnection();
        $update = (0 < (int) $this->f('resource_id')) ? true : false;
		 
        if ($update) {
            $req = 'UPDATE '.$this->con->pfx.'resources SET
               subject     = \''.$this->con->esc($this->f('subject')).'\',
               subtype_id  = \''.$this->con->esc($this->f('subtype_id')).'\',
               title       = \''.$this->con->esc($this->f('title')).'\',
               path        = \''.$this->con->esc($this->f('path')).'\',
               description = \''.$this->con->esc($this->f('description')).'\',
               publicationdate = \''.$this->con->esc($this->f('publicationdate')).'\',
               comment_support  = \''.$this->con->esc($this->f('comment_support')).'\',
               modifdate   = \''.date::stamp().'\',
               enddate     = \''.$this->con->esc($this->f('enddate')).'\',
               status      = \''.$this->con->esc($this->f('status')).'\'
               WHERE
               resource_id = \''.$this->con->esc($this->f('resource_id')).'\'';
            //               filRouge	   = \''.$this->con->esc($this->f('filRouge')).'\' 
        } else {
            $req = 'INSERT INTO '.$this->con->pfx.'resources SET
                website_id  = \''.$this->con->esc($this->f('website_id')).'\',
                type_id     = \''.PX_RESOURCE_MANAGER_ARTICLE.'\',
                subtype_id  = \''.$this->con->esc($this->f('subtype_id')).'\',
                comment_support  = \''.$this->con->esc($this->f('comment_support')).'\',
                user_id     = \''.$this->con->esc($this->f('user_id')).'\',
                subject     = \''.$this->con->esc($this->f('subject')).'\',
                creatorname = \'\',
                creatoremail = \'\',
                creatorwebsite  = \'\',
                publisher   = \'\',
                lang_id     = \''.config::f('lang').'\',
                title       = \''.$this->con->esc($this->f('title')).'\',
                description = \''.$this->con->esc($this->f('description')).'\',
                path        = \''.$this->con->esc($this->f('path')).'\',
                creationdate    = \''.date::stamp().'\',
                publicationdate = \''.date::stamp().'\',
                modifdate       = \''.date::stamp().'\',
                enddate         = \''.date::EOT().'\',
                status          = \''.$this->con->esc($this->f('status')).'\',
                size            = \'\',
                version         = \''.$this->con->esc($this->f('version')) .'\', 
                comment         = \'\',
                misc            = \'\',
                format          = \'text/html\',
                dctype          = \'Text\',
                dccoverage      = \'\',
                rights          = \'\' ';
               // filRouge	    = \''.$this->con->esc($this->f('filRouge')).'\' ' ;
            	
        }
//echo $req;
        if (!$this->con->execute($req)) {
            $this->setError('MySQL: '.$this->con->error(), 500);
            return false;
        }
        if (!$update) {
            //Insert, need to get the id of the article as
            //the identifier is based on it. Sadly, it is not possible to get
            //the value directly in the insert statement:
            // <http://dev.mysql.com/doc/mysql/en/INSERT.html>
            if (false == ($id = $this->con->getLastID())) {
                $this->setError('MySQL: '.$this->con->error(), 500);
                return false;
            }
            $this->setField('resource_id', $id);
            $this->setField('identifier', 'art-'.$id);
            $req = 'UPDATE '.$this->con->pfx.'resources SET
                identifier = \''.$this->f('identifier').'\'
                WHERE resource_id = \''.$this->con->esc($id).'\'';
            if (!$this->con->execute($req)) {
                $this->setError('MySQL: '.$this->con->error(), 500);
                return false;
            }
        }

        if (empty($id)) $id = $this->f('resource_id');

        if ($this->addAuthor($this->f('user_id'))
	            && $this->addToCategory($this->f('category_id'))
	            && $this->load($id)) {
            $this->runPostCommitHook();
            return true;
        } else {
            return false;
        }
    }

    /**
     * Remove the article from the database.
     *
     * @return bool Success
     */
    function remove()
    {
        $id = $this->f('resource_id');
        if (empty($id)) {
            return true;
        }

        $req = 'DELETE FROM '.$this->con->pfx.'resources WHERE
                resource_id = \''.$this->con->esc($id).'\'';
        if (!$this->con->execute($req)) {
            $this->setError('MySQL: '.$this->con->error(), 500);
            return false;
        }

        $req = 'DELETE FROM '.$this->con->pfx.'categoryasso WHERE
                identifier = \'art-'.$this->con->esc($id).'\'';
        if (!$this->con->execute($req)) {
            $this->setError('MySQL: '.$this->con->error(), 500);
            return false;
        }

        $req = 'DELETE FROM '.$this->con->pfx.'authorasso WHERE
                resource_id = \''.$this->con->esc($id).'\'';
        if (!$this->con->execute($req)) {
            $this->setError('MySQL: '.$this->con->error(), 500);
            return false;
        }

        $req = 'DELETE FROM '.$this->con->pfx.'articles WHERE
                resource_id = \''.$this->con->esc($id).'\'';
        if (!$this->con->execute($req)) {
            $this->setError('MySQL: '.$this->con->error(), 500);
            return false;
        }

        return true;
    }

    /**
     * Save the current page in the database.
     *
     * The page currently pointed in $this->pages is saved in the
     * database. It means that you need to be carefull between the
     * call to setPage() and commitPage() not to "move" the cursor.
     *
     * @return mixed Id of the page if success, false if error
     */
    function commitPage()
    {
        if (false === $this->checkPage()) {
            return false;
        }
        $number = $this->pages->f('page_number');
        $oldnumber = $this->pages->f('old_number');
        if (empty($oldnumber)) {
            $oldnumber = $this->pages->nbRow();
        }

        $this->getConnection();

        // Update the numbers of the pages if needed
        if ($oldnumber != $number) {
            $insReq = 'UPDATE '.$this->con->pfx.'articles SET
                page_number=page_number+1
                WHERE page_number < '.$this->con->esc($oldnumber).'
                AND page_number >= '.$this->con->esc($number).'
                AND resource_id=\''.$this->con->esc($this->f('resource_id')).'\'';
            if (!$this->con->execute($insReq)) {
                $this->setError('MySQL: '.$this->con->error(), 500);
                return false;
            }
            $insReq = 'UPDATE '.$this->con->pfx.'articles SET
                page_number=page_number-1
                WHERE page_number > '.$this->con->esc($oldnumber).'
                AND page_number <= '.$this->con->esc($number).'
                AND resource_id=\''.$this->con->esc($this->f('resource_id')).'\'';
            if (!$this->con->execute($insReq)) {
                $this->setError('MySQL: '.$this->con->error(), 500);
                return false;
            }
        }

        //Really add/update the content of the page
        if (0 == strlen($this->pages->f('page_id'))) {
            $insReq = 'INSERT INTO '.$this->con->pfx.'articles SET
               page_number = \''.$this->con->esc($this->pages->f('page_number')).'\',
               page_title = \''.$this->con->esc($this->pages->f('page_title')).'\',
               page_content = \''.$this->con->esc($this->pages->f('page_content')).'\',
               page_creationdate = \''.date::stamp().'\',
               page_modifdate = \''.date::stamp().'\',
               resource_id = \''.$this->con->esc($this->f('resource_id')).'\'';
        } else {
            $insReq = 'UPDATE '.$this->con->pfx.'articles SET
               page_number = \''.$this->con->esc($this->pages->f('page_number')).'\',
               page_title = \''.$this->con->esc($this->pages->f('page_title')).'\',
               page_content = \''.$this->con->esc($this->pages->f('page_content')).'\',
               page_modifdate = \''.date::stamp().'\'
               WHERE
               page_id = \''.$this->con->esc($this->pages->f('page_id')).'\'';
        }
        if (!$this->con->execute($insReq)) {
            $this->setError('MySQL: '.$this->con->error(), 500);
            return false;
        }
        $number = $this->pages->f('page_number');
        $this->loadPages(); //Synchro
        $this->pages->move($number-1); //Move to the just added page
        return $this->pages->f('page_id');
    }


    /**
     * Remove a page of the article.
     * Remove the current page.
     *
     * @return bool Success
     */
    function removePage()
    {
        $this->getConnection();

        $delReq = 'DELETE FROM '.$this->con->pfx.'articles WHERE
            resource_id = \''.$this->con->esc($this->f('resource_id')).'\'
            AND page_id=\''.$this->con->esc($this->pages->f('page_id')).'\'';

        if (!$this->con->execute($delReq)) {
            $this->setError('MySQL: '.$this->con->error(), 500);
            return false;
        }
        $this->loadPages(); //Synchro
        return true;
    }

    /**
     * Renum all page number
     * 
     * @return bool Success
     */
    function setPageNumber() {
    	$this->pages->moveStart();
        $id  = 1;
        $countOK = 0;
        while (!$this->pages->EOF()) {
        	$this->pages->setField('page_number', $id);
        	if ($this->commitPage()) $countOK++;
        	$id++;
        	$this->pages->moveNext();
        }
        if ($countOK == ($id-1)) 
        	return true;
       	else
       		return false;
    }
}
?>
