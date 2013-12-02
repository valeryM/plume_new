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
# Contributor(s):
# - Kato Fong (kato AT sdf.lonestar.org)
# ***** END LICENSE BLOCK ***** */

include_once dirname(__FILE__).'/../../inc/class.l10n.php';
include_once dirname(__FILE__).'/functions.php';

$m->l10n->loadPlugin($m->user->lang, 'alertcom');
$is_user_root = auth::asLevel(PX_AUTH_ROOT);
if (!$is_user_root):
    $m->setError( __('You do not have the rights to access this plugin.'));
else:
/*==============================================================================
 Process block
==============================================================================*/
//The variables
    //Getting Webmaster E-mail
    $webmaster = getWebmaster();
    $webmasterEmail = $webmaster->f('user_email');
    //Replacing E-mail example with Webmaster E-mail if no other address provided
    if ($_PX_config['email_for_sending_notification'] == 'your@email.com'):
        $px_email_for_sending_notification = $webmasterEmail;
    else:
        $px_email_for_sending_notification = $_PX_config['email_for_sending_notification'];
    endif;
    
    $px_comment_notification_status = $_PX_config['comment_notification_status'];

    if (!empty($_POST['save'])):
    // get the values from the form
        $px_comment_notification_status           = (int) $_POST['c_comment_notification_status'];
        $px_email_for_sending_notification        = (string) $_POST['c_email_for_sending_notification'];
        	
        // checking the value
        if (empty($px_email_for_sending_notification) || emailListTesting($px_email_for_sending_notification)):
        
            $m->setError( __('Error: please provide a valid e-mail or list of email!'), 400);
            if (!file_exists(dirname(__FILE__).'/../../conf/config.php') || !is_writable(dirname(__FILE__).'/../../conf/config.php')):
                
                $m->setError(sprintf( __('Error: The file <strong>%s</strong> is not available for writing.'),
                files::realpath(dirname(__FILE__).'/../../conf/').'/config.php'), 500);
            endif;
        endif;
	
	if (false === $m->error()):
        
		// open file for edition of the data
		include_once dirname(__FILE__).'/../../extinc/class.configfile.php';
		$cfg = new configfile(dirname(__FILE__).'/../../conf/config.php');
		$cfg->prefix = '_PX_config';
                $cfg->editVar('comment_notification_status',           (int) $px_comment_notification_status);
		$cfg->editVar('email_for_sending_notification',        (string) $px_email_for_sending_notification);
		
		if (!$cfg->saveFile()):
                
			$m->setError( __('Error: Error while saving the config file, check the permissions of the file.') , 500);
                else:
                
			$msg = __('The configuration was successfully updated.');
			header('Location: tools.php?p=alertcom&msg='.urlencode($msg));
			exit;
		endif;
	endif;
    endif;
	
/*==============================================================================
 Display block
==============================================================================*/
?>
<h1><?php  echo __('Alertcom'); ?></h1>

<h2><?php  echo __('Alert/comment processing'); ?></h2>

<form action="tools.php" method="post" id="formPost">	

<p class="field">
<label class="float" for="c_comment_notification_status" style="display:inline"><strong><?php  echo __('Alertcom status:'); ?></strong></label>
<?php echo form::combobox('c_comment_notification_status', array( __('Disabled')=> 0,  __('Notify published comments (default)') => 1, __('Notify All comments') => 2), $px_comment_notification_status); ?>
</p>
<p><?php echo __('Here you can disable Alertcom or set which type of comments should it notify for : the published ones (Valid) or all (including the junk comments).') ?></p>

<p class="field">
<label class="float" for="c_email_for_sending_notification" style="display:inline"><strong><?php  echo __('Recipient(s) address(es):'); ?></strong></label>
<?php echo form::textField('c_email_for_sending_notification', 35, 255, $px_email_for_sending_notification, '', ''); ?>
</p>
<p><?php echo __('Each time a comment is left on the site an alert message will be sent to the above address (you can also define multiple comma seperated addresses).') ?></p>

<p class="button">
  <?php echo form::hidden('p','alertcom');  ?>
  <input name="save" type="submit" class="submit" value="<?php  echo __('Save [s]'); ?>" accesskey="<?php  echo __('s'); ?>" />
  </p>
</form>

<?php
endif; // if user root
?>
