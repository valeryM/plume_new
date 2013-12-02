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
require_once $_PX_config['manager_path'].'/inc/class.rsslinks.php';

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

// Default value : List rss links 
//if ($_PX_config['mode_affichage_contenu']=='list' && empty($_REQUEST['op'])) $_REQUEST['op'] = 'list';
//if ( empty($_REQUEST['op'])) $_REQUEST['op'] = 'list';

/* ================================================= *
 *       Generate sub-menu                           *
 * ================================================= */

$px_submenu->addItem(__('Back to the list of resources'), 'index.php', 
                     'themes/'.$_px_theme.'/images/ico_back.png', false);

$px_submenu->addItem(__('New rss link'), 'rsslinks.php',
                     'themes/'.$_px_theme.'/images/rss_add.png', false, 
                     (!empty($_REQUEST['op'])||empty($_REQUEST['resource_id']))
                     );
$px_submenu->addItem(__('Rss links list'), 'rsslinks.php?op=list',
		'themes/'.$_px_theme.'/images/rss_edit.png', false);
/*
$px_submenu->addItem('&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', '', '', false);

$px_submenu->addItem(__('Events list'), 'events.php?op=list',
		'themes/'.$_px_theme.'/images/ico_datetime.png', false);

$px_submenu->addItem(__('News list'), 'news.php?op=list',
		'themes/'.$_px_theme.'/images/ico_news.png', false);

$px_submenu->addItem(__('Article list'), 'articles.php?op=list',
		'themes/'.$_px_theme.'/images/ico_article.png', false);

$px_submenu->addItem(__('Files or images'), 'xmedia.php',
		'themes/'.$_px_theme.'/images/ico_image.png', false);
$_site_url = $m->user->wdata[$GLOBALS['_PX_website_config']['website_id']]['website_url'].'/';
$px_submenu->addItem(__('See the site'), $_site_url,
		'themes/'.$_px_theme.'/images/ico_site.png', false);
*/
/* ========================================================================= *
 *                          Process block                                    *
 * ========================================================================= */

if (empty($_REQUEST['op'])) { 
    /* ===================================================================== *
     *                        add/edit/view a rss link                           *
     * ===================================================================== */
    $rsslinks = new Rsslinks();
    /*=================================================
     * Get current rss link and check if right to edit it
     *=================================================*/
    $is_editable = true;
    if (!empty($_REQUEST['resource_id'])) {
        if (false !== $m->loadResource($rsslinks, $_REQUEST['resource_id'])) {
            // check the rights
            if (!$m->asRightToEdit($rsslinks)) {
                $is_editable = false;
            }
            // en cas de copie,
            if ($do=='copy' && $m->asRightToCopy($rsslinks))  {
            	// prépare et sauvegarde le nouvel enregistrement
            	$rsslinks->setField('resource_id',false);
            	$rsslinks->setField('identifier','');
            	$rsslinks->setField('status', PX_RESOURCE_STATUS_INEDITION);
            	$rsslinks->cats=new Category(); // unset categories
            	$rsslinks->setField('title', __('Copy of'). ' '.$rsslinks->f('title') );
            	$is_editable=true;
            	unset($_REQUEST['resource_id']);
            
            }
            
        } else {
            $m->setError(__('Error: The requested rss link is not available.'), 
                         400);
            $is_editable = false;
        }
    } else {
        // Set default values for a new news.
        $rsslinks->setDefaults($m->user);
    }

    $px_submenu->addItem(__('See the rss link'), $rsslinks->getPath(),
                         'themes/'.$_px_theme.'/images/rss.png', 
                         false, 
                         ($rsslinks->f('status') == PX_RESOURCE_STATUS_VALIDE) &&
                         ($rsslinks->f('resource_id') > 0));


    /* ================================================= *
     *  No rss link, set default values or from preview      *
     * ================================================= */
    if ((!empty($_POST['preview']) || !empty($_POST['publish']) 
         || !empty($_POST['transform']) || !empty($_POST['addcategory'])
         || !empty($_POST['increase']) || !empty($_POST['increase_x']) 
         || !empty($_POST['decrease']) || !empty($_POST['decrease_x'])
         ) && $is_editable) {

        $rsslinks->set(form::getPostField('n_title'),
                   form::getPostField('n_subject'),
                   form::getPostField('n_content'),
                   form::getPostField('n_content_format'),
                   form::getPostField('n_status'),
        			'',
                   form::getDateAndTimeField('n_dt') /*Publication date */,
                   form::getDateAndTimeField('n_dt_e') /*End date */,
                   form::getPostField('n_noenddate'),
                   form::getPostField('n_comment_support'),
                   form::getPostField('n_subtype'));

        $rsslinks->setDetails(form::getPostField('n_titlewebsite'),
                          form::getPostField('n_linkwebsite'));
        if ($rsslinks->f('resource_id') == '') {
            $rsslinks->setField('category_id', form::getPostField('cat_id'));
        }

        if (!empty($_POST['increase']) || !empty($_POST['increase_x'])) {
            $m->user->increase('rsslinks_textarea_content');
        }
        if (!empty($_POST['decrease']) || !empty($_POST['decrease_x'])) {
            $m->user->decrease('rsslinks_textarea_content');
        }

        if (!empty($_POST['transform'])) {
            $rsslinks->setField('description', 
                            '=html'."\n"
                            .$news->getFormattedContent('description','html'));
        }
    }

    /* ================================================= *
     *               Add, edit a rss link                *
     * ================================================= */
    if (!empty($_POST['publish']) && $is_editable) {
        if ($rsslinks->f('resource_id') > 0) {
            // edit a rss link
            if (false !== ($id = $m->saveRsslink($rsslinks))) { 
                if ($rsslinks->f('status') != PX_RESOURCE_STATUS_INEDITION) { 
                    $m->setMessage(__('The rss link was successfully saved.'));
                    header('Location: rsslinks.php?op=list');
                } else {
                    header('Location: rsslinks.php?resource_id='.
                           $rsslinks->f('resource_id'));
                }
                exit;
            }                             
        } else {
            // add a rss link
            $px_rsslinks_category_id = form::getPostField('cat_id');
            if ($px_rsslinks_category_id=='allcat') $px_rsslinks_category_id=form::getPostField('location');
            $m->user->savePref('rsslinks_category_id', $px_rsslinks_category_id);
            $m->user->savePref('rsslinks_status', 
                               form::getPostField('n_status'));
            $m->user->savePref('content_format',
                               form::getPostField('n_content_format'));
            $m->user->savePref('rsslinks_subtype', 
                               form::getPostField('n_subtype'));
            $m->user->savePref('rsslinks_comment_support', 
                               form::getPostField('n_comment_support'));
            $rsslinks->setField('category_id', $px_rsslinks_category_id);
            
            if (false !== ($id = $m->saveRsslink($rsslinks))) {
                $m->setMessage(__('The rss link was successfully saved.'));
                if ($rsslinks->f('status') != PX_RESOURCE_STATUS_INEDITION) {
                    header('Location: rsslinks.php?op=list');
                } else {
                    header('Location: rsslinks.php?resource_id='.$id);
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
        && strlen($rsslinks->f('resource_id')) 
        && $is_editable) {

        $px_rsslinks_category_id = form::getField('cat_id');
        if ($px_rsslinks_category_id=='allcat') $px_rsslinks_category_id=form::getPostField('location');
        $m->user->savePref('rsslinks_category_id', $px_rsslinks_category_id);

        if ('main' == form::getField('addcategory')) {
            $type = PX_RESOURCE_CATEGORY_MAIN;
        } else {
            $type = PX_RESOURCE_CATEGORY_OTHER;
        } 
        
        if (false !== $m->addResourceInCategory($rsslinks, $px_rsslinks_category_id, 
                                                $type)) {
            header('Location: rsslinks.php?resource_id='.$rsslinks->f('resource_id'));
            exit;
        }
    }

    /* ================================================= *
     *              Remove from a category               *
     * ================================================= */
    if (strlen(form::getField('delcat')) 
        && strlen($rsslinks->f('resource_id')) 
        && strlen(form::getField('cat_id')) 
        && $is_editable) {

        $px_rsslinks_category_id = form::getField('cat_id');
        if (false !== $m->removeResourceFromCategory($rsslinks,
                                                     $px_rsslinks_category_id)) {
            header('Location: rsslinks.php?resource_id='.$rsslinks->f('resource_id'));
            exit;
        }
    }

    /* ================================================= *
     *               Delete the rss link                 *
     * ================================================= */
    if (strlen(form::getPostField('delete')) && $is_editable) {
        if ($m->delRsslink($rsslinks) !== false) {
            $m->setMessage(__('The rss link was successfully deleted.'));
            header('Location: rsslinks.php?op=list');
            exit;
        }
    }

    /* ================================================= *
     *                 Remove a comment                  *
     * ================================================= */
    if (strlen(form::getField('delete-comment')) 
        && strlen($rsslinks->f('resource_id')) 
        && strlen(form::getField('comment_id')) 
        && $is_editable) {

        $id = form::getField('comment_id');
        if (false !== ($ct = $m->getComment($id, $rsslinks->f('resource_id')))) {
            if (false !== $m->delComment($ct)) {
                $m->setMessage(__('The comment was successfully deleted.'));
                header('Location: rsslinks.php?resource_id='.$rsslinks->f('resource_id'));
                exit;
            }
        }
    }

    /* ================================================= *
     *                 Add/Edit a comment                *
     * ================================================= */
    if (strlen(form::getField('publish-comment')) 
        && strlen($rsslinks->f('resource_id')) 
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
                     $rsslinks->f('resource_id'), 
                     ($ct->f('comment_ip')) ? $ct->f('comment_ip') : $_SERVER["REMOTE_ADDR"],
                     form::getField('c_status'),
                     PX_COMMENT_TYPE_NORMAL,
                     ($ct->f('comment_id')) ? $ct->f('comment_user_id') : $m->user->getId()
            		 );
            if (false !== $m->saveComment($ct)) {
                $m->setMessage(__('The comment was successfully saved.'));
                header('Location: rsslinks.php?resource_id='.$rsslinks->f('resource_id'));
                exit;
            }
        }
    }
    

    /* ================================================= *
     * Get the categories for the rss link, array of status  *
     * array for the dates                               *
     * ================================================= */
    if ($is_editable) {
        $arry_cat = $m->getArrayCategories();
        $arry_subtypes = $m->getSubTypesArray('rsslinks');
        $arry_subtypes_extra = $m->getSubTypesArray('rsslinks', 1);        
    }


} // add/edit/view a rss link



/* ========================================================================= *
 *                            List the rss link                                  *
 * ========================================================================= */
if (!empty($_REQUEST['op']) && 'list' == $_REQUEST['op']) {

    //Get the category id and save it
    $cat_id = (!empty($_GET['cat_id'])) ? $_GET['cat_id'] : $m->user->getPref('rsslinks_category_id'); //$m->user->getPref('list_news_cat_id');

    //$m->user->savePref('list_news_cat_id', $cat_id, $_SESSION['website_id'], true);
    $m->user->savePref('rsslinks_category_id', $cat_id, $_SESSION['website_id'], true);
    if ($cat_id == 'allcat') $cat_id = '';
    
    //Get the search query
    $px_q = (!empty($_GET['q'])) ? $_GET['q'] : '';
    
    //Get available months and selected
    list($first, $last, $arry_months) = $m->getArrayMonths('rsslinks', $cat_id);
    $px_m_s = $m->user->getPref('list_rsslinks_month');
    $px_m = (!empty($_GET['m'])) ? $_GET['m'] : ((!empty($px_m_s)) ? $px_m_s : $last);
    $m->user->savePref('list_rsslinks_month', $px_m, $_SESSION['website_id'], true);
    
    if ($px_m == 'alldate') {
        //$px_m = $first;
        $px_m = '';
        $px_end   =  date::stamp(0, 1 /*1 month after now*/, 0);
    } else {
        $px_end   =  date::stamp(0, 1 /*1 month after $px_m */, 0, date::unix($px_m));
    }
    
    if (empty($px_q)) {
        $res = $m->getResources(''/* All users */, '' /* All status */, $cat_id, 'rsslinks', $px_m /*Date start */, $px_end /*Date end */, '' /* no limit */, 'DESC', false /* online */, true /*by path */);
        //get again as possibly modified because of the 'alldate' case
        $px_m = $m->user->getPref('list_rsslinks_month');
    } else {
        $res = $m->searchResources($px_q, false /*Not only the online resources */, 'rsslinks', 'ResourceSet' /*Class used for the results */);
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

$px_title = __('Rsslinks');
include dirname(__FILE__).'/mtemplates/_top.php';

echo '<h1 id="title_rsslink">'.__('Rsslinks').'</h1>'."\n\n";

if (empty($_REQUEST['op'])) {
    include dirname(__FILE__).'/mtemplates/rsslinks-edit.php';
    $px_resource_id = $rsslinks->f('resource_id');
    // Include list of comments
    // Include add/edit comment
} elseif (!empty($_REQUEST['op']) && 'list' == $_REQUEST['op']) { 
    include dirname(__FILE__).'/mtemplates/rsslinks-list.php';
}

include dirname(__FILE__).'/mtemplates/_bottom.php';

?>