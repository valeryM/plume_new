<?php
if ($cache->processPage(3600)):
	pxTemplateInit('remove_numbers');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php pxInfo('encoding'); ?>" />
<meta name="MSSmartTagsPreventParsing" content="TRUE" />
<title><?php pxInfo('name'); ?></title>
<?php include(dirname(__FILE__).'/inc/head-link.php'); ?>
<link rel="schema.DC" href="http://purl.org/dc/elements/1.1/" />
<?php pxHeadLinks(); ?>
<meta name="DC.title" content="<?php pxInfo('name'); ?>" />
<?php include(dirname(__FILE__).'/inc/head-meta.php'); ?>
</head>

<body class="category">

<div id="page">
     <div id="frame_header"></div>
     <div id="banner">
          <div id="banner_body">
               <?php include(dirname(__FILE__).'/inc/easy-access.php'); ?>
               <?php include(dirname(__FILE__).'/inc/banner-content.php'); ?>
          </div>
          <div id="banner_right"></div>
     </div>

     <div class="contenair">
          <div id="secontenair">

          <!-- menu -->
          <div id="col-content">
               <h2 class="categories"><?php echo __('Categories') ?></h2>
                    <?php pxPrimaryCategories('<ul id="top-categories">%s</ul>'); ?>

               <h2 class="news"><?php echo __('Recent news') ?></h2>
               <?php include(dirname(__FILE__).'/inc/recent-news.php'); ?>

               <h2 class="extra"><?php include(dirname(__FILE__).'/inc/rss-sitemap.php'); ?></h2>

               <div id="menuleft_foot"></div>
          </div> <!-- end menu -->

          <div id="main_frame">
               <div id="content">
                    <div class="resource">
                         <h2 class="restitle"><?php echo __('Sitemap') ?></h2>
                         <div class="footer_titre"></div>
                         <div class="resource_article">
                         <?php pxShowSitemap('all', 25); ?>
                         </div>
                         <hr class="invisible"/>
                    </div>
               </div>
          </div> <!-- end main -->

          </div>
     </div>

     <div class="contenair">
          <div id="menu_mask"></div>
     </div>
     
<?php 
include(dirname(__FILE__).'/inc/footer.php');
endif;
?>