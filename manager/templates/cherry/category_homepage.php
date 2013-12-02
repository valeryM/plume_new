<?php
if ($cache->processPage(180)):
    pxTemplateInit('remove_numbers');
    pxGetLastResources();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="<?php pxInfo('lang'); ?>">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php pxInfo('encoding'); ?>" />
<meta name="MSSmartTagsPreventParsing" content="TRUE" />
<title><?php pxInfo('name'); ?></title>
<?php include(dirname(__FILE__).'/inc/head-link.php'); ?>
<meta name="description" content="<?php pxInfo('name'); ?> - <?php pxMetasDescription(); ?>" />
<?php include(dirname(__FILE__).'/inc/head-meta.php'); ?>
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
		      <h2><?php echo __('Categories') ?></h2>
		      <?php pxPrimaryCategories('<ul>%s</ul>'); ?>
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
          <?php pxGetLastResources(); ?>
	        <?php while (!$last->EOF()) { ?>
		      <div class="resource-blue">
		      <div class="resource-blue-left"></div>
		      <div class="resource-content">
		      <h2><a href="<?php pxLastResPath(); ?>" title="<?php pxLastResTitle('%s'); ?>"><?php pxLastResTitle('%s'); ?></a></h2>
		      <p class="modified"><?php echo __('The') ?> <a href="<?php pxLastResPath(); ?>" title="<?php echo __('Publish Date of:') ?> <?php pxLastResTitle('%s'); ?>"><?php pxLastResDateModification(__('%Y-%m-%d</a> at %H:%M')); ?> <?php echo __('by') ?> <a href="<?php pxLastResAuthorEmail('mailto:%s'); ?>" title="<?php pxLastResAuthor(); ?><?php echo __(', publisher of') ?> <?php pxLastResTitle('%s'); ?>"><?php pxLastResAuthor(); ?></a>. <?php pxLastResCategories(__(' In %s'), ', ', __(' and ')); ?>.</p>
          <span class="lireplus"><a href="<?php pxLastResPath(); ?>" title="<?php pxLastResTitle('%s'); ?>"><?php echo __('Read the whole story') ?></a></span>
		      </div>
		      <div class="resource-blue-right"></div>
		      </div><!-- end resource -->
		      <div class="resource-desc-blue">
		      <div class="resource-desc-head"></div>
		      <?php pxLastResDescription(); ?>
		      <div class="resource-desc-foot"></div>
		      </div>
		
		      <?php $last->moveNext(); if (!$last->EOF()) { ?>
			    <div class="resource-green">
			    <div class="resource-green-left"></div>
			    <div class="resource-content">
			    <h2><a href="<?php pxLastResPath(); ?>" title="<?php pxLastResTitle('%s'); ?>"><?php pxLastResTitle('%s'); ?></a></h2>
			    <p class="modified"><?php echo __('The') ?> <a href="<?php pxLastResPath(); ?>" title="<?php echo __('Publish Date of:') ?> <?php pxLastResTitle('%s'); ?>"><?php pxLastResDateModification(__('%Y-%m-%d</a> at %H:%M')); ?> <?php echo __('by') ?> <a href="<?php pxLastResAuthorEmail('mailto:%s'); ?>" title="<?php pxLastResAuthor(); ?><?php echo __(', publisher of') ?> <?php pxLastResTitle('%s'); ?>"><?php pxLastResAuthor(); ?></a>. <?php pxLastResCategories(__(' In %s'), ', ', __(' and ')); ?>.</p>
          <span class="lireplus"><a href="<?php pxLastResPath(); ?>" title="<?php pxLastResTitle('%s'); ?>"><?php echo __('Read the whole story') ?></a></span>
			    </div>
			    <div class="resource-green-right"></div>
			    </div><!-- end resource -->
			    <div class="resource-desc-green">
			    <div class="resource-desc-head"></div>
			    <?php pxLastResDescription(); ?>
			    <div class="resource-desc-foot"></div>
			    </div>
		      <?php }
		      $last->moveNext();
		      } ?>

		      <hr class="invisible" />
     </div><!-- end content -->
     </div><!-- end mainfloat -->

<?php include(dirname(__FILE__).'/inc/footer.php'); ?>
<?php
    $cache->endCache();
endif;
?>