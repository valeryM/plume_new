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

define('PX_RESOURCE_MANAGER_NEWS', 'news');

/**
 * Data storage of a news.
 */
class News extends Resource
{
    /** Store the details, associated website, etc. */
    var $details = null;

    /**
     * Constructor.
     */
    function News($data='')
    {
        parent::Resource($data);
        $this->details = new recordset();
    }

    /**
     * Load the News.
     *
     * @param mixed Identifier or resource id ('')
     * @return bool Success
     */
    function load($id='')
    {
        if (false === parent::load($id)) 
            //categories and authors
            return false;
        
        if (PX_RESOURCE_MANAGER_NEWS != $this->f('type_id'))
            return false;

        if (false === $this->loadDetails()) 
            return false;

        $this->isModified = False;
        $this->runPostLoadHook();
        return true;
    }

    /**
     * Get the details of a news.
     * The details are the associated link and website.
     *
     * @return bool Success
     */
    function loadDetails()
    {
        $this->getConnection();
        $r = 'SELECT * FROM '.$this->con->pfx.'news 
            WHERE resource_id=\''.$this->f('resource_id').'\'';
        if (($rs = $this->con->select($r)) !== false) {
            $this->details = $rs;
            return true;
        } else {
            $this->con->setError();
            return false;
        }
    }

    /**
     * Return the content of the news as a string ready for indexation.
     *   
     * @param string Format of the string (html, wiki, text)
     * @return string The content of the news as a string
     */
    function getAsString($format='Html')
    {
        $string = str_repeat($this->f('title').' ', 5);
        $string .= ' '.str_repeat($this->f('subject').' ', 3);
        $string .= ' '.$this->getFormattedContent('description','Text');
        $string .= ' '.$this->details->f('news_titlewebsite');
        $string .= ' '.$this->details->f('news_linkwebsite');
        return $string;
    }

    /**
     * Set the default values for the news.
     *
     * @param object User object to have the preferences
     * @return bool True
     */
    function setDefaults($user)
    {
        parent::setDefaults($user);
        if ($user->getPref('news_status')) {
            $this->setField('status', $user->getPref('news_status'));
        } else {
            $this->setField('status', PX_RESOURCE_STATUS_INEDITION);
        }
        $this->setField('category_id', $user->getPref('news_category_id'));
        $this->setField('subtype_id', $user->getPref('news_subtype'));

        $this->isModified = true;
        return true;
    }

    /**
     * Set the basic data of a news.
     * The basic data are the one saved in the table `resources`.
     * There is only a check of the data, use the commit() method
     * to save in the database.
     *
     * @param string Title of the news
     * @param string Subject or keywords
     * @param string Content
     * @param string Format of the content
     * @param int Status
     * @param timestamp Date of publication
     * @param timestamp Date of end of publication
     * @param bool Use a date of end of publication
     * @param int Comment support
     * @param int Subtype of the news
     */
    function set($title, $subject, $content, $format, $status, $path,
    		 $datestart, $dateend, $useenddate, $comment_support, $subtype)
    {
        //Test in a row the field to return errors for all 
        //the litigious data, not only the first one.
        if (strlen($title) == 0) {
            $this->setError(__('You need to give a title.'), 400); 
        }
        if (false === ($datestart = date::clean($datestart))) {
            $this->setError(__('The publication date is wrong.'), 400);
        }
        if (!empty($useenddate)) $dateend = date::EOT();
        if (false === ($dateend = date::clean($dateend))) {
            $this->setError(__('The off-line date is wrong.'), 400);
        }
        if (empty($subtype)) {
            $this->setError(__('A type of resource is needed.'), 400); 
        }
        //if ($filRouge == '') $filRouge =0;
                  
        $this->setField('subtype_id', $subtype);
        $this->setField('subject', $subject);
        $this->setField('title', $title);
        $this->setField('description', '='.$format."\n".$content);
        $this->setField('publicationdate', $datestart);
        $this->setField('enddate', $dateend);
        $this->setField('comment_support', (int) $comment_support);
        $this->setField('status', $status);
        $this->setField('path', $path);
		//$this->setField('filRouge', $filRouge);
        $this->isModified = true;
        if (false !== ($error = $this->error())) {
            return false;
        }
        return true;
    }

    /**
     * Set the details of a news.
     *
     * @param string Associated website title
     * @param string Associated website URL
     * @return bool Success
     */
    function setDetails($title, $link, $shortcontent, $content, $format)
    {
        if (!empty($title)) {
            if (!preg_match('#^(http|ftp|news|https)://#i', $link)) {
                $this->setError(__('The associated link must start with http:// or a valid protocol.'), 400);
            }
        }   
        $this->details->setField('news_shortcontent', '='.$format."\n".trim($shortcontent));
        $this->details->setField('news_content', '='.$format."\n".trim($content));
        $this->details->setField('news_titlewebsite', trim($title));
        $this->details->setField('news_linkwebsite', trim($link));
        $this->isModified = true;
        if (false !== ($error = $this->error())) {
            return false;
        }
        return true;
    }

    /**
     * Check the integrity of the news.
     *
     * The error is set if error found.
     *
     * @return bool Success
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
        if (strlen($this->f('news_titlewebsite')) > 0) {
            if (strlen($this->f('news_linkwebsite'))
                && !preg_match('/^(http|ftp|news|https):\/\//', 
                               $this->f('news_linkwebsite'))) {
                $this->setError(__('The associated link must start with http:// or a valid protocol.'), 400);
            }
        }      
        
        if (false !== $this->error()) {
            return false;
        }
        return true;
    }

    /**
     * Get the path to the news.
     *
     * @param string Force type of path ('')
     * @return string The path
     */
    function getPath($type='')
    {
        // Old website compatibility hack
		if (preg_match('#^\d+\-#', $this->f('path'))) {
            return parent::getPath($type);
        } else {
            $path = $this->f('path');
            $this->setField('path', $this->f('resource_id').'-'.$path);
            $newpath = parent::getPath($type);
            $this->setField('path', $path);
            return $newpath;
        }
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
        Hook::register('onInitTemplate', 'News', 'hookOnInitTemplate');
        $l10n = new l10n(config::f('lang'));
        $l10n->loadTemplate(config::f('lang'), config::f('theme_id'));
        // Easy access
        $GLOBALS['_PX_render']['last'] = '';
        $last =& $GLOBALS['_PX_render']['last']; 
        $GLOBALS['_PX_render']['website'] = '';
        $website =& $GLOBALS['_PX_render']['website']; 
        $GLOBALS['_PX_render']['cat'] = '';
        $cat =& $GLOBALS['_PX_render']['cat']; 
        $GLOBALS['_PX_render']['news'] = '';
        $news =& $GLOBALS['_PX_render']['news']; 
        $GLOBALS['_PX_render']['ct'] = '';
        $ct =& $GLOBALS['_PX_render']['ct']; 

        // Parse query string to find the matching news
        list($news_id, $category_path) = News::parseQueryString($query);

        // Load the matching news
        // If news does not exists, returns error code
        // Will be catched up by the 404 at the end
        $sql = SQL::getOnlineResourceInCat($news_id, $category_path, 
                                           config::f('website_id'));
        $con =& pxDBConnect();
        if (($news = $con->select($sql, 'News')) !== false) {
            if (!$news->isEmpty() && $news->f('type_id')=='news') {
                $news->load();
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
        $cache = new Cache(urlencode('news%%'.$category_path.'%%'.$news_id));
        $cache->setCacheDirectory(config::getCacheDir());
        $cache->debug = config::f('debug');

        $GLOBALS['_PX_render']['ct'] = $news->comments;
        $GLOBALS['_PX_render']['res_id'] = $news_id;
        header(FrontEnd::getHeader($news->f('subtype_template')));
        // Load the template
		include config::f('manager_path').'/templates/'
            .config::f('theme_id').'/'.$news->f('subtype_template');
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
        if (config::f('action') == 'News') {
            $GLOBALS['_PX_render']['cat'] = FrontEnd::getCategory($GLOBALS['_PX_render']['news']->f('category_id'));;
            
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
            $GLOBALS['_PX_render']['res'] = FrontEnd::getResources($GLOBALS['_PX_render']['news']->f('category_id'),
                                                                   '', '', 1, $order);
            $GLOBALS['_PX_render']['ct_enabled'] = false;
            if ((config::f('comment_support') == 1) 
                or (config::f('comment_support') == 2 && 
                    $GLOBALS['_PX_render']['news']->f('comment_support') == 1)) {
                $GLOBALS['_PX_render']['ct_enabled'] = true;
            }
        }
        return true;
    }

    /**
     * Parse query string.
     *
     * @param string Query string
     * @return array (news id, category path)
     */
    public static function parseQueryString($query)
    {
		$category = '';
        $id = '';

		if (preg_match('#^(.*/)(\d+)\-([^\.]*)$#i', $query, $match)) {
			$category = $match[1];
			$id = (int) $match[2];
		}
        return array($id, $category);
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
        if (false === $this->isModified) {
            return true;
        }

        //Real INSERT/UPDATE into the tables
        $this->getConnection();
        $update = (0 < (int) $this->f('resource_id')) ? true : false;

        if ($update) {
            $req = 'UPDATE '.$this->con->pfx.'resources SET
                subject         = \''.$this->con->esc($this->f('subject')).'\',
                comment_support  = \''.$this->con->esc($this->f('comment_support')).'\',
                subtype_id      = \''.$this->con->esc($this->f('subtype_id')).'\',
                title           = \''.$this->con->esc($this->f('title')).'\',
                path =  \''.$this->con->esc($this->f('resource_id'))
                			.'-'.$this->con->esc(text::str2url($this->f('title'))).'\',
                description     = \''.$this->con->esc($this->f('description')).'\',
                publicationdate = \''.$this->con->esc($this->f('publicationdate')).'\',
                modifdate       = \''.date::stamp().'\',
                enddate         = \''.$this->con->esc($this->f('enddate')).'\',
                status          = \''.$this->con->esc($this->f('status')).'\' 
                
                WHERE resource_id = \''.$this->con->esc($this->f('resource_id')).'\'';
				//,filRouge		= \''.$this->con->esc($this->f('filRouge')).'\' 
        } else {
            $req = 'INSERT INTO '.$this->con->pfx.'resources SET
                website_id      = \''.$this->con->esc($this->f('website_id')).'\',
                type_id         = \''.PX_RESOURCE_MANAGER_NEWS.'\',
                subtype_id      = \''.$this->con->esc($this->f('subtype_id')).'\',
                comment_support  = \''.$this->con->esc($this->f('comment_support')).'\',
                user_id         = \''.$this->con->esc($this->f('user_id')).'\',
                subject         = \''.$this->con->esc($this->f('subject')).'\',
                creatorname     = \'\',
                creatoremail    = \'\',
                creatorwebsite  = \'\',
                publisher       = \'\',
                lang_id         = \''.config::f('lang').'\',
                title           = \''.$this->con->esc($this->f('title')).'\',
                description     = \''.$this->con->esc($this->f('description')).'\',
                creationdate    = \''.date::stamp().'\',
                publicationdate = \''.date::stamp().'\',

                modifdate       = \''.date::stamp().'\',
                enddate         = \''.date::EOT().'\',
                status          = \''.$this->con->esc($this->f('status')).'\',
                size            = \'\',
                version         = 1,
                comment         = \'\',
                misc            = \'\',
                format          = \'text/html\',
                dctype          = \'Text\',
                dccoverage      = \'\',
                rights          = \'\' ';
                //filRouge	    = \''.$this->con->esc($this->f('filRouge')).'\' ';
        }
        if (!$this->con->execute($req)) {
            $this->setError('MySQL: '.$this->con->error(), 500);
            return false;
        }

        if (!$update) {
            //Insert, need to get the id of the news as the path and 
            //the identifier are based on it. Sadly, it is not possible to get
            //the value directly in the insert statement:
            // <http://dev.mysql.com/doc/mysql/en/INSERT.html>
            if (false == ($id = $this->con->getLastID())) {
                $this->setError('MySQL: '.$this->con->error(), 500);
                return false;
            }
            $this->setField('resource_id', $id);
            $this->setField('identifier', 'news-'.$id);
            $this->setField('path', $id.'-'.$this->con->esc(text::str2url($this->f('title'))));
            $req = 'UPDATE '.$this->con->pfx.'resources SET 
                identifier = \''.$this->f('identifier').'\',
                path = \''.$this->f('path').'\'
                WHERE resource_id = \''.$this->con->esc($id).'\'';
            if (!$this->con->execute($req)) {
                $this->setError('MySQL: '.$this->con->error(), 500);
                return false;
            }
        }

        if (empty($id)) $id = $this->f('resource_id');

        if ($this->addAuthor($this->f('user_id'))
            && $this->addToCategory($this->f('category_id'))
            && $this->commitDetails()
            && $this->load($id)) {
            $this->runPostCommitHook();
            return true;
        } else {
            return false;
        }
    }



    /**
     * Save the details of a news.
     *
     * @return bool Success
     */
    function commitDetails()
    {
        $this->getConnection();
        
        //check if need to updated or insert
        if (0 < (int) $this->details->f('resource_id')) {
            $update = true;
        } else {
            $update = false;
        }

        if ($update) {
            $req = 'UPDATE '.$this->con->pfx.'news SET
                news_serial = \''.$this->con->esc(md5($this->f('title'))).'\',
            	news_shortcontent = \''.$this->con->esc($this->details->f('news_shortcontent')).'\',
            	news_content = \''.$this->con->esc($this->details->f('news_content')).'\',
                news_titlewebsite = \''.$this->con->esc($this->details->f('news_titlewebsite')).'\',
                news_linkwebsite = \''.$this->con->esc($this->details->f('news_linkwebsite')).'\'
                WHERE resource_id = \''.$this->con->esc($this->f('resource_id')).'\'';
        } else {
            $req = 'INSERT INTO '.$this->con->pfx.'news SET
                news_serial = \''.$this->con->esc(md5($this->f('title'))).'\',
           		news_shortcontent = \''.$this->con->esc($this->details->f('news_shortcontent')).'\',
            	news_content = \''.$this->con->esc($this->details->f('news_content')).'\',
                news_titlewebsite = \''.$this->con->esc($this->details->f('news_titlewebsite')).'\',
                news_linkwebsite = \''.$this->con->esc($this->details->f('news_linkwebsite')).'\',
                resource_id = \''.$this->con->esc($this->f('resource_id')).'\'';
        }
        if (!$this->con->execute($req)) {
            $this->setError('MySQL: '.$this->con->error(), 500);
            return false;
        }          
        $this->loadDetails(); //Synchro
        return true;
    }

    /**
     * Remove the news from the database.
     *
     * @return bool Success
     */
    function remove()
    {
        $id = $this->f('resource_id');
        if (empty($id)) {
            return true;
        }
                                           
        $delReq = 'DELETE FROM '.$this->con->pfx.'resources WHERE
                   resource_id = \''.$this->con->esc($id).'\'';
        if (!$this->con->execute($delReq)) {
            $this->setError('MySQL: '.$this->con->error(), 500);
            return false;
        }
                    
        $delReq = 'DELETE FROM '.$this->con->pfx.'categoryasso WHERE
                   identifier = \''.$this->con->esc($this->f('identifier')).'\'';
        if (!$this->con->execute($delReq)) {
            $this->setError('MySQL: '.$this->con->error(), 500);
            return false;
        }
        
        $delReq = 'DELETE FROM '.$this->con->pfx.'authorasso WHERE
                   resource_id = \''.$this->con->esc($id).'\'';
        if (!$this->con->execute($delReq)) {
            $this->setError('MySQL: '.$this->con->error(), 500);
            return false;
        }
        
        $delReq = 'DELETE FROM '.$this->con->pfx.'news WHERE
                   resource_id = \''.$this->con->esc($id).'\'';
        if (!$this->con->execute($delReq)) {
            $this->setError('MySQL: '.$this->con->error(), 500);
            return false;
        }

        return true;
    }

    /**
     * Remove details.
     * Remove the detailed informations from the `news` table for 
     * the current news.
     *
     * @return bool Success
     */
    function removeDetails()
    {
        if (0 < (int) $this->f('resource_id')
            && $this->f('resource_id') == $this->details->f('resource_id')
            ) {
            $this->getConnection();
            $req = 'DELETE FROM '.$this->con->pfx.'news WHERE 
               resource_id = \''.$this->con->esc($this->f('resource_id')).'\'';
            if (!$this->con->execute($req)) {
                $this->setError('MySQL: '.$this->con->error(), 500);
                return false;
            }          
            $this->loadDetails(); //Synchro
            return true;
        } else {
            $this->setError(__('Error: No details to be removed for the current news.'), 400);
            return false;
        }
    }
}
?>
