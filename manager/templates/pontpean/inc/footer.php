<div id="footer">
	<div class="bandeaubas">
	<?php pxLink::linkListByArea("basPage",'','<div class="linksBasPage">%s</div>','<div class="link">%s</div>');?>
	<div><?php pxInfo('website_name')?></div>
	</div>
	<!--  
	<span><?php //require(dirname(__FILE__).'/rss-sitemap.php'); ?></span>
	-->
	<p><?php echo __('POWERED by'); ?> <a href="http://www.plume-cms.net/" title="<?php echo __('Powered by PLUME CMS'); ?>">
		<img src="<?php pxInfo('filesurl'); ?>theme/<?php pxInfo('theme'); ?>/img/plume-cms-powered.png" alt="<?php echo __('Logo of Plume CMS'); ?>" height="18" width="55" />
		</a> <?php echo __('DESIGN by'); ?> Valéry MERLET & Communication / Mairie Pont-péan		
	</p>
</div><!-- end footer -->

<script src="<?php pxInfo('filesurl'); ?>theme/<?php pxInfo('theme')?>/js/jquery.corner.js" ></script>
<!-- <script src="<?php pxInfo('filesurl'); ?>theme/<?php pxInfo('theme')?>/js/jquery.hoverIntent.minified.js"></script> -->
<script src="<?php pxInfo('url'); ?>manager/js/superfish/js/hoverIntent.js"></script>
<script src="<?php pxInfo('filesurl'); ?>theme/<?php pxInfo('theme')?>/js/menuTooltip.js"></script>
<script src="<?php pxInfo('filesurl'); ?>theme/<?php pxInfo('theme')?>/js/sliderHomepage.js"></script>
<script src="<?php pxInfo('filesurl'); ?>theme/<?php pxInfo('theme')?>/js/slides/slides.jquery.js"></script>
<link rel="stylesheet" href="<?php pxInfo('filesurl'); ?>theme/<?php pxInfo('theme')?>/js/slides/slides_default.css" type="text/css" media="all" />
<script src="<?php pxInfo('filesurl'); ?>theme/<?php pxInfo('theme')?>/js/imageFlow/imageflow.js"></script>
<script src="<?php pxInfo('filesurl'); ?>theme/<?php pxInfo('theme')?>/js/lightbox/js/jquery.lightbox-0.5.js"></script>

<!-- Google Analytics -->
<script type="text/javascript">
	$(document).ready(function() {
		var _gaq = _gaq || [];
		_gaq.push(['_setAccount', 'UA-36112666-1']);
		_gaq.push(['_trackPageview']);
		
		(function() {
		var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
		ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
		var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
		})();
	});
</script>

<?php //pxGallery(); ?>