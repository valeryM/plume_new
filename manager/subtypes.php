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


require_once 'path.php';
require_once $_PX_config['manager_path'].'/prepend.php';
auth::checkAuth(PX_AUTH_ADMIN);

/*=================================================
 Create the manager object, load the libs and class
=================================================*/

$m = new Manager();
$_px_theme = $m->user->getTheme();

/*=================================================
 Submenu info
=================================================*/
$px_submenu->addItem(__('Back to the list of resources'), 'index.php', 
                      'themes/'.$_px_theme.'/images/ico_back.png', false);
$px_submenu->addItem(__('List the types'), 'subtypes.php', 
                     'themes/'.$_px_theme.'/images/ico_subtype.png', false);
$px_submenu->addItem(__('New type'), 'subtypes.php?op=add', 
                     'themes/'.$_px_theme.'/images/ico_new.png', false);

/*=================================================
 Set all the default values
=================================================*/
$px_id         = '';
$px_type_id    = '';
$px_name       = '';
$px_website_id = '';
$px_template   = '';
$px_cachetime  = '';
$px_extra1     = '';
$px_extra2     = '';
$px_inuse      = true;

/*==============================================================================
 Process block
==============================================================================*/
// get list of subtypes
if (empty($_REQUEST['op']) && empty($_REQUEST['s_id'])) { 
    $px_subtypes = $m->getSubTypes();
} 

// get data for requested subtype 
if (!empty($_REQUEST['s_id'])) {
    if (false !== ($px_subtype = $m->getSubTypes($_REQUEST['s_id']))) {
        $px_id         = $px_subtype->f('subtype_id');
        $px_type_id    = $px_subtype->f('type_id');
        $px_name       = $px_subtype->f('subtype_name');
        $px_template   = $px_subtype->f('subtype_template');
        $px_cachetime  = $px_subtype->f('subtype_cachetime');
        $px_extra1     = $px_subtype->f('subtype_extra1');
        $px_extra2     = $px_subtype->f('subtype_extra2');
        
        $px_inuse = $m->isSubTypeUsed($px_id);
    }
}

/*=================================================
 Save/Add the type
=================================================*/
if (!empty($_POST['save'])) {

    $px_type_id    = trim($_POST['s_type_id']);
    $px_name       = trim($_POST['s_name']);
    $px_template   = trim($_POST['s_template']);
    $px_cachetime  = trim($_POST['s_cachetime']);
    $px_extra1     = (isset($_POST['s_extra1'])) ? trim($_POST['s_extra1']) : '' ;
    $px_extra2     = (isset($_POST['s_extra2'])) ? trim($_POST['s_extra2']) : '' ;
		
	if (false !== ($id = $m->saveType($px_id, $px_type_id, $px_name, $px_template, $px_cachetime, $px_extra1, $px_extra2))) {
		$m->setMessage(__('The type was successfully saved.'));
		header('Location: subtypes.php');
		exit; 
	}	
} 

if (!empty($_POST['delete'])) {
	if (false !== ($id = $m->deleteType($px_id))) {
		$m->setMessage(__('The type of resource was successfully deleted.'));
		header('Location: subtypes.php');
		exit; 
	}	
} 

	 
$arry_templates = $m->getTemplates('resource');
/*==============================================================================
 Display block
==============================================================================*/

/*=================================================
 Set title of the page, and load common top page
=================================================*/
$px_title =  __('Resource types');
include dirname(__FILE__).'/mtemplates/_top.php';

echo '<h1 id="title_subtypes">'. __('Resource types')."</h1>\n\n";

/*=================================================
 Add/Edit a subtype
=================================================*/
if (!empty($_REQUEST['s_id']) or !empty($_REQUEST['op'])):
?>

<form action="subtypes.php" method="post" id="formPost" class="subtypes_style">
  <p class="field"><span><label class="float" for="s_type_id" style="display:inline"><strong><?php  echo __('Type of:'); ?></strong></label>
  	<?php echo form::combobox('s_type_id', 
  			array(  __('Article') => 'articles',   __('News') => 'news', __('Event') => 'events', __('Rsslink') => 'rsslinks'), 
  			$px_type_id,
  			'','', '',
  			"onChange=\"openCloseBlockIfArray('p_extra1','s_type_id',['news'],1,-1);\""
  		); ?>
  		</span>
  </p>

  <p class="field"><label class="float" for="s_name" style="display:inline"><strong><?php  echo __('Name of the type:'); ?></strong></label>
  <?php echo form::textField('s_name', 30, 30, $px_name, '', ''); ?> 
  </p>
  
  <p class="field"><span><label class="float" for="s_template" style="display:inline"><strong><?php  echo __('Template:'); ?></strong></label>
  <?php echo form::combobox('s_template', $arry_templates, $px_template); ?></span>
  </p>

  <p class="field"><label class="float" for="s_cachetime" style="display:inline"><strong><?php  echo __('Cache time in seconds:'); ?></strong></label>
  <?php echo form::textField('s_cachetime', 10, 20, $px_cachetime, '', ''); ?>
  </p>  
  
  <?php $p_extra1_style = ($px_type_id == 'news') ? '' : ' style="display: none"'; ?>
  
  <p class="field" id="p_extra1"<?php echo $p_extra1_style; ?>><span><label class="float" for="s_extra1" style="display:inline"><strong><?php  echo __('Have associated link to news items:'); ?></strong></label>
  <?php echo form::combobox('s_extra1', array(  __('Yes') => '1',  __('No') => '0'), $px_extra1); ?></span>
  </p>

  <p><?php echo __('Id to use in the templates:').' <strong>'.$px_id.'</strong>'; ?></p>

  <?php
  if (!empty($px_id)) {
   	 echo form::hidden('s_id',$px_id);
  } else { 
	 echo form::hidden('op','add');
  }
  ?>
  <p class="button"> <input name="save" type="submit" class="submit" value="<?php  echo __('Save [s]'); ?>"
  accesskey="<?php  echo __('s'); ?>" />  
  <?php 
    if (!$px_inuse) {
	echo '&nbsp;<input name="delete" type="submit" class="submit" '.'value="'.__('Delete [d]').'" accesskey="'.__('d').'" onclick="return '.
	'window.confirm(\''.addslashes( __('Are you sure you want to delete this type of resource?')).'\')" />';
   
  }
  ?>
  </p>
</form>

<?php
endif;

/*=================================================
 Display the list of subtypes
=================================================*/
if (empty($_REQUEST['op']) && empty($_REQUEST['s_id'])):

$px_news_title = '<h2>'. __('Types of news').'</h2>'."\n";
$px_articles_title = '<h2>'. __('Type of articles').'</h2>'."\n";

$current = ''; //$px_subtypes->f('type_id');

if ($px_subtypes->nbRow() == 0) {
	echo '<p>'.sprintf( __('You need to <a href="%s">add 4 types of resource</a>, one for the articles, one for the news, one for the events and on for the rss links.'), 'subtypes.php?op=add').'</p>';
}
    
while (!$px_subtypes->EOF()) {
    if ($current != $px_subtypes->f('type_id')) {
        if ($px_subtypes->getIndex() != 0) {
            echo "</ul>\n";
        }
        $current = $px_subtypes->f('type_id');
        $subTitle = '<h2>'. __('Types of '.$px_subtypes->f('type_id')).'</h2>'."\n";
        echo $subTitle;
        /*
        if ($current == 'articles') {
            echo $px_articles_title;
        } else {    
            echo $px_news_title;
        }
        */    
        echo "<ul class='subtypes_style'>\n";
    }
    echo '<li><a href="subtypes.php?s_id='.$px_subtypes->f('subtype_id').'">'.$px_subtypes->f('subtype_name').'</a> ['.__('Id to use in the templates:').' <strong>'.$px_subtypes->f('subtype_id').'</strong> ] </li>'."\n";
    
    $px_subtypes->moveNext();
}
echo "</ul>\n";
endif; 

/*=================================================
 Load common bottom page
=================================================*/
include dirname(__FILE__).'/mtemplates/_bottom.php';
?>
