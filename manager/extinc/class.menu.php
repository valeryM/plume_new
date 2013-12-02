<?php
/* -*- tab-width: 4; indent-tabs-mode: nil; c-basic-offset: 4 -*- */
/*
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
#  Loïc d'Anterroches - Add accesskey and first/last class support
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
# ***** END LICENSE BLOCK ***** */

/**
 * Classe Menu
 */

class menu
{
    /**
     * Constructor
     *
     * @param string Id of the menu
     * @param string Character to put after the image tag of the menu item
     * @param string Character to put just before the </li> in the list
     */
    function menu($id, $imgSpace='', $itemSpace='')
    {
        $this->id = $id;
        $this->imgSpace = $imgSpace;
        $this->itemSpace = $itemSpace;
        $this->items = array();
    }
    
    /**
     * Add a new item to the menu
     *
     * @param string Title of the menu link
     * @param string Url for the link
     * @param string Url of the link image
     * @param bool Active to get the "active" class set
     * @param bool Show the item (true)
     * @param string Accesskey for the item ('')
     */
    function addItem($title, $url, $img='', $active=false, $show=true, $accesskey='')
    {
        if($show) {
            if (is_array($url)) {
                $link = $url[0];
                $ahtml = (!empty($url[1])) ? ' '.$url[1] : '';
            } else {
                $link = $url;
                $ahtml = '';
            }
            if (strlen($accesskey) >= 1) {
                    $title2 = preg_replace('/('.$accesskey.')/i', '<span style="text-decoration: underline;">\1</span>', $title, 1);
                if ($title2 == $title) {
                    $title = $title.' <span class="accesskey">['.$accesskey.']</span>';
                } else {
                    $title = $title2;
                }
                $ahtml = ' accesskey="'.$accesskey.'"';
            }
            if ($link!='') {
           		$item = '<li'.(($active) ? ' class="active"' : ' class=""').'>';
            	$item .= '<a href="'.$link.'"'.$ahtml.'>';
            } else {
            	$item = '<span  '.(($active) ? ' class="active"' : ' class="" ').' style="padding:5px;float:left;display:inline-block;">';
            }
            if ($img!='') 
            	$item .= '<img src="'.$img.'" style="text-decoration: none;" alt="identification icon" />';
            if ($link != '') $item .= '</a>';
            $item .= $this->imgSpace;
            if ($link != '') 
            	$item .= '<a href="'.$link.'"'.$ahtml.'>';
            $item .= $title;
            if ($link != '') $item .= '</a>';
                      
            if ($link!='') 
            	$item .= '</li>'."\n";
           	else 
           		$item .= '</span>'."\n";
            $this->items[] = $item;
            /*
            $this->items[] = '<li'.(($active) ? ' class="active"' : ' class=""').'>'.
            		'<a href="'.$link.'"'.$ahtml.'>'.
            		(($img!='') ? 
            				'<img src="'.$img.'" style="text-decoration: none;" alt="identification icon" /></a>'.$this->imgSpace.'<a href="'.$link.'"'.$ahtml.'>' 
            				: ''
            				).
            		$title.'</a></li>'."\n";
            */
            
        }
    }

    /**
     * Returns the HTML code of the menu.
     *
     * @return string HTML code
     */
    function draw()
    {
    	$classMenu = ($this->id == 'menu') ? 'menuTop' : 'menuBottom';
        $res = '<ul id="'.$this->id.'" class="'.$classMenu.'">'."\n";
        $count = count($this->items);
        if ($count > 0) {
            for ($i=0; $i<$count; $i++) {
                if (0 == $i) {
                    $this->items[$i] = preg_replace('|class="active"|', 'class="active first"', $this->items[$i]);
                    $this->items[$i] = preg_replace('|class=""|', 'class="first"', $this->items[$i]);
                }
                if ($i+1 == $count) {
                    $this->items[$i] = preg_replace('|class="active"|', 'class="active last"', $this->items[$i]);
                    $this->items[$i] = preg_replace('|class=""|', 'class="last"', $this->items[$i]);
                }
                if ($i+1 < $count && $this->itemSpace != '') {
                    $res .= preg_replace('|</li>$|',$this->itemSpace.'</li>',$this->items[$i]);
                    $res .= "\n";
                } else {
                    $res .= $this->items[$i]."\n";
                }
            }
        } else {
            $res .= '<li>&nbsp;</li>';
        }
        $res .= '</ul>'."\n";
        // script to accept click not only on the link <a>
        $res .= '<script type="text/javascript" >'."\n";
        $res .= '$(document).ready(function() {'."\n";
        $res .= '    $(".'.$classMenu.' li").click(function() {'."\n";
        $res .= '        window.location=$(this).children("a").attr("href");'."\n";
        $res .= '     });'."\n";
        $res .= '});'."\n";
        $res .= '</script>'."\n"; 
               
        $res = preg_replace('|class=""|', '', $res);
        
        return $res;
    }
}
?>
