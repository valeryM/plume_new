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
 
//	Inclusion des ressources nécessaires
require_once dirname(__FILE__).'/class.bot.php';
require_once dirname(__FILE__).'/functions.bot.php';
require_once $_PX_config['manager_path'].'/inc/lib.frontend.php';
$m = new Manager();

pxTemplateInit('remove_numbers');

//	Déclaration des variables
echo	'<h1>'.__('Box of Tools').'</h1>'."\n\n";

echo	'<h2><pre>'.__('pxBOT::archivesList();').'</pre></h2>'."\n";
echo	'<h3>'.__('Usage').'</h3>'."\n".
		'<p>'.__('To replace your static links list by this one, just put the following code in your template:').'</p>'."\n".
		'<pre>'.
			'&lt;?php pxBOT::archivesListByYear($order, $list, $item$return); ?&gt;'."\r".
			'&lt;?php pxBOT::archivesListByYearByMonth($order, $list, $item$return); ?&gt;'."\r".
			'&lt;?php pxBOT::archivesListByYearByMonthByDay($order, $list, $item$return); ?&gt;'.
		'</pre>';
echo	'<ul>'.
			'<li>
				<strong>$order : </strong>(string) chaîne de caractère permettant de spécifier l\'ordre de tri des années (ASC ou DESC)
			</li>'.
			'<li>
				<strong>$list : </strong>(string) \'%s\' est la chaîne de substitution des élément parents. (exemple: &lt;ul&gt;‰s&lt;/ul&gt;)
			</li>'.
			'<li>
				<strong>$item : </strong>(string) \'%s\' est la chaîne de substitution des élément enfants. (exemple: &lt;li&gt;‰s&lt;/li&gt;)
			</li>'.
			'<li><strong>$return : </strong>(boolean) '.__('variable permettant de spécifier comment on souhaite retourner le résutat de la fonction.').
				'<ul>'.
					'<li>true: '.__('retourne le résultat avec "return $result"').'</li>'.
					'<li>false: '.__('retourne le résultat avec "echo $result"').'</li>'.
				'</ul>'.
			'</lI>'.
		'</ul>';
echo	'<h3>'.__('Examples').'</h3>';
//		$order='DESC', $list="<ul>%s</ul>", $item="<li>%s</li>", $return=false
echo	'<h4>pxBOT::archivesListByYear()</h4>';
 pxBOT::archivesListByYear($order='DESC', $list="<ul>%s</ul>", $item="<li>%s</li>", $return=false);
echo	'<h4>pxBOT::archivesListByYearByMonth()</h4>';
pxBOT::archivesListByYearByMonth($order='DESC', $list="<ul>%s</ul>", $item="<li>%s</li>", $return=false);
echo	'<h4>pxBOT::archivesListByYearByMonthByDay()</h4>';
pxBOT::archivesListByYearByMonthByDay($order='DESC', $list="<ul>%s</ul>", $item="<li>%s</li>", $return=false);
/*
echo	'<br />';
echo	'<h2><pre>'.__('pxBOT::tagsCloud();').'</pre></h2>'."\n";
echo	'<h3>'.__('Usage').'</h3>'."\n".
		'<p>'.__('To replace your static links list by this one, just put the following code in your template:').'</p>'."\n".
		'<pre>&lt;?php pxBOT::tagsCloud(); ?&gt;</pre>';
pxBOT::tagsCloud();
*/
?>