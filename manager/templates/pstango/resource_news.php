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

<body>

<div id="page">

<!-- Beginning Banner -->
<?php include(dirname(__FILE__).'/inc/banner.php'); ?>
<!-- End of Banner -->

<div id="main">
<!-- Beginning breadcrumb -->
<?php pxSingleCatTree('<ol class="tree">%s</ol>'); ?>
<!-- End Breadcrumb -->

<!-- Beginning menu -->
<div id="menu">
  <?php include(dirname(__FILE__).'/inc/menu.php'); ?>
</div>
<!-- end menu -->

<!-- beginning content -->
<div id="content">

<h2 class="news"><?php pxNewsTitle('%s'); ?></h2>
    <div class="resource">
    	<div class="infos">
           <p><span class="datetime"><?php pxNewsDateCreation(__('%Y-%m-%d at %H:%M')); ?></span>
	   <?php echo __('by'); ?> <span class="author"><a href="<?php pxNewsAuthorEmail('mailto:%s'); ?>"><?php pxNewsAuthor(); ?></a></span></p>
           <p><span class="cat"><?php pxNewsCategories(__(' In %s'), ', ', __(' and ')); ?></span></p>
           <p><span class="link"><a href="<?php pxNewsPath('fullurl'); ?>"><?php pxNewsPath('fullurl'); ?></a></span></p>
           <p><span class="comments"><?php echo __('Number of comments:'); ?></span> <a href="#comments" title="<?php echo __('Comments'); ?>"><?php pxNewsCountComments(); ?></a></p>
         </div>
   </div>
         
    <div id="news-content">
      <?php pxNewsContent(); ?>
      <?php pxNewsAssociatedLink('<p class="associated-link"><a href="%1$s">%2$s</a></p>'); ?>
    </div>
    <?php //absolute path for comments permalinks
    $respath = pxNewsPath('fullurl', true); ?>
    <h2 id="comments"><?php echo __('Comments'); ?></h2>
      <div class="resource">
      <?php include dirname(__FILE__).'/comments_inline.php'; ?>
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