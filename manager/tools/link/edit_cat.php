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

$c_title = $rs->f('title');
$c_zone = $rs->f('zone');

if (!$rs->isEmpty() && $action == 'edit_cat')
{
	$c_title = trim($_POST['c_title']);
	$c_zone = trim($_POST['c_zone']);
	if ($c_title)
	{
		if ($link->updCat($id,$c_zone,$c_title) == false) {
			$err = $link->con->error();
		} else {
			header('Location: '.$url.'$l_zone='.$c_zone);
			exit;
		}
	}
}

# Affichage
$px_submenu->addItem(__('Back'),array($url),$icon,false);

echo('<h1 style="padding-left:50px;background: transparent url(tools/link/themes/'.$_px_theme.'/icon.png) no-repeat left center;" >'.__('Links manager').'</h1>');
//echo('<h1 style="background: transparent url(tools/link/themes/'.$_px_theme.'/icon.png) no-repeat left center;" >'.__('Links manager').'</h1>');
echo('<h2>'.__('Edit rubric').'</h2>');

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
	echo(
	'<form action="'.$url.'" method="post">'.
	'<fieldset><legend>'.__('Edit rubric').'</legend>'.
	
	'<p class="field"><strong>'.
	'<label for="c_zone" class="float">'.__('Area name').' : </label></strong>'.
	form::textField('c_zone',40,255,$c_zone).'</p>'.
	
	'<p class="field"><strong>'.
	'<label for="c_title" class="float">'.__('Category name').' : </label></strong>'.
	form::textField('c_title',40,255,htmlspecialchars($c_title)).'</p>'.
	
	
	'<p class="button">'.form::hidden('action','edit_cat').
	form::hidden('page','edit_cat').
	form::hidden('id',$id).
	'<input type="submit" class="submit" value="'.__('save').'"/></p>'.
	'</fieldset>'.
	'</form>'
	);
}
?>
