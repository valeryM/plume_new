<?php 

 ?>
 <!DOCTYPE html>
<html>
<head>
<head>
	<meta charset="utf-8" />
	<!-- Set the viewport width to device width for mobile -->
	<meta name="viewport" content="width=device-width" />

<!-- <meta http-equiv="Content-Type" content="text/html; charset=<?php pxInfo('encoding'); ?>" /> -->
<meta name="MSSmartTagsPreventParsing" content="TRUE" />
<title><?php pxInfo('name'); ?></title>
<?php include(dirname(__FILE__).'/inc/head-link.php'); ?>
<meta name="description" content="<?php pxInfo('name'); ?> - <?php pxMetasDescription(); ?>" />
<?php include(dirname(__FILE__).'/inc/head-meta.php'); ?>
</head>
<body>
	<div id="page">
		<?php include dirname(__FILE__).'/inc/banner.php'; ?>	
		<?php include dirname(__FILE__).'/inc/menu_top.php'; ?>
		<div id="main">
		</div>

	</div><!-- end page -->

</body>
</html>