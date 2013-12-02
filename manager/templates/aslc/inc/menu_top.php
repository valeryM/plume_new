<div id="menu_top" class="menu">
<?php
	pxMenuPrimaryCategories('<ul id="myMenu" class="menu topnav">%s</ul>','<li class="topline">%s</li>',false,'category_position');
?>
</div>

<script type="text/javascript">

var myMenu=new menu.dd('myMenu',10);
myMenu.init('myMenu','menuhover');

</script>
