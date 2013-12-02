<?php
# ***** BEGIN LICENSE BLOCK *****
# Version: MPL 1.1/GPL 2.0/LGPL 2.1
#
# The contents of this file are subject to the Mozilla Public License Version
# 1.1 (the "License"); you may not use this file except in compliance with
# the License. You may obtain a copy of the License at
# http://www.mozilla.org/MPL/
#
# Software distributed under the License is distributed on an "AS IS" basis,
# WITHOUT WARRANTY OF ANY KIND, either express or implied. See the License
# for the specific language governing rights and limitations under the
# License.
#
# The Original Code is DotClear Weblog.
#
# The Initial Developer of the Original Code is
# Olivier Meunier.
# Portions created by the Initial Developer are Copyright (C) 2003
# the Initial Developer. All Rights Reserved.
#
# Contributor(s):
#
# Alternatively, the contents of this file may be used under the terms of
# either the GNU General Public License Version 2 or later (the "GPL"), or
# the GNU Lesser General Public License Version 2.1 or later (the "LGPL"),
# in which case the provisions of the GPL or the LGPL are applicable instead
# of those above. If you wish to allow use of your version of this file only
# under the terms of either the GPL or the LGPL, and not to allow others to
# use your version of this file under the terms of the MPL, indicate your
# decision by deleting the provisions above and replace them with the notice
# and other provisions required by the GPL or the LGPL. If you do not delete
# the provisions above, a recipient may use your version of this file under
# the terms of any one of the MPL, the GPL or the LGPL.
#
# ***** END LICENSE BLOCK *****

$is_user_root = auth::AsLevel(PX_AUTH_ROOT);
if (!$is_user_root):
    $m->setError( __('You do not have the rights to access this plugin.'));
else:  

$err = '';
$tool_url = '';

# Liste des thèmes
$tolls_root = dirname(__FILE__).'/../';
$objPlugins = new plugins($tolls_root);

$m->l10n->loadPlugin($m->user->lang, 'toolsmng');
$tools_list = $objPlugins->getPlugins('plugin',false);

$is_writable = is_writable($tolls_root);

# Installation d'un thème
if ($is_writable && !empty($_GET['tool_url']))
{
	$tool_url = $_GET['tool_url'];
	$parsed_url = parse_url($tool_url);
	
	if (empty($parsed_url['scheme']) || !preg_match('/^http|ftp$/',$parsed_url['scheme'])
	|| empty($parsed_url['host']) || empty($parsed_url['path']))
	{
		$err =  __('Invalid URL.');
	}
	else
	{
		if (($err = $objPlugins->install($tool_url)) === true)
		{
            $msg =  __('The plugin was successfully installed.');
			header('Location: tools.php?p=toolsmng&msg='.urlencode($msg));
			exit;
		}
	}
}

# Suppression d'un thème
$delete = (!empty($_GET['delete'])) ? $_GET['delete'] : '';

if ($is_writable && $delete != '' && in_array($delete,array_keys($tools_list)) && $delete != 'default')
{
	deltree($tolls_root.'/'.$delete);
    $msg =  __('The plugin was successfully deleted.');
	header('Location: tools.php?p=toolsmng&msg='.urlencode($msg));
	exit;
}

if($err != '')
{
	echo '<div class="erreur"><p><strong>'. __('Error(s):').'</strong></p>'.$err.'</div>';
}
?>

<h1><?php  echo __('Plugin manager'); ?></h1>

<!-- <h2><?php  echo __('Install a plugin'); ?></h2>
<?php
if (!$is_writable)
{
	echo '<p>'.sprintf( __('The system has no write access to the folder %s, check its permissions.'), $tolls_root).'</p>';
}
else
{
	echo '<form action="tools.php" method="get">'.
		'<p><label for="tool_url">'. __('Give the plugin file URL (http or ftp):').'</label>'.
		form::textField('tool_url',50,'',$tool_url).'</p>'.
		'<p><input type="submit" class="submit" value="'. __('Install').'" />'.
		'<input type="hidden" name="p" value="toolsmng" /></p>'.
		'</form>';
	
}
?>
-->
<h2><?php  echo __('List of installed plugins'); ?></h2>
<dl id="plugin-manager">
<?php
foreach ($tools_list as $k => $v)
{
	echo '<dt><span class="tools_style">'.$v['label'].'</span> - '.$k.'</dt>';
	echo '<dd>'.$v['desc'].' <br />'.
		__('by').' <span class="author_style">'.$v['author'].'</span> - '. __('version').' '.$v['version'].' <br />';
	
	if ($k != 'toolsmng') {
		echo '<a href="tools.php?p=toolsmng&amp;delete='.$k.'" '.
		'onclick="return window.confirm(\''.addslashes(__('Are you sure you want to delete this plugin?')).'\')">'. __('delete').'</a>';
	}
	
	echo '</dd>';
}
?>
</dl>
<?php 
endif;
?>
