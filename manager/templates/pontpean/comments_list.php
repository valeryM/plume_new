<?php pxTemplateInit(); ?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8" />
<!-- Set the viewport width to device width for mobile -->
<meta name="viewport" content="width=device-width" />

<!-- <meta http-equiv="Content-Type" content="text/html; charset=<?php pxInfo('encoding'); ?>" /> -->
<meta name="MSSmartTagsPreventParsing" content="TRUE" />
<title>Comments - <?php pxInfo('name'); ?>
</title>
<?php include(dirname(__FILE__).'/inc/head-link.php'); ?>
<meta name="description" content="<?php pxInfo('description'); ?>" />
<?php include(dirname(__FILE__).'/inc/head-meta.php'); ?>
<meta name="DC.Title"
	content="<?php echo __('Comments'); ?> - <?php pxInfo('name'); ?>" />
</head>

<body class="category">

	<div id="page">
		<?php require dirname(__FILE__).'/inc/banner2.php'; ?>
		<?php require dirname(__FILE__).'/inc/menu_top.php'; ?>
		<div id="main">
			<div id="mainleft">
				<div id="content">
					<h2 class="comment-preview">
						<?php echo __('Comment'); ?>
					</h2>

					<?php
					while (!$res->cur->comments->EOF()) {
        				echo $res->cur->comments->getContent();
        				echo $res->cur->comments->countContent();
        				$res->cur->comments->moveNext();
    				}
    				?>
					<hr class="invisible" />
				</div>
				<!-- end content -->

				<div id="menuleft">
					<div class="col-content">

						<?php pxSubCategories(__('<h2>Categories</h2> %s')); ?>

						<h2>
							<?php echo __('Links'); ?>
						</h2>
						<?php pxLink::linkList(); ?>

					</div>
					<!-- col-content -->
				</div>
				<!-- end menuleft -->

			</div>
			<!-- end mainfloat -->

			<div id="menuright">
				<div class="col-content">
					<?php pxSingleCatTitle('<h2 class="category">%s</h2>'); ?>
					<div class="pxSingleCatDescription">
						<?php pxSingleCatDescription(); ?>
					</div>
					<?php include(dirname(__FILE__).'/inc/welcome.php'); ?>
					<?php include(dirname(__FILE__).'/inc/recent-events.php'); ?>
					<h2>
						<?php echo __('Links'); ?>
					</h2>
					<?php pxLink::linkList(); ?>
				</div>
				<!-- col-content -->
			</div>
			<!-- end menuright -->

		</div>
		<!-- end main -->

	</div> 	<!-- end page -->
	<?php include(dirname(__FILE__).'/inc/footer.php'); ?>
</body>
</html>
