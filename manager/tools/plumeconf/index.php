<?php
/* -*- tab-width: 4; indent-tabs-mode: nil; c-basic-offset: 4 -*- */
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

$m->l10n->loadPlugin($m->user->lang, 'plumeconf');

$is_user_root = auth::asLevel(PX_AUTH_ROOT);
if (!$is_user_root):
    $m->setError( __('You do not have the rights to access this plugin.'));
else: 
/*==============================================================================
 Process block
==============================================================================*/
$px_lang   = $_PX_config['lang'];
$px_format = $_PX_config['content_format'];
$px_max_size = (int) $_PX_config['max_upload_size']/1024;
$px_url_format = $_PX_config['url_format'];
$px_debug = $_PX_config['debug'];
$px_log404errors = $_PX_config['log404errors'];
$px_akismet_key = config::f('akismet_key');
$px_typepad_antispam_key = config::f('typepad_antispam_key');

// get the available languages
$localedir = dirname(__FILE__).'/../../locale/';
$arry_lang = array();
$arry_codes = $m->l10n->getIsoCodes();
$D = dir($localedir);
while(false !== ($entry = $D->read())) {
	if (is_dir($localedir.$entry) && preg_match('/^[a-z]{2}$/', $entry)) {
		$label = $arry_codes[$entry];
		$arry_lang[$label] = $entry;
	}
}
$D->close();
$arry_lang['English'] = 'en';

if (!empty($_POST['save'])) {
	// get the values from the form
	$px_lang         = (string) $_POST['c_lang'];
	$px_format       = (string) $_POST['c_format'];
	$px_max_size     = (int)    $_POST['c_max_size'];
	$px_url_format   = (string) $_POST['c_url_format'];
	$px_debug        = (bool)   $_POST['c_debug'];
	$px_log404errors = (bool)   $_POST['c_log404errors'];
	$px_comment_stat = (int)    $_POST['c_comment_stat'];
	$px_akismet_key  = (string) $_POST['c_akismet_key'];
    $px_typepad_antispam_key  = (string) $_POST['c_typepad_antispam_key'];

	// check that they are ok
	$px_debug = ($px_debug) ? true : false;
	$px_log404errors = ($px_log404errors) ? true : false;
	if (empty($px_lang)) {
		$m->setError( __('Error: You must provide a default language.'), 400);
	}
	if (empty($px_max_size) or !preg_match('/^[0-9]+$/',$px_max_size)) {
		$m->setError( __('Error: The maximum size must be an integer.'), 400);
	} else {
		$px_max_size_save = $px_max_size * 1024; //conversion in bytes
	}
	if (empty($px_url_format)) {
		$m->setError( __('Error: You must provide an URL format.'), 400);
	}
	if (empty($px_format)) {
		$m->setError( __('Error: You must provide a default content format.'), 400);
	}
	if (	!file_exists(dirname(__FILE__).'/../../conf/config.php')
			|| !is_writable(dirname(__FILE__).'/../../conf/config.php')) {
		$m->setError(sprintf( __('Error: The file <strong>%s</strong> is not available for writing.'),
		files::realpath(dirname(__FILE__).'/../../conf/').'/config.php'), 500);
	}

	if (false === $m->error()) {
		// open file for edition of the data
		include_once dirname(__FILE__).'/../../extinc/class.configfile.php';
		$cfg = new configfile(dirname(__FILE__).'/../../conf/config.php');
		$cfg->prefix = '_PX_config';
		$cfg->editVar('lang',            (string) $px_lang);
		$cfg->editVar('content_format',  (string) $px_format);
		$cfg->editVar('max_upload_size', (int) $px_max_size_save);
		$cfg->editVar('url_format',      (string) $px_url_format);
		$cfg->editVar('debug',           (bool) $px_debug);
		$cfg->editVar('log404errors',    (bool) $px_log404errors);
        if (false === $cfg->addVar('akismet_key', (string) $px_akismet_key)) {
            $cfg->editVar('akismet_key', (string) $px_akismet_key);
        }
        if (false === $cfg->addVar('typepad_antispam_key', (string) $px_typepad_antispam_key)) {
            $cfg->editVar('typepad_antispam_key', (string) $px_typepad_antispam_key);
        }

		if (!$cfg->saveFile()) {
			$m->setError( __('Error: Error while saving the config file, check the permissions of the file.') , 500);
		} else {
			$msg = __('The configuration was successfully updated.');
			header('Location: tools.php?p=plumeconf&msg='.urlencode($msg));
			exit;
		}
	}


}

/*==============================================================================
 Display block
==============================================================================*/

?>

<h1><?php  echo __('PLUME CMS Configuration'); ?></h1>

<p><?php  echo __('You can modify here the default configuration of PLUME CMS. This will affect all the websites that are managed with PLUME CMS. Please note that some of the information can be overwritten on a site by site, or user by user basis.'); ?></p>

<form action="tools.php" method="post" id="formPost">
  <p class="field"><label class="float" for="c_lang" style="display:inline"><strong><?php  echo __('Default language:'); ?></strong></label>
  <?php  echo form::combobox('c_lang', $arry_lang, $px_lang); ?></p>
  <p><?php  echo __('You can chose only from the installed languages.'); ?>
  </p>
  
  <p class="field"><label class="float" for="c_format" style="display:inline"><strong><?php  echo __('Default content format:'); ?></strong></label>
  <?php echo form::combobox('c_format', array('HTML'=>'html','Wiki'=>'wiki'), $px_format); ?>
  </p>

  <p class="field"><label class="float" for="c_max_size" style="display:inline"><strong><?php  echo __('Maximum size of the files and images in KB:'); ?></strong></label>
  <?php echo form::textField('c_max_size', 10, 55, $px_max_size, '', ''); ?></p>
  <p><?php echo sprintf( __('You cannot give a maximum size greater than <strong>%s</strong> as it is the maximum size allowed by your system.'), ini_get('upload_max_filesize')); ?>
  </p>

  <p class="field"><label class="float" for="c_url_format" style="display:inline"><strong><?php  echo __('Format of the URL:'); ?></strong></label>
  <?php echo form::combobox('c_url_format', array( __('Simple (default)')=> 'simple',  __('Nice URL') => 'mod_rewrite'), $px_url_format); ?></p>
  <p><?php  echo sprintf(__('To enable the <em>Nice URL</em> you need a webserver with the support of rewriting rules, like <em>mod_rewrite</em> with Apache. Check the online documentation to see how to enable this on your server. Examples of configuration are <a href="%s">available on the community supported documentation</a>.'), 'http://pxsystem.sourceforge.net/community/RewriteUrl'); ?>
  </p>  

  <p class="field"><label class="float" for="c_debug" style="display:inline"><strong><?php  echo __('Display debug information:'); ?></strong></label>
  <?php echo form::combobox('c_debug', array(  __('Yes') => true,  __('No') => false), $px_debug); ?></p>
  <p><?php  echo __('When you have the debug information enabled, you can see them when looking at the end of the public page source.'); ?>
  </p>    

  <p class="field"><label class="float" for="c_log404errors" style="display:inline"><strong><?php  echo __('Log the pages not found:'); ?></strong></label>
  <?php echo form::combobox('c_log404errors', array(  __('Yes') => true,  __('No') => false), $px_log404errors); ?></p>
  <p><?php  echo __('The log of the page not found can be managed from the Smart 404 Errors plugin. Be carefull that you need to purge this log from times to times if you enable the logging.'); ?>
  </p>    

  <p class="field"><label class="float" for="c_max_size" style="display:inline"><strong><?php  echo __('Akismet key:'); ?></strong></label>
  <?php echo form::textField('c_akismet_key', 15, 55, $px_akismet_key, '', ''); ?></p>
  <p><?php echo sprintf(__('This is an optional configuration information. You can learn more about Akismet to fight spam on <a href="%s">their website</a>.'), 'http://www.akismet.com'); ?>
  </p>

  <p class="field"><label class="float" for="c_max_size" style="display:inline"><strong><?php  echo __('TypePad Antispam key:'); ?></strong></label>
  <?php echo form::textField('c_typepad_antispam_key', 35, 55, $px_typepad_antispam_key, '', ''); ?></p>
  <p><?php echo sprintf(__('This is an optional configuration information. You can learn more about TypePad Antispam to fight spam on <a href="%s">their website</a>.'), 'http://antispam.typepad.com'); ?>
  </p>

  <p class="button">
  <?php echo form::hidden('p','plumeconf');  ?>
  <input name="save" type="submit" class="submit" value="<?php  echo __('Save [s]'); ?>" accesskey="<?php  echo __('s'); ?>" />
  </p>
</form>



<?php
endif; // end of the 'is root' check
?>


