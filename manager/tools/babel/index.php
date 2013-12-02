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

include_once dirname(__FILE__).'/../../inc/class.l10n.php';
include_once dirname(__FILE__).'/../../inc/class.files.php';
include_once dirname(__FILE__).'/../../inc/class.checklist.php';

$m->l10n->loadPlugin($m->user->lang, 'babel');
$is_user_root = auth::asLevel(PX_AUTH_ROOT);
if (!$is_user_root):
    $m->setError( __('You do not have the rights to access this plugin.'));
else:


?>
<h1><?php  echo __('Babel Fish'); ?></h1>

<h2><?php  echo __('Optimization of the locale files'); ?></h2>
<?php
if (!empty($_GET['op']) && ($_GET['op'] == 'op')) {
	echo '<p><strong>'.__('Results of the optimization:').'</strong></p>'."\n\n";
	$checklist = new checklist();
	$files = array();
	files::listfiles($_PX_config['manager_path'].'/locale', $files, '/\.lang$/');
	files::listfiles($_PX_config['manager_path'].'/tools', $files, '/\.lang$/');
	$i = 1;
	foreach($files as $file) {
		$checklist->addTest('file'.$i, l10n::optimizeLocale($file),
			sprintf(__('Locale file <strong>%s</strong> optimized.'), $file),
			sprintf(__('Error optimizing the file <strong>%s</strong>.'), $file));
			$i++;
	}
	echo $checklist->getHtml('themes/'.$_px_theme.'/images');
	if ($checklist->checkAll()) {
		echo '<p>'.__('The optimization was successfull. You will have a little increase in the speed of the manager. You need to run again the optimization after adding a new plugin or upgrading your installation.').'</p>'."\n";
	} else {
		echo '<p>'.__('The optimization was not successfull. Do not worry too much, this would have not increased a lot the speed of the manager.').'</p>'."\n";
	}
} else {
	echo '<p>'.__('The optimization convert the <strong>.lang</strong> files in <strong>PHP</strong> files ready for inclusion, thus improving the speed of the manager. There is an automatic check to use the <strong>.lang</strong> file if more recent.').'</p>'."\n";
	echo '<p>'.sprintf(__('<a href="%s">Run the optimization</a>.'), 'tools.php?p=babel&amp;op=op').'</p>'."\n";
}


endif; // if user root
?>
