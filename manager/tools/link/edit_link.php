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

$id = $_REQUEST['id'];

$rs =& $link->getEntry($id);

$l_label = $rs->f('label');
$l_zone = $rs->f('zone');
$l_href = $rs->f('href');
$l_title = $rs->f('title');
$l_lang = $rs->f('lang');
$l_rel = $rs->f('rel');
$l_position = $rs->f('position');
$l_cible = $rs->f('cible');
$l_style = $rs->f('style');

if (!$rs->isEmpty() && $action == 'edit_link')
{
	$l_label = trim($_POST['l_label']);
	$l_zone = trim($_POST['l_zone']);
	$l_title = trim($_POST['l_title']);
	$l_href = trim($_POST['l_href']);
	$l_lang = trim($_POST['l_lang']);
	$l_cible = trim($_POST['l_cible']);
	$l_cat = isset($_POST['l_cat']) ? trim($_POST['l_cat']) : '-1';
	$old_cat = isset($_POST['old_cat']) ? trim($_POST['old_cat']) : '-1';
	$l_style = trim($_POST['l_style']);
	
	if (!$l_label || !$l_href)
	{
		$err = __('You must provide at least a label and an URL');
	}
	elseif (!$link->isURI($l_href, array('domain_check' => false, 'allowed_schemes' => $link->protocols))) {
		$err = __('You must provide a valid URL');
	}
	else
	{

		$rel = '';
		
		if (isset($_POST['identity'])) {
			$rel .= $_POST['identity'];
		} else {
			if(isset($_POST['friendship']))		$rel .= ' '.$_POST['friendship'];
			if(isset($_POST['physical']))		$rel .= ' met';
			if(isset($_POST['professional']))	$rel .= ' '.implode(' ',$_POST['professional']);
			if(isset($_POST['geographical']))	$rel .= ' '.$_POST['geographical'];
			if(isset($_POST['family']))			$rel .= ' '.$_POST['family'];
			if(isset($_POST['romantic']))		$rel .= ' '.implode(' ',$_POST['romantic']);
		}

		if ($link->updLink($id,$l_zone, $l_label,$l_href,$l_cible,$l_title,$l_lang, $rel, $l_cat, $old_cat, $l_style) == false) {
			$err = $link->con->error();
		} else {
			header('Location: '.$url.'&l_zone='.$l_zone);
			exit;
		}
	}
}

# Affichage
$px_submenu->addItem(__('Back'),array($url),$icon,false);
//echo '<h1>'.__('Links manager').'</h1>';
echo('<h1 style="padding-left:50px;background: transparent url(tools/link/themes/'.$_px_theme.'/icon.png) no-repeat left center;" >'.__('Links manager').'</h1>');
echo('<h2>'.__('Edit link').'</h2>');

if ($err != '') {
	echo(
	'<div class="erreur"><p><strong>'.__('Error(s)').' :</strong></p>'.
	'<p>'.$err.'</p>'.
	'</div>'
	);
}

if ($rs->isEmpty())
{
	echo('<p class="message">'.__('No link').'</p>');
}
else
{
	$array_zone = $link->getZones();
	# Affichage du formulaire
	echo (
	'<form action="'.$url.'" method="post">'.
	'<fieldset><legend>'.__('Edit link').'</legend>'.
	'<p class="field"><strong>'.
	'<label for="l_label" class="float">'.__('Label').' : </label></strong>'.
	form::textField('l_label',40,255,htmlspecialchars($l_label)).'</p>'.

	'<p class="field"><strong>'.
	'<label for="l_zone" class="float">'.__('Area name').' : </label></strong>'.
	/*form::textField('l_zone',40,255,$l_zone).'</p>'.*/
	form::comboBox('l_zone', $array_zone, $l_zone).'</p>'.
	
	'<p class="field"><strong>'.
	'<label for="l_href" class="float">'.__('URL').' : </label></strong>'.
	form::textField('l_href',40,255,htmlspecialchars($l_href)).'</p>'.

	'<p class="field">'.
	'<label for="l_cible" class="float">'.__('Cible').' : </label>'.
	form::textField('l_cible', 40, 255, $l_cible).'  '.
	__('Vide par défaut. Saisir _blank ou le nom d\'une fenêtre pour ouvrir dans une autre page').
	'</p>'.
	 
	'<p class="field">'.
	'<label for="l_title" class="float">'.__('Description').' ('.__('optional').') : </label>'.
	form::textField('l_title',40,255,htmlspecialchars($l_title)).'</p>'.
	
	'<p class="field">'.
	'<label for="l_style" class="float">'.__('Style').' ('.__('optional').') : </label>'.
	form::textField('l_style',30,30,htmlspecialchars($l_style)) . '</p>'.
	
	'<p class="field">'.
	'<label for="l_lang" class="float">'.__('Language').' ('.__('optional').') : </label>'.
	form::textField('l_lang',2,2,htmlspecialchars($l_lang)) . '</p>'.

	
	'<p class="button">'.form::hidden('action','edit_link').
	form::hidden('page','edit_link').
	form::hidden('id',$id).
	'<input type="submit" class="submit" value="'.__('save').'"/></p>'.
	'</fieldset>'.

	//'</form>'.
/*	
	'<fieldset><legend>'.__('XFN').'</legend>'.

	'<p class="field">'.
	'<label class="float">'.__('Me').'</label>'.
	form::checkbox('identity', 'me', ($l_rel == 'me')).__('Another link for myself').'</p>'.
	
	'<p class="field">'.
	'<label class="float">'.__('Friendship').'</label>'.
	form::radio('friendship', 'contact', (strpos($l_rel, 'contact'))).__('Contact').
	form::radio('friendship', 'acquaintance', (strpos($l_rel, 'acquaintance'))).__('Acquaintance').
	form::radio('friendship', 'friend', (strpos($l_rel, 'friend'))).__('Friend').
	form::radio('friendship', '').__('None').
	'</p>'.

	'<p class="field">'.
	'<label class="float">'.__('Physical').'</label>'.
	form::checkbox('physical', 'met', (strpos($l_rel, 'met'))).__('Met').
	'</p>'.

	'<p class="field">'.
	'<label class="float">'.__('Professional').'</label>'.
	form::checkbox('professional[1]', 'co-worker', (strpos($l_rel, 'co-worker'))).__('Co-worker').
	form::checkbox('professional[2]', 'colleague', (strpos($l_rel, 'colleague'))).__('Colleague').
	'</p>'.

	'<p class="field">'.
	'<label class="float">'.__('Geographical').'</label>'.
	form::radio('geographical', 'co-resident', (strpos($l_rel, 'co-resident'))).__('Co-resident').
	form::radio('geographical', 'neighbor', (strpos($l_rel, 'neighbor'))).__('Neighbor').
	form::radio('geographical', '').__('None').
	'</p>'.

	'<p class="field">'.
	'<label class="float">'.__('Family').'</label>'.
	form::radio('family', 'child', (strpos($l_rel, 'child'))).__('Child').
	form::radio('family', 'parent', (strpos($l_rel, 'parent'))).__('Parent').
	form::radio('family', 'sibling', (strpos($l_rel, 'sibling'))).__('Sibling').
	form::radio('family', 'spouse', (strpos($l_rel, 'spouse'))).__('Spouse').
	form::radio('family', 'kin', (strpos($l_rel, 'kin'))).__('Kin').
	form::radio('family', '').__('None').
	'</p>'.

	'<p class="field">'.
	'<label class="float">'.__('Romantic').'</label>'.
	form::checkbox('romantic[1]', 'muse', (strpos($l_rel, 'muse'))).__('Muse').
	form::checkbox('romantic[2]', 'crush', (strpos($l_rel, 'crush'))).__('Crush').
	form::checkbox('romantic[3]', 'date', (strpos($l_rel, 'date'))).__('Date').
	form::checkbox('romantic[4]', 'sweetheart', (strpos($l_rel, 'sweetheart'))).__('Sweetheart').
	'</p>'.
	'</fieldset>'.
*/
	'</form>'
	
	);
}
?>
