<div id="easy-access-right"></div>
<div id="easy-access">
     <p><a href="#content" title="<?php echo __('Go to content') ?>"><?php echo __('Go to content') ?></a> | <a href="#menuleft" title="<?php echo __('Go to menu') ?>"><?php echo __('Go to menu') ?></a></p>
     <form action="<?php pxInfo('url'); ?>search.php" method="get">
     <fieldset>
     <label for="q">
     <input type="text" name="q" value="<?php echo __('Search') ?>" id="q" />
     <input type="image" src="<?php pxInfo('filesurl'); ?>theme/cherry/img/bt-search.png" value="<?php echo __('Search') ?>" alt="Search" name="s" id="search-s" />
     </label>
     </fieldset>
     </form>
</div>
<div id="easy-access-left"></div><!-- easy-access -->

<div id="titre-left"></div>
<div id="titre">
     <h1 id="top"><a href="<?php pxInfo('url'); ?>" accesskey="1" title="<?php echo __('Back to the home page') ?>"><?php pxInfo('name'); ?></a></h1>
     <p class="description"><?php pxInfo('description'); ?></p>
</div>
<div id="titre-right"></div> <!-- titre -->