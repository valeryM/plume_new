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

require_once dirname(__FILE__).'/lib.sqlutils.php';

/**
 * This class regroups a set of static methods used to create
 * the SQL queries used in the manager to select a set of
 * resources. 
 * All the classes needing SQL queries against the main tables should
 * use these methods. If needed the queries can be refactored. The goal
 * is to be able to provide some abstraction for a possible port to
 * another RDBMS.
 */
class SQL
{
    /**
     * Get a user by its user id.
     *
     * @param int User id
     * @return string Ready to use SQL
     */
    public static function getUser($id)
    {
        $con =& pxDBConnect();
        return 'SELECT * FROM '.$con->pfx.'users 
                WHERE user_id=\''.$con->escapeStr($id).'\'';
    }

    /**
     * Get website levels for a user
     *
     * @param int User id
     * @return string Ready to use SQL
     */
    public static function getWebsiteLevels($user_id)
    {
        $con =& pxDBConnect();
        return 'SELECT * FROM '.$con->pfx.'grants
                LEFT JOIN '.$con->pfx.'websites 
                  ON '.$con->pfx.'websites.website_id='
                  .$con->pfx.'grants.website_id
                WHERE user_id LIKE \''.$con->esc($user_id).'\'';
    }

    /**
     * Get a website by its id.
     *
     * @param string Website id
     * @return string Ready to use SQL
     */
    public static function getWebsite($id)
    {
        $con =& pxDBConnect();
        return 'SELECT * FROM '.$con->pfx.'websites
                WHERE website_id=\''.$con->escapeStr($id).'\'';
    }

    /**
     * Get all websites
     *
     * @return string Ready to use SQL
     */
    public static function getWebsites()
    {
    	$con =& pxDBConnect();
    	return 'SELECT * FROM '.$con->pfx.'websites';
    }
    
    /**
     * 
     * Get th last date of modification into the resources for website id
     * @param string Website id
     * @return String ready to use SQL
     */
    public static function getLastModif($id) {
        $con =& pxDBConnect();
        return 'SELECT max(modifdate) AS datemodif FROM '.$con->pfx.'resources 
                WHERE website_id=\''.$con->escapeStr($id).'\'';
    }

    /**
     * Get a resource by its identifier.
     * If the identifier is only composed of digits it is considered as
     * being the 'resource_id' else the 'identifier'.
     * If the category id is empty, the left join is using the main
     * category for the link.
     *
     * @param mixed Identifier or resource id
     * @param int Category id ('')
     * @param string Website id ('')
     * @return string Ready to use SQL
     */
    public static function getResourceByIdentifier($id, $catid='', $website='')
    {
        $con =& pxDBConnect();
        if (empty($catid)) {
            $r = SQL::getResources();
        } else {
            $r = SQL::getResources(false);
            $r .= "\n".'AND '.$con->pfx.'categories.category_id=\''
                .$con->esc($catid).'\'';
        }
        if (preg_match('/^[0-9]+$/', $id)) {
            $r .= ' AND '.$con->pfx.'resources.resource_id=\''
                .$con->escapeStr($id).'\'';
        } else {
            $r .= ' AND '.$con->pfx.'resources.identifier=\''
                .$con->escapeStr($id).'\'';
        }
        if ($website != '') {
            $r .= ' AND '.$con->pfx.'resources.website_id=\''
                .$con->escapeStr($website).'\'';
        }
        //echo $r;
        return $r;
    }

    /**
     * Get a resource by its path. 
     * The path do not include the category path.
     *
     * @param string Path
     * @param string Website id - all the websites if not provided ('')
     * @return string Ready to use SQL
     */
    public static function getResourceByPath($path, $website='')
    {
        $con =& pxDBConnect();
        $r = SQL::getResources().' AND '
            .$con->pfx.'resources.path=\''.$con->esc($path).'\'';
        if (!empty($website)) {
            $r .= ' AND '.$con->pfx.'resources.website_id=\''
                .$con->esc($website).'\'';
        }
        return $r;
    }

    /**
     * Get comment by id.
     * If a resource id is given, the comment must be associated to the given
     * resource.
     *
     * @param int Id of the comment.
     * @param int Id of the resource ('')
     * @return string Ready to use SQL
     */
    public static function getCommentById($id, $resource_id='')
    {
        $con =& pxDBConnect();
        $r = 'SELECT * FROM '
            .$con->pfx.'comments '
            .'LEFT JOIN '.$con->pfx.'resources '
            .'ON '.$con->pfx.'resources.resource_id='
            .$con->pfx.'comments.resource_id '
            .'WHERE comment_id=\''.$con->esc($id).'\'';
        if (!empty($resource_id)) {
            $r .= ' AND '.$con->pfx.'comments.resource_id=\''
                .$con->esc($resource_id).'\'';
        }
        return $r;
    }

    /**
     * Get comments for a given website.
     * Left join on the associated resources, if no website id is given, all
     * the comments are returned.
     *
     * @param string Website id ('')
     * @param string Resource id ('')
     * @param int Status ('')
     * @param string Modification date order of the comments ('ASC')
     * @param int Limit (0)
     * @return string Ready to use SQL
     */
    public static function getComments($website_id='', $resource_id='', 
                         $status='', $order='ASC', $limit=0)
    {
        $con =& pxDBConnect();
        $r = 'SELECT * FROM '.$con->pfx.'comments '
            .'LEFT JOIN '.$con->pfx.'resources '
            .'ON '.$con->pfx.'resources.resource_id='
            .$con->pfx.'comments.resource_id';
        if (!empty($website_id) or !empty($resource_id) or !empty($status)) {
            $r .= ' WHERE ';
        }
        $cond = array();
        if (!empty($website_id)) {
            $cond[] = $con->pfx.'resources.website_id=\''
                .$con->esc($website_id).'\' ';
        }
        if (!empty($resource_id)) {
            $cond[] = $con->pfx.'resources.resource_id=\''
                .$con->esc($resource_id).'\'';
        }
        if (!empty($status)) {
            $cond[] = $con->pfx.'comments.comment_status=\''
                .$con->esc($status).'\'';
        }
        $cond_string = join(' AND ', $cond);

        $r .= $cond_string.'  ORDER BY '
            .$con->pfx.'comments.comment_update '.$order;
        if ($limit > 0) {
            $r .= ' LIMIT '.$limit;
        }
        return $r;
    }

    /**
     * Get comments count for a given website.
     * Left join on the associated resources, if no website id is given, all
     * the comments are used.
     *
     * @param string Website id ('')
     * @param string Resource id ('')
     * @param int Status ('')
     * @return string Ready to use SQL
     */
    public static function countComments($website_id='', $resource_id='', $status='')
    {
        $con =& pxDBConnect();
        $r = 'SELECT COUNT(*) AS n_comments FROM '.$con->pfx.'comments '
            .'LEFT JOIN '.$con->pfx.'resources '
            .'ON '.$con->pfx.'resources.resource_id='
            .$con->pfx.'comments.resource_id';
        if (!empty($website_id) or !empty($resource_id) or !empty($status)) {
            $r .= ' WHERE ';
        }
        $cond = array();
        if (!empty($website_id)) {
            $cond[] = $con->pfx.'resources.website_id=\''
                .$con->esc($website_id).'\' ';
        }
        if (!empty($resource_id)) {
            $cond[] = $con->pfx.'resources.resource_id=\''
                .$con->esc($resource_id).'\'';
        }
        if (!empty($status)) {
            $cond[] = $con->pfx.'comments.comment_status=\''
                .$con->esc($status).'\'';
        }
        $cond_string = join(' AND ', $cond);
        $r .= $cond_string;
        return $r;
    }

    /**
     * Get a category by path.
     *
     * @param string Path
     * @param string Website id - all the websites if not provided ('')
     * @return string Ready to use SQL
     */
    public static function getCategoryByPath($path, $website='')
    {
        $con =& pxDBConnect();
        $r = 'SELECT * FROM '.$con->pfx.'categories
           LEFT JOIN '.$con->pfx.'websites 
             ON '.$con->pfx.'websites.website_id='
            .$con->pfx.'categories.website_id
              WHERE '.$con->pfx.'categories.category_path=\''
            .$con->esc($path).'\'';
        if (!empty($website)) {
            $r .= ' AND '.$con->pfx.'categories.website_id=\''
                .$con->esc($website).'\'';
        }
        return $r;
    }

    /**
     * Get a category by its id.
     *
     * @param int Id 
     * @return string Ready to use SQL
     */
    public static function getCategoryById($id)
    {
        $con =& pxDBConnect();
		
        $sql= 'SELECT * FROM '.$con->pfx.'categories
           LEFT JOIN '.$con->pfx.'websites 
             ON '.$con->pfx.'websites.website_id='
            .$con->pfx.'categories.website_id
              WHERE '.$con->pfx.'categories.category_id=\''
            .$con->esc($id).'\'';
		return $sql;	
    }

	
	    /**
     * Get the category alllowed for a user identified by its id.
     *
     * @param int Id 
     * @return string Ready to use SQL
     */
    public static function getCategoryForUser($userid)
    {
        $con =& pxDBConnect();
		
        $sql= 'SELECT * FROM '.$con->pfx.'categories
           LEFT JOIN '.$con->pfx.'websites 
             ON '.$con->pfx.'websites.website_id='.$con->pfx.'categories.website_id
			LEFT JOIN '.$con->pfx.'usercats ON '.$con->pfx.'usercats.website_id='.$con->pfx.'websites.website_id
				AND '.$con->pfx.'usercats.category_id='.$con->pfx.'categories.category_id
              WHERE '.$con->pfx.'usercats.user_id='.$con->esc($userid);
		return $sql;	
    }
    /**
     * Get an online resource in a category.
     *
     * If first parameter is not an integer, it is considered as the path.
     *
     * @param mixed Resource path or id
     * @param mixed Category path or id 
     * @param string Website id
     * @return string Ready to use SQL
     */
    public static function getOnlineResourceInCat($res, $cat, $website)
    {
        $con =& pxDBConnect();
        $r = SQL::getResources(false);
        $r .= ' AND '
            .$con->pfx.'resources.website_id=\''.$con->esc($website).'\' ';
        if (preg_match('/^[0-9]+$/', $cat)) {
        	$r .= ' AND category_id=\''.$con->esc($cat).'\' ';
        } else {
			$r .= ' AND category_path LIKE \''.$con->esc($cat).'\' ';
        }
        /*
			AND '.$con->pfx.'resources.publicationdate <= '.date::stamp().'
			AND '.$con->pfx.'resources.enddate >= '.date::stamp().'
			AND '.$con->pfx.'resources.status=1';
        */
        $r .= ' AND ( ';
        $r .= '('.$con->pfx.'resources.type_id = \'events\'  AND '.$con->pfx.'resources.enddate >= '.date::stamp().') '; 
        $r .= ' OR ('.$con->pfx.'resources.publicationdate <= '.date::stamp();
        $r .= ' AND '.$con->pfx.'resources.enddate >= '.date::stamp().') )';
        $r .= ' AND '.$con->pfx.'resources.status=1';
/*
        if ($this->availableonline) {
        	$r .= ' AND (('.$this->con->pfx.'resources.publicationdate <= '.date::stamp();
        	$r .= ' AND '.$this->con->pfx.'resources.enddate >= '.date::stamp().' ) ';
        	$r .= ' OR ('.$this->con->pfx."resources.type_id = 'events' AND ".$this->con->pfx.'resources.enddate >= '.date::stamp().') ) ';
        		
        }
        */
        if (preg_match('/^[0-9]+$/', $res)) {
            $r .= ' AND '.$con->pfx.'resources.resource_id=\''
                .$con->esc($res).'\'';
        } elseif ($res!='') {
            $r .= ' AND '.$con->pfx.'resources.path LIKE \''
                .$con->esc($res).'\'';
        }
        //echo $r;
        return $r;
    }

    /**
     * Get an resource in a category.
     *
     * If first parameter is not an integer, it is considered as the path.
     *
     * @param mixed Resource path or id
     * @param string Category path
     * @param string Website id
     * @return string Ready to use SQL
     */
    public static function getResourceInCat($res, $cat, $website)
    {
    	$con =& pxDBConnect();
    	$r = SQL::getResources(false);
    	$r .= ' AND '
    	.$con->pfx.'resources.website_id=\''.$con->esc($website).'\'
    	AND category_path LIKE \''.$con->esc($cat).'\'
    	AND '.$con->pfx.'resources.status=1';
    	/*
    	AND '.$con->pfx.'resources.publicationdate <= '.date::stamp().'
    	AND '.$con->pfx.'resources.enddate >= '.date::stamp().'    	 
    	 */
    	if (preg_match('/^[0-9]+$/', $res)) {
    		$r .= "\n".'AND '.$con->pfx.'resources.resource_id=\''
    		.$con->esc($res).'\'';
    	} else {
    		$r .= "\n".'AND '.$con->pfx.'resources.path LIKE \''
    		.$con->esc($res).'\'';
    	}
    	//echo $r;
    	return $r;
    }
    
    

    /**
     * Get resources.
     *
     * Get all the basic data, if the category returned is the main category.
     * a WHERE clause is already open.
     * Can be used to then further limit by website, id or user.
     *
     * @param bool Associated to the main category (true)
     * @return string Ready to use SQL
     */
    public static function getResources($main_category=true)
    {
        $con =& pxDBConnect();

        $sql = 'SELECT * 
        	 FROM '.$con->pfx.'resources
           LEFT JOIN '.$con->pfx.'categoryasso USING(identifier)
           LEFT JOIN '.$con->pfx.'categories USING(category_id)
           LEFT JOIN '.$con->pfx.'websites 
             ON '.$con->pfx.'websites.website_id='.$con->pfx.'resources.website_id 
		   LEFT JOIN '.$con->pfx.'subtypes USING(subtype_id)               
           LEFT JOIN '.$con->pfx.'articles USING (resource_id) 
           LEFT JOIN '.$con->pfx.'news USING (resource_id)
           LEFT JOIN '.$con->pfx.'events USING (resource_id)
           WHERE ('.$con->pfx.'articles.page_number IS NULL OR '.$con->pfx.'articles.page_number=1) ';

        if ($main_category == true) {
            include_once dirname(__FILE__).'/class.resource.php';
            $sql .= "\n".' AND  categoryasso_type=\''
                .PX_RESOURCE_CATEGORY_MAIN.'\' ';
        }
        
        return $sql;
    }

    /**
     * Get resources in a category.
     *
     * @param id Category id
     * @return string Ready to use SQL
     */
    public static function getResourcesInCat($id,$cat='',$website='')
    {
    	$con =& pxDBConnect();

        $sql = 'SELECT *        	
        	 FROM '.$con->pfx.'resources 
           LEFT JOIN '.$con->pfx.'categoryasso USING(identifier)
           LEFT JOIN '.$con->pfx.'categories USING(category_id)
           LEFT JOIN '.$con->pfx.'websites 
             ON '.$con->pfx.'websites.website_id='.$con->pfx.'resources.website_id 
           LEFT JOIN '.$con->pfx.'articles USING (resource_id) 
           LEFT JOIN '.$con->pfx.'news USING (resource_id)
           LEFT JOIN '.$con->pfx.'events USING (resource_id)
           WHERE '.$con->pfx.'categories.category_id=\''.$con->esc($id).'\' 
            AND ('.$con->pfx.'articles.page_number IS NULL OR '.$con->pfx.'articles.page_number=1)';
        if ($website != '') {
        	$sql .= "\n".'AND '.$con->pfx.'resources.website_id=\''
        			.$con->esc($website).'\'';
        }
        return $sql;
    }

    /**
     * Get the last resources.
     *
     * @param string Website id ('')
     * @param string Type of resource ('')
     * @param int Category id ('')
     * @param int Maximum number of results ('')
     * @return string Ready to use SQL
     */
    public static function getLastResources($website='', $type='', $category='', $limit='')
    {
        $con =& pxDBConnect();
        if ($category == '') {
            $main_category = true;
        } else {
            $main_category = false;
        }
        $sql = SQL::getResources($main_category);
        if ($main_category == false) {
            $sql .= "\n". 'AND '
                .$con->pfx.'categories.category_id=\''
                .$con->esc($category).'\'';
        }
        if ($website != '') {
            $sql .= "\n".'AND '.$con->pfx.'resources.website_id=\''
                .$con->esc($website).'\'';
        }
        if ($type != '') {
            $sql .= "\n".'AND '.$con->pfx.'resources.type_id=\''
                .$con->esc($type).'\'';
        }
        $sql .= ' ORDER BY '.$con->pfx.'resources.modifdate DESC';
        if ($limit != '') {
            $limit = (preg_match('/^[0-9]+$/',$limit)) ? '0,'.$limit : $limit;
            $sql .= ' LIMIT '.$con->esc($limit);
        }
        return $sql;
    }


}
?>
