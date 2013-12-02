<?php
pxTemplateInit('remove_numbers');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php pxInfo('encoding'); ?>" />
<meta name="MSSmartTagsPreventParsing" content="TRUE" />
<title>Search - <?php pxInfo('name'); ?></title>
<?php include(dirname(__FILE__).'/inc/head-link.php'); ?>
<link rel="schema.DC" href="http://purl.org/dc/elements/1.1/" />
<?php pxHeadLinks(); ?>
<meta name="DC.title" content="Search - <?php pxInfo('name'); ?>" />
<?php include(dirname(__FILE__).'/inc/head-meta.php'); ?>
</head>

<body class="category">

<div id="page">
     <div id="banner">
          <?php include(dirname(__FILE__).'/inc/easy-access.php'); ?>
          <?php include(dirname(__FILE__).'/inc/banner-content.php'); ?>
     </div> <!-- end banner -->

     <div id="col-content">
     <div id="col-content-head"></div>
          <div class="menuright">
          <div class="menuright_head"></div>
             <div class="col-content">
             <div id="research"></div>
             <h2 class="categoryz"><?php echo __('Result(s)') ?></h2>
             <p><?php echo __('We tried to search for the pages that match your request.') ?></p>
             <p class="modified"><?php echo __('Feel free to refine the search.') ?></p>
             <form id="search" action="<?php pxInfo('url'); ?>search.php" method="get">
             <p><input class="champ-chercher" type="text" name="q" value="<?php pxSearchQuery('%s'); ?>" id="q" />
             <input class="bouton-chercher" type="image" src="<?php pxInfo('filesurl'); ?>theme/orange/img/bt-search.png" name="s" id="search-s" /></p>
             </form>
             </div>
          <div class="menuright_foot"></div>
          </div>
          
          <div class="menuright">
          <div class="menuright_head"></div>
             <div class="col-content">
             <h2 class="categories"><?php echo __('Categorie(s)') ?></h2>
             <?php pxPrimaryCategories('<ul id="top-categories">%s</ul>'); ?>
             </div>
          <div class="menuright_foot"></div>
          </div>
          
          <div class="menuright">
          <div class="menuright_head"></div>
               <div class="col-content">
               <h2 class="links"><?php echo __('Links') ?></h2>
               <?php pxLink::linkList(); ?>
               </div>
          <div class="menuright_foot"></div>
          </div>
          
          <?php include(dirname(__FILE__).'/inc/rss-sitemap.php'); ?>          
     <div id="col-content-foot"></div>
     </div> <!-- end menu -->
     
     <div id="content">
          <?php if($res->EOF()): ?>
                <ol class="tree"><li><?php echo __('The search engine was unable to find any result.') ?></li></ol>
          <?php else: ?>
                <ol class="tree"><li><?php echo __('Result(s) of the search on') ?> <?php pxSearchQuery('<strong>%s</strong>.'); ?></li></ol>
          <?php endif; ?>
          <?php while (!$res->EOF()): ?>
                <div class="resource">
                <div class="resource_head"></div>
                <h2 class="restitle"><a href="<?php pxResPath(); ?>" title="<?php echo __('Read the whole story:') ?> <?php pxResTitle('%s'); ?>"><?php pxResTitle('%s'); ?></a></h2>
	              <p class="modified"><?php echo __('The') ?> <a href="<?php pxResPath(); ?>" title="<?php echo __('Publish Date of:') ?> <?php pxResTitle('%s'); ?>"><?php pxResDateModification(__('%Y-%m-%d at %H:%M')); ?></a> <?php echo __('by') ?> <a href="<?php pxResAuthorEmail('mailto:%s'); ?>" title="<?php pxResAuthor(); ?><?php echo __(', publisher of:') ?> <?php pxResTitle('%s'); ?>"><?php pxResAuthor(); ?></a>. <?php pxResCategories(__('In %s')); ?>.</p>
                </div>
                <div class="resource_foot"></div>
                <div class="art-description">
                <div class="art-description_head"></div>
                <?php pxResDescription(); ?>
                <span class="links-foot-art">
                <span class="score-search"><?php echo __('Score:') ?> <?php pxResSearchScore(); ?></span>
                <a class="readmore" href="<?php pxResPath(); ?>" title="<?php echo __('Read the whole story:') ?> <?php pxResTitle('%s'); ?>"><?php echo __('Read the whole story') ?></a>
                </span>
                <p class="modified"></p>
                <div class="art-description_foot"></div>
                </div>
          <?php
          $res->moveNext();
          endwhile; ?>
          <hr class="invisible"/>
     </div>
<?php include(dirname(__FILE__).'/inc/footer.php'); ?>