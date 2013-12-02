<?php 
if ($cache->processPage(180)):
    pxTemplateInit(); 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php pxInfo('encoding'); ?>" />
<meta name="MSSmartTagsPreventParsing" content="TRUE" />
<title><?php pxNewsTitle('%s'); ?></title>
<?php include(dirname(__FILE__).'/inc/head-link.php'); ?>
<meta name="description" content="<?php pxNewsTitle('%s'); ?>" />
<meta name="DC.Date.modified" scheme="W3CDTF" content="<?php pxNewsDatePublication('%Y-%m-%d'); ?>" />
<?php include(dirname(__FILE__).'/inc/head-meta.php'); ?>
<meta name="DC.Author" content="<?php pxResAuthor(); ?>" />
<meta name="DC.Title" content="<?php pxNewsTitle('%s'); ?>" />
</head>

<body class="news">

<div id="page">
     <div id="banner">
	   <?php include(dirname(__FILE__).'/inc/easy-access.php'); ?>
     <?php pxSingleCatTree('<ol class="tree">%s</ol>'); ?>
     </div><!-- end banner -->

     <div id="mainfloat">
     <div id="menuleft">
	        <div class="col-content-green">
		      <div class="menuleft-green-top"></div>
		      <?php pxSubCategories(__('<h2>Categories</h2> %s')); ?>
		      <h2><?php echo __('Main categories') ?></h2>
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
          <div class="resource-blue">
		      <div class="resource-blue-left"></div>
		      <div class="resource-content">
		      <h2><?php pxNewsTitle('%s'); ?></h2>
          <p class="modified"><?php echo __('The') ?> <a href="<?php pxNewsPath(); ?>"><?php pxNewsDateCreation(__('%Y-%m-%d</a> at %H:%M')); ?> <?php echo __('by') ?> <a href="<?php pxNewsAuthorEmail('mailto:%s'); ?>"><?php pxNewsAuthor(); ?></a>. <?php pxNewsCategories(__('In %s')); ?>.</p>
          <span class="lireplus"><?php echo __('Number of comments:') ?> <?php pxNewsCountComments() ?> </span>
          </div>
          <div class="resource-blue-right"></div>
          </div><!-- end resource -->
		      <div class="resource-desc-blue">
		      <div class="resource-desc-head"></div>
          <?php pxNewsContent(); ?>
		      <div class="resource-desc-foot"></div>
		      </div>

          <div class="chapitre">
          <?php include dirname(__FILE__).'/comments_inline.php'; ?>
          </div>

		      <hr class="invisible" />
     </div><!-- end content -->
     </div><!-- end mainfloat -->

<?php include(dirname(__FILE__).'/inc/footer.php'); ?>
<?php
    $cache->endCache();
endif;
?>