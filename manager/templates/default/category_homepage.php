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
		
		<h1 id="top"><a href="<?php pxInfo('url'); ?>" accesskey="1"><?php pxInfo('name'); ?></a></h1>
		<p class="description"><?php pxInfo('description'); ?></p>
	</div><!-- end banner -->		

<div id="main">

<div id="mainfloat">

	<div id="content">
		<?php while (!$last->EOF()): ?>
			<div class="resource">
			<h2><a href="<?php pxLastResPath(); ?>" title="<?php pxLastResTitle('%s'); ?>"><?php pxLastResTitle('%s'); ?></a></h2>
			<p class="modified"><?php echo __('On'); ?> <a href="<?php pxLastResPath(); ?>" title="<?php echo __('Publish Date of:'); ?> <?php pxLastResTitle('%s'); ?>"><?php pxLastResDateModification(__('%Y-%m-%d</a> at %H:%M')); ?> <?php echo __('by'); ?> <a href="<?php pxLastResAuthorEmail('mailto:%s'); ?>" title="<?php pxLastResAuthor(); ?><?php echo __(', publisher of'); ?> <?php pxLastResTitle('%s'); ?>"><?php pxLastResAuthor(); ?></a></p>
			<?php pxLastResDescription(); ?>
			</div><!-- end resource -->
		<?php
		$last->moveNext();
		endwhile;
		?>

		<hr class="invisible" />
	</div><!-- end content -->

	<div id="menuleft">
		<div class="col-content">
		<h2><?php echo __('Categories'); ?></h2>

		<?php pxPrimaryCategories('<ul id="top-categories">%s</ul>'); ?>

		<h2><?php echo __('Links'); ?></h2>
		<?php pxLink::linkList(); ?>

		<h2><?php echo __('In short'); ?></h2>
		<ul>
			<?php while (!$res->EOF()): ?>
			<li><a href="<?php pxResPath(); ?>" title="<?php pxResTitle(); ?>"><?php pxResTitle(); ?></a></li>
			<?php
			$res->moveNext();
			endwhile; ?>
		</ul>
		</div><!-- col-content -->
	</div><!-- end menuleft -->

</div><!-- end mainfloat -->

	<div id="menuright">
		<div class="col-content">
			<?php include(dirname(__FILE__).'/inc/recent-news.php'); ?>
			<?php include(dirname(__FILE__).'/inc/recent-events.php'); ?>
			<?php include(dirname(__FILE__).'/inc/rss-sitemap.php'); ?>
		</div><!-- col-content -->
	</div><!-- end menuright -->

</div><!-- end main -->

<?php include(dirname(__FILE__).'/inc/footer.php'); ?>
<?php
    $cache->endCache();
endif;
?>
