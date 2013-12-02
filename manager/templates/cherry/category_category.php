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
     <?php pxSingleCatTree('<ol class="tree">%s</ol>'); ?>
     </div><!-- end banner -->

     <div id="mainfloat">
     <div id="menuleft">
          <div class="col-content-green">
		      <div class="menuleft-green-top"></div>
		      <?php pxSingleCatTitle('<h2 class="category">%s</h2>'); ?>
		      <div class="pxSingleCatDescription"><?php pxSingleCatDescription(); ?></div>
		      <p class="resources-number"><?php pxSingleCatNbResources(__('No resources'), __('1 resource'), __('<strong>%s</strong> resources')); ?> <?php echo __('in this category.') ?></p>
		      <div class="menuleft-green-bottom"></div>
		      </div>
		
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
          <?php while (!$res->EOF()) { ?>
		      <div class="resource-blue">
		      <div class="resource-blue-left"></div>
		      <div class="resource-content">
		      <h2><a href="<?php pxResPath(); ?>" title="<?php pxResTitle('%s'); ?>"><?php pxResTitle('%s'); ?></a></h2>
		      <p class="modified"><?php echo __('The') ?> <a href="<?php pxResPath(); ?>" title="<?php echo __('Publish Date of:') ?> <?php pxResTitle('%s'); ?>"><?php pxResDateModification(__('%Y-%m-%d')); ?></a> <?php pxResDateModification(__('at %H:%M')); ?> <?php echo __('by') ?> <a href="<?php pxResAuthorEmail('mailto:%s'); ?>" title="<?php echo __('publisher of:') ?> <?php pxResTitle('%s'); ?>"><?php pxResAuthor(); ?></a>. <?php pxResCategories(__('In %s')); ?>.</p>
          <span class="lireplus"><a href="<?php pxResPath(); ?>" title="<?php pxResTitle('%s'); ?>"><?php echo __('Read the whole story') ?></a></span>
		      </div>
		      <div class="resource-blue-right"></div>
		      </div><!-- end resource -->
		      <div class="resource-desc-blue">
		      <div class="resource-desc-head"></div>
		      <?php pxResDescription('<p>%s', 200); ?>
		      <span style="font-size:.8em;"> <a href="<?php pxResPath(); ?>" title="<?php pxResTitle('%s'); ?>"><?php echo __('Read the whole story') ?></a></span></p>
		      <div class="resource-desc-foot"></div>
		      </div>
		
		      <?php $res->moveNext(); if (!$res->EOF()) { ?>
			    <div class="resource-green">
			    <div class="resource-green-left"></div>
			    <div class="resource-content">
			    <h2><a href="<?php pxResPath(); ?>" title="<?php pxResTitle('%s'); ?>"><?php pxResTitle('%s'); ?></a></h2>
          <p class="modified"><?php echo __('The') ?> <a href="<?php pxResPath(); ?>" title="<?php echo __('Publish Date of:') ?> <?php pxResTitle('%s'); ?>"><?php pxResDateModification(__('%Y-%m-%d')); ?></a> <?php pxResDateModification(__('at %H:%M')); ?> <?php echo __('by') ?> <a href="<?php pxResAuthorEmail('mailto:%s'); ?>" title="<?php echo __('publisher of:') ?> <?php pxResTitle('%s'); ?>"><?php pxResAuthor(); ?></a>. <?php pxResCategories(__('In %s')); ?>.</p>
          <span class="lireplus"><a href="<?php pxResPath(); ?>" title="<?php pxResTitle('%s'); ?>"><?php echo __('Read the whole story') ?></a></span>
          </div>
			    <div class="resource-green-right"></div>
			    </div><!-- end resource -->
			    <div class="resource-desc-green">
			    <div class="resource-desc-head"></div>
			    <?php pxResDescription('<p>%s', 200); ?>
			    <span style="font-size:.8em;"> <a href="<?php pxResPath(); ?>" title="<?php pxResTitle('%s'); ?>"><?php echo __('Read the whole story') ?></a></span></p>
		      <div class="resource-desc-foot"></div>
			    </div>
		      <?php
		      $res->moveNext(); }
		      } ?>
		
		      <?php pxSingleCatNextPage(-1,__('<p><a href="%s">Previous Page</a></p>')); ?>
		      <?php pxSingleCatNextPage(1,__('<p><a href="%s">Next Page</a></p>')); ?>

		      <hr class="invisible" />
     </div><!-- end content -->
     </div><!-- end mainfloat -->

<?php include(dirname(__FILE__).'/inc/footer.php'); ?>
<?php
    $cache->endCache();
endif;
?>