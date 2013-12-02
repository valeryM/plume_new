<?php
# ***** BEGIN LICENSE BLOCK *****
# This file is part of Teditor, plugin for Plume CMS
# Copyright (C) 2004-2006 Gilles ACCAD.
#
# Teditor is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.
#
# Teditor is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
#
# ***** END LICENSE BLOCK ***** */

if (!auth::asLevel(PX_AUTH_ROOT)) {
   $m->setError(__('You do not have the rights to access this plugin.'));
} 
else {
    /**************************************************************************
    /	Initialisation
    /*************************************************************************/
    include_once dirname(__FILE__).'/teditor.init.php';
    include_once dirname(__FILE__).'/teditor.class.php';

    $edition = new teditor();

    /**************************************************************************
    /	Affichage
    /*************************************************************************/

    if (($_SERVER['QUERY_STRING']=='p=teditor') || (substr(($_SERVER['QUERY_STRING']),0,13)=='p=teditor&msg')) {
        $px_submenu->addItem( __('Templates list'),'tools.php?p=teditor','',0, 0);
    } else {
        $px_submenu->addItem( __('Templates list'),'tools.php?p=teditor','',0, 1);
        $px_submenu->addItem( __('Categories templates'),'tools.php?p=teditor&amp;w=categ','','', $edition->isLnkActive('categ'));
        $px_submenu->addItem( __('Resources templates'),'tools.php?p=teditor&amp;w=res','','', $edition->isLnkActive('res'));
        $px_submenu->addItem( __('Comments templates'),'tools.php?p=teditor&amp;w=com','','', $edition->isLnkActive('com'));
        $px_submenu->addItem( __('Other templates'),'tools.php?p=teditor&amp;w=other','','', $edition->isLnkActive('other'));
        $px_submenu->addItem( __('CSS'),'tools.php?p=teditor&amp;w=css','','', $edition->isLnkActive('css'));
        $px_submenu->addItem( __('Configuration'),'tools.php?p=teditor&amp;w=conf','','', $edition->isLnkActive('conf'));
    }


    if (isset($_GET['w'])) {
    	switch ($_GET['w']) {
    		case "categ" :
    			echo '<h2>'.__('Category template edition')."</h2>\n"; 
    			echo $edition->renderEditor($edition->arry_templates_c,'',__('Choose the template to edit in the listbox above.'));
    		break;
    	    
    		case "res" :
    			echo '<h2>'.__('Resource template edition')."</h2>\n";     
    			echo $edition->renderEditor($edition->arry_templates_r,'',__('Choose the template to edit in the listbox above.'));
    		break;
    		
    		case "com" :
    			echo '<h2>'.__('Comments template edition')."</h2>\n";     
    			echo $edition->renderEditor($edition->arry_templates_cm,'',__('Choose the template to edit in the listbox above.'));
    		break;  
    	    
    		case "other" :  
    			echo '<h2>'.__('Other templates edition')."</h2>\n";     
    			echo $edition->renderEditor($edition->arry_templates_o,'',__('Choose the template to edit in the listbox above.'));
    		break;
    		
    		case "css" :  
    			echo '<h2>'.__('CSS edition')."</h2>\n";
    			if ($edition->listTemplates($edition->getCSS(),'css')){
    				echo $edition->renderEditor($edition->getCSS(),'css',__('Choose the template to edit in the listbox above.'));
    			} else {
    				echo '<p style="color:red">'.__('No CSS files. Check your <a href="tools.php?p=teditor&amp;w=conf">configuration</a> settings')."</p>\n";
    			}
    		break;
    		
    		case "conf" :
    			echo '<h2>'.__('Plugin configuration')."</h2>\n";       
    			if (isset($_POST['b_saved'])) {
    				$edition->saveConfig();
    				echo  $edition->frmShowConfig();				
    			} else {
    				echo  $edition->frmShowConfig();
    			}
    		break;
    	
    		default :
    			echo $edition->showTemplatesList();
    	}
    } else { 
    	if (isset($_POST['b_tcreation'])) {
    		if(empty($_POST['c_tcreation'])) {
    			$m->setError(__('Error : you must give a name'),400);
    		} else  {
    			echo $edition->createTemplate($_POST['c_tcreation']);
    		}
    	} elseif (isset($_POST['b_tdelete'])) {
    		$edition->delTemplate($_POST['c_delete']);
        } else {
    		echo '<h2>'.__('Templates operations')."</h2>\n";   
    		echo $edition->frmCreateTemplate();
    		echo $edition->showTemplatesList();
    	}
    }
}
?>
