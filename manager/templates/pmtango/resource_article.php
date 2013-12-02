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

<body>

<div id="page">

<!-- Beginning Banner -->
<?php include(dirname(__FILE__).'/inc/banner.php'); ?>
<!-- End of Banner -->

<div id="main">
<!-- Beginning breadcrumb -->
<?php pxSingleCatTree('<ol class="tree">%s</ol>'); ?>
<!-- End Breadcrumb -->

<!-- Beginning Menu -->
<div id="menu">
  <?php include(dirname(__FILE__).'/inc/menu.php'); ?>
</div>
<!-- End of Menu -->

<!-- Beginning content -->
<div id="content">

<h2 class="article"><?php pxArtTitle(); ?></h2>
  <div class="resource">
    <?php pxArtListPages(__('<div id="art-pages-list"><h3>Pages of the article</h3> %s</div>')); ?>
    <?php if (pxArtPageIsFirst()): ?>
          <div class="infos">
	    <p><span class="datetime"><?php pxArtDateModification(__('%Y-%m-%d at %H:%M'), '%s'); ?></span>
	    <?php echo __('by'); ?> <span class="author"><a href="<?php pxArtAuthorEmail('mailto:%s'); ?>"><?php pxArtAuthor(); ?></a></span></p>
            <p><span class="cat"><?php pxArtCategories(__(' In %s'), ', ', __(' and ')); ?></span></p>
	    <p><span class="link"><a href="<?php pxArtPath('fullurl'); ?>"><?php pxArtPath('fullurl'); ?></a></span></p>
            <p><span class="comments"><?php echo __('Number of comments:'); ?></span> <a href="#comments" title="<?php echo __('Comments'); ?>"><?php pxArtCountComments(); ?></a></p>
	  </div>
          <div class="description">
	    <?php pxArtDescription(); ?>
          </div>

    <?php else: ?>
        <div class="infos">
	  <p><span class="datetime"><?php pxArtDateModification(__('%Y-%m-%d at %H:%M'), '%s'); ?></span>
	    <?php echo __('by'); ?> <span class="author"><a href="<?php pxArtAuthorEmail('mailto:%s'); ?>"><?php pxArtAuthor(); ?></a></span></p>
        </div>
    <?php endif; ?>
  </div>
  
<div id="article-content">
    <h3 class="art-page-title"><?php pxArtPageTitle(); ?></h3>
    <?php pxArtPageContent(); ?>
    </div>
   <?php //absolute path for comments permalinks
   $respath = pxArtPath('fullurl', true);  ?>
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