<?php
if ($cache->processPage(3600)):
	pxTemplateInit('remove_numbers');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php pxInfo('encoding'); ?>" />
<meta name="MSSmartTagsPreventParsing" content="TRUE" />
<title>Site map of <?php pxInfo('name'); ?></title>
<?php include(dirname(__FILE__).'/inc/head-link.php'); ?>
<link rel="schema.DC" href="http://purl.org/dc/elements/1.1/" />
<?php pxHeadLinks(); ?>
<meta name="DC.title" content="Site map of <?php pxInfo('name'); ?>" />
<?php include(dirname(__FILE__).'/inc/head-meta.php'); ?>
</head>

<body>

<div id="page">

<!-- Beginning Banner -->
<?php include(dirname(__FILE__).'/inc/banner.php'); ?>
<!-- End of Banner -->

<div id="main">
<!-- Beginning breadcrumb -->
<ol class="tree"><li><a href="<?php pxInfo('url'); ?>"><?php echo __('Home'); ?></a></li></ol>
<!-- End Breadcrumb -->

<!-- Beginning Menu -->
<div id="menu">
  <?php include(dirname(__FILE__).'/inc/menu.php'); ?>
</div>
<!-- end Menu -->

<!-- Beginning content -->
<div id="content">
	
	<div id="sitemap">
	   <h2><?php echo __('Sitemap'); ?></h2>
	     <?php pxShowSitemap('all', 25); ?>
	</div>

<hr class="invisible" />

</div>
<!-- end content -->

</div>
<!-- end main -->

<?php include(dirname(__FILE__).'/inc/footer.php'); ?>
</div>
</body>
</html>
<?php
    $cache->endCache();
endif;
?>