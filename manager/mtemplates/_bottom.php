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

if (basename($_SERVER['SCRIPT_NAME']) == '_bottom.php') exit;

if (count($m->user->webs) > 1):
// propose the switch form
$arry_sites = array();
reset($m->user->webs);
foreach ($m->user->webs as $site => $score) {
    $arry_sites[$m->user->wdata[$site]['website_name']] = $site;    
}


?>
<div id='switch'>
<form action='index.php' method='post' id='formSwitch'>
<p>
<span class="nowrap"><label for="switchid"
  style="display:inline"><?php  echo __('<strong>Current site</strong>:'); ?></label> 
<?php echo form::combobox('switchid',$arry_sites,$m->user->website); ?>
<input type='submit' class="submit" value='<?php  echo __('Change'); ?>' />
<input name="goto" id="goto" type="submit" class="submit" value="<?php  echo __('See the site'); ?>" accesskey="<?php  echo __('i'); ?>" /></span>   
</p>
</form>
</div>
<?php else: ?>
<div id='switch'>
<p>
<?php  echo __('<strong>Current site</strong>:'); ?> <a href="index.php?goto=1&amp;switchid=<?php echo $m->user->website; ?>" accesskey="<?php  echo __('i'); ?>"><?php echo $m->user->wdata[$m->user->website]['website_name']; ?></a>
</p>
</div>
<?php endif;?>
 

</div>
</div>
<?php 
if ($px_title !=  __('Files and images')) 
	echo PathSelector::getScriptloader();
?>
<div id="footer">
<a href="http://www.plume-cms.net/"><img
src="themes/<?php echo $_px_theme; ?>/images/plume-cms-powered.png" alt="PLUME CMS" /></a></div>

</body>
</html>
<?php 
if (!empty($m->con) && is_object($m->con)) {
    $m->con->close();
}
showDebugInfo();
?>

