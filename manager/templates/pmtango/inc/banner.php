<div id="title">
<!-- Beginning easy-access -->
<div id="easy-access">
  <p><a href="#content" title="<?php echo __('Go to the content'); ?>"><img src="<?php pxInfo('filesurl'); ?>theme/pstango/img/ico-content.png" alt="<?php echo __('Go to the content'); ?>" /></a>&nbsp;
     <a href="#menu" title="<?php echo __('Go to the menu'); ?>"><img src="<?php pxInfo('filesurl'); ?>theme/pstango/img/ico-menu.png" alt="<?php echo __('Go to the menu'); ?>" /></a>&nbsp;
     <a href="<?php pxInfo('url'); ?>?/sitemap/" title="<?php echo __('Sitemap'); ?>"><img src="<?php pxInfo('filesurl'); ?>theme/pstango/img/ico-sitemap.png" alt="<?php echo __('Sitemap'); ?>" /></a>&nbsp;
     <a href="<?php pxInfo('url'); ?>rss.php" title="<?php echo __('Feeds'); ?>"><img src="<?php pxInfo('filesurl'); ?>theme/pstango/img/ico-rssfeed.png" alt="<?php echo __('Feeds'); ?>" /></a>
     </p>
  <form action="<?php pxInfo('url'); ?>search.php" method="get">
   <fieldset>
    <label for="q">
     <input type="text" name="q" value="<?php echo __('Search'); ?>" id="q" />
     <input type="submit"  name="s" value="<?php echo __('OK'); ?>" id="search-s" />
    </label>
   </fieldset>
  </form>
</div>
<!-- End easy-access -->
      <h1><?php pxInfo('name'); ?></h1>
      <p class="description"><?php pxInfo('description'); ?></p>
</div>