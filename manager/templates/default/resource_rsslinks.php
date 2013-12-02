<?php 
if ($cache->processPage(180)):
    pxTemplateInit('remove_numbers'); 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php pxInfo('encoding'); ?>" />
<meta name="MSSmartTagsPreventParsing" content="TRUE" />
<title><?php pxRsslinksTitle('%s'); ?></title>
<?php include(dirname(__FILE__).'/inc/head-link.php'); ?>
<meta name="description" content="<?php pxRsslinksTitle('%s'); ?>" />
<meta name="DC.Date.modified" scheme="W3CDTF" content="<?php pxRsslinksDatePublication('%Y-%m-%d'); ?>" />
<?php include(dirname(__FILE__).'/inc/head-meta.php'); ?>
<meta name="DC.Author" content="<?php pxResAuthor(); ?>" />
<meta name="DC.Title" content="<?php pxRsslinksTitle('%s'); ?>" />
</head>

<body class="rsslinks">
<div id="page">
	<div id="banner">
	<?php include(dirname(__FILE__).'/inc/easy-access.php'); ?>
		<h1 id="top"><a href="<?php pxInfo('url'); ?>"><?php pxInfo('name'); ?></a></h1>
		<p class="description"><?php pxInfo('description'); ?></p>
	</div><!-- end banner -->

<div id="main">

<?php pxSingleCatTree('<ol class="tree">%s</ol>'); ?>
	<div id="mainfloat">
		<!-- beginning content -->
		<div id="content">
		
		<h2 class="rsslinks"><?php pxRsslinksTitle('%s'); ?></h2>
		    <div class="resource">
		    	<div class="infos">
		           <p><span class="datetime"><?php pxRsslinksDateCreation(__('%Y-%m-%d at %H:%M')); ?></span>
			   <?php echo __('by'); ?> <span class="author"><a href="<?php pxRsslinksAuthorEmail('mailto:%s'); ?>"><?php pxRsslinksAuthor(); ?></a></span></p>
		           <p><span class="cat"><?php pxRsslinksCategories(__(' In %s'), ', ', __(' and ')); ?></span></p>
		           <p><span class="link"><a href="<?php pxRsslinksPath('fullurl'); ?>"><?php pxRsslinksPath('fullurl'); ?></a></span></p>
		           <p><span class="comments"><?php echo __('Number of comments:'); ?></span> <a href="#comments" title="<?php echo __('Comments'); ?>"><?php pxRsslinksCountComments(); ?></a></p>
		         </div>
		   </div>
		         
		    <div id="rsslinks-content">
		      <?php pxRsslinksContent(); ?>
		      <?php //pxRsslinksAssociatedLink('<p class="associated-link"><a href="%1$s">%2$s</a></p>'); ?>
		    </div>
		    <?php //absolute path for comments permalinks
		    $respath = pxRsslinksPath('fullurl', true); ?>
		    <h2 id="comments"><?php echo __('Comments'); ?></h2>
		      <div class="resource">
		      <?php include dirname(__FILE__).'/comments_inline.php'; ?>
		      </div>
		    <hr class="invisible" />
		</div>
		<!-- end content -->
		
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