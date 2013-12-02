		<div id="easy-access">
			<!--
			<div style="line-height:18px;">			 
				<a href="#content" title="<?php //echo __('Go to the content'); ?>">
					<?php //echo __('Go to the content'); ?>
				</a>
				&nbsp;|&nbsp;
				<a href="#menuleft" title="<?php //echo __('Go to the menu'); ?>">
					<?php //echo __('Go to the menu'); ?>
				</a>				
			</div>
			-->
			<div >
				<form action="<?php pxInfo('url'); ?>search.php" method="get">
					<fieldset>
						<label for="q">
							<input type="text" name="q" value="<?php echo __('Search'); ?>" id="q" onclick="$(this).val('');" />
							<input type="image" src="<?php pxInfo('filesurl'); ?>theme/<?php pxInfo('theme'); ?>/img/bt-search.png" value="Search" alt="<?php echo __('Search'); ?>" name="s" id="search-s" />
						</label>
					</fieldset>
				</form>
			</div>
		</div><!-- easy-access -->