<?php pxTemplateInit(); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php pxInfo('encoding'); ?>" />
<meta name="MSSmartTagsPreventParsing" content="TRUE" />
<title>Comments - <?php pxInfo('name'); ?></title>
<?php include(dirname(__FILE__).'/inc/head-link.php'); ?>
<meta name="description" content="<?php pxInfo('description'); ?>" />
<?php include(dirname(__FILE__).'/inc/head-meta.php'); ?>
<meta name="DC.Title" content="Comments - <?php pxInfo('name'); ?>" />
</head>

<body class="category">

<div id="page">
     <div id="banner">
	   <?php include(dirname(__FILE__).'/inc/easy-access.php'); ?>
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
         <h2 class="comment-preview"><?php echo __('Comment') ?></h2>
         <?php
         while (!$res->cur->comments->EOF()) {
         echo $res->cur->comments->getContent();
         $res->cur->comments->moveNext();
         }
         ?>

         <hr class="invisible" />
    </div><!-- end content -->
    </div><!-- end mainfloat -->

<?php include(dirname(__FILE__).'/inc/footer.php'); ?>