<?php 
if ($cache->processPage(180)):
    pxTemplateInit('remove_numbers'); 
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
	
		<h1 id="top"><a href="<?php pxInfo('url'); ?>"><?php pxInfo('name'); ?></a></h1>
		<p class="description"><?php pxInfo('description'); ?></p>
	</div><!-- end banner -->

<div id="main">

<?php pxSingleCatTree('<ol class="tree">%s</ol>'); ?>

<div id="mainfloat">
	<div id="content">

    <h2 class="restitle"><?php pxNewsTitle('%s'); ?></h2>
    <div class="news-infos">
			<p class="modified"><?php echo __('On'); ?> <a href="<?php pxNewsPath(); ?>"><?php pxNewsDateCreation(__('%Y-%m-%d</a> at %H:%M')); ?>
			<?php echo __('by'); ?> <a href="<?php pxNewsAuthorEmail('mailto:%s'); ?>"><?php pxNewsAuthor(); ?></a>.<?php pxNewsCategories(__(' In %s'), ', ', __(' and ')); ?>
                        </p>
                        <p class="comment-count">
                        <?php echo __('Number of comments:') ?> <?php pxNewsCountComments() ?>
                        </p>
    </div><!-- end news-infos -->

    <?php pxNewsContent(); ?>
    <?php include dirname(__FILE__).'/comments_inline.php'; ?>

	<hr class="invisible"/>
	</div><!-- end content -->



	<div id="menuleft">
		<div class="col-content">
		<h2><?php echo __('Back to'); ?></h2>
		<ul><li><a href="<?php pxSingleCatPath(); ?>"><?php pxSingleCatTitle('%s'); ?></a></li></ul>

		<?php pxSubCategories(__('<h2>Categories</h2> %s')); ?>

		<h2><?php echo __('Links'); ?></h2>
		<?php pxLink::linkList(); ?>

		</div><!-- end col-content -->
	</div><!-- end menuleft -->

</div><!-- end mainfloat -->



	<div id="menuright">
		<div class="col-content">
	
		<h2><?php echo __('Main categories'); ?></h2>
		<?php pxPrimaryCategories('<ul id="top-categories">%s</ul><br />'); ?>
		
		<?php include(dirname(__FILE__).'/inc/rss-sitemap.php'); ?>
		</div><!-- col-content -->
	</div><!-- end menuright -->


</div><!-- end main -->

<?php include(dirname(__FILE__).'/inc/footer.php'); ?>
<?php
    $cache->endCache();
endif;
?>