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

<body class="category">

<div id="page">
	<div id="banner">
	<?php include(dirname(__FILE__).'/inc/easy-access.php'); ?>
	
		<h1 id="top"><a href="<?php pxInfo('url'); ?>"><?php pxInfo('name'); ?></a></h1>
		<p class="description"><?php pxInfo('description'); ?></p>
	</div><!-- end banner -->


<div id="main">
<?php pxSingleCatTree('<ol class="tree">%s</ol>'); ?>

<div id="mainfloat">
	<div id="content">
	
<?php while (!$res->EOF()): ?>
    <div class="resource">
    <h2><a href="<?php pxResPath(); ?>"><?php pxResTitle('%s'); ?></a></h2>
    <p class="modified"><?php echo __('On'); ?> <a href="<?php pxResPath(); ?>"><?php pxResDateModification(__('%Y-%m-%d at %H:%M')); ?></a> <?php echo __('by'); ?> <a href="<?php pxResAuthorEmail('mailto:%s'); ?>"><?php pxResAuthor(); ?></a>.<?php pxResCategories(__(' In %s'), ', ', __(' and ')); ?>
    </p>
    <p class="comment-count">
    <?php echo __('Number of comments:') ?> <?php pxResCountComments() ?>
    </p>
    </div><!-- end resource -->
<?php 
$res->moveNext(); 
endwhile; ?>

	<?php pxSingleCatNextPage(-1,__('<p><a href="%s">Previous Page</a></p>')); ?>
	<?php pxSingleCatNextPage(1,__('<p><a href="%s">Next Page</a></p>')); ?>

	<hr class="invisible"/>
	</div><!-- end content -->



	<div id="menuleft">
		<div class="col-content">
		<h2><?php echo __('Back to'); ?></h2>
		<ul><li><a href="<?php pxParentCatPath(); ?>"><?php pxParentCatTitle('%s'); ?></a></li></ul>

		<?php pxSubCategories(__('<h2>Categories</h2> %s')); ?>

		<h2><?php echo __('Links'); ?></h2>
		<?php pxLink::linkList(); ?>

		</div><!-- end col-content -->
	</div><!-- end menuleft -->

</div><!-- end mainfloat -->



	<div id="menuright">
		<div class="col-content">
	    <?php pxSingleCatTitle('<h2 class="category">%s</h2>'); ?>
	    
	    <div class="pxSingleCatDescription"><?php pxSingleCatDescription(); ?></div>
	    <p class="resources-number"><?php pxSingleCatNbResources(__('No resources'), __('1 resource'), __('<strong>%s</strong> resources')); ?> <?php echo __('in this category.'); ?></p>
		
			<h2><?php echo __('Main categories'); ?></h2>
			<?php pxPrimaryCategories('<ul id="top-categories">%s</ul>'); ?>

			<?php include(dirname(__FILE__).'/inc/recent-news.php'); ?>
			<?php include(dirname(__FILE__).'/inc/rss-sitemap.php'); ?>
		</div><!-- col-content -->
	</div><!-- end menuright -->


</div><!-- end main -->

<?php include(dirname(__FILE__).'/inc/footer.php'); ?>
<?php
    $cache->endCache();
endif;
?>