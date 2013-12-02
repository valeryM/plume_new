<?php
if ($cache->processPage(3600)):
pxTemplateInit('remove_numbers');
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8" />
<!-- Set the viewport width to device width for mobile -->
<meta name="viewport" content="width=device-width" />

<!-- <meta http-equiv="Content-Type" content="text/html; charset=<?php pxInfo('encoding'); ?>" /> -->
<meta name="MSSmartTagsPreventParsing" content="TRUE" />
<title>Site map of <?php pxInfo('name'); ?>
</title>
<?php include(dirname(__FILE__).'/inc/head-link.php'); ?>
<link rel="schema.DC" href="http://purl.org/dc/elements/1.1/" />
<?php pxHeadLinks(); ?>
<meta name="DC.title" content="Site map of <?php pxInfo('name'); ?>" />
<?php include(dirname(__FILE__).'/inc/head-meta.php'); ?>
</head>

<body class="category">

	<div id="page">
		<?php include dirname(__FILE__).'/inc/banner.php'; ?>
		<?php include dirname(__FILE__).'/inc/menu_top.php'; ?>
		<div id="main">

			<div id="mainfloat">
				<div id="content">
					<div id="sitemap">
						<h1>
							<?php echo __('Sitemap'); ?>
						</h1>
						<?php pxShowSitemap('all', 25); ?>
					</div>

					<hr class="invisible" />
				</div>
				<!-- end content -->

			</div>
			<!-- end mainfloat -->

			<div id="menuright">
				<div class="col-content">
					<?php include(dirname(__FILE__).'/inc/welcome.php'); ?>
					<?php include(dirname(__FILE__).'/inc/calendar-events.php'); ?>
					<h2>
						<?php echo __('Links'); ?>
					</h2>
					<?php pxLink::linkList(); ?>
				</div><!-- col-content -->
			</div>
			<!-- end menuright -->

		</div>
		<!-- end main -->

		<?php 
		include(dirname(__FILE__).'/inc/footer.php');
		?>
	</div>
	<!-- end page -->

</body>
</html>
<?php 
	$cache->endCache();
endif;
?>