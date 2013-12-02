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

require_once 'path.php';
require_once $_PX_config['manager_path'].'/prepend.php';
require_once $_PX_config['manager_path'].'/inc/class.events.php';

auth::checkAuth(PX_AUTH_NORMAL);
$is_user_admin = auth::asLevel(PX_AUTH_ADMIN);

$m = new Manager();
$_px_theme = $m->user->getTheme();

$do="";
$old_art=0;
if (!empty($_REQUEST['do'])) $do=$_REQUEST['do'];
if (!empty($_REQUEST['old_art'])) $old_art=$_REQUEST['old_art'];
if ($do=="copy" && $old_art==0)  {
	if (!empty($_REQUEST['resource_id'])) $old_art=$_REQUEST['resource_id'];
}


// Default value : List events 
//if ($_PX_config['mode_affichage_contenu']=='list' && empty($_REQUEST['op'])) $_REQUEST['op'] = 'list';
/* ================================================= *
 *       Generate sub-menu                           *
 * ================================================= */

$px_submenu->addItem(__('Back to the list of resources'), 'index.php', 
                     'themes/'.$_px_theme.'/images/ico_back.png', false);
/*
$px_submenu->addItem(__('Events list'), 'events.php?op=list', 
                     'themes/'.$_px_theme.'/images/ico_datetime.png', false);
*/
$px_submenu->addItem(__('New event'), 'events.php',
                     'themes/'.$_px_theme.'/images/ico_new.png', false, 
                     (!empty($_REQUEST['op'])||empty($_REQUEST['resource_id']))
                     );
$px_submenu->addItem(__('Events list'), 'events.php?op=list',
		'themes/'.$_px_theme.'/images/ico_datetime.png', false);

/* ========================================================================= *
 *                          Process block                                    *
 * ========================================================================= */

if (empty($_REQUEST['op'])) { 
    /* ===================================================================== *
     *                        add/edit/view a event                           *
     * ===================================================================== */
    $events = new Events();
    /*=================================================
     * Get current event and check if right to edit it
     *=================================================*/
    $is_editable = true;
    if (!empty($_REQUEST['resource_id'])) {
        if (false !== $m->loadResource($events, $_REQUEST['resource_id'])) {
            // check the rights
            if (!$m->asRightToEdit($events)) {
                $is_editable = false;
            }
            // en cas de copie,
            if ($do=='copy' && $m->asRightToCopy($events))  {
            	// prépare et sauvegarde le nouvel enregistrement
            	$events->setField('resource_id',false);
            	$events->setField('identifier','');
            	$events->setField('status', PX_RESOURCE_STATUS_INEDITION);
            	$events->cats=new Category(); // unset categories
            	$events->setField('title', __('Copy of'). ' '.$events->f('title') );
            	$is_editable=true;
            	unset($_REQUEST['resource_id']);
            
            }
            
        } else {
            $m->setError(__('Error: The requested event is not available.'), 
                         400);
            $is_editable = false;
        }
    } else {
        // Set default values for a new news.
        $events->setDefaults($m->user);
    }

    $px_submenu->addItem(__('See the event'), $events->getPath(),
                         'themes/'.$_px_theme.'/images/ico_datetime.png', 
                         false, 
                         ($events->f('status') == PX_RESOURCE_STATUS_VALIDE) &&
                         ($events->f('resource_id') > 0));


    /* ================================================= *
     *  No events, set default values or from preview      *
     * ================================================= */
    if ((!empty($_POST['preview']) || !empty($_POST['publish']) 
         || !empty($_POST['transform']) || !empty($_POST['addcategory'])
         || !empty($_POST['increase']) || !empty($_POST['increase_x']) 
         || !empty($_POST['decrease']) || !empty($_POST['decrease_x'])
         ) && $is_editable) {
        $events->set(form::getPostField('n_title'),
                   form::getPostField('n_subject'),
                   form::getPostField('n_content'),
                   form::getPostField('n_content_format'),
                   form::getPostField('n_status'),
        			form::getPostField('n_path'),
                   form::getTimeField('n_dt') /*Publication date */,
                   form::getTimeField('n_dt_e') /*End date */,
                   'x',
                   form::getPostField('n_comment_support'),
                   form::getPostField('n_subtype'));
                   //form::getPostField('filRouge'));

        $events->setDetails(form::getDateAndTimeField('n_startdate'), form::getDateAndTimeField('n_enddate'),
                          form::getPostField('n_shortcontent'),form::getPostField('n_content'),
                          form::getPostField('n_content_format'), form::getPostField('n_noenddate') );

                          
        if ($events->f('resource_id') == '') {
            $events->setField('category_id', form::getPostField('cat_id'));
        }

        if (!empty($_POST['increase']) || !empty($_POST['increase_x'])) {
            $m->user->increase('events_textarea_content');
        }
        if (!empty($_POST['decrease']) || !empty($_POST['decrease_x'])) {
            $m->user->decrease('events_textarea_content');
        }

        if (!empty($_POST['transform'])) {
            $events->setField('description', 
                            '=html'."\n"
                            .$events->getFormattedContent('n_content','html'));
         }
    }
    
    /* ================================================= *
     *              copy an event                    *
     * ================================================= */
    /*
        if (!empty($_GET['do'])) {
	    if ($is_editable && $_GET['do']=='copy' && $events->f('status') != PX_RESOURCE_STATUS_INEDITION) {
	    	
	    	if ($events->f('resource_id')>0) {
	    		// define new values to prepare recordset
	    		$events->setField('resource_id',false);
	    		$events->setField('identifier','');
	    		$events->setField('status', PX_RESOURCE_STATUS_INEDITION);
	    		$events->details->setField('resource_id',false);
	    		$events->details->setField('identifier','');
	    		$events->authors->setField('resource_id',false);
	    		$events->authors->setField('user_id',$m->user->getId());
	    		$events->cats->setField('identifier',false);
	    		$events->cats->setField('category_id',false);
	    		$events->setField('title', __('Copy of'). ' '.$events->f('title') );
	    		
	    		// run save method
	    		$events->isModified = true;
	    		if (false !== ($id = $m->saveEvents($events))) {
	    				$m->setMessage(__('The event was successfully copied.'));
	    				header('Location: events.php?resource_id='.
	    						$events->f('resource_id'));

	    		} else {
	    			$m->setMessage(__('The event could not be copied.'));
	    			header('Location: events.php?op=list');
	    		}
	    		exit;
	    	}
	    }
    }
    */

    /* ================================================= *
     *               Add, edit a event                    *
     * ================================================= */
    if (!empty($_POST['publish']) && $is_editable) {
        if ($events->f('resource_id') > 0) {
            // edit a events
            if (false !== ($id = $m->saveEvents($events))) { 
                if ($events->f('status') != PX_RESOURCE_STATUS_INEDITION) { 
                    $m->setMessage(__('The event was successfully saved.'));
                    header('Location: events.php?op=list');
                } else {
                    header('Location: events.php?resource_id='.
                           $events->f('resource_id'));
                }
                exit;
            }                             
        } else {
            // add a event
            $px_events_category_id = form::getPostField('cat_id');
            if ($px_events_category_id=='allcat') $px_events_category_id=form::getPostField('location');
            $m->user->savePref('events_category_id', $px_events_category_id);
            $m->user->savePref('events_status', 
                               form::getPostField('n_status'));
            $m->user->savePref('event_path', form::getPostField('n_path'));
            $m->user->savePref('content_format',
                               form::getPostField('n_content_format'));
            $m->user->savePref('events_subtype', 
                               form::getPostField('n_subtype'));
            $m->user->savePref('events_comment_support', 
                               form::getPostField('n_comment_support'));
            $m->user->savePref('events_shortcontent', 
                               form::getPostField('n_shortcontent'));
                               
            //$m->user->savePref('filRouge',form::getPostField('filRouge'));
                               
            $events->setField('category_id', $px_events_category_id);
            if (false !== ($id = $m->saveEvents($events))) {
                $m->setMessage(__('The event was successfully saved.'));
                if ($events->f('status') != PX_RESOURCE_STATUS_INEDITION) {
                    header('Location: events.php?op=list');
                } else {
                    header('Location: events.php?resource_id='.$id);
                }
                exit;
            }  
        }
    }

    /* ================================================= *
     *               Associate to a category             *
     * ================================================= */
    if (strlen(form::getField('addcategory')) 
        && strlen(form::getField('cat_id')) 
        && strlen($events->f('resource_id')) 
        && $is_editable) {

        $px_events_category_id = form::getField('cat_id');
        if ($px_events_category_id=='allcat') $px_events_category_id=form::getPostField('location');
        $m->user->savePref('events_category_id', $px_events_category_id);

        if ('main' == form::getField('addcategory')) {
            $type = PX_RESOURCE_CATEGORY_MAIN;
        } else {
            $type = PX_RESOURCE_CATEGORY_OTHER;
        } 
        
        if (false !== $m->addResourceInCategory($events, $px_events_category_id, 
                                                $type)) {
            header('Location: events.php?resource_id='.$events->f('resource_id'));
            exit;
        }
    }

    /* ================================================= *
     *              Remove from a category               *
     * ================================================= */
    if (strlen(form::getField('delcat')) 
        && strlen($events->f('resource_id')) 
        && strlen(form::getField('cat_id')) 
        && $is_editable) {

        $px_events_category_id = form::getField('cat_id');
        if (false !== $m->removeResourceFromCategory($events,
                                                     $px_events_category_id)) {
            header('Location: events.php?resource_id='.$events->f('resource_id'));
            exit;
        }
    }

    /* ================================================= *
     *               Delete the events                     *
     * ================================================= */
    if (strlen(form::getPostField('delete')) && $is_editable) {
        if ($m->delEvents($events) !== false) {
            $m->setMessage(__('The event was successfully deleted.'));
            header('Location: events.php?op=list');
            exit;
        }
    }

    /* ================================================= *
     *                 Remove an event                  *
     * ================================================= */
    if (strlen(form::getField('delete-comment')) 
        && strlen($events->f('resource_id')) 
        && strlen(form::getField('comment_id')) 
        && $is_editable) {

        $id = form::getField('comment_id');
        if (false !== ($ct = $m->getComment($id, $events->f('resource_id')))) {
            if (false !== $m->delComment($ct)) {
                $m->setMessage(__('The comment was successfully deleted.'));
                header('Location: events.php?resource_id='.$events->f('resource_id'));
                exit;
            }
        }
    }

    /* ================================================= *
     *                 Add/Edit an event                *
     * ================================================= */
    if (strlen(form::getField('publish-comment')) 
        && strlen($events->f('resource_id')) 
        && $is_editable) {
        $id = form::getField('comment_id');
        $ct = new Comment();
        if ((int) $id > 0) {
            if (false === $ct->load($id)) {
                $m->setError(__('The requested comment does not exist.'));
            }
        }
        if (false === $m->error()) {
            $ct->set(form::getField('c_author'), 
                     form::getField('c_email'), 
                     form::getField('c_website'), 
                     form::getField('c_content'),
                     $events->f('resource_id'), 
                     ($ct->f('comment_ip')) ? $ct->f('comment_ip') : $_SERVER["REMOTE_ADDR"],
                     form::getField('c_status'),
            		form::getField('c_path'),
                     PX_COMMENT_TYPE_NORMAL,
                     ($ct->f('comment_id')) ? $ct->f('comment_user_id') : $m->user->getId());
            if (false !== $m->saveComment($ct)) {
                $m->setMessage(__('The comment was successfully saved.'));
                header('Location: events.php?resource_id='.$events->f('resource_id'));
                exit;
            }
        }
    }
    

    /* ================================================= *
     * Get the categories for the news, array of status  *
     * array for the dates                               *
     * ================================================= */
    if ($is_editable) {
        $arry_cat = $m->getArrayCategories();
        $arry_subtypes = $m->getSubTypesArray('events');
        $arry_subtypes_extra = $m->getSubTypesArray('events', 1);        
    }


} // add/edit/view a events



/* ========================================================================= *
 *                            List the events                                  *
 * ========================================================================= */
if (!empty($_REQUEST['op']) && 'list' == $_REQUEST['op']) {

    //Get the category id and save it
    $cat_id = (!empty($_GET['cat_id'])) ? $_GET['cat_id'] : $m->user->getPref('events_category_id'); //$m->user->getPref('list_events_cat_id');
    //$m->user->savePref('list_events_cat_id', $cat_id, $_SESSION['website_id'], true);
    $m->user->savePref('events_category_id', $cat_id, $_SESSION['website_id'], true);
    if ($cat_id == 'allcat') $cat_id = '';
    
    //Get the search query
    $px_q = (!empty($_GET['q'])) ? $_GET['q'] : '';
    
    //Get available months and selected
    list($first, $last, $arry_months) = $m->getArrayMonths('events', $cat_id);
    $px_m_s = $m->user->getPref('list_events_month');
    $px_m = (!empty($_GET['m'])) ? $_GET['m'] : ((!empty($px_m_s)) ? $px_m_s : $last);
    $m->user->savePref('list_events_month', $px_m, $_SESSION['website_id'], true);
    
    if ($px_m == 'alldate') {
        //$px_m = $first;
        $px_m = '';
        $px_end   =  date::stamp(0, 1 /*1 month after now*/, 0);
    } else {
        $px_end   =  date::stamp(0, 1 /*1 month after $px_m */, 0, date::unix($px_m));
    }
	//$fltFilRouge = (!empty($_GET['fltFilRouge'])) ? $_GET['fltFilRouge'] : 0;
	//$m->user->savePref('fltFilRouge', $fltFilRouge, $_SESSION['website_id'], true);
    
    // si pas de critères de recherche : on charge toutes les ressources
    if (empty($px_q)) {
        $res = $m->getResources(''/* All users */, '' /* All status */, $cat_id, 'events', $px_m /*Date start */, $px_end /*Date end */, '' /* no limit */, 'DESC', false /* online */, true /*by path */);
        //get again as possibly modified because of the 'alldate' case        
        $px_m = $m->user->getPref('list_events_month');
    // Sinon on lance la recherche avec les critères
    } else {
        $res = $m->searchResources($px_q, false /*Not only the online resources */, 'events', 'ResourceSet' /*Class used for the results */);
        //Search is made on all the date and all the categories
        $px_m = 'alldate';
        $cat_id = 'allcat';
    }
    
    // Categories
    $arry_cat = $m->getArrayCategories(true);

}

/* ========================================================================= *
 *                              Display block                                *
 * ========================================================================= */

$px_title = __('Events');
include dirname(__FILE__).'/mtemplates/_top.php';

echo '<h1 id="title_events">'.__('Events').'</h1>'."\n\n";

if (empty($_REQUEST['op'])) {
	// Include add/edit event	
    include dirname(__FILE__).'/mtemplates/events-edit.php';
    $px_resource_id = $events->f('resource_id');
    
    // Include list of events
} elseif (!empty($_REQUEST['op']) && 'list' == $_REQUEST['op']) { 
	include dirname(__FILE__).'/mtemplates/events-list.php';
}

include dirname(__FILE__).'/mtemplates/_bottom.php';

?>