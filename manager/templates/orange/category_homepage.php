<?php
if ($cache->processPage(180)):
    pxTemplateInit('remove_numbers');
    pxGetLastResources();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="<?php pxInfo('lang'); ?>">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php pxInfo('encoding'); ?>" />
<meta name="MSSmartTagsPreventParsing" content="TRUE" />
<title><?php pxInfo('name'); ?></title>
<?php include(dirname(__FILE__).'/inc/head-link.php'); ?>
<meta name="description" content="<?php pxInfo('description'); ?>" />
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
               <h2 class="descmenu"><?php echo __('Welcome') ?></h2>
               <p class="description"><?php pxInfo('description'); ?></p>
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
               <?php include(dirname(__FILE__).'/inc/recent-news.php'); ?>
               </div>
          <div class="menuright_foot"></div>
          </div>
          
          <div class="menuright">
          <div class="menuright_head"></div>
               <div class="col-content">
               <?php include(dirname(__FILE__).'/inc/search-form.php'); ?>
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
     </div>
          
     <div id="content">
          <?php pxGetLastResources(); ?>
          <?php while (!$last->EOF()): ?>
          <div class="resource">
          <div class="resource_head"></div>
          <h2 class="restitle"><a href="<?php pxLastResPath(); ?>" title="<?php echo __('Read the whole story:') ?> <?php pxLastResTitle('%s'); ?>"><?php pxLastResTitle('%s'); ?></a></h2>
          <p class="modified"><?php echo __('The') ?> <a href="<?php pxLastResPath(); ?>" title="<?php echo __('Publish Date of:') ?> <?php pxLastResTitle('%s'); ?>"><?php pxLastResDateModification(__('%Y-%m-%d</a> at %H:%M')); ?> <?php echo __('by') ?> <a href="<?php pxLastResAuthorEmail('mailto:%s'); ?>" title="<?php pxLastResAuthor(); ?><?php echo __(', publisher of:') ?> <?php pxLastResTitle('%s'); ?>"><?php pxLastResAuthor(); ?></a>. <?php pxLastResCategories(__(' In %s'), ', ', __(' and ')); ?>.</p>
          </div>
          <div class="resource_foot"></div>
          <div class="art-description">
          <div class="art-description_head"></div>
          <?php pxLastResDescription(); ?>
          <span class="links-foot-art">
          <a class="readmore" href="<?php pxLastResPath(); ?>" title="<?php echo __('Read the whole story:') ?> <?php pxLastResTitle('%s'); ?>"><?php echo __('Read the whole story') ?></a>
          <a class="laisser-comment" href="<?php pxLastResPath(); ?>#leave-comment" title="<?php echo __('Write a comment for:') ?> <?php pxLastResTitle('%s'); ?>"><?php echo __('Write a comment') ?></a>
          </span>
          <div class="art-description_foot"></div>
          </div>
          <?php
          $last->moveNext();
          endwhile;
          ?>
          <hr class="invisible"/>
     </div>

<?php include(dirname(__FILE__).'/inc/footer.php'); ?>
<?php
    $cache->endCache();
endif;
?>