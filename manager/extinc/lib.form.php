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
 
function php_f_combobox($name,$arryData,$default='',$tabindex='',$class='',$id='', $extra='')
{
	$res = '<select name="'.$name.'" ';
	
	if($class != '')
		$res .= 'class="'.$class.'" ';
	
	if($id != '')
		$res .= 'id="'.$id.'" ';
	else
		$res .= 'id="'.$name.'" ';
	$res .= ($tabindex != '') ? 'tabindex="'.$tabindex.'" ' : '';
	if($extra != '')
		$res .= $extra.' ';
	$res .= '>'."\n";
	
	foreach($arryData as $k => $v)
	{
		$res .= '<option value="'.$v.'"';
		
		if($v == $default)
			$res .= ' selected="selected"';
		
		$res .= '>'.$k.'</option>'."\n";
	}
	
	$res .= '</select>'."\n";
	
	return $res;
}

function php_f_textField($id,$size,$max,$default='',$tabindex='',$html='')
{
	$res = '<input type="text" size="'.$size.'" name="'.$id.'" id="'.$id.'" ';
	
	$res .= ($max != '') ? 'maxlength="'.$max.'" ' : '';
	$res .= ($tabindex != '') ? 'tabindex="'.$tabindex.'" ' : '';
	$res .= ($default != '') ? 'value="'.$default.'" ' : '';
	$res .= $html;
	
	$res .= ' />';
	
	return $res;
}

function php_f_textArea($id,$cols,$rows,$default='',$tabindex='',$html='')
{
	$res = '<textarea cols="'.$cols.'" rows="'.$rows.'" ';
	$res .= 'name="'.$id.'" id="'.$id.'" ';
	$res .= ($tabindex != '') ? 'tabindex="'.$tabindex.'" ' : '';
	$res .= $html.'>';
	$res .= $default;
	$res .= '</textarea>';
	
	return $res;
}

function php_f_button($type='submit',$value='ok',$id='',$tabindex='')
{
	$res = '<input type="'.$type.'" value="'.$value.'" ';
	$res .= ($id != '') ? 'name="'.$id.'" id="'.$id.'" ' : '';
	$res .= ($tabindex != '') ? 'tabindex="'.$tabindex.'" ' : '';
	$res .= ' />';
	
	return $res;
}

function php_f_hidden($id,$value)
{
	return '<input type="hidden" name="'.$id.'" id="'.$id.'" value="'.$value.'" />';
}

function php_f_checkbox($id, $value, $checked=false,$tabindex='',$html='')
{
	$res = '<input type="checkbox" value="'.$value.'" ';
	$res .= 'name="'.$id.'" id="'.$id.'" ';
	$res .= ($tabindex != '') ? 'tabindex="'.$tabindex.'" ' : '';
    $res .= ($checked) ? 'checked="checked" ' : '';
	$res .= $html.' />';
	
	return $res;
}
?>
