<div id="menu_top" class="menu">
<?php
	if (isset($GLOBALS['_PX_render']['mcat'])){
		$catId = $GLOBALS['_PX_render']['mcat']->f('category_id');
		//pxMyMenuPrimaryCategories($catId,'<ul id="myMenu" class="menu topnav">%s</ul>','<li class="topline">%s</li>',false,'category_position');
		pxAfficheMenuRubrique3($catId);
	}
?>
</div>
