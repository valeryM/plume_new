<?php 
// ajout commentaire

if ($cache->processPage(180)):
    pxTemplateInit('remove_numbers'); 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php pxInfo('encoding'); ?>" />
<meta name="MSSmartTagsPreventParsing" content="TRUE" />
<title><?php pxSingleCatTitle('%s'); ?> - <?php pxArtTitle('%s'); ?></title>
<?php include(dirname(__FILE__).'/inc/head-link.php'); ?>
<meta name="description" content="<?php pxSingleCatTitle('%s'); ?> : <?php pxArtTitle('%s'); ?> - <?php pxMetasDescription(); ?>" />
<meta name="DC.Date.modified" scheme="W3CDTF" content="<?php pxArtDatePublication('%Y-%m-%d'); ?>" />
<?php include(dirname(__FILE__).'/inc/head-meta.php'); ?>
<meta name="DC.Author" content="<?php pxResAuthor(); ?>" />
<meta name="DC.Title" content="<?php pxSingleCatTitle('%s'); ?> - <?php pxArtTitle('%s'); ?>" />
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

<h1 id="art-title"><?php pxArtTitle(); ?></h1>

<div id="mainfloat">
	<div id="content">

    <h2 class="restitle"><?php pxArtTitle(); ?></h2>
    
    <?php if (pxArtPageIsFirst()): ?>
    <div id="art-description">
	    <p class="modified"><?php echo __('On'); ?> <a href="<?php pxArtPath(); ?>"><?php pxArtDateModification(__('%Y-%m-%d</a> at %H:%M'), '%s'); ?>
	    <?php echo __('by'); ?> <a href="<?php pxArtAuthorEmail('mailto:%s'); ?>"><?php pxArtAuthor(); ?></a>. <?php pxArtCategories(__(' In %s'), ', ', __(' and ')); ?>
	    </p>
	    <p class="comment-count">
	    <?php echo __('Number of comments:') ?> <?php pxArtCountComments() ?>
	    </p>
	    <?php pxArtDescription(); ?>
    </div><!-- end content -->

    
    <?php else: ?>
    <div id="art-description">
			<p class="modified"><?php echo __('On'); ?> <a href="<?php pxArtPath(); ?>"><?php pxArtDateModification(__('%Y-%m-%d</a> at %H:%M'), '%s'); ?> <?php echo __('by'); ?> <a href="<?php pxArtAuthorEmail('mailto:%s'); ?>"><?php pxArtAuthor(); ?></a>.
			</p>
   </div><!-- end art-description -->
    <?php endif; ?>


    <h2 class="art-page-title"><?php pxArtPageTitle(); ?></h2>
    <?php pxArtPageContent(); ?>
    <?php pxArtListPages(__('<div id="art-pages-list"><h3>Pages of the article</h3> %s</div>')); ?>

    <?php include dirname(__FILE__).'/comments_inline.php'; ?>

		<hr class="invisible" />
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
	    <?php pxArtListPages(__('<h2>Articles pages</h2> %s')); ?>
	
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