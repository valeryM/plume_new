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
<meta name="DC.title" content="<?php echo __('404 Page not found !'); ?> <?php pxInfo('name'); ?>" />
<?php include(dirname(__FILE__).'/inc/head-meta.php'); ?>
</head>

<body>

<div id="page">

<!-- Beginning Banner -->
<?php include(dirname(__FILE__).'/inc/banner.php'); ?>
<!-- End Banner -->

<div id="main">
<!-- Beginning breadcrumb -->
<ol class="tree"><li><a href="<?php pxInfo('url'); ?>"><?php echo __('Home'); ?></a></li></ol>
<!-- End Breadcrumb -->

<!-- Beginning menu -->
<div id="menu">
  <?php include(dirname(__FILE__).'/inc/menu.php'); ?>
</div>
<!-- End of menu -->

<!-- Beginning content -->
<div id="content">
	
	<h2 class="err404"><?php echo __('404 Error - Page not found'); ?></h2>
	
	<p><?php echo __('The page you are looking for is not available anymore. We tried to search for the pages that match your request. Feel free to refine the search.'); ?></p>
	<p><?php echo __('Results of the search on:'); ?> <?php pxSearchQuery('<strong>%s</strong>'); ?>.</p>

<?php while (!$res->EOF()): ?>
<div class="resource">
      <p class="read"><a href="<?php pxResPath(); ?>" title="<?php pxResTitle('%s'); ?>"><?php echo __('Read'); ?></a></p>
      <?php if ($res->f('type_id') == PX_RESOURCE_MANAGER_NEWS): ?>
    <h3 class="news"><?php pxResTitle('%s'); ?></h3>
      <?php else: ?>
    <h3 class="article"><?php pxResTitle('%s'); ?></h3>
      <?php endif; ?>
      <div class="infos">
      <p><span class="datetime"><?php pxResDateModification(__('%Y-%m-%d at %H:%M')); ?></span> <?php echo __('by'); ?> <span class="author"><a href="<?php pxResAuthorEmail('mailto:%s'); ?>"><?php pxResAuthor(); ?></a></span>.</p>
      <p><span class="cat"><?php pxResCategories(__(' In %s'), ', ', __(' and ')); ?></span></p>
      </div>
      <div class="description">
      <?php pxResDescription('<p>%s...</p>', 200); ?>
      </div>
      <p class="score"><span class="score"><?php echo __('Score:'); ?></span> <?php pxResSearchScore(); ?></p>
</div>
<?php 
$res->moveNext(); 
endwhile; ?>        


<hr class="invisible" />

</div>
<!-- end content -->

</div>
<!-- end main -->

<?php include(dirname(__FILE__).'/inc/footer.php'); ?>
</div>
</body>
</html>