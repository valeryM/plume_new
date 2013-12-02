<div id="footer">
	<span><?php include(dirname(__FILE__).'/rss-sitemap.php'); ?></span>
	<p><?php echo __('POWERED by'); ?> <a href="http://www.plume-cms.net/" title="<?php echo __('Powered by PLUME CMS'); ?>">
		<img src="<?php pxInfo('filesurl'); ?>theme/<?php pxInfo('theme'); ?>/img/plume-cms-powered.png" alt="<?php echo __('Logo of Plume CMS'); ?>" height="18" width="55" />
		</a> <?php echo __('DESIGN by'); ?> Valéry MERLET		
	</p>
</div><!-- end footer -->
<?php pxGallery(); ?>