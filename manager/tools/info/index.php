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

include_once dirname(__FILE__).'/../../extinc/lib.image.php';
include_once dirname(__FILE__).'/lib.dir.php';

$m->l10n->loadPlugin($m->user->lang, 'info');

if (!$is_user_root):
    $m->setError( __('You do not have the rights to access this plugin.'));
else:    

if (!empty($_GET['cleancache']) && !empty($_GET['id'])) {
    // try to clean the cache
    $siteid = $_GET['id'];
    if (!empty($m->user->wdata[$siteid]['website_name'])) {
        // ok the site exists
        recursiveDelete($_PX_config['manager_path'].'/cache/'.$siteid.'/');
        clearstatcache();
        @mkdir($_PX_config['manager_path'].'/cache/'.$siteid.'/', 0777);
        @touch($_PX_config['manager_path'].'/cache/'.$siteid.'/MASS_UPDATE', time());
        $msg =  __('The cache of the website has been reset.');
		header('Location: tools.php?p=info&msg='.urlencode($msg));
		exit; 
    } else {
        $m->setError( __('Error&nbsp;: Website not available.'), 400);
    }                
}

?>
<h1><?php  echo __('Information'); ?></h1>

<h2><?php  echo __('General information'); ?></h2>

<?php


// Version 
if (file_exists(dirname(__FILE__).'/../../VERSION')) {
	$px_version = trim(implode('',file(dirname(__FILE__).'/../../VERSION')));
	
	echo '<p>'.sprintf( __('You are using PLUME CMS version <strong>%s</strong>.'), $px_version).'</p>';
}

$con =& pxDBConnect();
$rs = $con->select('SHOW TABLE STATUS FROM `'.$_PX_config['db']['db_database'].'`'); 

echo '<p>'. __('The PLUME CMS tables in your database are:').'</p>';
echo '<table class="clean-table">';
echo '<tr><th>'. __('Name').'</th><th>'. __('Records').'</th><th>'. __('Size').'</th></tr>';
while (!$rs->EOF())
{
	if (preg_match('#'.$con->pfx.'#',$rs->f('name'))) {
		echo '<tr>';
		echo '<td>'.$rs->f('name').'</td>';
		echo '<td>'.$rs->f('rows').'</td>';
		echo '<td>'.prettySize($rs->f('Data_length')+$rs->f('Index_length')).'</td>';
		echo '</tr>';
	}
	
	$rs->moveNext();
}
echo '</table>';
?>

<h3><?php  echo __('Cache information'); ?></h3>

<?php
echo '<table class="clean-table">';
echo '<tr><th>'. __('Website').'</th><th>'. __('Cache size').'</th><th>'. __('Reset the cache').'</th></tr>';
reset($m->user->webs);
foreach ($m->user->webs as $site => $score) {
    $size = dirSize($_PX_config['manager_path'].'/cache/'.$site.'/');
    if ($size) {
        $link = sprintf('<a href="tools.php?p=info&amp;cleancache=1&amp;id='.$site.'">%s</a>', __('Reset'));
    } else {
        $link = '&nbsp;';
    }         
    echo '<tr>';
    echo '<td>'.$m->user->wdata[$site]['website_name'].'</td>';
    echo '<td>'.prettySize($size).'</td>';
    echo '<td>'.$link.'</td>';
    echo '</tr>';
}
echo '</table>';

?>

<p><?php  echo __('If you are making modifications to your templates, you should clean the cache before testing them.'); ?></p>

<h3><?php  echo __('File information'); ?></h3>

<?php $img_check = '<img src="themes/'.$_px_theme.'/images/check_%s.png" alt="" />'; ?>

<?php

if (is_writable($_PX_config['manager_path'].'/cache')) {
	echo '<p>'.sprintf($img_check,'on').' '.sprintf( __('The system has write access on the folder %s.'),cleanDirname($_PX_config['manager_path'].'/cache')).'</p>';
} else {
	echo '<p>'.sprintf($img_check,'off').' '.sprintf( __('The system has no write access on the folder %s.'),cleanDirname($_PX_config['manager_path'].'/cache')).'</p>';
}

if (is_writable($_PX_config['manager_path'].'/conf')) {
	echo '<p>'.sprintf($img_check,'on').' '.sprintf( __('The system has write access on the folder %s.'),cleanDirname($_PX_config['manager_path'].'/conf')).'</p>';
} else {
	echo '<p>'.sprintf($img_check,'off').' '.sprintf( __('The system has no write access on the folder %s.'),cleanDirname($_PX_config['manager_path'].'/conf')).'</p>';
}

reset($m->user->webs);
foreach ($m->user->webs as $site => $score) {
    if (is_writable($_PX_config['manager_path'].'/conf/configweb_'.$site.'.php')) {
	    echo '<p>'.sprintf($img_check,'on').' '.sprintf( __('The system has write access on the file %s.'),cleanDirname($_PX_config['manager_path'].'/conf/configweb_'.$site.'.php')).'</p>';
    } else {
	    echo '<p>'.sprintf($img_check,'off').' '.sprintf( __('The system has no write access on the file %s.'),cleanDirname($_PX_config['manager_path'].'/conf/configweb_'.$site.'.php')).'</p>';
    }
    if (is_writable($m->user->wdata[$site]['website_xmedia_path'])) {
	    echo '<p>'.sprintf($img_check,'on').' '.sprintf( __('The system has write access on the folder %s.'),cleanDirname($m->user->wdata[$site]['website_xmedia_path'])).'</p>';
    } else {
	    echo '<p>'.sprintf($img_check,'off').' '.sprintf( __('The system has no write access on the folder %s.'),cleanDirname($m->user->wdata[$site]['website_xmedia_path'])).'</p>';
    }
    if (is_writable($m->user->wdata[$site]['website_xmedia_path'].'/thumb')) {
	    echo '<p>'.sprintf($img_check,'on').' '.sprintf( __('The system has write access on the folder %s.'),cleanDirname($m->user->wdata[$site]['website_xmedia_path'].'/thumb')).'</p>';
    } else {
	    echo '<p>'.sprintf($img_check,'off').' '.sprintf( __('The system has no write access on the folder %s.'),cleanDirname($m->user->wdata[$site]['website_xmedia_path'].'/thumb')).'</p>';
    }
}

?>


<h3><?php  echo __('Server information'); ?></h3>

<p><?php echo sprintf( __('Your PHP version is <strong>%s</strong>.'),phpversion()); ?></p>

<?php
if (($rs = $con->select('SELECT VERSION() AS version')) !== false) {
	$mysql_version = preg_replace('/-log$/','',$rs->f(0));
	echo '<p>'.sprintf( __('Your MySQL version is <strong>%s</strong>.'),$mysql_version).'</p>';
	
}

if (!empty($_SERVER['SERVER_SOFTWARE'])) {
	echo '<p>'.sprintf( __('Your webserver is <strong>%s</strong>.'),$_SERVER['SERVER_SOFTWARE']).'</p>';
}

$gd_version = gd_version(); 

if ($gd_version) {
	echo '<p>'.sprintf( __('Your GD graphic library version is <strong>%s</strong>.'),$gd_version).'</p>';
}

endif;
?>
