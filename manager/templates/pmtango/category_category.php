<?php
if ($cache->processPage(180)):
    pxTemplateInit('order_res_manual|res_per_page:10|remove_numbers');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php pxInfo('encoding'); ?>" />
<meta name="MSSmartTagsPreventParsing" content="TRUE" />
<title><?php pxSingleCatTitle('%s'); ?> - <?php pxInfo('name'); ?></title>
<?php include(dirname(__FILE__).'/inc/head-link.php'); ?>
<meta name="description" content="<?php pxSingleCatTitle('%s'); ?> - <?php pxMetasDescription(); ?>" />
<?php include(dirname(__FILE__).'/inc/head-meta.php'); ?>
<meta name="DC.Title" content="<?php pxSingleCatTitle('%s'); ?> - <?php pxInfo('name'); ?>" />
</head>

<body>

<div id="page">

<!-- Beginning Banner -->
<?php include(dirname(__FILE__).'/inc/banner.php'); ?>
<!-- End of Banner -->

<div id="main">
<!-- Beginning Breadcrumb -->
<?php pxSingleCatTree('<ol class="tree">%s</ol>'); ?>
<!-- End Breadcrumb -->

<!-- Beginning Menu -->
<div id="menu">

  <?php include(dirname(__FILE__).'/inc/menu.php'); ?>
 
  <h2 class="back"><?php echo __('Back to'); ?></h2>
     <ul><li><a href="<?php pxParentCatPath(); ?>"><?php pxParentCatTitle('%s'); ?></a></li></ul>

</div>
<!-- End of Menu -->

<!-- beginning content -->
<div id="content">

<?php pxSingleCatTitle('<h2 class="category">%s</h2>'); ?>
<div class="resource">
    <?php pxSubCategories(__('<div id="subcat"><h3>Subcategories</h3> %s</div>')); ?>
      <div class="infos">
      <p><?php pxSingleCatNbResources(__('No resources'), __('1 resource'), __('<strong>%s</strong> resources')); ?> <?php echo __('in this category.'); ?></p>
      </div>
      <div class="description">
      <?php pxSingleCatDescription(); ?>
      </div>
</div>

<?php while (!$res->EOF()): ?>
<div class="resource">
      <p class="read"><a href="<?php pxResPath(); ?>" title="<?php pxResTitle('%s'); ?>"><?php echo __('Read'); ?></a></p>
      <?php if ($res->f('type_id') == PX_RESOURCE_MANAGER_NEWS): ?>
    <h3 class="news"><?php pxResTitle('%s'); ?></h3>
      <?php else: ?>
    <h3 class="article"><?php pxResTitle('%s'); ?></h3>
      <?php endif; ?>
      <div class="infos">
      <p><span class="datetime"><?php pxResDateModification(__('%Y-%m-%d at %H:%M')); ?></span> <?php echo __('by'); ?> <span class="author"><a href="<?php pxResAuthorEmail('mailto:%s'); ?>"><?php pxResAuthor(); ?></a></span>.</p>
      <p><span class="cat"><?php pxResCategories(__(' In %s'), ', ', __(' and ')); ?></span></p>
      <p><span class="comments"><?php echo __('Number of comments:') ?> <a href="<?php pxResPath(); ?>#comments" title="<?php echo __('Comments') ?>"><?php pxResCountComments() ?></a></span></p>
      </div>
      <div class="description">
      <?php pxResDescription(); ?>
      </div>
</div>
<?php 
$res->moveNext(); 
endwhile; ?>

	<?php pxSingleCatNextPage(-1,__('<p><a href="%s">Previous Page</a></p>')); ?>
	<?php pxSingleCatNextPage(1,__('<p><a href="%s">Next Page</a></p>')); ?>

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