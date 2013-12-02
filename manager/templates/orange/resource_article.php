<?php
if ($cache->processPage(180)):
    pxTemplateInit('remove_numbers');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php pxInfo('encoding'); ?>" />
<meta name="MSSmartTagsPreventParsing" content="TRUE" />
<title><?php pxSingleCatTitle('%s'); ?> - <?php pxArtTitle('%s'); ?></title>
<?php include(dirname(__FILE__).'/inc/head-link.php'); ?>
<meta name="description" content="<?php pxSingleCatTitle('%s'); ?> : <?php pxArtTitle('%s'); ?> - <?php pxMetasDescription(); ?>" />
<meta name="DC.Date.modified" scheme="W3CDTF" content="<?php pxArtDatePublication('%Y-%m-%d'); ?>" />
<?php include(dirname(__FILE__).'/inc/head-meta.php'); ?>
<meta name="DC.Author" content="<?php pxResAuthor(); ?>" />
<meta name="DC.Title" content="<?php pxSingleCatTitle('%s'); ?> - <?php pxArtTitle('%s'); ?>" />
</head>

<body class="category">

<div id="page">
     <div id="banner">
          <?php include(dirname(__FILE__).'/inc/easy-access.php'); ?>
          <?php include(dirname(__FILE__).'/inc/banner-content.php'); ?>
     </div> <!-- end banner -->

     <div id="col-content">
     <div id="col-content-head"></div>
          <?php pxArtListPages(__('<div class="menuright"><div class="menuright_head"></div><div class="col-content"><h2 class="pagesarticles">Pages of the article</h2> %s </div><div class="menuright_foot"></div></div>')); ?>

          <div class="menuright">
               <div class="menuright_head"></div>
               <div class="col-content">
               <h2 class="categoryz"><a href="<?php pxSingleCatPath(); ?>"><?php pxSingleCatTitle('%s'); ?></a></h2>
               <?php pxSingleCatDescription(); ?>
               <p class="modified"><?php pxSingleCatNbResources(__('No resources'), __('1 resource'), __('<strong>%s</strong> resources')); ?> <?php echo __('in this category.') ?></p>
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
               <?php include(dirname(__FILE__).'/inc/recent-news.php'); ?>
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

           <div class="resource">
           <div class="resource_head"></div>
           <h2 class="restitle"><?php pxArtTitle(); ?></h2>
           <p class="modified"><?php echo __('The') ?> <?php pxArtDateModification(__('%Y-%m-%d at %H:%M'), '%s'); ?> <?php echo __('by') ?> <a href="<?php pxArtAuthorEmail('mailto:%s'); ?>" title="<?php pxArtAuthor(); ?><?php echo __(', publisher of:') ?> <?php pxArtTitle('%s'); ?>"><?php pxArtAuthor(); ?></a>. <?php pxArtCategories(__(' In %s'), ', ', __(' and ')); ?>.<br />
           <?php echo __('Number of comments:') ?> <a href="<?php pxArtPath(); ?>#leave-comment" title="<?php echo __('Read the comments for:') ?> <?php pxArtTitle('%s'); ?>"><?php pxArtCountComments() ?></a></p>
           </div>
           <div class="resource_foot"></div>
           
           <div class="art-description">
           <div class="art-description_head"></div>
           <?php pxArtDescription(); ?>
           <div class="art-description_foot"></div>
           </div>

           <h2 class="art-page-title"><?php pxArtPageTitle(); ?></h2>
           <?php pxArtPageContent(); ?>
           <?php pxArtListPages(__('<div id="art-pages-list"><h3>Pages of the article</h3> %s</div>')); ?>
           <?php include dirname(__FILE__).'/comments_inline.php'; ?>

           <hr class="invisible"/>
      </div>

<?php include(dirname(__FILE__).'/inc/footer.php'); ?>
<?php
    $cache->endCache();
endif;
?>