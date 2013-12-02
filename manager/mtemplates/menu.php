<?php
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

if (basename($_SERVER['SCRIPT_NAME']) == 'menu.php') exit;
//echo 'logo :'.$m->website->f('website_img');
// get the link for website image
if ($m->website->f('website_img') != '' && $m->website->f('website_img') != null) {
	$img_url = 'themes/'.$_px_theme.'/images/'.$m->website->f('website_img');
	$px_menu->addItem('<img src="'.$img_url.'" alt="Site" style="height:32px"/>', '','','',true,'');
}
// Main menu
$px_menu->addItem(__('Content'), 'index.php', 
		  'themes/'.$_px_theme.'/images/ico_content.png', 
		  (preg_match('/^index.php|news.php|xmedia.php|articles.php|subtypes.php$/',basename($_SERVER['PHP_SELF']))), 
		  true, __('c'));
$px_menu->addItem(__('Comments'), 'comments.php', 
		  'themes/'.$_px_theme.'/images/ico_comments.png', 
		  (basename($_SERVER['PHP_SELF']) == 'comments.php'), 
		  ($_PX_website_config['comment_support'] != 3), __('m'));
$px_menu->addItem(__('Categories'), 'categories.php', 
		  'themes/'.$_px_theme.'/images/ico_cat.png', 
		  (preg_match('/^categories/',basename($_SERVER['PHP_SELF']))),
		  auth::asLevel(PX_AUTH_ADVANCED), __('a'));
$px_menu->addItem(__('Authors'),'users.php',
		  'themes/'.$_px_theme.'/images/ico_authors.png', 
		  (preg_match('/^users/',basename($_SERVER['PHP_SELF']))), 
		  auth::asLevel(PX_AUTH_ADMIN), __('r'));
$px_menu->addItem(__('Preferences'), 'prefs.php', 
		  'themes/'.$_px_theme.'/images/ico_pref.png', 
		  (preg_match('/^prefs/',basename($_SERVER['PHP_SELF']))), 
		  true, __('f'));
$px_menu->addItem(__('Tools'), 'tools.php', 
		  'themes/'.$_px_theme.'/images/ico_tools.png', 
		  (basename($_SERVER['PHP_SELF']) == 'tools.php'), 
		  auth::asLevel(PX_AUTH_ADMIN), __('t'));
$px_menu->addItem(__('Sites'), 'sites.php', 
		  'themes/'.$_px_theme.'/images/ico_sites.png', 
		  (basename($_SERVER['PHP_SELF']) == 'sites.php'), 
		  auth::asLevel(PX_AUTH_ADMIN), __('e'));
$px_menu->addItem(__('Help'), 'help.php', 
		  'themes/'.$_px_theme.'/images/ico_help.png', 
		  (basename($_SERVER['PHP_SELF']) == 'help.php'), true, __('h'));
   
echo $px_menu->draw();


?>