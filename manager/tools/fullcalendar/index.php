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
* Slides, A Slideshow Plugin for jQuery
* Intructions: http://slidesjs.com
* By: Nathan Searles, http://nathansearles.com
* 
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
	<h1>Planning d'évènements</h1>
	<p>Ce plugin permet d'intégrer les évènements d'un planning externes.</p>
	<p>Il suffit d'ajouter le composant avec le bouton prévu dans la barre d'outil de l'éditeur.</p>
	<p><img src="tools/fullcalendar/img/toolbar.png" /></p>
	<p>et de remplir les champs pour créer le planning : spécifier l'url du service et les paramètres nécessaires.</p>
	<p><img src="tools/fullcalendar/img/dialogbox.png" /><img src="tools/fullcalendar/img/content.png" /></p>
	<p>Pour modifier le contenu, un clic droit sur l'image ou un double-clic permet d'ouvrir la boite de dialogue.</p>
	<p><img src="tools/fullcalendar/img/contextual_menu.png" /></p>
	<br/>
	<p>Les données sont chargées au moment de l'affichage dans le site. Elles sont construites selon le standard Google feed.</p>
	<p><img src="tools/fullcalendar/img/calendar-sample.png" /></p>
	<p>La mise en forme peut se faire dans le CSS fullcalendar.css  (dossier tools/fullcalendar/fullcalendar/).</p>
	<p>Ce Plugin a été réalisé en s'appuyant sur le plugin FullCalendar:</p><br/>
	<span style="padding-left:80px;display:block">
		FullCalendar v1.5.4<br/>
		<a href="http://arshaw.com/fullcalendar/" target="fullcalendar" >http://arshaw.com/fullcalendar/</a><br/>
		Use fullcalendar.css for basic styling.<br/>
		For event drag &amp; drop, requires jQuery UI draggable.<br/>
		For event resizing, requires jQuery UI resizable.<br/>
<br/>
		Copyright (c) 2011 Adam Shaw<br/>
		Dual licensed under the MIT and GPL licenses, located in<br/>
		MIT-LICENSE.txt and GPL-LICENSE.txt respectively.
	</span>
</div>

<?php 
endif;  // if user root
?>