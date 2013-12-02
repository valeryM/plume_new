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
 * Levels of rights for the users. Equivalent to the ones
 * in lib.auth.php, need a merging at some point.
 */
define('PX_USER_LEVEL_ADMIN', 9);
define('PX_USER_LEVEL_ADVANCED', 5);
define('PX_USER_LEVEL_INTERMEDIATE', 4);
define('PX_USER_LEVEL_SIMPLE', 1);
define('PX_USER_LEVEL_DISABLE', 0);

require_once dirname(__FILE__).'/class.l10n.php';

/**
 * Basic manager, it implements basic functionnalities
 */
class BasicManager extends CError
{
    var $con  = null;
    var $user = null;
    var $l10n = null;

    function BasicManager()
    {
        $this->con = & pxDBConnect();
    }
    
    function setUser(&$user)
    {
        global $_PX_website_config;
        $this->user = $user;
        $this->l10n = new l10n($this->user->lang);
        if (!empty($this->user->website)) {
            if (file_exists($GLOBALS['_PX_config']['manager_path'].'/conf/configweb_'.$this->user->website.'.php')) {
                include_once($GLOBALS['_PX_config']['manager_path'].'/conf/configweb_'.$this->user->website.'.php');
            }
        }
        return true;
    }
    
    function getUsers()
    {
        $r = 'SELECT * FROM '.$this->con->pfx.'users';
        if ($this->user->f('user_id') != 1) {
            $r .= "\n".'LEFT JOIN '.$this->con->pfx.'grants ON
          '.$this->con->pfx.'users.user_id='.$this->con->pfx.'grants.user_id
          WHERE '.$this->con->pfx.'grants.website_id=\''.$this->user->website.'\'';
        }
        if (($rs = $this->con->select($r, 'User')) !== false) {
            return $rs;
        } else {
            $this->setError('MySQL: '.$this->con->error(), 500);
            return false;
        }
    }

    function getUserById($id)
    {
        if (strlen($id) && preg_match('/^[0-9]+$/',$id)) {
            $where = 'WHERE user_id=\''.$this->con->escapeStr($id).'\'';
        } else {
            $where = 'WHERE user_username=\''.$this->con->escapeStr($id).'\'';
        }
        $r = 'SELECT * FROM '.$this->con->pfx.'users '.$where;
        if (($rs = $this->con->select($r, 'User')) !== false) {
            return $rs;
        } else {
            $this->setError('MySQL : '.$this->con->error(), 500);
            return false;
        }
    }
    
    function getUsedGroups($id) {
    	$r = 'SELECT * FROM '.$this->con->pfx.'users WHERE user_group='.$id;
    	if (($rs = $this->con->select($r, 'UsedGroups')) !== false) {
    		return $rs;
    	} else {
    		$this->setError('MySQL: '.$this->con->error(), 500);
    		return false;
    	}
    }

	function getUserGroups()  {
		$r = 'SELECT * FROM '.$this->con->pfx.'usergroups';
	    if (($rs = $this->con->select($r, 'Groups')) !== false) {
            return $rs;
        } else {
            $this->setError('MySQL: '.$this->con->error(), 500);
            return false;
        }
	}

	function getUserGroup($id)  {
		$r = 'SELECT * FROM '.$this->con->pfx.'usergroups WHERE group_id='.$id;
		if (($rs = $this->con->select($r, 'Group')) !== false) {
			return $rs;
		} else {
			$this->setError('MySQL: '.$this->con->error(), 500);
			return false;
		}
	}
	
    /** Get all the categories of the current website.

    @param int parent id of the categories ('')
    @param string extra condition ('')
    @param string order condition ('ORDER BY category_path')
    @return object recordset
    */
    function getCategories($parentid = '', $extra = '', $order = 'ORDER BY category_path')
    {
		// Si l'utilisateur en cours est l'admin on prends toute la liste		
		if ($this->user->getWebsiteLevel($this->user->website) == PX_USER_LEVEL_ADMIN) {
			$sql='SELECT * FROM '.$this->con->pfx.'categories WHERE website_id=\''.$this->user->website.'\'';
		} else {
			// Sinon on filtre sur la liste autorisée (dans usercats)
			$sql='SELECT * FROM '.$this->con->pfx.'categories LEFT JOIN '.$this->con->pfx.'usercats 
				ON '.$this->con->pfx.'categories.category_id = '.$this->con->pfx.'usercats.category_id
				AND '.$this->con->pfx.'categories.website_id = '.$this->con->pfx.'usercats.website_id
				WHERE '.$this->con->pfx.'categories.website_id = \''.$this->user->website.'\'
				AND user_id ='. $this->user->getId() ;			
		}
        //$sql = 'SELECT * FROM '.$this->con->pfx.'categories WHERE website_id=\''.$this->user->website.'\'';
        if (!empty($parentid)) {
            $sql .= ' AND category_parentid=\''.$this->con->escapeStr($parentid).'\'';
        }
		
        if (!empty($extra)) {
            $sql .= ' AND ('.$extra.')';
        }
        $sql .= ' '.$order;

        if (($rs = $this->con->select($sql)) !== false) {
            return $rs;
        } else {
            $this->setError('MySQL : '.$this->con->error(), 500);
            return false;
        }
    }

    /** Get the categories for a Path of the current website.

    @param int parent id of the categories ('')
    @param string extra condition ('')
    @param string order condition ('ORDER BY category_path')
    @return object recordset
    */
    function getCategoriesFromParent($parentid = '', $extra = '', $order = 'ORDER BY category_path', $all_level = true)
    {
		// Si l'utilisateur en cours est l'admin on prends toute la liste		
		if ($this->user->getWebsiteLevel($this->user->website) == PX_USER_LEVEL_ADMIN) {
			$sql='SELECT * FROM '.$this->con->pfx.'categories WHERE website_id=\''.$this->user->website.'\'';
		} else {
			// Sinon on filtre sur la liste autorisée (dans usercats)
			$sql='SELECT * FROM '.$this->con->pfx.'categories LEFT JOIN '.$this->con->pfx.'usercats 
				ON '.$this->con->pfx.'categories.category_id = '.$this->con->pfx.'usercats.category_id
				AND '.$this->con->pfx.'categories.website_id = '.$this->con->pfx.'usercats.website_id
				WHERE '.$this->con->pfx.'categories.website_id = \''.$this->user->website.'\'
				AND user_id ='. $this->user->getId() ;			
		}
		//echo 'parentid:'.$parentid;
        if (!empty($parentid) || $parentid==0) {
        	//echo 'parentid not empty';
        	if (false !== ($flt=$this->getCategory($parentid)) ) {
        		//$filtre  = $flt->f('category_path') . '%';
        		if (true == $all_level) {
        			$sql .= ' AND category_path LIKE \''.$flt->f('category_path') . '%\' ';
        		} else {
        			$sql .= ' AND category_parentid = '.$parentid .' ';
        		}
        	}
            
        }
		
        if (!empty($extra)) {
            $sql .= ' AND ('.$extra.')';
        }
        $sql .= ' '.$order;
        //echo $sql;
        if (($rs = $this->con->select($sql)) !== false) {
            return $rs;
        } else {
            $this->setError('MySQL : '.$this->con->error(), 500);
            return false;
        }
    }    
    
    /** Get id and name of the categories for a id of the current website.

    @param int parent id of the categories ('')
    @param string extra condition ('')
    @param string order condition ('ORDER BY category_path')
    @return object recordset
    */
    function getCategoriesLightFromParent($parentid = '', $extra = '', $order = 'ORDER BY category_path', $all_level = true)
    {
		// Si l'utilisateur en cours est l'admin on prends toute la liste		
    	$level = $this->user->getWebsiteLevel($this->user->website);
		if ($level == PX_USER_LEVEL_ADMIN) {
			$sql='SELECT '.$this->con->pfx.'categories.category_id, category_name FROM '.$this->con->pfx.'categories WHERE website_id=\''.$this->user->website.'\' ';
		} else {
			// Sinon on filtre sur la liste autorisée (dans usercats)
			$sql='SELECT '.$this->con->pfx.'categories.category_id, category_name FROM '.$this->con->pfx.'categories LEFT JOIN '.$this->con->pfx.'usercats 
				ON '.$this->con->pfx.'categories.category_id = '.$this->con->pfx.'usercats.category_id
				AND '.$this->con->pfx.'categories.website_id = '.$this->con->pfx.'usercats.website_id
				WHERE '.$this->con->pfx.'categories.website_id = \''.$this->user->website.'\'
				AND user_id ='. $this->user->getId() ;			
		}
		//echo 'parentid:'.$parentid;
        if (!empty($parentid) || $parentid==0) {
        	//echo 'parentid not empty';
        	if (false !== ($flt=$this->getCategory($parentid)) ) {
        		//$filtre  = $flt->f('category_path') . '%';
        		if (true == $all_level) {
        			$sql .= ' AND category_path LIKE \''.$flt->f('category_path') . '%\' ';
        		} else {
        			if ($level == PX_USER_LEVEL_ADMIN)
        				$sql .= ' AND category_parentid = '.$parentid .' AND category_id != '.$parentid.' ';
        			else 
        				$sql .= ' AND category_parentid >= '.$parentid .' AND category_id != '.$parentid.' ';
        		}
        	}
            
        }
		
        if (!empty($extra)) {
            $sql .= ' AND ('.$extra.')';
        }
        $sql .= ' '.$order;
        //echo $sql;
        if (($rs = $this->con->select($sql)) !== false) {
            return $rs;
        } else {
            $this->setError('MySQL : '.$this->con->error(), 500);
            return false;
        }
    }    
    
    /**
     * Get the list of categories into an array
     * @return false or the array of categories (id => name + path)
     */
    function getArrayCategories()   {
    	$cats = array();
    	if ( ($rs= $this->getCategories())=== true ) {
    		while (!$rs->EOF()) {
    			$cats[$rs->f('category_id')] = $rs->f('category_name') . ' (' . $rs->f('category_path') . ')';
    			$rs->moveNext();
    		}
    		return $cats;
    	} else {
    		return false;
    	}
    }
    
    
    /** 
     * Get a category by id or path
     * @param int => by id, string => by path
     * @return object recordset
    **/
    function getCategory($id)
    {
        if (preg_match('/^[0-9]+$/', $id)) {
            $r = 'SELECT * FROM '.$this->con->pfx.'categories WHERE website_id=\''.$this->user->website.'\'
                  AND category_id=\''.$this->con->escapeStr($id).'\'';
        } else {
            $r = 'SELECT * FROM '.$this->con->pfx.'categories WHERE website_id=\''.$this->user->website.'\'
                  AND category_path=\''.$this->con->escapeStr($id).'\'';
        }

        if (($rs = $this->con->select($r)) !== false) {
            return $rs;
        } else {
            $this->setError('MySQL : '.$this->con->error(), 500);
            return false;
        }
    }


    
    /** 
     * Get templates.
     *
     * Get the list of templates for the website with a filter by
     * radical.
     *
     * @return array
     * @param  string radical
     */
    function getTemplates($rad='')
    {
        $res = array();
        if (strlen($rad) > 0) {
            $regex = '/^('.$rad.')\_([A-Za-z0-9\_\-]+)(\.tpl|\.php|\.html|\.htm|\.xhtml)$/i';
        } else {
            $regex = '/^()([A-Za-z0-9\_\-]+)(\.tpl|\.php|\.html|\.htm|\.xhtml)$/i';
        }
        
        if (false !== ($rep=@opendir(config::f('manager_path').'/templates/'.config::f('theme_id')))) {
            while ($file = readdir($rep)) {
                if (preg_match($regex, $file, $match)) {
                    $res[$match[2].$match[3]] = $file;
                }
            }
        } else {
            $this->setError( __('Error: Impossible to load the list of templates, check the availability of the template folder.'), 500);
        }
        return $res;
    }


	/**
	 * Get the site()s
	 * @param integer website id (optional)
	 * @return recordset or false
	 */
    function getSites($id = '')
    {
        $r = 'SELECT * FROM '.$this->con->pfx.'websites';
        if (strlen($id))
            $r .= ' WHERE website_id = \''.$this->con->escapeStr($id).'\'';

        if (($rs = $this->con->select($r)) !== false) {
            return $rs;
        } else {
            $this->setError('MySQL : '.$this->con->error(), 500);
            return false;
        }
    }


    /**
    Get resources by search.
    
    @return object Resource or as specified
    @param string Query string
    @param bool Is the resource available online (true)
    @param string Type of the resource (all)
    @param string Type of object to return
    */
    function searchResources($query, $online=true, $type='', $class='ResourceSet')
    {
        include_once dirname(__FILE__).'/class.search.php';
        include_once dirname(__FILE__).'/class.resourceset.php';

        $this->con = &pxDBConnect();
        $s = new Search($this->con, $this->user->website);
        $sql = $s->create_search_query_string($query, true, $type);
        $extra = '';
        if ($online) {
            $extra .= ' AND '.$this->con->pfx.'resources.publicationdate <= '.date::stamp();
            $extra .= ' AND '.$this->con->pfx.'resources.enddate >= '.date::stamp();  
            $extra .= ' AND '.$this->con->pfx.'resources.status = '.PX_RESOURCE_STATUS_VALIDE;
        }
        if (!empty($type)) {
            $extra .=  ' AND '.$this->con->pfx.'resources.type_id=\''.$this->con->escapeStr($type).'\'';
        }
        $sql = sprintf($sql, $extra);

        if (($rs = $this->con->select($sql, $class)) !== false) {
            return $rs;
        } else {
            $this->setError('MySQL : '.$this->con->error(), 500);
            return false;
        }       
    
    }

    /** Get a list of resources, possible filter by user, date, status,
        category.

    @author loic d'Anterroches
    @return object Resource
    @param int user id
    @paran int status
    @param int category
    @param string type_id
    @param int date start (last modification)
    @param int date end (last modification)
    @param string limit
    @param string order ASC or DESC [default]
    @paran bool availableonline if set to true, will find only the that have good startdate and enddate for today
    */
    function getResources($user_id='', $status='', $category='', $type='', $datestart='', $dateend='', $limit='', $order='DESC', $availableonline = false, $byPath = false)
    {
    	$cols = '';
    	if ($type == 'events') {
    		$cols = ', '.$this->con->pfx.'events.* ';
    	} elseif ($type == 'news') {
    		$cols = ', '.$this->con->pfx.'news.* ';
    	}
        $r = 'SELECT DISTINCT '.$this->con->pfx.'resources.* '.$cols.' FROM '.$this->con->pfx.'resources 
              LEFT JOIN '.$this->con->pfx.'categoryasso ON '.$this->con->pfx.'categoryasso.identifier='.$this->con->pfx.'resources.identifier
              LEFT JOIN '.$this->con->pfx.'categories ON '.$this->con->pfx.'categoryasso.category_id='.$this->con->pfx.'categories.category_id
              
              LEFT JOIN '.$this->con->pfx.'websites ON '.$this->con->pfx.'websites.website_id='.$this->con->pfx.'resources.website_id ';
        if ($type == 'news') {
            $r .= ' LEFT JOIN '.$this->con->pfx.'news ON '.$this->con->pfx.'news.resource_id='.$this->con->pfx.'resources.resource_id ';
        } elseif ($type == 'events')  {
            $r .= ' LEFT JOIN '.$this->con->pfx.'events ON '.$this->con->pfx.'events.resource_id='.$this->con->pfx.'resources.resource_id ';
      	}
        $r .= ' WHERE '.$this->con->pfx.'resources.website_id=\''.$this->user->website.'\' ';

        if (strlen($user_id)/* || !authAsLevel(PX_USER_LEVEL_ADVANCED, $this->user->website)*/) {
            $r .= ' AND '.$this->con->pfx.'resources.user_id=\'';
            $r .= ( (strlen($user_id)) ? $user_id : $this->user->user['user_id']).'\'';
        }
        if (strlen($status))
            $r .= ' AND '.$this->con->pfx.'resources.status=\''.$this->con->escapeStr($status).'\'';
        if (strlen($category) ) {
        	if ($byPath == true) {
        		$cat = $this->getCategory($category);
            	$r .= ' AND '.$this->con->pfx.'categories.category_path LIKE \''.$cat->f('category_path').'%\'';
        	} else {
        		$r .= ' AND '.$this->con->pfx.'categories.category_id=\''.$this->con->escapeStr($category).'\'';
        	}
        } else 
            $r .= ' AND '.$this->con->pfx.'categoryasso.categoryasso_type='.PX_RESOURCE_CATEGORY_MAIN.' ';
        if (strlen($type))
            $r .= ' AND '.$this->con->pfx.'resources.type_id=\''.$this->con->escapeStr($type).'\'';
        /*
        if ($filRouge == 1) 
        	$r .= ' AND '.$this->con->pfx.'resources.filRouge=\''.$this->con->escapeStr($filRouge).'\'';
        */	
        // Définition du filtre sur la date selon le type de ressource
        $filtreDate = 'resources.modifdate';
        if ($type == 'articles') {
        	$filtreDate = 'resources.modifdate';
        } elseif ($type == 'news' /*|| $type == 'events' */) {
        	$filtreDate = 'resources.publicationdate';
        } elseif ($type == 'events') {
        	$filtreDate = 'events.event_startdate';
        }
        // Application du filtre si les critères sont définis
        if (strlen($datestart)) {
            $r .= ' AND '.$this->con->pfx.$filtreDate.' >= '.$datestart;
        }
        if (strlen($dateend)) {
            $r .= ' AND '.$this->con->pfx.$filtreDate.' <= '.$dateend;
        }

        if ($availableonline) {
            $r .= ' AND '.$this->con->pfx.'resources.publicationdate <= '.date::stamp();
            $r .= ' AND '.$this->con->pfx.'resources.enddate >= '.date::stamp();
        }
        if ($order == 'DESC' or $order == 'ASC') {
            $r .= ' ORDER BY '.$this->con->pfx.$filtreDate.' '.(($order == 'DESC') ? 'DESC' : 'ASC');
        } else {
            $r .= ' '.sprintf($order, $this->con->pfx);
        }

        // By Olivier Meunier <<
        if ($limit != '') {
            $limit = (preg_match('/^[0-9]+$/',$limit)) ? '0,'.$limit : $limit;
            $r .= ' LIMIT '.$limit.' ';
        }
        // >>
//echo $r;
        if (($rs = $this->con->select($r, 'resourceset')) !== false) {
            return $rs;
        } else {
            $this->setError('MySQL : '.$this->con->error(), 500);
            return false;
        }
        
    }

    /** Get a resource by its identifier or id

    @author loic d'Anterroches
    @return object Resource or false
    @param  string/int identifier
    @param  string type of object to return default is 'Resource'
    */
    function getResourceByIdentifier($identifier = '', $class = 'Resource')
    {
        $r = 'SELECT * FROM '.$this->con->pfx.'resources
              LEFT JOIN '.$this->con->pfx.'categoryasso ON '.$this->con->pfx.'categoryasso.identifier='.$this->con->pfx.'resources.identifier
              LEFT JOIN '.$this->con->pfx.'categories ON '.$this->con->pfx.'categoryasso.category_id='.$this->con->pfx.'categories.category_id
              LEFT JOIN '.$this->con->pfx.'websites ON '.$this->con->pfx.'websites.website_id='.$this->con->pfx.'resources.website_id ';
        if ($class == 'News') {
            $r .= ' LEFT JOIN '.$this->con->pfx.'news ON '.$this->con->pfx.'news.resource_id='.$this->con->pfx.'resources.resource_id ';
        } else if ($class == 'Events') {
        	$r .= ' LEFT JOIN '.$this->con->pfx.'events ON '.$this->con->pfx.'events.resource_id='.$this->con->pfx.'resources.resource_id ';
        }
        $r .= ' WHERE
                categoryasso_type=\''.PX_RESOURCE_CATEGORY_MAIN.'\'';
        if (strlen($identifier) && preg_match('/^[0-9]+$/',$identifier))
            $r .= ' AND '.$this->con->pfx.'resources.resource_id=\''.$this->con->escapeStr($identifier).'\'';
        elseif (strlen($identifier))
            $r .= ' AND '.$this->con->pfx.'resources.identifier=\''.$this->con->escapeStr($identifier).'\'';
        else
            return false;
		
        if (($rs = $this->con->select($r, $class)) !== false) {
            $rs->load();
            return $rs;
        } else {
            $this->setError('MySQL: '.$this->con->error(), 500);
            return false;
        }
    }



    /**
    @note Available under MPL 1.1/GPL 2.0/LGPL 2.1 with DotClear Weblog
    More details: http://www.dotclear.net/
    @author Olivier Meunier
    */
    function getAllDates($format='m',$type='',$cat='')
    {
        if ($format == 'y') {
            $len = 4;
        } elseif ($type == 'd') {
            $len = 8;
        } else {
            $len = 6;
        }

        $reqPlus = ' WHERE '.$this->con->pfx.'resources.website_id=\''.$this->user->website.'\' ';

        if (strlen($type)) $reqPlus .= ' AND '.$this->con->pfx.'resources.type_id=\''.$this->con->escapeStr($type).'\' ';
        if (strlen($cat))  $reqPlus .= ' AND  category_id =\''.$this->con->escapeStr($cat).'\' ';

        if ($type == 'events')  {
        	$strReq = 'SELECT DISTINCT RPAD(LEFT(CONCAT(event_startdate),'.$len.'),8,\'01\') ';
	        $strReq .= ' FROM '.$this->con->pfx.'resources ';
	        if (strlen($cat))
	            $strReq .= ' LEFT JOIN '.$this->con->pfx.'categoryasso ON '.$this->con->pfx.
	                'categoryasso.identifier='.$this->con->pfx.'resources.identifier ';
			$strReq .= ' LEFT JOIN '.$this->con->pfx.'events ON '.
				$this->con->pfx.'events.resource_id='.$this->con->pfx.'resources.resource_id ';
	        $strReq .= $reqPlus.' ORDER BY event_startdate DESC ';
        	
        } elseif ($type == 'news') {
	        $strReq = 'SELECT DISTINCT RPAD(LEFT(CONCAT(publicationdate),'.$len.'),8,\'01\') ';
	        $strReq .= ' FROM '.$this->con->pfx.'resources ';
	        if (strlen($cat))
	            $strReq .= ' LEFT JOIN '.$this->con->pfx.'categoryasso ON '.$this->con->pfx.
	                'categoryasso.identifier='.$this->con->pfx.'resources.identifier ';
	
	        $strReq .= $reqPlus.' ORDER BY publicationdate DESC ';
        } else {
	        $strReq = 'SELECT DISTINCT RPAD(LEFT(CONCAT(modifdate),'.$len.'),8,\'01\') ';
	        $strReq .= ' FROM '.$this->con->pfx.'resources ';
	        if (strlen($cat))
	            $strReq .= ' LEFT JOIN '.$this->con->pfx.'categoryasso ON '.$this->con->pfx.
	                'categoryasso.identifier='.$this->con->pfx.'resources.identifier ';
	
	        $strReq .= ' LEFT JOIN '.$this->con->pfx.'events ON '.
	        		$this->con->pfx.'events.resource_id='.$this->con->pfx.'resources.resource_id ';
	        
	        $strReq .= $reqPlus.' ORDER BY modifdate DESC ';
        } 
	    if (($rs = $this->con->select($strReq)) === false) {
            $this->setError('MySQL : '.$this->con->error(), 500);
            return false;
        } else {
            $res = array();
            while(!$rs->EOF()) {
                $res[str_pad($rs->field(0),14,'0')] = str_pad($rs->field(0),14,'0');
                $rs->moveNext();
            }
            return $res;
        }
    }

    function getEarlierDate($format='m', $type='', $cat='', $website='')
    {
        if ($format == 'y') {
            $len = 4;
        } elseif ($type == 'd') {
            $len = 8;
        } else {
            $len = 6;
        }

        if (strlen($website) == 0) {
            $website = $this->user->website;
        }
        $reqPlus = ' WHERE '.$this->con->pfx.'resources.website_id=\''.$this->con->escapeStr($website).'\' ';

        if (strlen($type)) $reqPlus .= ' AND '.$this->con->pfx.'resources.type_id=\''.$this->con->escapeStr($type).'\' ';
        if (strlen($cat))  $reqPlus .= ' AND  category_id =\''.$this->con->escapeStr($cat).'\' ';

        $strReq = 'SELECT DISTINCT RPAD(LEFT(CONCAT(modifdate),'.$len.'),8,\'01\') ';
        $strReq .= ' FROM '.$this->con->pfx.'resources ';
        if (strlen($cat))
            $strReq .= 'LEFT JOIN '.$this->con->pfx.'categoryasso ON '.$this->con->pfx.
                'categoryasso.identifier='.$this->con->pfx.'resources.identifier';

        $strReq .= $reqPlus.' ORDER BY modifdate DESC LIMIT 1';

        if (($rs = $this->con->select($strReq)) === false) {
            $this->setError('MySQL : '.$this->con->error(), 500);
            return false;
        } else {
            if ($rs->nbRow() == 1)
                return str_pad($rs->field(0),14,'0');
            return false;
        }
    }


    /**
    Search for a resource. Restriction to available online or by type.

    @param string Query string
    @param bool Available online (true)
    @param string Type of resource ('')
    @return object Recordset of the resources
    */
    function search($query, $online = true, $type = '')
    {

    }
    
} // end class Manager
?>
