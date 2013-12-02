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
* PdfViewer
* Intructions: http://www.pdfobject.com
* By: Philip Hutchison (pipwerks.com)
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
/*
if (!$is_user_root):
	$m->setError( __('You do not have the rights to access this plugin.'));
else:
*/
?>

<div style="display:block">
	<h1>Lecteur PDF</h1>
	<p>Ce plugin permet d'intégrer un document PDF dans un contenu.</p>
	<p>Il suffit d'ajouter le composant avec le bouton prévu dans la barre d'outil de l'éditeur.</p>
	<p><img src="tools/fullcalendar/img/toolbar.png" /></p>
	<p>et de remplir les champs pour créer le lecteur : spécifier l'url du document et les paramètres nécessaires.</p>
	<p><img src="tools/fullcalendar/img/dialogbox.png" /><img src="tools/fullcalendar/img/content.png" /></p>
	<p>Pour modifier le contenu, un clic droit sur l'image ou un double-clic permet d'ouvrir la boite de dialogue.</p>
	<p><img src="tools/fullcalendar/img/contextual_menu.png" /></p>
</div>

<?php 
//endif;  // if user root
?>