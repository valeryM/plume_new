<?php 
if ($cache->processPage(180)):
    pxTemplateInit('remove_numbers');
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

<body>
<div id="page">
<!-- Beginning Banner -->
<?php include(dirname(__FILE__).'/inc/banner.php'); ?>
<!-- End of Banner -->

<div id="main">

<!-- Beginning Breadcrumb -->
<ol class="tree"><li><a href="<?php pxInfo('url'); ?>"><?php pxSingleCatTitle('%s'); ?></a></li></ol>
<!-- End Breadcrumb -->

<!-- Beginning Menu -->
<div id="menu">

  <?php include(dirname(__FILE__).'/inc/menu.php'); ?>

  <h2 class="short"><?php echo __('In short'); ?></h2>
      <ul>
	<?php if (!$res->EOF()): ?>
        <?php while (!$res->EOF()): ?>
	<li><a href="<?php pxResPath(); ?>" title="<?php pxResTitle(); ?>"><?php pxResTitle(); ?></a></li>
	<?php
	$res->moveNext(); 
        endwhile; ?>
        <?php else: ?>
        <li><?php echo __('No resources'); ?></li>
        <?php endif; ?>
      </ul>

</div>
<!-- End of Menu -->

<!-- Beginning content -->
<div id="content">

<div class="column">
<h2><?php echo __('Recent articles'); ?></h2>
  <?php pxGetLastResources(3,'articles'); ?>
  <?php if (!$last->EOF()): ?>
  <?php while (!$last->EOF()): ?>
	<div class="resource">
            <p class="read"><a href="<?php pxLastResPath(); ?>" title="<?php pxLastResTitle('%s'); ?>"><?php echo __('Read'); ?></a></p>
	  <h3 class="article"><?php pxLastResTitle('%s'); ?></h3>
            <div class="infos">
	    <p><span class="datetime"><?php pxLastResDateModification(__('%Y-%m-%d at %H:%M')); ?></span> <?php echo __('by'); ?> <span class="author"><a href="<?php pxLastResAuthorEmail('mailto:%s'); ?>" title="<?php pxLastResAuthor(); ?><?php echo __(', publisher of'); ?> <?php pxLastResTitle('%s'); ?>"><?php pxLastResAuthor(); ?></a></span></p>
	    <p><span class="comments"><?php echo __('Number of comments:') ?> <a href="<?php pxLastResPath(); ?>#comments" title="<?php echo __('Comments') ?>"><?php pxLastResCountComments() ?></a></span></p>
            </div>
            <div class="description">
            <?php pxLastResDescription(); ?>
            </div>
	</div><!-- end resource -->
  <?php
  $last->moveNext(); 
  endwhile; ?>
  <?php else: ?>
        <p><?php echo __('No resources'); ?></p>
  <?php endif; ?>
<hr class="invisible" />
</div>

<div class="column">
<h2><?php echo __('Recent news'); ?></h2>
  <?php pxGetLastResources(3,'news'); ?>
  <?php if (!$last->EOF() ): ?>
  <?php while (!$last->EOF()): ?>
       <div class="resource">
           <p class="read"><a href="<?php pxLastResPath(); ?>" title="<?php pxLastResTitle('%s'); ?>"><?php echo __('Read'); ?></a></p>
	 <h3 class="news"><?php pxLastResTitle(); ?></h3>
           <div class="infos">
           <p><span class="datetime"><?php pxLastResDateModification(__('%Y-%m-%d at %H:%M')); ?></span> <?php echo __('by'); ?> <span class="author"><a href="<?php pxLastResAuthorEmail('mailto:%s'); ?>" title="<?php pxLastResAuthor(); ?><?php echo __(', publisher of'); ?> <?php pxLastResTitle('%s'); ?>"><?php pxLastResAuthor(); ?></a></span></p>
	   <p><span class="comments"><?php echo __('Number of comments:') ?> <a href="<?php pxLastResPath(); ?>#comments" title="<?php echo __('Comments') ?>"><?php pxLastResCountComments() ?></a></span></p>
           </div>
         </div>
  <?php
  $last->moveNext(); 
  endwhile ?>
  <?php else: ?>
  <p><?php echo __('No resources'); ?></p>
  <?php endif; ?>
  <hr class="invisible" />
</div>

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
