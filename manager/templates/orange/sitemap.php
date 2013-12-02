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
          <?php include(dirname(__FILE__).'/inc/banner-content.php'); ?>
     </div> <!-- end banner -->
     
     <div id="col-content">
     <div id="col-content-head"></div>
          <div class="menuright">
          <div class="menuright_head"></div>
               <div class="col-content">
               <?php include(dirname(__FILE__).'/inc/recent-news.php'); ?>
               </div>
          <div class="menuright_foot"></div>
          </div>
	  
          <div class="menuright">
          <div class="menuright_head"></div>
               <div class="col-content">
               <?php include(dirname(__FILE__).'/inc/search-form.php'); ?>
               </div>
          <div class="menuright_foot"></div>
          </div>

          <div class="menuright">
          <div class="menuright_head"></div>
               <div class="col-content">
               <h2 class="links"><?php echo __('Links') ?></h2>
               <?php pxLink::linkList(); ?>
               </div>
          <div class="menuright_foot"></div>
          </div>

          <?php include(dirname(__FILE__).'/inc/rss-sitemap.php'); ?>
     <div id="col-content-foot"></div>
     </div>
     
     <div id="content">
          <div class="resource">
          <div class="resource_head"></div>
          <h2 class="restitle"><?php echo __('Sitemap') ?></h2>
          <p class="modified"><?php echo __('All the content of the website in one eye blink') ?></p>
          </div>
          <div class="resource_foot"></div>
          
          <div class="art-description">
          <div class="art-description_head"></div>
          <div id="plan-site">
          <?php pxShowSitemap('all', 25); ?>
          </div>
          <div class="art-description_foot"></div>
          </div>

          <hr class="invisible"/>
     </div>

<?php 
include(dirname(__FILE__).'/inc/footer.php'); 
endif;
?>