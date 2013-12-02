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
# The Initial Developer of the Original Code is
# Olivier Meunier.
# Portions created by the Initial Developer are Copyright (C) 2003
# the Initial Developer. All Rights Reserved.
#
# Contributor(s):
# - Sebastien Fievet
#
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
#
# ***** END LICENSE BLOCK ***** */

/**
 * Small note on design:
 * 
 * Both links and categories are stored in the same database table.
 * The difference is that 'label' and 'href' fields are empty for categories.
 *
 * This is quite hacky but perfectly fits a simple two level design.
 *
 */ 
$m->l10n->loadPlugin($m->user->lang, 'link');

require dirname(__FILE__).'/class.link.php';
$_REQUEST['header'] = link::insertHeader();
Hook::register('onPrintHeaderManagerPage', 'Link', 'insertHeader');

if (!isset($con)) {
	$con =& pxDBConnect();
}

$url = 'tools.php?p=link';
$icon = 'tools/link/themes/'.$_px_theme.'/icon_small.png';

$link = new link($_SESSION['website_id']);

if(!$link->isRunning()) {
	$_REQUEST['action']='install';
}

$action = !empty($_REQUEST['action']) ? $_REQUEST['action'] : NULL;
$page = !empty($_REQUEST['page']) ? $_REQUEST['page'] : NULL;
$l_zone = !empty($_REQUEST['l_zone']) ? $_REQUEST['l_zone'] : NULL;
$l_style = !empty($_REQUEST['l_style']) ? $_REQUEST['l_style'] : NULL;

$err = '';

if ($action == 'install') {
	include dirname(__FILE__).'/dbinstall.php';
}

if ($page == 'edit_link' && !empty($_REQUEST['id']))
{
	include dirname(__FILE__).'/edit_link.php';
}
elseif ($page == 'edit_cat' && !empty($_REQUEST['id']))
{
	include dirname(__FILE__).'/edit_cat.php';
}
else
{
	$l_label = $l_title = $l_href = $l_lang = $l_cible = '';
	// $l_zone = $l_style = '';
	$c_title = '';
	
	# Ajout d'un lien
	if ($action == 'add_link')
	{
		if (isset($_POST['l_label'])) $l_label = trim($_POST['l_label']);
		if (isset($_POST['l_zone'])) $l_zone = trim($_POST['l_zone']);
		if (isset($_POST['l_title'])) $l_title = trim($_POST['l_title']);
		if (isset($_POST['l_href'])) $l_href = trim($_POST['l_href']);
		$l_lang = trim($_POST['l_lang']);
		$l_cat = trim($_POST['l_cat']);
		$l_cible = trim($_POST['l_cible']);
		$l_style = trim($_POST['l_style']);
		
		if ($l_label!='' || $l_href!='')
		{
			$err = __('You must provide at least a label and an URL');
		}
		elseif ($l_zone!='') {
			$err = __('You must provide a valid area');
		}
		elseif (!$link->isURI($l_href, array('domain_check' => false, 'allowed_schemes' => $link->protocols))) {
			$err = __('You must provide a valid URL');
		}
		else
		{
			if ($link->addLink($l_zone, $l_label,$l_href,$l_cible, $l_title,$l_lang,$l_cat, $l_link) == false) {
				$err = $link->con->error();
			} else {
				header('Location: '.$url.'&l_zone='.$l_zone);
				exit;
			}
		}
	}
	# Ajout d'une catégorie
	elseif ($action == 'add_cat')
	{
		$c_title = trim($_POST['c_title']);
		$c_zone = trim($_POST['c_zone']);
		if ($c_title && $c_zone)
		{
			if ($link->addCat($c_zone,$c_title) == false) {
				$err = $link->con->error();
			} else {
				header('Location: '.$url.'&l_zone='.$l_zone);
				exit;
			}
		} else {
			$err = __('You must provide a valid area and a label');
		}
	}
	# Suppression
	elseif ($action == 'delete' && !empty($_GET['id']))
	{
		if ($link->delEntry($_GET['id']) == false) {
			$err = $link->con->error();
		} else {
			header('Location: '.$url.'&l_zone='.$l_zone.'#link');
			exit;
		}
	}
	# Classic ord
	if (isset($_POST['linkOrd']) && is_array($_POST['linkOrd']))
	{
		if ($link->ordEntries($_POST['linkOrd']) === false) {
			$err = $link->con->error();
		} else {
			header('Location: '.$url.'&l_zone='.$l_zone);
			exit;
		}
	}
	# DragNdrop
	if (!empty($_POST['dndSort']))
	{
		$linkOrd = array();
		foreach (explode(';',$_POST['dndSort']) as $k => $v) {
			$linkOrd[substr($v,3)] = $k;
		}
		
		if ($link->ordEntries($linkOrd) === false) {
			$err = $link->con->error();
		} else {
			header('Location: '.$url.'&l_zone='.$l_zone);
			exit;
		}
	}
	# Monter / descendre un lien
	elseif (($action == 'up_link' || $action == 'down_link') && !empty($_GET['id']))
	{
		$dir = ($action == 'up_link') ? '+' : '-';
		
		if ($link->ordLink($_GET['id'],$dir) == false) {
			$err = $link->con->error();
		} else {
			header('Location: '.$url.'&l_zone='.$l_zone.'#link');
			exit;
		}
	}
	# Monter / descendre une catégorie
	elseif (($action == 'up_cat' || $action == 'down_cat') && !empty($_GET['id']))
	{
		$dir = ($action == 'up_cat') ? '+' : '-';
		
		if ($link->ordCat($_GET['id'],$dir) == false) {
			$err = $link->con->error();
		} else {
			header('Location: '.$url.'&l_zone='.$l_zone.'#link');
			exit;
		}
	}
	
	# Affichage ---
	//echo '<h1>'.__('Links manager').'</h1>';
	echo('<h1 style="padding-left:50px;background: transparent url(tools/link/themes/'.$_px_theme.'/icon.png) no-repeat left center;" >'.__('Links manager').'</h1>');
	
	// zone pour filtrer les liens par zone
	$array_zone = $link->getZones();
	$array_zone_all = array_merge(array(__('All values') => ""), $array_zone);
	echo '<form action="'.$url.'" method="post"><p id="link-select">';
	
	echo form::hidden('action',$action,false);
	echo form::hidden('page',$page,false);
	//echo form::hidden('p',$p,false);
	echo '<label for="l_zone" style="display:inline;"><strong>'. __('Area name').' : </strong></label>';
	echo form::comboBox('l_zone', $array_zone_all, $l_zone,'','','','prompt="'.__('Select a value').'"');
	echo '<input class="submit" type="submit" value="'. __('ok').'" />';
	echo '</p></form>';
	if ($err != '') {
		echo(
		'<div class="erreur"><p><strong>'.__('Error(s)').' :</strong></p>'.
		'<p>'.$err.'</p>'.
		'</div>'
		);
	}
	$rs =& $link->getEntries($l_zone);
	
	echo(
	'<p>'.__('Drag items to change their positions.').'</p>'.
	'<form action="'.$url.'" method="post">'.
	'<div id="sortlinks">'
	);
	while (!$rs->EOF())
	{
		$link_id = $rs->f('link_id');
		$link_zone = $rs->f('zone');
		$link_ord = $rs->f('position');
		
		// pas de label et pas de lien => c'est une catégorie
		$is_cat = !$rs->f('label') && !$rs->f('href');
		
		$i_label = ($is_cat) ? $rs->f('title') : $rs->f('label');
		
		if ($is_cat) {
			$del_msg = __('Are you sure you want to delete this category?');
		} else {
			$del_msg = sprintf(__('Are you sure you want to delete this %s?'),__('link'));
		}
		
		echo('<div class="sort" id="dnd'.$link_id.'">');
		
		echo(
		'<p>'.($is_cat ? '<span class="category_style">' : '').
		'<a href="'.$url.'&amp;action=delete&amp;id='.$link_id.'" '.
		'onclick="return window.confirm(\''.addslashes($del_msg).'\')">'.
		'<img src="themes/'.$_px_theme.'/images/delete.png" alt="'.__('delete').'" '.
		'title="'.__('delete').'" class="status" /></a>'
		);
		
		
		if ($is_cat) {
			echo('<a href="'.$url.'&amp;id='.$link_id.'&amp;page=edit_cat" title="'.__('Edit rubric').'">'.
			$i_label.'</a>');
		} else {
			echo('<a href="'.$url.'&amp;id='.$link_id.'&amp;page=edit_link" title="'.__('Edit link').'">'.
			$i_label.'</a>');
		}
		
		echo(($is_cat ? '</span>' : '').'</p>');
		
		if (!$is_cat)
		{
			echo(
			'<p>'.
			htmlspecialchars($rs->f('href')).
			' - '.$rs->f('title').
			'     (lang='.$rs->f('lang').' , '. 
			' target='.$rs->f('cible').', style='.$rs->f('style').' )' .
			'</p>'
			);
		}
		
		echo('<p class="nojsfield">');
		
		if ($rs->int_index > 0) {
			echo(
			'<a href="'.$url.'&amp;id='.$link_id.'&amp;action=up_'.($is_cat ? 'cat' : 'link').'">'.
			'<img src="tools/link/themes/'.$_px_theme.'/images/arrow_up.png" '.
			'alt="'.sprintf(__('Move %s up'), $i_label).'" /></a>'
			);
		} else {
			echo('<img src="tools/link/themes/'.$_px_theme.'/images/empty.png" alt="" width="12" />');
		}
		
		if ($rs->int_index+1 < $rs->nbRow()) {
			echo(
			'<a href="'.$url.'&amp;id='.$link_id.'&amp;action=down_'.($is_cat ? 'cat' : 'link').'">'.
			'<img src="tools/link/themes/'.$_px_theme.'/images/arrow_down.png" '.
			'alt="'.sprintf(__('Move %s down'),$i_label).'" /></a>'
			);
		} else {
			echo('<img src="tools/link/themes/'.$_px_theme.'/images/empty.png" alt="" width="12" />');
		}
		
		echo('</p>');
		
		echo('</div>');
		
		$rs->moveNext();
	}
	echo(
	'</div>'.
	'<p class="button"><input type="hidden" id="dndSort" name="dndSort" value="" />'.
	'<input type="submit" class="submit" value="'.__('save order').'" /></p>'.
	'</form>'
	);
	
	echo(
	'<form action="'.$url.'" method="post">'.
	'<fieldset><legend><span class="link_style">'.__('New link').'</span></legend>'.
	'<p class="field"><strong>'.
	'<label for="l_label" class="float">'.__('Label').' : </label></strong>'.
	form::textField('l_label', 40, 255, $l_label).'</p>'.
	
	'<p class="field"><strong>'.
	'<label for="l_zone" class="float">'.__('Zone d\'affichage').' : </label></strong>'.
	/*form::textField('l_zone', 40, 255, $l_zone).'</p>'.*/
	form::comboBox('l_zone', $array_zone, $l_zone).'</p>'.
	
	'<p class="field"><strong>'.
	'<label for="l_href" class="float">'.__('URL').' : </label></strong>'.
	form::textField('l_href', 40, 255, $l_href).'</p>'.

	'<p class="field"><strong>'.
	'<label for="l_cible" class="float">'.__('Cible').' : </label></strong>'.
	form::textField('l_cible', 40, 255, $l_cible).'   '.
	__('Vide par défaut. Saisir _blank ou le nom d\'une fenêtre pour ouvrir dans une autre page').
	'</p>'.
	
	'<p class="field">'.
	'<label for="l_title" class="float">'.__('Description').' ('.__('optional').') : </label>'.
	form::textField('l_title', 40, 255, $l_title).'</p>'.

	'<p class="field">'.
	'<label for="l_style" class="float">'.__('Style').' ('.__('optional').') : </label>'.
	form::textField('l_style',30,30,$l_style) . '</p>'.
	
	'<p class="field">'.
	'<label for="l_lang" class="float">'.__('Language').' ('.__('optional').') : </label>'.
	form::textField('l_lang', 2, 2, $l_lang) . '</p>'.
	
	'<p class="button">'.form::hidden('action','add_link',false).
	'<input type="submit" class="submit" value="'.__('save').'"/></p>'.
	'</fieldset>'.
	'</form>'
	);
	
	echo(
	'<form action="'.$url.'" method="post">'.
	'<fieldset><legend><span class="category_style">'.__('New rubric').'</span></legend>'.
	
	'<p class="field"><strong>'.
	'<label for="l_zone" class="float">'.__('Area name').' : </label></strong>'.
	form::textField('c_zone', 40, 255, $l_zone).'</p>'.
	
	'<p class="field"><strong>'.
	'<label for="c_title" class="float">'.__('Category name').' : </label></strong>'.
	form::textField('c_title', 40, 255, $c_title).'</p>'.
	
	'<p class="button">'.form::hidden('action','add_cat',false).
	'<input type="submit" class="submit" value="'.__('save').'"/></p>'.
	'</fieldset>'.
	'</form>'
	);
	
	echo(
	'<h2>'.__('Usage').'</h2>'.
	'<p>'.__('To replace your static links list by this one, just put the following code in your template:').'</p>'.
	'<pre>&lt;?php pxLink::linkList(); ?&gt;</pre>'.
	'<p>'.__('or'). '</p><pre>&lt;?php pxLink::linkListByArea("'.__('Area name').'"); ?&gt;</pre>'.
	'<p>'.__('or'). '</p><pre>&lt;?php pxLink::linkListByCategory("'.__('Category name').'"); ?&gt;</pre>'
	);
}
?>
