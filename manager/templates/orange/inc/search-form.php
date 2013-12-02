<div id="research"></div>
<h2 class="formsearch"><?php echo __('Search') ?></h2>
<form id="search" action="<?php pxInfo('url'); ?>search.php" method="get">
<p><input class="champ-chercher" type="text" name="q" value="<?php pxSearchQuery('%s'); ?>" id="q" />
<input class="bouton-chercher" type="image" src="<?php pxInfo('filesurl'); ?>theme/orange/img/bt-search.png" name="s" id="search-s" /></p>
</form>