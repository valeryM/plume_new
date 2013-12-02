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
<meta name="DC.Title" content="Comments - <?php pxInfo('name'); ?>" />
</head>

<body class="category">

	<div id="page">
		<?php include dirname(__FILE__).'/inc/banner.php'; ?>
		<?php include dirname(__FILE__).'/inc/menu_top.php'; ?>
		<div id="main">
			<div id="mainfloat">
				<div id="content">
					<h2 class="comment-preview">
						<?php echo __('Comment preview'); ?>
					</h2>
					<div class="px-comment-preview">
						<?php pxCtContent('<p>%s</p>'); ?>
					</div>

					<?php pxCtErrors(__('<div class="px-comment-error">%s</div><h3 class="px-comment-add">Modify your comment</h3>')); ?>

					<form class="px-comment" action="<?php pxCtAction(); ?>"
						method="post">
						<fieldset>
							<?php pxCtSpamControl(); ?>
							<input name="redirect" value="<?php pxCtRedirect(); ?>"
								type="hidden" />
							<p>
								<label for='c_author'><?php echo __('Author:'); ?> </label> <input
									type="text" id='c_author' name="c_author"
									value="<?php pxCtAuthor(); ?>" />
							</p>
							<p>
								<label for='c_email'><?php echo __('Email:'); ?> </label> <input
									type="text" id='c_email' name="c_email"
									value="<?php pxCtEmail('%s', 'text'); ?>" />&nbsp;*
							</p>
							<p>
								<label for='c_website'><?php echo __('Website:'); ?> </label> <input
									type="text" id='c_website' name="c_website"
									value="<?php pxCtWeb(); ?>" />
							</p>
							<p>
								<label for='c_content'><?php echo __('Comment'); ?> </label><br />
								<textarea id='c_content' name="c_content" cols="80" rows="7">
									<?php pxCtContent('%s', 'textarea'); ?>
								</textarea>
							</p>
							<p class="px-comment-validation">
								<?php echo __('Your post will be online after validation by the webmaster.'); ?>
							</p>
							<p class="px-comment-required-email">
								*&nbsp;
								<?php echo __('required but not shared or displayed'); ?>
							</p>
							<p class="input-submit">
								<input type="submit" name="c_preview"
									value="<?php echo __('Preview'); ?>" /> <input type="submit"
									name="Submit" value="<?php echo __('Submit'); ?>" />
							</p>
						</fieldset>
					</form>

					<hr class="invisible" />
				</div>
				<!-- end col-content -->

			</div>
			<!-- end mainfloat -->

			<div id="menuright">
				<div class="col-content">
					<?php pxSingleCatTitle('<h2 class="category">%s</h2>'); ?>
					<div class="pxSingleCatDescription">
						<?php pxSingleCatDescription(); ?>
					</div>
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
		<?php include(dirname(__FILE__).'/inc/footer.php'); ?>
	</div>
	<!-- end page -->

</body>
</html>
