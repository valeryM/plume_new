<?php
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
     <?php pxSingleCatTree('<ol class="tree">%s</ol>'); ?>
     </div><!-- end banner -->

     <div id="mainfloat">
     <div id="menuleft">
          <?php pxArtListPages(__('<div class="col-content-green"><div class="menuleft-green-top"></div><h2>Articles pages</h2> %s<div class="menuleft-green-bottom"></div></div>')); ?>
		
		      <div class="col-content-blue">
		      <div class="menuleft-blue-top"></div>
          <?php pxSubCategories(__('<h2>Categories</h2> %s')); ?>
		      <h2><?php echo __('Main categories') ?></h2>
		      <?php pxPrimaryCategories('<ul id="top-categories">%s</ul>'); ?>
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
		      <h2><?php pxArtTitle(); ?></h2>
		
		      <?php if (pxArtPageIsFirst()): ?>
          	<p class="modified"><?php echo __('The') ?> <a href="<?php pxArtPath(); ?>"><?php pxArtDateModification(__('%Y-%m-%d</a> at %H:%M'), '%s'); ?> <?php echo __('by') ?> <a href="<?php pxArtAuthorEmail('mailto:%s'); ?>"><?php pxArtAuthor(); ?></a>. <?php pxArtCategories(__('In %s')); ?>.</p>
          	<span class="lireplus"><?php echo __('Number of comments:') ?> <?php pxArtCountComments() ?> </span>
          	</div>
		      <div class="resource-blue-right"></div>
		      </div><!-- end resource -->
		      <div class="resource-desc-blue">
		      <div class="resource-desc-head"></div>
		      <?php pxArtDescription(); ?>
		      <div class="resource-desc-foot"></div>
		      </div>

          	<?php else: ?>
          	<p class="modified"><?php echo __('The') ?> <a href="<?php pxArtPath(); ?>"><?php pxArtDateModification(__('%Y-%m-%d</a> at %H:%M'), '%s'); ?> <?php echo __('by') ?> <a href="<?php pxArtAuthorEmail('mailto:%s'); ?>"><?php pxArtAuthor(); ?></a>.</p>
          	<span class="lireplus"><?php echo __('Number of comments:') ?> <?php pxArtCountComments() ?> </span>
          	</div>
		      <div class="resource-blue-right"></div>
		      </div><!-- end resource -->
		      <div class="resource-desc-blue">
		      <div class="resource-desc-head"></div>
		      <?php pxArtDescription(); ?>
		      <div class="resource-desc-foot"></div>
		      </div>
          	<?php endif; ?>
		
		      <div class="chapitre">
          	<h2 class="art-page-title"><?php pxArtPageTitle(); ?></h2>
          	<?php pxArtPageContent(); ?>
          	<?php pxArtListPages(__('<div id="art-pages-list"><h3>Pages of the article</h3> %s</div>')); ?>

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