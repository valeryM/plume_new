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

if (basename($_SERVER['SCRIPT_NAME']) == 'news-list.php') exit;


echo '<form action="news.php" method="get">';
echo '<p id="resource-select">';
echo '<label for="m" style="display:inline;"><strong>'. __('Month:').' </strong></label>';
echo form::comboBox('m',$arry_months, $px_m);
//echo ' <label for="cat_id" style="display:inline;"><strong>'. __('Category:').' </strong></label>';
/*
echo form::comboBox('cat_id',$arry_cat,$cat_id);
echo ' <input type="hidden" name="op" id="op" value="list" /><input class="submit" type="submit" value="'. __('ok').'" />';
echo '</p></form>';
*/
//echo '<input name="location" type="hidden" style="height:20px" value="'.$location.'">';
//echo '<input type="hidden" name="cat_id" value="allcat">';
echo '<input type="hidden" name="op" id="op" value="list" />';
echo '<input type="hidden" name="p" value="news">';
$location = PathSelector::getLocation();
echo PathSelector::getCategoryPathSelector($location,__('Category:'));

//type="submit"
//echo '<input class="submit" type="submit" value="'. __('ok').'" />';

// Ajout bouton pour le fil rouge
/*
echo '<span  style="position:relative">';
echo '	<span class="filRouge" >&nbsp;</span>';
echo '	<span style="position:relative;left:30px;">';

$checked = ($fltFilRouge == 1) ? true : false;
echo form::checkbox('fltFilRouge', $fltFilRouge ,$checked,'','onclick="if (this.checked) this.value=1; else $this.value=0;"' );
echo '		<label for="filRouge" style="display:inline" >';
echo __('filtreFilRouge');
echo '		</label>';
echo '	</span>';
echo '</span>';
*/

//echo '<button class="filterButton"  title="'. __('Apply filter').'" ></button>&nbsp';
//echo '<button class="resetButton" title="'. __('reset the filter').'" ></button>';
echo '</p>';
echo '</form>';

if ($res->isEmpty()) {
    echo '<p class="noresource">'. __('No news.').'</p>'."\n\n";
} else {
    echo '<script type="text/javascript">'."\n<!--\n".
        "var js_post_ids = new Array('".implode("','",$res->getIDs('resource_id', 'content'))."');\n".
        "//-->\n</script>\n";
        
    echo '<p id="showhide"><a href="#" onclick="mOpenClose(js_post_ids,1); return false;">'. __('Show all').'</a>'.
        ' - <a href="#" onclick="mOpenClose(js_post_ids,-1); return false;">'. __('Hide all').'</a></p>';
        
   
    while (!$res->EOF()) {
    	if ($m->asRightToView($res))  {	//$m->asRightToView($res)
    		$copylinks = '';
    		$editlinks='';
    		// copy link
    		if ($m->asRightToCopy($res) ) {
    			$copylinks = '[<span class="editlink"><a href="'.$res->f('type_id').'.php?do=copy&resource_id='.$res->f('resource_id').'">'.__('copy').'</a></span>]';
    		}    	
	        // edition links 
	        //if ($m->user->f('user_id') == $res->f('user_id') || auth::asLevel(PX_AUTH_ADVANCED, $_SESSION['website_id'])) {
	        if ($m->asRightToEdit($res)) {
	            $editlinks = ' [<span class="editlink"><a href="'.$res->f('type_id').'.php?resource_id='.$res->f('resource_id').'">'. __('edit').'</a></span>]';
	        } else {
	            $editlinks = ' [<span class="editlink"><a href="'.$res->f('type_id').'.php?resource_id='.$res->f('resource_id').'">'. __('visualize').'</a></span>]';      
	        }
	        //Link to see the resource on the site
	        $seeonweblink = ' [<span class="link_style"><a target="_preview" href="'.$res->getPath().'">'.__('See the news').'</a></span>]';
	        switch ($res->f('status')) {
	        case PX_RESOURCE_STATUS_OFFLINE:
	            $res_class = 'cancel';
	            $res_img = '<img src="themes/'.$_px_theme.'/images/check_off.png" title="'.__('Resource off-line').'" alt="'.__('Resource off-line').'" class="status" />';
	            break;
	        case PX_RESOURCE_STATUS_VALIDE:
	            $res_class = 'published';
	            $res_img = '<img src="themes/'.$_px_theme.'/images/check_on.png" title="'.__('Resource on-line').'" alt="'.__('Resource on-line').'" class="status" />';
	            break;
	        case PX_RESOURCE_STATUS_TOBEVALIDATED:
	            $res_class = 'published';
	            $res_img = '<img src="themes/'.$_px_theme.'/images/check_wait.png" title="'.__('Resource waiting for validation').'" alt="'.__('Resource waiting for validation').'" class="status" />';
	            break;
	        case PX_RESOURCE_STATUS_INEDITION:
	        default:
	            $res_class = 'published';
	            $res_img = '<img src="themes/'.$_px_theme.'/images/check_edit.png" title="'.__('Resource in edition').'" alt="'.__('Resource in edition').'" class="status" />';
	            break;
	        }
	        echo '<div class="resourcebox '.$res_class.'" id="p'.$res->f('resource_id').'">'.
	            '<a href="#" onclick="openCloseSpan(\'content'.$res->f('resource_id').'\',0); return false;" title="'.__('Show/hide').'">'.
	            '<img src="themes/'.$_px_theme.'/images/plus.png" class="show_button" id="img_content'.$res->f('resource_id').'" '.
	            'alt="'. __('show/hide').'" /></a> ';
	        echo $res_img;
	        
	        /*
			if ($res->f('filRouge')==1) {
				echo '<img src="themes/'.$_px_theme.'/images/trombone_007.png" title="'.__('statusFilRouge').'" alt="'.__('statusFilRouge').'" class="statusFilRouge" /> ';
			}
	        */
	        echo '<p class="resource_title"><span class="news_style">'.$res->f('title').'</span> - '.__('by');
		
	        $temp = '';
	        while (!$res->extEOF('authors')) {
	            //Testing the level of the user to define if the
	            //edition link could be added
	            if ((auth::asLevel(PX_AUTH_ROOT) 
	                 && $res->extf('authors','user_id') == 1) 
	                || (auth::asLevel(PX_AUTH_ADMIN) 
	                    && $res->extf('authors','user_id') != 1)) {
	                $edituser = ' <span class="author_style"><a href="users.php?user_id='.$res->extf('authors','user_id').'" title="'.__('Edit author profile').'">'.$res->extf('authors','user_realname').'</a></span>';
	            } else {
	                $edituser = ' <span class="author_style">'.$res->extf('authors','user_realname').'</span>';
	            }
	            //end testing level user
	            $temp .= $edituser;
	            $res->extMoveNext('authors');
	        }
	        echo $temp;
	        echo ' - '. __('in');
	        $temp = array();
	        $base = '<span class="category_style">%s%s%s</span>%s';
	        while (!$res->extEOF('cats')) {
	            $_def = array('', $res->extf('cats','category_name'), '', '');
	            if (auth::asLevel(PX_AUTH_ADVANCED)) {
	                $_def[0] = '<a href="index.php?cat_id='.$res->extf('cats','category_id').'" title="'.__('Resource list').'">';
	                $_def[2] = '</a>';
	            }
	            $_def[3] = '<span class="small">&nbsp;('.$res->extf('cats','category_path').')</span>';
	            $temp[] = vsprintf($base, $_def);
	            $res->extMoveNext('cats');
	        }
	        echo ' '.implode(', ', $temp)."<br />\n";
		    echo '<span class="date-time">'.date( __('Y/m/d at H:i:s'),date::unix($res->f('modifdate')));
	        echo "</span>".$editlinks.$copylinks . $seeonweblink."</p>\n\n";
	        echo '<div id="content'.$res->f('resource_id').'" class="hided" style="display:none;">';
	        echo "<div class=\"description_style\">\n".$res->cur->getFormattedContent('description')."</div>\n";
	        echo "\n<p class='idmakelink'>".__('Id to make a link:').' '.$res->f('identifier')."</p>\n<hr class='invisible' /></div></div>\n\n";    
    	}
    	$res->moveNext();
    }
}
    
/* ================================================= *
 *            Form to search in the news             *
 * ================================================= */
echo '<form action="news.php" method="get"><p id="search">';
echo '<label for="q" style="display:inline;"><strong>'.
__('Search for a news item:').' </strong></label>';
echo form::textField('q', 30, 255, $px_q); 
echo ' <input class="submit" type="submit" value="'.
__('ok').'" /><input type="hidden" name="op" value="list" />';
echo '</p></form>'."\n\n";
    
?>
