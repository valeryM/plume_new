<?php
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
# ***** END LICENSE BLOCK *****
require_once dirname(__FILE__).'/prepend.php';
$_px_p = 20; //percentage of wizard done.

if (empty($_SESSION['step2'])) {
	header('Location: check.php');
	exit;
}

//load the language file
$_PX_config['encoding'] = 'utf-8';
$l = new l10n($_SESSION['lang'], 'install');

$manager_langs = l10n::getAvailableLocales();
$isocodes      = l10n::getIsoCodes();

$mlangs = array(); //list of languages for the manager

reset($manager_langs);
foreach ($manager_langs as $iso) {
	$mlangs[$isocodes[$iso]] = $iso;
}

if (!empty($_POST['mlang'])) {
	$_SESSION['manager_encoding'] = 'utf-8';
	$_SESSION['manager_lang'] = $_POST['mlang'];
	$_SESSION['step3'] = true;
	header('Location: dbinfo.php');
	exit;
}

include dirname(__FILE__).'/_top.php';

echo '<form action="encoding.php" method="post" id="formPost">'."\n\n";

echo '<h2>'.__('Default language for the manager').'</h2>'."\n\n";

echo '<p>'.__('The default language of the manager is the one the users entering the manager for the first time will see. At any time they can change it in their preferences.').'</p>'."\n\n";
?>
<p class="field">
<label class="float" for="mlang" style="display:inline"><?php  echo __('Default manager language:'); ?></label>
<?php echo form::combobox('mlang', $mlangs, $_SESSION['lang']);  ?>
<input name="next" type="submit" class="submit" value="<?php  echo __('Next'); ?>"  accesskey="n" />
</p>
</form>

<?php
include dirname(__FILE__).'/_bottom.php';
?>
