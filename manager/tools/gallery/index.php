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
	<h1>Gallerie d'images</h1>
	<p>Ce plugin permet d'intégrer une gallerie d'image dans un contenu.</p>
	<p>Il suffit d'ajouter le composant avec le bouton prévu dans la barre d'outil de l'éditeur.</p>
	<p><img src="tools/gallery/img/toolbar.png" /></p>
	<p>et de remplir les champs pour créer le diaporama : sélectionner un image pour afficher dans la gallerie toutes les image du dossier.</p>
	<p><img src="tools/gallery/img/dialogbox.png" /><img src="tools/gallery/img/content.png" /></p>
	<p>Pour modifier le contenu, un clic droit sur l'image ou un double-clic permet d'ouvrir la boite de dialogue.</p>
	<p><img src="tools/gallery/img/contextual_menu.png" /></p>
	<br/>
	<p>Les données sont chargées au moment de l'affichage dans le site.</p>
	<p><img src="tools/gallery/img/final_content.png" /></p>
	<p>La mise en forme peut se faire dans le CSS slides_default.css  (dossier tools/gallery/slides/).</p>
	<p>Ce Plugin a été réalisé en s'appuyant sur le plugin JQuery Slider:</p><br/>
	<span style="padding-left:80px;display:block">
		<a href="http://slidesjs.com" target="gallery">http://slidesjs.com</a><br/>
		By: Nathan Searles, http://nathansearles.com<br/>
		Version: 1.1.9<br/>
	</span>
</div>

<?php 
endif;  // if user root
?>