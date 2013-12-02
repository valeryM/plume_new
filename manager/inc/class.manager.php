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

require_once dirname(__FILE__).'/class.l10n.php';
require_once dirname(__FILE__).'/class.resource.php';
require_once dirname(__FILE__).'/class.basicmanager.php';
require_once dirname(__FILE__).'/class.user.php';
require_once dirname(__FILE__).'/class.article.php';
require_once dirname(__FILE__).'/class.news.php';
require_once dirname(__FILE__).'/class.rsslinks.php';
require_once dirname(__FILE__).'/class.events.php';
require_once dirname(__FILE__).'/class.mail.php';
//require_once dirname(dirname(__FILE__)).'/tools/htmlValidator/Services/W3C/HTMLValidator.php';

include_once dirname(__FILE__).'/class.basicmanager.php';

class Manager extends BasicManager
{
    var $con  = null;
    var $user = null;
	var $cats = null;
	var $groups = null;
	var $website = null;
    
    /**
     * Constructor.
     * Depending on the context, a user is automatically created
     * from the session data.
     */
    function Manager()
    {
        $this->con =& pxDBConnect();

        if ('manager' == config::f('context')) {
            //create a user from the session
            $this->user = new User();
            $this->user->synchronize();
            $this->website = new Website();
            $this->website = $this->getSites($this->user->website);
            $this->l10n = new l10n($this->user->lang);
        }
    }


    /**
     * Set a message.
     *
     * @param string Message
     */
    function setMessage($msg)
    {
        $_SESSION['message'] = $msg;
    }

    /**
     * Get the message
     *
     * The message is poped.
     *
     * @return string Message
     */
    function getMessage()
    {
        $msg = '';
        if (!empty($_SESSION['message'])) {
            $msg = $_SESSION['message'];
            $_SESSION['message'] = '';
            unset($_SESSION['message']);
        }
        return $msg;
    }

    /**
     * Set a message.
     *
     * @param string Message
     */
    function setPopupMessage($msg)
    {
        $_SESSION['popupmessage'] = $msg;
    }

    /**
     * Get the message
     *
     * The message is poped.
     *
     * @return string Message
     */
    function getPopupMessage()
    {
        $msg = '';
        if (!empty($_SESSION['popupmessage'])) {
            $msg = $_SESSION['popupmessage'];
            $_SESSION['popupmessage'] = '';
            unset($_SESSION['popupmessage']);
        }
        return $msg;
    }
    
    /* Functions used in the manager to populate data for the 
     * forms etc...
     * ------------------------------------------------------------ */
    function getArrayUserGroups()  {
       $this->groups = $this->getUserGroups();
       $arry_grp = array();
		
       while (!$this->groups->EOF()) {
            $name  = $this->groups->f('group_name');
            $arry_grp[$name] = $this->groups->f('group_id');
            $this->groups->moveNext();
        }
        return $arry_grp;
    }
    
    /**
     *  If $addallcat set to true a special "All the categories" is also
     *  given. Used for the listing of the resources in the manager.
     */
    function getArrayCategories($addAllCat=false)
    {
        $this->cats = $this->getCategories();
        $arry_cat = array();
		
        if ($addAllCat)
            $arry_cat[ __('All the categories')]='allcat';
        
        while (!$this->cats->EOF()) {
			//echo $cats->f('category_name') . "-" $cats->f('category_path');
            $name  = $this->cats->f('category_name');
            $name .= ' ('.$this->cats->f('category_path').')';
            if (isGhostCat($this->cats->f('category_path'), $this->cats->f('category_isGhost'))) 
                $name .= ' ['. __('Hidden category').']';
            $arry_cat[$name] = $this->cats->f('category_id');
            $this->cats->moveNext();
        }
        return $arry_cat;
    }
	
    

    /**
     * Check if a category is into the list of categories for the user
     * @param integer Id of category to check
     * @return boolean true if Id is into the list, false if not.
     */
	function isFromCategoryList($id=0)   
	{
		$rep = false;
		$this->cats = $this->getCategories();
		//$this->cats->moveFirst();
		//if ($this->user->cats == null) $this->user->loadCategories();
		while (!$this->cats->EOF() )  {
			//echo "  id recherche " . $id . " ---  id dans this->cats : " .$this->cats->f('category_id');
			if ($id == $this->cats->f('category_id') ) {
				//echo "categorie trouve : " . $rep;
				$rep = true;
				break;
			}
			$this->cats->moveNext();
		}
		return $rep;
	}
        
    /**
     * Get the list of months for the drop-down selectors.
     *
     * @param string type of resource
     * @param int category id
     * @param bool (true) add the all dates choice (true by default!!!)
     * @return array($first, $last, array of months + year)
     */
    function getArrayMonths($type='', $cat_id='', $addalldates=true)
    {
        $arry_months = array();
        if ($addalldates)
            $arry_months[__('All the dates')] = 'alldate';
        $last = '';
        $k = '';
        //getAllDates returns the values in time DESC order
        foreach ($this->getAllDates('m', $type, $cat_id) as $k => $v) {
            if (empty($last)) $last = $k;
            $arry_months[__(strftime('%B',date::unix($k))).' '.strftime('%Y',date::unix($k))] = $k;
        }
        return array($k, $last, $arry_months);
    }

    /**
     * Get the list of months for the drop-down selectors.
     *
     * @return array Months
     */
    function getArrayOnlyMonths()
    {
        $arry_months = array();
        for ($i=1; $i<=12; $i++) {
            $month = sprintf('%02d', $i);
            $arry_months[__(strftime('%B', strtotime('2000-'.$month.'-01')))] = $month;
        }
        return $arry_months;
    }


    /**
     * Get the array of possible resource status.
     *
     * @return array Resource status
     */
    function getArrayResStatus()
    {
        $arry_status = array();
        $arry_status[__('In edition')] = PX_RESOURCE_STATUS_INEDITION;
        $arry_status[__('Waiting for validation')] = PX_RESOURCE_STATUS_TOBEVALIDATED;
        if (auth::asLevel(PX_USER_LEVEL_INTERMEDIATE, $_SESSION['website_id'])) {
            $arry_status[__('On-line')] = PX_RESOURCE_STATUS_VALIDE;
        }
        $arry_status[__('Off-line')] = PX_RESOURCE_STATUS_OFFLINE;   
        return $arry_status;
    }

    /**
     * Get the array of possible comment status.
     *
     * @return array Comment status
     */
    function getArrayCommentStatus()
    {
        $arry_status = $this->getArrayResStatus();
        $arry_status[__('Junk')] = PX_RESOURCE_STATUS_JUNK;
        unset($arry_status[__('In edition')]);
        return $arry_status;
    }

    /**
     * Get the array of possible support of the comment
     * for a resource.
     *
     * @return array Comment support
     */
    function getArrayCommentSupport()
    {
        $arry_status = array(__('Comments open') => 1,
                             __('Comments closed') => 3);
        return $arry_status;
    }

    /* ===================================================================== *
     *                                                                       *
     *                     Management of the resources                       *
     *                                                                       *
     * ===================================================================== */

    /**
     * Check if a user has the rights to edit a resource.
     *
     * A ressource can be edited by a user if:
     * - The user is "owner" of the resource and the ressource is not online.
     * - The user is "owner" of the resource and user has at least an PX_USER_LEVEL_INTERMEDIATE level.
     * - The user has at least an PX_USER_LEVEL_ADVANCED level.
     *
     * @param &object Resource object
     * @return bool 
     */
    function asRightToEdit(&$res)
    {
        if (auth::asLevel(PX_USER_LEVEL_ADVANCED,$res->f('website_id'))
		|| (auth::asLevel(PX_USER_LEVEL_INTERMEDIATE,$res->f('website_id'))
			&& $res->f('user_id') == $this->user->getId())
				||($res->f('user_id') == $this->user->getId()
					 && $res->f('status') !=PX_RESOURCE_STATUS_VALIDE)
            ) {
            return true;
        } else {
            return false;
        }
    }
    /**
     * Check if a user has the rights to edit a resource.
     *
     * A ressource can be edited by a user if:
     * - The user is "owner" of the resource and the ressource is not online.
     * - The user is "owner" of the resource and user has at least an PX_USER_LEVEL_INTERMEDIATE level.
     * - The user has at least an PX_USER_LEVEL_ADVANCED level.
     *
     * @param &object Resource object
     * @return bool 
     */
     function asRightToCopy(&$res)
    {
        if ( ($res->f('status') ==PX_RESOURCE_STATUS_VALIDE 
        				|| $res->f('status') ==PX_RESOURCE_STATUS_OFFLINE )
        	&& (auth::asLevel(PX_USER_LEVEL_ADVANCED,$res->f('website_id'))
				|| (auth::asLevel(PX_USER_LEVEL_INTERMEDIATE,$res->f('website_id'))
						&& $res->f('user_id') == $this->user->getId())				
				||($res->f('user_id') == $this->user->getId() ) ) )  {
            return true;
        } else {
            return false;
        }
    }
    /**
     * Check if user has the rights to view a resource.
     * 
     * Return true if the category from the resource is in the list of categories for the user.
     * @param &object Resource object
     * @return boolean
     */
    function asRightToView(&$res)
    {
		//find if the category from the resource is in the list of Category from user
		//$user_local=$this->user->getId();
		//$res->f('website_id');
		$id=$res->f('category_id');
        //echo "res->category_id " . $id . " ";
		return ($this->isFromCategoryList($id ) ) ;
        
    }

    /**
     * Load a resource for the current website.
     * As the resource object has a type, all the type checking is done
     * by the object. Only the website checking is done by this method.
     * 
     * @param &object Resource object in which the resource will be set
     * @param int Resource id
     * @return bool Success
     */
    function loadResource(&$res, $id)
    {
        if (false === $res->load($id))
            return false;
        if ($res->f('website_id') != $this->user->website)
            return false;

        return true;
    }




    /** 
     * Add a resource in a category.
     * 
     * @param &object Reference of the resource object
     * @param int Category id
     * @param int Type of category (PX_RESOURCE_CATEGORY_MAIN)
     * @return bool Success
     */
    function addResourceInCategory(&$res, $catid, 
                                   $type=PX_RESOURCE_CATEGORY_MAIN)
    {
        //check the rights
        if (!$this->asRightToEdit($res)) {
            $this->setError(__('Error: You do not have the correct rights to edit this resource.'), 400);
            return false;
        }
        if (!$res->addToCategory($catid, $type)) {
            $this->bulkSetError($res->error());
            return false;
        } else {
        	// Send a mail to notify what's adding
        	//if (PX_CONFIG_MAIL_ON_CREATE == true)
        		//@TODO : Envoil mail à la création 
        		//$this->sendEmail('Ajout d\'une resource dans une catégorie', 'catégorie='.$catid, $cat->f('website_id'),PX_CONFIG_MAIL_LEVEL);
        }
        $this->triggerMassUpdate();
        return true;
    }
    
    /**
     * Remove a resource from a category
     *
     * @param &object Reference of the resource object
     * @param int Category id
     * @return bool Success
     */
    function removeResourceFromCategory(&$res, $catid)
    {
        //check the rights
        if (!$this->asRightToEdit($res)) {
            $this->setError(__('Error: You do not have the correct rights to edit this resource.'), 400);
            return false;
        }
        if (!$res->removeFromCategory($catid)) {
            $this->bulkSetError($res->error());
            return false;
        }
        $this->triggerMassUpdate();
        return true;    
    }
   





    /* ===================================================================== *
     *                                                                       *
     *                           Utility functions                           *
     *                                                                       *
     * ===================================================================== */
 
    /**
     * Trigger mass update for a given website. 
     * If no website given, use the currently managed by the user.
     *
     * @param string Website id ('')
     * @return bool true
     */
    public static function triggerMassUpdate($website='')
    {
        if (empty($website)) $website = config::f('website_id'); 
        @touch(dirname(__FILE__).'/../cache/'.$website.'/MASS_UPDATE', time());
        @chmod(dirname(__FILE__).'/../cache/'.$website.'/MASS_UPDATE', 0666);
    }

    /**
     * Index a resource.
     *
     * @param &object Resource object
     * @return bool Success
     */
    function indexResource(&$res)
    {
        include_once dirname(__FILE__).'/class.search.php';

        $s = new Search($this->con, $res->f('website_id'));
        
        $s->index(html_entity_decode($res->getAsString(),ENT_QUOTES,'UTF-8'), $res->f('resource_id'));
        if (false !== $s->error()) {
            return false;
        }
        return true;
    }
        
    /**
     * Remove a resource from the index.
     *  
     * @param &object Resource object
     * @return bool Success
     */
    function indexRemove(&$res)
    {
        include_once dirname(__FILE__).'/class.search.php';

        $s = new Search($this->con, $res->f('website_id'));
        $s->remove_from_index($res->f('resource_id'));
        if (false !== $s->error()) {
            return false;
        } 
        return true;
    }


    /**
     * Get the list of subtypes, ordered by type
     *
     * @param int Optional, limit the search to one subtype
     * @param string Optional, limit the search to a type of resource
     * @return object RecordSet
     */
    function getSubTypes($id='', $type='')
    {
        $r = 'SELECT * FROM '.$this->con->pfx.
            'subtypes WHERE website_id=\''.$this->user->website.'\'';
        
        if (!empty($id)) 
            $r .= ' AND subtype_id=\''.$this->con->escapeStr($id).'\'';
        if (!empty($type)) 
            $r .= ' AND type_id=\''.$this->con->escapeStr($type).'\'';
        
        $r .= ' ORDER BY type_id'; 
        if (($rs = $this->con->select($r)) !== false) {
            return $rs;
        } else {
            $this->setError('MySQL: '.$this->con->error(), 500);
            return false;
        }        
    }

    /**
     * Get an array of the subtypes. 
     * Used in the display of the subtypes.
     *
     * @param string Type of resource ('')
     * @param int Limit to the one having the given extra data set (0)
     * @return array Ready to use in the display
     */
    function getSubTypesArray($type='', $extra=0)
    {
        $subtypes = $this->getSubTypes('', $type);
        $arry_subtypes = array();

        while (!$subtypes->EOF()) {
            if (0 == $extra or $subtypes->f('subtype_extra'.$extra) == 1) {
                $arry_subtypes[$subtypes->f('subtype_name')] = $subtypes->f('subtype_id');
            } 
            $subtypes->moveNext();    
        }
        return $arry_subtypes;
    }


    
    /**
    Check if a subtype is used
    @return bool True if in use
    @param int Subtype id
    */
    function isSubTypeUsed($id)
    {
        $req = 'SELECT COUNT(*) AS total FROM '.$this->con->pfx.'resources WHERE subtype_id=\''.$this->con->escapeStr($id).'\'';
        if (($rs = $this->con->select($req)) === false) {
            $this->setError('MySQL: '.$this->con->error(), 500);
            return true; //by security set as used
        }
        if (0 == (int) $rs->f('total')) {
            return false;
        }                                
        return true;        
    }
    
    /**
    Delete a subtype
    @return bool Success or not
    @param int Subtype id
    */
    function deleteType($id)
    {
        if ($this->isSubTypeUsed($id)) {
            $this->setError(__('Impossible to delete this type as it is in use.'), 400);            
            return false;
        }
        $delReq = 'DELETE FROM '.$this->con->pfx.'subtypes WHERE subtype_id=\''.$this->con->escapeStr($id).'\'';  
        if (!$this->con->execute($delReq)) {
            $this->setError('MySQL: '.$this->con->error(), 500);
            return false;
        } 

        return true;
    }

    /**
    Save/add the type
    
    @return int Id of the subtype (false if error)
    @param int Id of the subtype (empty if adding a new)
    @param string Type id 'news' or 'articles'
    @param string Name of the subtype
    @param string Template file
    @param int Time for the cache
    @param string Extra information 1
    @param string Extra information 2
    @param string Website id (''), use the current website id from the user object if empty
    */
    function saveType($id, $type_id, $name, $template, $cachetime, $extra1, $extra2, $website='')
    {
        if (empty($name)) {
            $this->setError(__('You must provide a name.'), 400);
        }
        if (empty($template)) {
            $this->setError(__('You must provide a template.'), 400);
        }
        if (!preg_match('/^[-]{0,1}[0-9]+$/', $cachetime)) {
            $this->setError(__('The cachetime must be an integer.'), 400);
        }
        // if errors we go out.
        if (false !== $this->error()) {
            return false;
        }

        $new = false;
        if (empty($id)) {
            // get a new id
            $new = true;
            $req = 'INSERT INTO ';
        } else {
            $req = 'UPDATE ';
        }
	if (empty($website)) $website = $this->user->website;

	//            subtype_id=\''.$this->con->escapeStr($id).'\',
	$req .= $this->con->pfx.'subtypes SET
            type_id=\''.$this->con->escapeStr($type_id).'\',
            website_id=\''.$this->con->escapeStr($website).'\',
            subtype_name=\''.$this->con->escapeStr($name).'\',
            subtype_template=\''.$this->con->escapeStr($template).'\',
            subtype_cachetime=\''.$this->con->escapeStr($cachetime).'\',
            subtype_extra1=\''.$this->con->escapeStr($extra1).'\',
            subtype_extra2=\''.$this->con->escapeStr($extra2).'\'';

	if (!$new) {
	    $req .= ' WHERE subtype_id=\''.$this->con->escapeStr($id).'\'';
	}

	if (!$this->con->execute($req)) {
	    $this->setError('MySQL: '.$this->con->error(), 500);
	    return false;
	}
	if (empty($id)) $id = $this->con->getLastID();
	$this->triggerMassUpdate();

	return $id;
    }

        

    function saveUser($id, $username, $password, $realname, $email, $pubemail, $authwebs = null,$group=0, $path_media ='', $lang='')
    {
    	global $_PX_website_config;
    	
        if ($id == 1 && $this->user->f('user_id') != 1) {
            $this->setError(__('Error: You do not have the rights to modify this user.'), 400);
            return false;
        }
        if (preg_match('/[^A-Za-z0-9]/', $username)) {
            $this->setError(__('Error: The login is not valid, only letters and digits allowed.'), 400);
            return false;
        }
        // get the user with the same username if available
        if (false === ($user = $this->getUserById($username))) {
            return false;
        }

        // add a user check
        if (empty($id)) {
            if ($user->nbRow() > 0) {
                $this->setError(__('Error: This login is already used, please use another one.'), 400);
                return false;
            }
            if (strlen($password) == 0) {
                $this->setError(__('Error: You need to give a password.'), 400);
                return false;
            }
        } else {
            if ($user->nbRow() > 0 && $user->f('user_id') != $id) {
                $this->setError(__('Error: This login is already used, please use another one.'), 400);
                return false;
            }
        }
        if (strlen($realname) == 0) {
            $this->setError(__('Error: You need to give a name.'), 400);
            return false;
        }
        // formattage du champ Path_media
        if ($path_media !='') {
	        $path_media = str_replace('\\','/',$path_media);
	        if (substr($path_media,0,1) == '/' ) {
	        	$path_media = substr($path_media,1);
	        }
	        if (substr($path_media,strlen($path_media))=='/') {
	        	$path_media = substr($path_media,0,strlen($path_media)-1);
	        }
	        //$path_media = str_replace('/','_',$path_media);
	        
	        // be sure the folder exist
	        if (!files::createfolder($_PX_website_config['xmedia_root'].'/'.$path_media)) {
	        	$this->setError(__('Error: media folder is not correct.'. $_PX_website_config['xmedia_root'].'/'.$path_media),400);
	        	return false;
	        }
        }
        
        if (empty($id)) {
            $insReq = 'INSERT INTO ';
        } else {
            $insReq = 'UPDATE ';
        }
        $insReq .= $this->con->pfx.'users SET
                        user_username = \''.$this->con->escapeStr($username).'\',
                        user_realname = \''.$this->con->escapeStr($realname).'\',
                        user_email    = \''.$this->con->escapeStr($email).'\',
                        user_pubemail = \''.$this->con->escapeStr($pubemail).'\',
                        user_group = \''.$this->con->escapeStr($group).'\'';
        
        if (!empty($lang)) {
                $insReq.= ', lang_id = \''.$this->con->escapeStr($lang).'\',
                        country_id = \''.$this->con->escapeStr($lang).'\'';
        }
        if (!empty($path_media)) {
        		$insReq .= ', user_path_media = \''.$this->con->escapeStr($path_media).'\'';
        }
        if (!empty($password)) {
            $insReq .= ', user_password = \''.$this->con->escapeStr(md5($password)).'\'';
        }
        if (empty($id)) {
            $insReq .= ', user_creationdate = \''.date::stamp().'\'';
        } else {
            $insReq .= ' WHERE user_id = \''.$this->con->escapeStr($id).'\'';
        }
        if (!$this->con->execute($insReq)) {
            $this->setError('MySQL: '.$this->con->error(), 500);
            return false;
        }

        if (empty($id)) $id = $this->con->getLastID();

        if (!is_null($authwebs)) {
            // update the rights for the websites
            $delReq = 'DELETE FROM '.$this->con->pfx.'grants WHERE user_id = \''.$this->con->escapeStr($id).'\'';
            if (!$this->con->execute($delReq)) {
                $this->setError('MySQL: '.$this->con->error(), 500);
                return false;
            }
            foreach ($authwebs as $site => $score) {
                $insReq = 'INSERT INTO '.$this->con->pfx.'grants SET
                                                user_id = \''.$this->con->escapeStr($id).'\',
                                                website_id = \''.$this->con->escapeStr($site).'\',
                                                level = \''.$this->con->escapeStr($score).'\'';
                if (!$this->con->execute($insReq)) {
                    $this->setError('MySQL: '.$this->con->error(), 500);
                    return false;
                }
            }
        }
        // save default prefs if create by admin
        if (!empty($id) && auth::asLevel(PX_AUTH_ROOT)) {
        	$thisUser = new User($id);
        	$thisUser->savePref('theme', 'pmtango', '#all#');
        	$thisUser->savePref('lang',$this->con->escapeStr($lang),'#all#');
        	unset($thisUser);
        }
        // if saving user himself
        if (!empty($id) and $id == $this->user->f('user_id')) {
            $this->user->load($id);
            $this->user->synchronize(PX_USER_SYNCHRO_TO_SESSION);
        }
        return $id;
    }
	
    function saveUserCats($user_id=0, $category=0)  {
    	// on supprime d'abord l'enregistrement (ou cas ou il existe)
    	$sql='DELETE FROM '.$this->con->pfx.'usercats WHERE user_id='. $user_id .'
    			AND category_id='. $category;   
    	$this->con->execute($sql);
    	// ensuite on rajoute l'enregistrement
    	$sql_site='SELECT website_id FROM '.$this->con->pfx.'categories WHERE category_id='. $category;
    	
    	$sql='INSERT INTO '.$this->con->pfx.'usercats SET 
    			user_id='. $user_id .',
    			website_id= (' . $sql_site .'),
    			category_id='. $category;
    	
    	$rep=$this->con->execute($sql);
    	return $rep;
    }
    
    function saveGroup($group_id=0, $group_name='') {
    	if ($group_id!=0) {
    		$sql = 'UPDATE '.$this->con->pfx."usergroups SET group_name='".$group_name."' WHERE group_id=".$group_id;    		
    	} else {
    		$sql = 'INSERT INTO '.$this->con->pfx."usergroups SET group_name='".$this->con->escapeStr($group_name)."' ";
    	}    	
    	$rep=$this->con->execute($sql);
    	//echo $sql;
    	return $rep;
    }

    function saveUserCatsAllAdmin($category=0)  {
    	
    	$sqlAdmin = 'SELECT * FROM '.$this->con->pfx.'grants WHERE level=9';
    	$admin = new Recordset();
    	//if ( (
    	$admin = $this->con->select($sqlAdmin); 
    	while (!$admin->EOF()) {
    		$sql_site='SELECT website_id FROM '.$this->con->pfx.'categories WHERE category_id='. $category;
		   	$sql='INSERT INTO '.$this->con->pfx.'usercats SET 
		   			user_id='. $admin->f('user_id') .',
		   			website_id = (' . $sql_site .'),
		   			category_id='. $category;
     		$this->con->execute($sql);  			
    		$admin->MoveNext();
    	}
    	
    }

    function saveUserCatsFromParent($parentid=0,$category=0)  {
    	 
    	$sqlUsers = 'SELECT * FROM '.$this->con->pfx.'usercats WHERE category_id='.$parentid;
    	$users = new Recordset();
    	$users = $this->con->select($sqlUsers);
    	while (!$users->EOF()) {
    		//$sql_site='SELECT website_id FROM '.$this->con->pfx.'categories WHERE category_id='. $category;
    		$sql='INSERT INTO '.$this->con->pfx.'usercats SET
    				user_id='. $users->f('user_id') .',
    				website_id = (' . $users->f('website_id') .'),
    				category_id='. $category;
    		$this->con->execute($sql);
    		$users->MoveNext();
    	}
    	 
    }
    
    
    function delUserCats($user_id=0)  {
    	// on supprime tous les enregistrement
    	$sql='DELETE FROM '.$this->con->pfx.'usercats WHERE user_id='. $user_id;
    	return $this->con->execute($sql);
    }
    
    function delUser($id)
    {
        if (false === ($user = $this->getUserById($id))) {
        	$this->setError(__('Data does not exist!'));
            return false;
        }
        $res = $user->getListResources();
        if ($res->nbRow() > 0 || $id == 1) {
            $this->setError(__('Error: This user cannot be deleted.'), 400);
            return false;
        }
        $delReq = 'DELETE FROM '.$this->con->pfx.'grants WHERE user_id = \''.$this->con->escapeStr($id).'\'';
        if (!$this->con->execute($delReq)) {
            $this->setError('MySQL: '.$this->con->error(), 500);
            return false;
        }
        $delReq = 'DELETE FROM '.$this->con->pfx.'users WHERE user_id = \''.$this->con->escapeStr($id).'\'';
        if (!$this->con->execute($delReq)) {
            $this->setError('MySQL: '.$this->con->error(), 500);
            return false;
        }
        return true;

    }
    
    function delGroup($id) {
    	if (false === ($user = $this->getUserGroup($id))) {
    		$this->setError(__('Data does not exist!'));
    		return false;
    	}
    	$delReq = 'DELETE FROM '.$this->con->pfx.'usergroups WHERE group_id = '.$id;
    	if (!$this->con->execute($delReq)) {
    		$this->setError('MySQL: '.$this->con->error(), 500);
    		return false;
    	}
    	return true;
    }


    /**
     * Save a site or create a new one. 
     *
     * If $id is empty, a new site is created and the
     * log of the creation is set in &$log_new_site. 
     * The log is pure HTML ready for display.
     */
    function saveSite($id, $name, $description, $sitelang, $website_address, $website_path, $xmedia_name, $support_comments, $status_comments, $value_comment, &$log_new_site, $force_new_id='',$image_new_site='')
    {
        include_once dirname(__FILE__).'/../extinc/class.configfile.php';
        include_once dirname(__FILE__).'/class.checklist.php';
        include_once dirname(__FILE__).'/class.files.php';
        include_once dirname(__FILE__).'/lib.auth.php';
        global $_PX_config;

        $update = (empty($id)) ? false : true;
        $xmedia_path = '';
        if (!empty($website_path) && !empty($xmedia_name)) {
            $xmedia_path = files::real_path($website_path).'/'.$xmedia_name;
        }
        $parsedurl = parse_url($website_address);

        if ($update) {
            // check the data if $update
            if (0 == strlen(trim($id))) {
                $this->setError(__('Error: Internal error, please report your actions leading to this error message.'),500);
            }
            if (preg_match('/[^A-Za-z0-9]/', $id)) {
                $this->setError(sprintf(__('Error: The id of the website "%s" can only contain letters and numbers.'), htmlspecialchars($id)),500);
            }
        }
        
        files::createfolder($xmedia_path);
        files::createfolder($website_path);

        if (empty($xmedia_path) or !file_exists($xmedia_path)) {
            $this->setError(sprintf(__('Error: File and image folder %s not available. Check the name you gave.'), $xmedia_path), 400);
        }
        if (!empty($xmedia_path) && file_exists($xmedia_path) && !is_writable($xmedia_path)) {
            $this->setError(sprintf(__('Error: No write access to the file and image folder %s. Check the name you gave.'), $xmedia_path), 400);
        }
        if (empty($website_path) or !file_exists($website_path)) {
            $this->setError(sprintf(__('Error: The document root folder %s is not available.'), $website_path), 400);
        }
        if (!empty($website_path) && file_exists($website_path) && !is_writable($website_path)) {
            $this->setError(sprintf(__('Error: No write access to the root folder %s.'), $website_path), 400);
        }
        if (2 != strlen(trim($sitelang))) {
            $sitelang = 'en';
        }
        if (0 == strlen(trim($website_address))) {
            $this->setError(__('Error: You must give a website address.'),400);
        }
        if (0 == strlen(trim($description))) {
            $this->setError(__('Error: You must give a description.'),400);
        }
        if ((0 != strlen(trim($website_address))) && (!is_array($parsedurl)
                                                      or empty($parsedurl['scheme'])
                                                      or !preg_match('/(http|https)/', $parsedurl['scheme'])
                                                      or empty($parsedurl['host'])
                                                      )) {
            $this->setError(__('Error: You must provide a valid website address.'),400);
        }
        if (0 == strlen(trim($name))) {
            $this->setError(__('Error: You must give a name.'),400);
        }

        // if errors, break
        if (false !== $this->error(true, false)) {
            return false;
        }

        // Generate all the needed information for at least the update
        $xmedia_path = files::real_path($xmedia_path);
        $website_path = files::real_path($website_path);
        $reurl = (!empty($parsedurl['path'])) ? $parsedurl['path'] : '';

        $reurl        = preg_replace('#(/)+$#', '', $reurl);
        $website_path = preg_replace('#(/)+$#', '', $website_path);
        $xmedia_reurl = preg_replace('#(/)+$#', '', $reurl.'/'.$xmedia_name);

        $domain = trim($parsedurl['host']);
        $secure = (strtolower($parsedurl['scheme']) == 'https');

        // get the website with this id
        if (empty($id)) {
            if (!empty($force_new_id)) {
                //to be able to force the first site with the "default" id.
                $id = $force_new_id; 
            } else {
                //new id from the address (without http but the s)
                $id = substr(preg_replace('/[^A-Za-z0-9]/', '', $website_address), 4); 
            }
        }
        $site = $this->getSites($id);
        if ($update) {
            if (!file_exists(dirname(__FILE__).'/../conf/configweb_'.$id.'.php') || !is_writable(dirname(__FILE__).'/../conf/configweb_'.$id.'.php')) {
                $this->setError(sprintf(__('Error: The configuration file %s is not writeable.'), files::real_path(dirname(__FILE__).'/../conf/').'/configweb_'.$id.'.php'), 500);
                return false;
            }
            if ($site->nbRow() == 0) {
                $this->setError(__('This site is not available.') , 400);
                return false;
            }

        } else {
            // check if this website already exists
            if ($site->nbRow() >= 1) {
                $this->setError(__('Error: Id already used.') , 400);
                return false;
            }
            // need to create a new file
            // copy paste the config_default.php
            if (!is_writable(dirname(__FILE__).'/../conf/')) {
                $this->setError(sprintf(__('Error: The configuration folder %s is not writeable.'),
                                         files::real_path(dirname(__FILE__).'/../conf/')), 500);
                return false;
            }
            $source_file      = dirname(__FILE__).'/../conf/configweb_default.copy.php';
            $destination_file = dirname(__FILE__).'/../conf/configweb_'.$id.'.php';
            if (file_exists($destination_file)) {
                @unlink ($destination_file);
            }
            if (!copy($source_file, $destination_file)) {
                $this->setError(__('Error: Impossible to create the configuration file.') , 500);
                return false;
            }
            @chmod($destination_file, 0666);

        }
        // open file for edition of the data
        $cfg = new configfile(dirname(__FILE__).'/../conf/configweb_'.$id.'.php');
        $cfg->prefix = '_PX_website_config';
        $cfg->editVar('website_id',    (string) $id);
        $cfg->editVar('xmedia_root',   (string) $xmedia_path);
        $cfg->editVar('domain',        (string) $domain);
        $cfg->editVar('rel_url',       (string) $reurl);
        $cfg->editVar('rel_url_files', (string) $xmedia_reurl);
        $cfg->editVar('secure',        (bool)   $secure);
        $cfg->editVar('lang',          (string) $sitelang);
        $cfg->editVar('comment_support', (int) $support_comments);
        $cfg->editVar('comment_default_status', (int) $status_comments);
        $cfg->editVar('comment_default_value', (int) $value_comment);
        if (!$cfg->saveFile()) {
            $this->setError(__('Error: Impossible to create the configuration file.') , 500);
            return false;
        }
        // no update or add in the db
        if ($update) {
            $insReq = 'UPDATE '.$this->con->pfx.'websites SET ';
        } else {
            $insReq = 'INSERT INTO '.$this->con->pfx.'websites SET
                                          website_id =\''.$this->con->escapeStr($id).'\',
                                          website_startdate = \''.date::stamp().'\', ';
        }
        $securestring = $secure ? 's' : '';
        $insReq .= 'website_name   = \''.$this->con->escapeStr($name).'\', ';
        $insReq .= 'website_url    = \''.$this->con->escapeStr('http'.$securestring.'://'.$domain.$reurl).'\', ';
        $insReq .= 'website_reurl  = \''.$this->con->escapeStr($reurl).'\', ';
        $insReq .= 'website_path   = \''.$this->con->escapeStr('').'\', ';
        $insReq .= 'website_xmedia_reurl   = \''.$this->con->escapeStr($xmedia_reurl).'\', ';
        $insReq .= 'website_xmedia_path   = \''.$this->con->escapeStr($xmedia_path).'\', ';
        $insReq .= 'website_description  = \''.$this->con->escapeStr($description).'\' ';
        //$insReq .= 'website_img = \''.$this->con->escapeStr($image_new_site).'\' ';
        if ($update) {
            $insReq .= 'WHERE website_id =\''.$this->con->escapeStr($id).'\'';
        }

        if (!$this->con->execute($insReq)) {
            $this->setError('MySQL: '.$this->con->error(), 500);
            return false;
        }


        if (!$update) {
            // As this user adds the site, give him root access to it
            $insReq = 'INSERT INTO '.$this->con->pfx.'grants SET
                    website_id =\''.$this->con->escapeStr($id).'\',
                    user_id = \''.$this->con->escapeStr($this->user->f('user_id')).'\',
                    level = \''.PX_AUTH_ADMIN.'\'';

            if (!$this->con->execute($insReq)) {
                $this->setError('MySQL: '.$this->con->error(), 500);
                return false;
            }

            // Add the root category
            $hp_title = __('Home Page');
            $hp_desc = __('Root category, that plays the role of homepage.');
            $hp_kw = __('homepage, index, default');
            $insReq = 'INSERT INTO '.$this->con->pfx.'categories SET
                  website_id=\''.$this->con->escapeStr($id).'\',
                  category_name=\''.$this->con->escapeStr($hp_title).'\',
                  category_description=\''.$this->con->escapeStr($hp_desc).'\',
                  category_keywords=\''.$this->con->escapeStr($hp_kw).'\',
                  category_path=\'/\',
                  category_publicationdate=\''.date::stamp().'\',
                  category_creationdate=\''.date::stamp().'\',
                  category_enddate=99991231235959,
                  category_template=\'category_homepage.php\',
                  category_type=\'default\',
                  category_cachetime=86400';
            if (!$this->con->execute($insReq)) {
                $this->setError('MySQL: '.$this->con->error(), 500);
                return false;
            }
            if (false == ($catid = $this->con->getLastID())) {
                $this->setError('MySQL: '.$this->con->error(), 500);
                return false;
            }
            $updReq = 'UPDATE '.$this->con->pfx.'categories SET category_parentid=\''.$this->con->escapeStr($catid).'\'
                   WHERE category_id = \''.$this->con->escapeStr($catid).'\'';

            if (!$this->con->execute($updReq)) {
                $this->setError('MySQL: '.$this->con->error(), 500);
                return false;
            }

            // Add 4 default subtypes
            if (false === $this->saveType('', 'articles', __('Article'), 'resource_article.php', 3600, '', '', $id)) {
                return false;
            }
            if (false === $this->saveType('', 'news', __('News'), 'resource_news.php', 3600, '1', '', $id)) {
                return false;
            }
            if (false === $this->saveType('', 'events', __('Events'), 'resource_events.php', 3600, '1', '', $id)) {
            	return false;
            }
            if (false === $this->saveType('', 'rsslinks', __('Rss links'), 'resource_rsslinks.php', 3600, '1', '', $id)) {
            	return false;
            }            
            // All the database related work is done. The creation is a 
            // success, we may have error to copy the 
            // files, but they are not erros, only "warnings".
            // 1- Create the xmedia/thumb folder
            // 2- Create the xmedia/theme/default folder
            // 3- Copy folder manager/templates/default/style into 
            //    folder created in 2
            // 4- If id != 'default' copy from the 'default' document 
            //    root config.php index.php prepend.php rss.php search.php
            //    into new document root
            // 5- Edit config.php for $_PX_config['manager_path'] and to 
            //    load the good config file.
            $f = new files();
            $checklist = new checklist();
            // 1- Create the xmedia/thumb folder
            // not necessary with elfinder ...
            $checklist->addTest('thumb-folder', files::is_success($f->createfolder($xmedia_path.'/thumb', 0777)) ? 1 : 2,
                                sprintf(__('Thumbnail folder %s created successfully.'), files::real_path($xmedia_path.'/thumb')),
                                '' /* no error */,
                                sprintf(__('Unable to create the thumbnail folder %s.'), $xmedia_path.'/thumb'));
            // 2- Create the xmedia/theme/default folder
            $checklist->addTest('theme-folder', (files::is_success($f->createfolder($xmedia_path.'/theme', 0777)) && files::is_success($f->createfolder($xmedia_path.'/theme/default', 0777))) ? 1 : 2,
                                sprintf(__('Theme folder %s created successfully.'), files::real_path($xmedia_path.'/theme/default')),
                                '' /* no error */,
                                sprintf(__('Unable to create the theme folder %s.'), $xmedia_path.'/theme/default'));

            // 3- Copy folder manager/templates/default/style into 
            //    folder created in 2
            $checklist->addTest('content-theme-folder',
                                files::is_success($f->copyfolder(files::real_path(dirname(__FILE__).'/../templates/default/style'),
                                                                 files::real_path($xmedia_path.'/theme/default'))) ? 1 : 2,
                                sprintf(__('Theme files successfully copied from %s to the theme folder.'), files::real_path(dirname(__FILE__).'/../templates/default/style')),
                                '' /* no error */,
                                sprintf(__('Unable to copy the theme files from %s to the theme folder.'), files::real_path(dirname(__FILE__).'/../templates/default/style')));

            // 4- If id != 'default' copy from the 'default' document root 
            //    config.php index.php prepend.php rss.php search.php
            //    into new document root
            if ('default' != $id) {
                $checklist->addTest('config-php',
                                    files::is_success($f->copyfile(files::real_path(dirname(__FILE__).'/../../config.php'), $website_path.'/config.php')) ? 1 : 2,
                                    sprintf(__('Config file successfully copied from %s to the document root folder.'), files::real_path(dirname(__FILE__).'/../../config.php') ),
                                    '' /* no error */,
                                    sprintf(__('Unable to copy the config file from %s to the document root folder.'), files::real_path(dirname(__FILE__).'/../../config.php') ));

                $checklist->addTest('index-php',
                                    files::is_success($f->copyfile(files::real_path(dirname(__FILE__).'/../../index.php'), $website_path.'/index.php')) ? 1 : 2,
                                    sprintf(__('Index file successfully copied from %s to the document root folder.'), files::real_path(dirname(__FILE__).'/../../index.php') ),
                                    '' /* no error */,
                                    sprintf(__('Unable to copy the index file from %s to the document root folder.'), files::real_path(dirname(__FILE__).'/../../index.php') ));

                $checklist->addTest('prepend-php',
                                    files::is_success($f->copyfile(files::real_path(dirname(__FILE__).'/../../prepend.php'), $website_path.'/prepend.php')) ? 1 : 2,
                                    sprintf(__('Prepend file successfully copied from %s to the document root folder.'), files::real_path(dirname(__FILE__).'/../../prepend.php') ),
                                    '' /* no error */,
                                    sprintf(__('Unable to copy the prepend file from %s to the document root folder.'), files::real_path(dirname(__FILE__).'/../../prepend.php') ));

                $checklist->addTest('rss-php',
                                    files::is_success($f->copyfile(files::real_path(dirname(__FILE__).'/../../rss.php'), $website_path.'/rss.php')) ? 1 : 2,
                                    sprintf(__('Rss file successfully copied from %s to the document root folder.'), files::real_path(dirname(__FILE__).'/../../rss.php') ),
                                    '' /* no error */,
                                    sprintf(__('Unable to copy the rss file from %s to the document root folder.'), files::real_path(dirname(__FILE__).'/../../rss.php') ));

                $checklist->addTest('search-php',
                                    files::is_success($f->copyfile(files::real_path(dirname(__FILE__).'/../../search.php'), $website_path.'/search.php')) ? 1 : 2,
                                    sprintf(__('Search file successfully copied from %s to the document root folder.'), files::real_path(dirname(__FILE__).'/../../search.php') ),
                                    '' /* no error */,
                                    sprintf(__('Unable to copy the search file from %s to the document root folder.'), files::real_path(dirname(__FILE__).'/../../search.php') ));
                
                // 5- Edit config.php for $_PX_config['manager_path'] and to 
                //    load the good config file.
                //    open file for edition of the data
                $cfg = new configfile($website_path.'/config.php');
                $cfg->prefix = '_PX_config';
                $cfg->editVar('manager_path', (string) files::real_path($_PX_config['manager_path']));
                $edit_config_success = 1;
                if (!$cfg->saveFile()) {
                    $edit_config_success = 2;
                } else {
                    if (!file_exists($website_path.'/config.php') or !is_writable($website_path.'/config.php')) {
                        $edit_config_success = 2;
                    } else {
                        $config_file = @join('', @file($website_path.'/config.php'));
                        $config_file = preg_replace('/configweb\_([A-Za-z0-9]+)\.php/', 'configweb_'.$id.'.php', $config_file);
                        $open = @fopen($website_path.'/config.php', 'w');
                        @fwrite($open, $config_file);
                        @fclose($open);
                    }
                }
                $checklist->addTest('edit-config-php', $edit_config_success,
                                    sprintf(__('Config file %s successfully updated.'), files::real_path($website_path.'/config.php') ),
                                    '' /* no error */,
                                    sprintf(__('Unable to update the config file %s.'), files::real_path($website_path.'/config.php') ));

            } //end of if not 'default'
            $path = ('default' != $id) ? 'themes/'.$GLOBALS['_px_theme'].'/images' : '../themes/default/images';
            $log_new_site = $checklist->getHtml($path);

        }
        $this->triggerMassUpdate();
        return true;

    }

    function delSite($id)
    {
        if (preg_match('/[^A-Za-z0-9]/', $id)) {
            $this->setError(__('Error: Invalid id, it must contain only letters and digits.'),400);
            return false;
        }

        // get the website with this id
        $site = $this->getSites($id);
        if ($site->nbRow() == 0) {
            $this->setError(__('This site is not available.') , 400);
            return false;
        }
        $date = $this->getEarlierDate('m', '', '', $id);
        if (strlen($date) == 14) {
            $this->setError(__('Error: The site can only be deleted if empty.') , 400);
            return false;
        }


        if (!file_exists(dirname(__FILE__).'/../conf/configweb_'.$id.'.php')
            || !is_writable(dirname(__FILE__).'/../conf/configweb_'.$id.'.php')) {

            $this->setError(sprintf(__('Error: The configuration file %s is not writeable.'),
                                     files::real_path(dirname(__FILE__).'/../conf/').'/configweb_'.$id.'.php'), 500);
            return false;
        }


        $delReq = 'DELETE FROM '.$this->con->pfx.'websites WHERE website_id =\''.$this->con->escapeStr($id).'\'';
        if (!$this->con->execute($delReq)) {
            $this->setError('MySQL: '.$this->con->error(), 500);
            return false;
        }
        $delReq = 'DELETE FROM '.$this->con->pfx.'grants WHERE website_id =\''.$this->con->escapeStr($id).'\'';
        if (!$this->con->execute($delReq)) {
            $this->setError('MySQL: '.$this->con->error(), 500);
            return false;
        }
        $delReq = 'DELETE FROM '.$this->con->pfx.'userprefs WHERE website_id =\''.$this->con->escapeStr($id).'\'';
        if (!$this->con->execute($delReq)) {
            $this->setError('MySQL: '.$this->con->error(), 500);
            return false;
        }
        // VM delete data into tables subtypes and userprefs
        $delReq = 'DELETE FROM '.$this->con->pfx.'subtypes WHERE website_id =\''.$this->con->escapeStr($id).'\'';
        if (!$this->con->execute($delReq)) {
        	$this->setError('MySQL: '.$this->con->error(), 500);
        	return false;
        }
        $delReq = 'DELETE FROM '.$this->con->pfx.'userprefs WHERE website_id =\''.$this->con->escapeStr($id).'\'';
        if (!$this->con->execute($delReq)) {
        	$this->setError('MySQL: '.$this->con->error(), 500);
        	return false;
        }
        
        @unlink(dirname(__FILE__).'/../conf/configweb_'.$id.'.php');
        return true;
    }


    /**
     * Switch the theme of a website.
     *
     * @param string Id of the website
     * @param string New theme for the website
     * @return bool Success
     */
    function switchSiteTheme($id, $theme)
    {
        if (!auth::asLevel(PX_AUTH_ADMIN, $id)) {
            $this->setError(__('You do not have the rights to edit this website.') , 400);
            return false;
        }
        // get the website with this id
        $site = $this->getSites($id);
        if ($site->nbRow() == 0) {
            $this->setError(__('This site is not available.') , 400);
            return false;
        }
        if (preg_match('/[^A-Za-z0-9]/', $theme)) {
            $this->setError(__('The theme is invalid. It must contain only letters and digits.'),400);
            return false;
        }
        // Update the configuration file.
        include_once dirname(__FILE__).'/../extinc/class.configfile.php';
        $cfg = new configfile(dirname(__FILE__).'/../conf/configweb_'.$id.'.php');
        $cfg->prefix = '_PX_website_config';
        $cfg->editVar('theme_id', (string) $theme);

        if (!$cfg->saveFile()) {
            $this->setError(__('Impossible to save the configuration file.'), 500);
            return false;
        }
        // Copy the theme css files in the xmedia folder
        // Do not check the errors. This is only the style, the user can copy
        // by hand if needed.
        $f = new files();
        $f->createfolder($site->f('website_xmedia_path').'/theme/'.$theme, 0777);
        $f->copyfolder(files::real_path(dirname(__FILE__).'/../templates/'.$theme.'/style'), 
                       files::real_path($site->f('website_xmedia_path').'/theme/'.$theme),
                       PX_FILES_OVERWRITE_IF_NEWER);
        return true;


    }

	
    /* ====================================================================== *
     *                                                                        *
     *                        Category Management                             *
     *                                                                        *
     * ====================================================================== *
     */

    /**
     * Load a category for the current website.
     * 
     * @param &object Category object in which the category will be set
     * @param int Category id
     * @return bool Success
     */
    function loadCategory(&$cat, $id)
    {
        if (false === $cat->load($id)) {
            return false;
        }
        if ($cat->f('website_id') != $this->user->website) {
            return false;
        }
        return true;
    }


    /** 
     * Save a category.
     *
     * @param &object Category to be saved
     * @return Mixed Id of the category or false if error
     */
    function saveCategory(&$cat)
    {
        if (!auth::asLevel(PX_USER_LEVEL_ADVANCED, $cat->f('website_id'))) {
            $this->setError(__('You do not have the rights to save a category'), 400);
            return false;
        }
        if (false === $cat->commit()) {
            return false;
        } else {
        	// Send a mail to notify what's adding
        	//@TODO : Envoil mail à la création 
        	//if (PX_CONFIG_MAIL_ON_CREATE == true) 
        	//	$this->sendEmail('Ajout/Modification d\'une catégorie', 'Catégorie : '.$cat->f('category_id') , $cat->f('website_id'),PX_CONFIG_MAIL_LEVEL);
        }
        if($cat->f('has_xmedia_folder')==1){
        	if (!$this->checkMediaFolder(config::f('xmedia_root').$cat->f('category_path')) )
        		return false;
        }
        $this->triggerMassUpdate();
        return $cat->f('category_id');
    }
    
    /** 
     * Remove a category. 
     *
     * @param &object Category to be removed
     * @return bool Success
     */
    function delCategory(&$cat)
    {
        if (!auth::asLevel(PX_USER_LEVEL_ADVANCED, $cat->f('website_id'))) {
            $this->setError(__('You do not have the rights to remove a category'), 400);
            return false;
        }
        if (false === $cat->remove()) {
            $this->bulkSetError($cat->error());
            return false;
        }
        $this->triggerMassUpdate();
        return true;
    }  


    /* ====================================================================== *
     *                                                                        *
     *                        Resource Management                             *
     *                                                                        *
     * ====================================================================== *
     */

    /**
     * Check a resource.
     *
     * Check a resource, set the error of the manager from the results of
     * the check, if errors are found.
     *
     * @param &object Resource object
     * @return bool Success
     */
    function check(&$res)
    {
        if (false === $res->check()) {
            $this->bulkSetError($res->error());
            return false;
        }
        return true;
    }
    
    
    /* ====================================================================== *
     *                                                                        *
     *                         Html Validator                                 *
     *                                                                        *
     * ====================================================================== *
     */
 	 function htmlIsValid($content) {
 	 	// @TODO Ajout gestion du message de retour 	
 	 	return true;
 	 	$rep = false;
 	 	$validator = new Services_W3C_HTMLValidator();
 	 	$docType = '<!DOCTYPE html >';
 	 	$enteteDoc = '<html><head><title>Analyse Contenu</title></head><body>';
 	 	$basDoc = '</body></html>';
 	 	$content = $docType.$enteteDoc."\n".$content."\n".$basDoc;
 	 	$result = $validator->validateFragment($content); 	//@TODO utf8_encode ?
 	 	if (!$result) {
 	 		// webservice non dispo
 	 		$this->setError(__('Error : webService HTMLValidator doesn\'t work !'),400);
 	 		$this->setMessage(__('Error : webService HTMLValidator doesn\'t work !'));
 	 		// retour ok car il ne faut pas bloquer la sauvegarde
 	 		$rep = true;
 	 	} else {
 	 		if ($result->isValid()) {
 	 			// le html est valide.
 	 			$rep = true;
 	 		} else {
 	 			// html invalide
 	 			$msg = '<div>'.$content.'</div>';
 	 			$msg .= '<div><ul>';
 	 			foreach($result->errors as $error)  {
 	 				$msg .=  '<li><span>ligne '.$error->line.' col'.$error->col.'</span>&nbsp;:&nbsp;';
 	 				$msg .= '<span>';
 	 				if (!empty($error->message)) $msg .= $error->message;
 	 				$msg .= '<br/>'.$error->explanation.'</span></li>';
 	 			}
 	 			$msg .= '</ul></div>';
 	 			$this->setPopupMessage($msg);
 	 			$rep = false;
 	 		}
 	 	}
 	 	return $rep;
 	 }
    
    /* ====================================================================== *
     *                                                                        *
     *                          News Management                               *
     *                                                                        *
     * ====================================================================== *
     */
    
    /**
     * Save a news.
     *
     * @param &object News object
     * @return mixed Id of the news or false
     */
    function saveNews(&$news)
    {
        // first check the integrity of the news
        if (true !== $this->check($news)) {
            return false;
        }
        
        if (true !== $this->asRightToEdit($news)) {
            $this->setError(__('Error: You do not have the rights to edit this news.'), 400);
            return false;
        }
        if (false === $news->commit()) {
            $this->bulkSetError($news->error());
            return false;
        }
        $this->indexResource($news);
        $this->triggerMassUpdate();
        Hook::run('onNewsSave', array('news' => &$news, 'm' => &$m));
        return $news->f('resource_id');
    }

    /**
     * Remove a news from the database.
     *
     * @param &object News object
     * @return bool Success
     */
    function delNews(&$news)
    {
        if (true !== $this->asRightToEdit($news)) {
            $this->setError(__('Error: You do not have the rights to edit this news.'), 400);
            return false;
        }

        $this->indexRemove($news);

        if (false === $news->remove()) {
            $this->bulkSetError($news->error());
            return false;
        }

        $this->triggerMassUpdate();
        return true;    
    }

    

    /* ====================================================================== *
     *                                                                        *
     *                          Rss links Management                               *
     *                                                                        *
     * ====================================================================== *
     */
    
    /**
     * Save a rss link.
     *
     * @param &object Rsslink object
     * @return mixed Id of the rss link or false
     */
    function saveRsslink(&$rsslinks)
    {
        // first check the integrity of the news
        if (true !== $this->check($rsslinks)) {
            return false;
        }
        
        if (true !== $this->asRightToEdit($rsslinks)) {
            $this->setError(__('Error: You do not have the rights to edit this rss link.'), 400);
            return false;
        }
        if (false === $rsslinks->commit()) {
            $this->bulkSetError($rsslinks->error());
            return false;
        }
        $this->indexResource($rsslinks);
        $this->triggerMassUpdate();
        Hook::run('onRsslinksSave', array('rsslinks' => &$rsslinks, 'm' => &$m));
        return $rsslinks->f('resource_id');
    }

    /**
     * Remove a rss link from the database.
     *
     * @param &object Rsslinks object
     * @return bool Success
     */
    function delRsslink(&$rsslinks)
    {
        if (true !== $this->asRightToEdit($rsslinks)) {
            $this->setError(__('Error: You do not have the rights to edit this rss link.'), 400);
            return false;
        }

        $this->indexRemove($rsslinks);

        if (false === $rsslinks->remove()) {
            $this->bulkSetError($rsslinks->error());
            return false;
        }

        $this->triggerMassUpdate();
        return true;    
    }
    
    /* ====================================================================== *
     *                                                                        *
     *                          Events Management                               *
     *                                                                        *
     * ====================================================================== *
     */
    
    /**
     * Save a event.
     *
     * @param &object Events object
     * @return mixed Id of the event or false
     */
    function saveEvents(&$events)
    {
        // first check the integrity of the events
        if (true !== $this->check($events)) {
        	
            return false;
        }
        if (true !== $this->asRightToEdit($events)) {
            $this->setError(__('Error: You do not have the rights to edit this events.'), 400);
            return false;
        }
        if (false === $events->commit()) {
            $this->bulkSetError($events->error());
            
            return false;
        }
        $this->indexResource($events);
        $this->triggerMassUpdate();
        Hook::run('onEventsSave', array('events' => &$events, 'm' => &$m));
        return $events->f('resource_id');
    }

    /**
     * Remove a event from the database.
     *
     * @param &object Events object
     * @return bool Success
     */
    function delEvents(&$events)
    {
        if (true !== $this->asRightToEdit($events)) {
            $this->setError(__('Error: You do not have the rights to edit this events.'), 400);
            return false;
        }

        $this->indexRemove($events);

        if (false === $events->remove()) {
            $this->bulkSetError($events->error());
            return false;
        }

        $this->triggerMassUpdate();
        return true;    
    }
    /* ====================================================================== *
     *                                                                        *
     *                          Article Management                            *
     *                                                                        *
     * ====================================================================== *
     */



    /**
     * Save an article.
     *
     * Automatically add a new or update an old.
     *
     * @param &object Article object
     * @return mixed Id of the article if success, else false
     */
    function saveArticle(&$ar)
    {
        // first check the integrity of the article
        if (true !== $this->check($ar)) {
            return false;
        }
        if (true !== $this->asRightToEdit($ar)) {
            $this->setError(__('Error: You do not have the rights to edit this article.'), 400);
            return false;
        }
        if (false === $ar->commit()) {
            $this->bulkSetError($ar->error());
            return false;
        }
        $this->indexResource($ar);
        $this->triggerMassUpdate();
        Hook::run('onArticleSave', array('art' => &$ar, 'm' => &$m));
        return $ar->f('resource_id');
    }


    /**
     * Remove an article from the database.
     *
     * @param &object Article object
     * @return bool Success
     */
    function delArticle(&$ar)
    {
        if (true !== $this->asRightToEdit($ar)) {
            $this->setError(__('Error: You do not have the rights to edit this article.'), 400);
            return false;
        }

        $this->indexRemove($ar);

        if (false === $ar->remove()) {
            $this->bulkSetError($ar->error());
            return false;
        }

        $this->triggerMassUpdate();
        return true;    
    }


    /**
     * Check an article page.
     *
     * Check a page, set the error of the manager from the results of
     * the check, if errors are found.
     *
     * @param &object Article object
     * @return bool Success
     */
    function checkArticlePage(&$ar)
    {
        if (false === $ar->checkPage()) {
            $this->bulkSetError($ar->error());
            return false;
        }
        return true;
    }

    /**
     * Save the current page of an article.
     *
     * @param &object Article object
     * @return mixed Id of the page if success else false
     */
    function saveArticlePage(&$ar)
    {
        if (true !== $this->asRightToEdit($ar)) {
            $this->setError(__('Error: You do not have the rights to edit this article.'), 400);
            return false;
        }
        if (false === $ar->commitPage()) {
            $this->bulkSetError($ar->error());
            return false;
        }
        $this->indexResource($ar);
        $this->triggerMassUpdate();
        return $ar->pages->f('page_id');
    }

    /**
     * Delete the current page of an article.
     *
     * @param &object Article object
     * @return bool Success
     */
    function delArticlePage(&$ar)
    {
        if (true !== $this->asRightToEdit($ar)) {
            $this->setError(__('Error: You do not have the rights to edit this article.'), 400);
            return false;
        }
        if (false === $ar->removePage()) {
            $this->bulkSetError($ar->error());
            return false;
        } else {
        	$ar->setPageNumber();
        }
        $this->indexResource($ar);
        $this->triggerMassUpdate();
        return true;
    }

    /* ====================================================================== *
     *                                                                        *
     *                         Comments Management                            *
     *                                                                        *
     * ====================================================================== *
     */

    /**
     * Check if a user has the rights to edit a comment.
     *
     * A comment can be edited by a user if:
     * - The user is "owner" of the resource associated to the comment.
     * - The user is the "owner" of the comment.
     * - The user has at least an PX_USER_LEVEL_ADVANCED level.
     *
     * @param &object Comment object
     * @return bool 
     */
    function asRightToEditComment(&$ct)
    {
        
        if (auth::asLevel(PX_USER_LEVEL_ADVANCED, $ct->f('website_id'))
            || ($ct->f('comment_user_id') == $this->user->getId())
            || ($ct->f('user_id') == $this->user->getId())
            ) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Get the list of comments in the current website.
     *
     * @param int Resource id ('')
     * @param int Maximum number of comments (0)
     * @param int Status of the comments ('')
     * @return mixed Comment object or false if errors.
     */
    function getComments($resource_id='', $limit=0, $status='')
    {
        include_once dirname(__FILE__).'/class.comment.php';
        $sql = SQL::getComments($this->user->website, $resource_id, $status, 'DESC', $limit);
        if (false !== ($ct = $this->con->select($sql, 'Comment'))) {
            return $ct;
        } else {
            $this->setError('MySQL: '.$this->con->error(), 500);
            return false;
        }
    }

    /**
     * Get a comment in the current website associated to a given resource.
     *
     * @param int Comment id
     * @param int Resource id
     * @return mixed Comment object or false if errors.
     */
    function getComment($id, $resource_id)
    {
        include_once dirname(__FILE__).'/class.comment.php';
        $sql = SQL::getCommentById($id, $resource_id);
        if (false !== ($ct = $this->con->select($sql, 'Comment'))) {
            if ($ct->isEmpty()) {
                $this->setError(__('This comment is not available.'));
                return false;
            }
            return $ct;
        } else {
            $this->setError('MySQL: '.$this->con->error(), 500);
            return false;
        }
    }


    /**
     * Save a comment.
     *
     * Automatically add a new or update an old.
     *
     * @param &object Comment object
     * @return mixed Id of the comment if success, else false
     */
    function saveComment(&$ct)
    {
        if (true !== $this->check($ct)) {
            return false;
        }
        if (true !== $this->asRightToEditComment($ct)) {
            $this->setError(__('Error: You do not have the rights to edit this comment.'), 400);
            return false;
        }
        if (false === $ct->commit()) {
            $this->bulkSetError($ct->error());
            return false;
        }
        $this->triggerMassUpdate();
        return $ct->f('comment_id');
    }


    /**
     * Remove a comment from the database.
     *
     * @param &object Comment object
     * @return bool Success
     */
    function delComment(&$ct)
    {
        if (true !== $this->asRightToEditComment($ct)) {
            $this->setError(__('Error: You do not have the rights to edit this comment.'), 400);
            return false;
        }
        if (false === $ct->remove()) {
            $this->bulkSetError($ct->error());
            return false;
        }
        $this->triggerMassUpdate();
        return true;    
    }


    /* ====================================================================== *
     *                                                                        *
     *                        Help System Management                          *
     *                                                                        *
     * ====================================================================== *
     */

    /**
     * Get a help file. Returned as a string containing only the HTML content
     * with the good locale and the encoding.
     *  
     * @param string Id of the help to get
     * @param string Id of the plugin if the help is from a plugin
     * @param bool Get also the title and the id of the help
     * @return string The help
     */
    function getHelp($id, $plugin='', $getall=false)
    {
        if (preg_match('/[^a-z_\-]/i', $id) 
            or preg_match('/[^a-z_\-]/i', $plugin)) {
            return '';
        }
        $lang = $this->user->lang;

        if (empty($plugin)) {
            $file = config::f('manager_path');
        } else {
            $file = config::f('manager_path').'/tools/'.$plugin;
        }
        $file .= '/help/'.$lang.'/'.$id.'.html';

        if (false !== ($help = $this->getHelpChapter($file))) {
            if ($getall) {
                return $help;
            } else {
                return $help[2];
            }
        } else {
            return '';
        }
    }

    /**
     * Get all the help files, sorted by file name usefull to list the 
     * possible themes in the help.
     *  
     * @param string Id of the plugin if the help is from a plugin
     * @return array The help list
     */
    function getHelpChapters($plugin='')
    {
        if (strlen($plugin) > 0 && preg_match('/[^a-z_\-]/i', $plugin)) {
            return false;
        }
        $lang = $this->user->lang;

        if (empty($plugin)) {
            $helpfolder = config::f('manager_path');
        } else {
            $helpfolder = config::f('manager_path').'/tools/'.$plugin;
        }
        $helpfolder .= '/help/'.$lang.'/';
                
        include_once dirname(__FILE__).'/class.files.php';
                
        $files = array();
        files::listfiles($helpfolder, $files, '/\.html$/');
                
        sort($files);
        reset($files);
        $chapters = array();
        foreach ($files as $file) {
            $chapters[] = $this->getHelpChapter($file);
        }
        return $chapters;
    }
        
    /**
     * Get the id and the title of a help chapter from the file name and the 
     * content encoded with the output encoding.
     *  
     * @param string file name
     * @return array 0=id 1=title 2=content
     */
    function getHelpChapter($file)
    {

        if (!file_exists($file)) {
            $GLOBALS['_PX_debug_data']['help'][] = __('File not found: ').$file;
            return false;
        }
        $id = substr(basename($file), 0, -5);
        $html = file($file);
        $html[0] = '';
        $htmlfile = implode('', $html);
        $title = substr($htmlfile, strpos($htmlfile,'<title>') + strlen('<title>'));
        $title = substr($title, 0, strpos($title, '</title>'));
        $htmlfile = substr($htmlfile, strpos($htmlfile,'<body>') + strlen('<body>'));
        $htmlfile = substr($htmlfile, 0, strpos($htmlfile, '</body>'));
        return array($id, $title, $htmlfile);
    }

    /**
     * Returns the contextual help link
     * 
     * @param string chapter, correspond to the file of the help
     * @param string section the part to see in the file
     * @param string plugin ('')
     * @return string Help link
     */
    function HelpLink($chapter, $section, $plugin='')
    {
        $theme = $this->user->getTheme();
                
        if (strlen($plugin) > 0) $plugin = '&amp;p='.$plugin;
        $link = 'help.php?c='.$chapter.$plugin;
        $linkpop = $link.'&amp;mode=popup';
        $link .= '#'.$section;
        $linkpop .= '#'.$section;
        $img = 'themes/'.$theme.'/images/ico_help_small.png';
        $help = '<a title="'.__('Help').'" href="'.$link.'" onclick="popup(\''.$linkpop.'\'); return false;">'.
            '<img class="minihelp" src="'.$img.'" alt="'.__('Help').'" /></a>';
        return $help;
    }


    /**
     * Send an email to the users of the website.
     *
     * @param string Subject of the email
     * @param string Content of the email
     * @param int Website id
     * @param int Minimum level of the user to get the email PX_AUTH_ADVANCED
     */
    function sendEmail($subject, $content, $website, $level=PX_AUTH_ADVANCED) 
    {
        $to_emails = array();
        $users = $this->getUsers();
        while(!$users->EOF()) {
            if ($users->getWebsiteLevel($website) >= $level) {
                $to_emails[] = $users->f('user_email');
            }
            $users->moveNext();			
        }
        foreach ($to_emails as $to_email) {
            $email = new Plume_Mail('noreply@plume-cms.net', $to_email, 
                                    $subject);
            $email->addMessage($content, 'text/plain');
            $email->sendMail(); 
        }
    }
    
    function checkMediaFolder($path) {
    	if (!file_exists($path)) {
    		//$this->setError('création dossier '.$path);
    		if (files::createfolder($path) != PX_FILES_SUCCESS) {
    			$this->setError('Erreur de création du dossier '.$path);
    			return false;
    		} else {
    			return true;
    		}
    	}
    }
}
?>
