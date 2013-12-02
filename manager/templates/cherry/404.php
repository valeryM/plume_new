<?php
pxTemplateInit('remove_numbers');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php pxInfo('encoding'); ?>" />
<meta name="MSSmartTagsPreventParsing" content="TRUE" />
<title>404 - <?php pxInfo('name'); ?></title>
<?php include(dirname(__FILE__).'/inc/head-link.php'); ?>
<link rel="schema.DC" href="http://purl.org/dc/elements/1.1/" />
<?php pxHeadLinks(); ?>
<meta name="DC.title" content="404 Page not found ! <?php pxInfo('name'); ?>" />
<?php include(dirname(__FILE__).'/inc/head-meta.php'); ?>
</head>

<body class="category">

<div id="page">
     <div id="banner">
     <?php include(dirname(__FILE__).'/inc/easy-access.php'); ?>
     <ol class="tree"><li><?php echo __('Results of the search on:') ?> <?php pxSearchQuery('<span style="color:#96ca2f;">%s</span>'); ?></li></ol>
     </div><!-- end banner -->

     <div id="mainfloat">
     <div id="menuleft">
          <div class="col-content-green">
		      <div class="menuleft-green-top"></div>
		      <h2><?php echo __('Categories') ?></h2>
		      <?php pxPrimaryCategories('<ul id="top-categories">%s</ul>'); ?>
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
          <div class="resource-desc-blue">
		      <div class="resource-desc-head"></div>
          <h1 class="err404"><?php echo __('404 Error - Page not found') ?></h1>
          <p><?php echo __('The page you are looking for is not available anymore. We tried to search for the pages that match your request. Feel free to refine the search.') ?></p>
		      <div class="resource-desc-foot"></div>
		      </div>

          <?php if ($res->EOF()) : ?>
          <div class="resource-desc-blue">
		      <div class="resource-desc-head"></div>
          <p><?php echo __('Sorry, the search engine found no result.') ?></p>
		      <div class="resource-desc-foot"></div>
		      </div>
		      <?php endif; ?>

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
		      <span class="score-blue"><?php echo __('Score:') ?> <?php pxResSearchScore(); ?></span></p>
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
			    <span class="score-green"><?php echo __('Score:') ?> <?php pxResSearchScore(); ?></span></p>
		      <div class="resource-desc-foot"></div>
			    </div>
		      <?php
		      $res->moveNext(); }
		      } ?>

          <hr class="invisible" />
     </div><!-- end content -->
     </div><!-- end mainfloat -->

<?php include(dirname(__FILE__).'/inc/footer.php'); ?>