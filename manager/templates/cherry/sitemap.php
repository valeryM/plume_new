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

<body class="category">

<div id="page">
     <div id="banner">
	   <?php include(dirname(__FILE__).'/inc/easy-access.php'); ?>
	   <ol class="tree"><li><?php echo __('Site Map') ?></li></ol>
	   </div><!-- end banner -->

    <div id="mainfloat">
    <div id="menuleft">
         <div class="col-content-green">
		     <div class="menuleft-green-top"></div>
		     <h2><?php echo __('Categories') ?></h2>
		     <?php pxPrimaryCategories('<ul id="top-categories">%s</ul>'); ?>
		     <div class="menuleft-green-bottom"></div>
		     </div>
		
         <div class="col-content-blue">
         <div class="menuleft-blue-top"></div>
         <?php include(dirname(__FILE__).'/inc/recent-news.php'); ?>
         <div class="menuleft-blue-bottom"></div>
         </div>

		     <div class="col-content-green">
		     <div class="menuleft-green-top"></div>
		     <?php include(dirname(__FILE__).'/inc/rss-sitemap.php'); ?>
		     <div class="menuleft-green-bottom"></div>
		     </div>

		     <div class="col-content-blue">
		     <div class="menuleft-blue-top"></div>
         <h2><?php echo __('Links') ?></h2>
		     <?php pxLink::linkList(); ?>
		     <div class="menuleft-blue-bottom"></div>
		     </div>
    </div><!-- end menuleft -->

    <div id="content">
		     <div id="sitemap">
         <?php pxShowSitemap('all', 25); ?>
         </div>
		
	       <hr class="invisible" />
    </div><!-- end content -->
    </div><!-- end mainfloat -->

<?php 
include(dirname(__FILE__).'/inc/footer.php'); 
endif;
?>