<?php
if ($cache->processPage(180)):
    pxTemplateInit('order_res_manual|res_per_page:10|remove_numbers');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php pxInfo('encoding'); ?>" />
<meta name="MSSmartTagsPreventParsing" content="TRUE" />
<title><?php pxSingleCatTitle('%s'); ?> - <?php pxInfo('name'); ?></title>
<?php include(dirname(__FILE__).'/inc/head-link.php'); ?>
<meta name="description" content="<?php pxSingleCatTitle('%s'); ?> - <?php pxMetasDescription(); ?>" />
<?php include(dirname(__FILE__).'/inc/head-meta.php'); ?>
<meta name="DC.Title" content="<?php pxSingleCatTitle('%s'); ?> - <?php pxInfo('name'); ?>" />
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
             <h2 class="categoryz"><?php pxSingleCatTitle('%s'); ?></h2>
             <?php pxSingleCatDescription(); ?>
             <p class="modified"><?php pxSingleCatNbResources(__('No resources'), __('1 resource'), __('<strong>%s</strong> resources')); ?> <?php echo __('in this category.') ?></p>
             </div>
        <div class="menuright_foot"></div>
        </div>
	
        <div class="menuright">
        <div class="menuright_head"></div>
             <div class="col-content">
             <h2 class="goback"><?php echo __('Back to') ?></h2>
             <ul><li><a href="<?php pxParentCatPath(); ?>"><?php pxParentCatTitle('%s'); ?></a></li></ul>
             </div>
        <div class="menuright_foot"></div>
        </div>
        
        <div class="menuright">
        <div class="menuright_head"></div>
             <div class="col-content">
             <?php pxSubCategories(__('<h2 class="subcategories">Categorie(s)</h2> %s')); ?>
             <h2 class="categories"><?php echo __('Main Categorie(s)') ?></h2>
             <?php pxPrimaryCategories('<ul id="top-categories">%s</ul>'); ?>
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
          <?php pxSingleCatTree('<ol class="tree">%s</ol>'); ?>
          <?php if (!$res->EOF()): ?>
          <?php while (!$res->EOF()): ?>
          <div class="resource">
          <div class="resource_head"></div>
          <h2 class="restitle"><a href="<?php pxResPath(); ?>" title="<?php echo __('Read the whole story:') ?> <?php pxResTitle('%s'); ?>"><?php pxResTitle('%s'); ?></a></h2>
	        <p class="modified"><?php echo __('The') ?> <a href="<?php pxResPath(); ?>" title="<?php echo __('Publish Date of:') ?> <?php pxResTitle('%s'); ?>"><?php pxResDateModification(__('%Y-%m-%d at %H:%M')); ?></a> <?php echo __('by') ?> <a href="<?php pxResAuthorEmail('mailto:%s'); ?>" title="<?php pxResAuthor(); ?><?php echo __(', publisher of:') ?> <?php pxResTitle('%s'); ?>"><?php pxResAuthor(); ?></a>. <?php pxResCategories(__(' In %s'), ', ', __(' and ')); ?>.</p>
          </div>
          <div class="resource_foot"></div>
          <div class="art-description">
          <div class="art-description_head"></div>
          <?php pxResDescription(); ?>
          <span class="links-foot-art">
          <a class="readmore" href="<?php pxResPath(); ?>" title="<?php echo __('Read the whole story:') ?> <?php pxResTitle('%s'); ?>"><?php echo __('Read the whole story') ?></a>
          <a class="laisser-comment" href="<?php pxResPath(); ?>#leave-comment" title="<?php echo __('Write a comment for:') ?> <?php pxResTitle('%s'); ?>"><?php echo __('Number of comments:') ?> <?php pxResCountComments() ?></a>
          </span>
          <div class="art-description_foot"></div>
          </div>
          <?php
          $res->moveNext();
          endwhile; ?>
          <?php pxSingleCatNextPage(-1,__('<p><a href="%s">Previous Page</a></p>')); ?>
	        <?php pxSingleCatNextPage(1,__('<p><a href="%s">Next Page</a></p>')); ?>
          <?php else: ?>
          <div class="resource">
          <div class="resource_head"></div>

          <h2 class="restitle"><?php echo __('Sorry,') ?></h2>
          <p class="modified"><?php echo __('Check back later...') ?></p>
          <p><?php echo __('There is no resource for the moment in this category.') ?></p>
          </div>
          <div class="resource_foot"></div>
          <?php endif; ?>
          <hr class="invisible"/>
   </div>
   
<?php include(dirname(__FILE__).'/inc/footer.php'); ?>
<?php
    $cache->endCache();
endif;
?>