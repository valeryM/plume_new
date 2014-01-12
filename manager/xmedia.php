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
require_once dirname(__FILE__).'/prepend.php';
auth::checkAuth(PX_AUTH_NORMAL);

$m = new Manager();
$_px_theme = $m->user->getTheme();
$m->user->load();

if (false === config::loadWebsite($_SESSION['website_id'])) {
	$m->setError(sprintf(__('Error: Configuration file of the website (<strong>%s</strong>) not available. Please check your installation.'), $_SESSION['website_id']), 500);
}

/* ================================================= *
 *       Generate sub-menu                           *
* ================================================= */
$px_submenu->addItem(__('Back to the list of resources'), 'index.php',
		'themes/'.$_px_theme.'/images/ico_back.png', false);
/*
$px_submenu->addItem(__('Files or images'), 'xmedia.php',
		'themes/'.$_px_theme.'/images/ico_image.png', false);
*/

$px_submenu->addItem(__('News list'), 'news.php?op=list',
		'themes/'.$_px_theme.'/images/ico_news.png', false);

$px_submenu->addItem(__('Article list'), 'articles.php?op=list',
		'themes/'.$_px_theme.'/images/ico_article.png', false);

$px_submenu->addItem(__('Events list'), 'events.php?op=list',
		'themes/'.$_px_theme.'/images/ico_datetime.png', false);

$px_submenu->addItem(__('Rss links list'), 'rsslinks.php?op=list',
		'themes/'.$_px_theme.'/images/rss_edit.png', false);
/*
$px_submenu->addItem(__('List the types'), 'subtypes.php',
		'themes/'.$_px_theme.'/images/ico_subtype.png', false);
*/
$_site_url = $m->user->wdata[$GLOBALS['_PX_website_config']['website_id']]['website_url'].'/';
$px_submenu->addItem(__('See the site'), $_site_url,
		'themes/'.$_px_theme.'/images/ico_site.png', false);


/**
 * for the moment the logic is here, completely based on the work done in
 * dotclear with extension to almost all the kind of files, but will need
 * to move it into classes at some point.
 */
//$px_gd_version = gd_version();
$mode = (!empty($_REQUEST['mode'])) ? htmlspecialchars($_REQUEST['mode']) : '';
//$env  = (!empty($_GET['env'])) ? $_GET['env'] : 1;
$env = (!empty($_GET['env'])) ? intval($_GET['env']) : 1;

// CKEDITOR : Variables passées depuis une boite de dialogue / Explorer le serveur
// Required: anonymous function number as explained above.
//[CKEditor],[CKEditorFuncNum],[langCode]
$Editor = (!empty($_REQUEST['CKEditor'])) ? $_REQUEST['CKEditor'] : '' ;
$funcNum = (!empty($_REQUEST['CKEditorFuncNum'])) ? $_REQUEST['CKEditorFuncNum'] : '' ;
$langCode = (!empty($_REQUEST['langCode'])) ? $_REQUEST['langCode'] : '' ;
$vars ='';
if ($mode!='') $vars.= '&amp;mode='.$mode;
if ($Editor!='') $vars .= '&amp;CKEditor='.$Editor;
if ($funcNum!='') $vars .= '&amp;CKEditorFuncNum='.$funcNum;
if ($langCode !='') $vars .= '&amp;langCode='.$langCode;

$is_writable = false;
$is_dir = true;

if (true === $m->error()) {
	die();
	exit;
}

/*=============================================================================
 Display block
 =============================================================================*/

/*=================================================
 Set title of the page, and load common top page
 =================================================*/

$px_title =  __('Files and images');

if ($mode == 'popup') {
	include dirname(__FILE__).'/mtemplates/_pop_top_jq.php';
} else {
	// sub menu
	//$px_submenu->addItem( __('Back to the list of resources'),'index.php','themes/'.$_px_theme.'/images/ico_back.png',false);

	include dirname(__FILE__).'/mtemplates/_top_jq.php';
}

echo '<h1 id="title_files">'. __('Files and images')."</h1>\n\n";

Hook::run('onShowDirectoryContentManager', array('m' => &$m));

if ($mode == 'popup') {
	include dirname(__FILE__).'/mtemplates/_pop_bottom.php';
} else {
	include dirname(__FILE__).'/mtemplates/_bottom.php';
}
?>