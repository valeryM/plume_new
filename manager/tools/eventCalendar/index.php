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
#	authors: 
#		Jaime Fernandez (@vissit)
#		Nerea Navarro (@nereaestonta)
#	company:	
#		Paradigma Tecnologico (@paradigmate)
#
# Contributor(s):
# - Valéry MERLET
#
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
#
# ***** END LICENSE BLOCK ***** */

 
//	Inclusion des ressources nécessaires
require_once $_PX_config['manager_path'].'/inc/lib.frontend.php';
$m = new Manager();

if (!$is_user_root):
	$m->setError( __('You do not have the rights to access this plugin.'));
else:
?>

<div style="display:block">
	<div style="width:450px;display:inline-block;"><?php pxEventsCalendarWidget(); ?></div>
	<div style="display:inline-block; vertical-align:top;">
		<p>Permet d'intégrer aux templates du site un calendrier dynamique pour afficher les évènements de la période</p>
		<p>Il suffit d'ajouter le code suivant à l'endroit souhaité dans le template pour que ce calendrier s'affiche</p>
		<pre style="text-align:center">&lt;?php pxEventsCalendarWidget(); ?&gt;</pre>		
		<p>Les données sont chargées par une requête AJAX (tools/eventCalendar/json/events.json.php).</p>
		<p>La mise en forme peut se faire dans le CSS eventCalendar_theme_responsive.css  (dossier tools/eventCalendar/css).</p>
		<p>Ce Plugin a été réalisé en s'appuyant sur le plugin :</p><br/>
		<span style="padding-left:80px;display:block">
			jquery.eventCalendar.js<br/>
			version: 0.54;<br/>
			date: 18-04-2013<br/>
			authors: Jaime Fernandez (@vissit) &amp; Nerea Navarro (@nereaestonta)<br/>
			company: Paradigma Tecnologico (@paradigmate)
		</span>
		<p><a href="tools/eventCalendar/index.html" target="_blank">Lien vers la page du site (version locale)</a></p>
	</div>
</div>

<?php 
endif;  // if user root
?>