<?php
pxTemplateInit('remove_numbers');
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="<?php pxInfo('encoding'); ?>" />
	<!-- Set the viewport width to device width for mobile -->
	<meta name="viewport" content="width=device-width" />	
	<meta name="MSSmartTagsPreventParsing" content="TRUE" />
	<title>404 - <?php pxInfo('name'); ?></title>
	<?php require(dirname(__FILE__).'/inc/head-link.php'); ?>
	<?php //pxHeadLinks(); ?>
	<meta name="DC.Title" content="<?php echo __('404 Page not found !'); ?> <?php pxInfo('name'); ?>" />
	<?php require(dirname(__FILE__).'/inc/head-meta.php'); ?>
</head>
<body class="404 category">
	<div id="page">
		<?php include dirname(__FILE__).'/inc/banner2.php'; ?>
		<?php include dirname(__FILE__).'/inc/menu_top.php'; ?>
		<div id="main">
			<div id="mainleft">
				<div id="content">
					<h1 class="err404">
						<?php echo __('404 Error - Page not found'); ?>
					</h1>
					<p>
						<?php echo __('The page you are looking for is not available anymore. We tried to search for the pages that match your request. Feel free to refine the search.'); ?>
					</p>
					<p>
						<?php echo __('Results of the search on:'); ?>
						<?php $query=$_SERVER['QUERY_STRING']; ?>
						<?php pxSearchQuery('<strong>%s</strong>'); ?>
					</p>

					<?php while (!$res->EOF()): ?>
					<div class="resource">
						<h2>
							<a href="<?php pxResPath(); ?>"><?php pxResTitle('%s'); ?> </a>
						</h2>
						<p class="modified">
							<?php echo __('The'); ?>
							&nbsp; <a href="<?php pxResPath(); ?>"><?php pxResDateModification(__('%Y-%m-%d</a> at %H:%M')); ?>&nbsp;<?php echo __('by'); ?>
							</a>&nbsp; <a href="<?php pxResAuthorEmail('mailto:%s'); ?>"><?php pxResAuthor(); ?>
							</a>.
							<?php pxResCategories(__(' In %s'), ', ', __(' and ')); ?>
							.
						</p>

						<?php  pxResDescription('<p>%s...</p>', 200); ?>
						<p class="score">
							<?php echo __('Score:'); ?>
							<?php pxResSearchScore(); ?>
						</p>
					</div>
					<!-- end resource -->
					<?php 
						$res->moveNext();
					endwhile; 
					?>

					<hr class="invisible" />
				</div>
				<!-- end content -->

			</div>
			<!-- end mainfloat -->

			<div id="menuright">
				<div id="infoPratique">
					<?php //require('inc/breadCrumbs.php'); ?>				
					<?php require('inc/links.php'); ?>
				</div>
			</div><!-- end menuright -->

		</div>
		<!-- end main -->

	</div>
	<!-- end page -->
	<?php require(dirname(__FILE__).'/inc/footer.php'); ?>
</body>
</html>