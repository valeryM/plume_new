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

$_px_p = 50; //percentage of wizard done.

if (empty($_SESSION['step5'])) {
	header('Location: dbinstall.php');
	exit;
}

//load the language file
$_PX_config['encoding'] = $_SESSION['manager_encoding'];
$l = new l10n($_SESSION['lang'], 'install');
$l->l10n($_SESSION['lang']); //load the general lang file

$_PX_config['db']['db_login']     = $_SESSION['dblogin'];
$_PX_config['db']['db_password']  = $_SESSION['dbpass'];
$_PX_config['db']['db_server']    = $_SESSION['dbserver'];
$_PX_config['db']['db_database']  = $_SESSION['dbname'];
$_PX_config['db']['table_prefix'] = $_SESSION['dbprefix'];
$_PX_config['db_version'] = $_SESSION['db_version'];
$_PX_config['debug'] = true;

$u = new User(); //empty user
// create a new Manager object and create a new user, the id=1 will be set automatically.
$m = new Manager();
$m->setUser($u);
$error = false;
$error_mess = '';

$px_username = (!empty($_POST['u_username'])) ? $_POST['u_username'] : '';
$px_realname = (!empty($_POST['u_realname'])) ? $_POST['u_realname'] : '';
$px_email = (!empty($_POST['u_email'])) ? $_POST['u_email'] : '';
$px_pubemail = (!empty($_POST['u_pubemail'])) ? $_POST['u_pubemail'] : '';
$px_password = (!empty($_POST['u_password'])) ? $_POST['u_password'] : '';

if (!empty($px_username)) {
	if ($m->saveUser('', $px_username, $px_password, $px_realname, $px_email, $px_pubemail)) {
		$u = new User(1); //just created user
		$u->savePref('lang', $_SESSION['manager_lang']);
		$_SESSION['username'] = $px_username;
		$_SESSION['realname'] = $px_realname;
		$_SESSION['email'] = $px_email;
		$_SESSION['pubemail'] = $px_pubemail;
		$_SESSION['password'] = $px_password;
		$_SESSION['step6'] = true;
		header('Location: firstwebsite.php');
		exit;
	} else {
		$error = true;
		$error_mess = $m->error(true, false);
	}
}
include dirname(__FILE__).'/_top.php';

echo '<h2>'.__('First user creation').'</h2>'."\n\n";


if($error === true) {
	echo '<p class="important">'.__('Errors during the installation of the first user. Please check the error messages.').'</p>'."\n\n";
    echo "\n\n" . $error_mess . "\n\n";
}
?>
<p><?php echo __('The first user will be the global administrator of the PLUME CMS installation.'); ?></p>

<form action="firstuser.php" method="post" id="formPost">
  <p class="field"><label for="u_username"><strong><?php  echo __('Login:'); ?></strong></label>
  <?php echo php_f_textField('u_username', 30, 30, $px_username, '', ''); ?><br />
  <?php echo __('The login must contain only letters and digits and no spaces.'); ?></p>

  <p class="field"><label for="u_realname"><strong><?php  echo __('Name:'); ?></strong></label>
  <?php echo php_f_textField('u_realname', 30, 50, $px_realname, '', ''); ?></p>

  <p class="field"><label for="u_email"><strong><?php  echo __('Email <span class="small">(not shown)</span>:'); ?></strong></label>
  <?php echo php_f_textField('u_email', 30, 50, $px_email, '', ''); ?>
  </p>

  <p class="field"><label for="u_pubemail"><?php  echo __('Public email:'); ?></label>
  <?php echo php_f_textField('u_pubemail', 30, 50, $px_pubemail, '', ''); ?></p>

  <p class="field"><label for="u_password"><strong><?php  echo __('Password:'); ?></strong></label>
  <?php echo php_f_textField('u_password', 30, 50, '', '', ''); ?></p>

<p>
<input name="next" type="submit" class="submit" value="<?php  echo __('Next'); ?>"  accesskey="n" />
</p>
</form>

<?php

include dirname(__FILE__).'/_bottom.php';

?>
