		<div id="easy-access">
			<p><a href="#content" title="<?php echo __('Go to the content'); ?>"><?php echo __('Go to the content'); ?></a> | <a href="#menuleft" title="<?php echo __('Go to the menu'); ?>"><?php echo __('Go to the menu'); ?></a></p>
			<form action="<?php pxInfo('url'); ?>search.php" method="get">
				<fieldset>
					<label for="q">
						<input type="text" name="q" value="<?php echo __('Search'); ?>" id="q" />
						<input type="image" src="<?php pxInfo('filesurl'); ?>theme/default/img/bt-search.png" value="Search" alt="<?php echo __('Search'); ?>" name="s" id="search-s" />
					</label>
				</fieldset>
			</form>
		</div><!-- easy-access -->