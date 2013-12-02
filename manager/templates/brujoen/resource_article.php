<?php
if ($cache->processPage(180)):
    pxTemplateInit('remove_numbers');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php pxInfo('encoding'); ?>" />
<meta name="MSSmartTagsPreventParsing" content="TRUE" />
<title><?php pxArtTitle('%s'); ?></title>
<?php include(dirname(__FILE__).'/inc/head-link.php'); ?>
<?php pxHeadLinks(); ?>
<meta name="description" content="<?php pxInfo('description'); ?>" />
<?php include(dirname(__FILE__).'/inc/head-meta.php'); ?>
</head>

<body class="category">

<div id="page">
     <div id="frame_header"></div>
     <div id="banner">
          <div id="banner_body">
               <?php include(dirname(__FILE__).'/inc/easy-access.php'); ?>
               <?php include(dirname(__FILE__).'/inc/banner-content.php'); ?>
          </div>
          <div id="banner_right"></div>
     </div>
     
     <div class="contenair">
          <div id="secontenair">
          
          <div id="col-content">
               <h2 class="back"><?php echo __('Back to') ?></h2>
               <ul><li><a href="<?php pxSingleCatPath(); ?>"><?php pxSingleCatTitle('%s'); ?></a></li></ul>
               
               <?php pxSubCategories(__('<h2 class="subcategories">Categorie(s)</h2> %s')); ?>

               <h2 class="categories"><?php echo __('Main categorie(s)') ?></h2>
               <?php pxPrimaryCategories('<ul id="top-categories">%s</ul>'); ?>

               <h2 class="search"><?php echo __('Search') ?></h2>
                    <form id="search" action="<?php pxInfo('url'); ?>search.php" method="get">
                    <input type="text" style="width:180px;margin:0px 0px 5px 0px" name="q" value="" id="q" />
                    <input style="float:right;margin:-25px 0px 0px 0px" type="image" src="<?php pxInfo('filesurl'); ?>theme/brujoen/img/bt-search.png" name="s" id="search-s" />
                    </form>

               <h2 class="links"><?php echo __('Links') ?></h2>
                    <?php pxLink::linkList(); ?>

               <h2 class="extra"><?php include(dirname(__FILE__).'/inc/rss-sitemap.php'); ?></h2>

               <div id="menuleft_foot"></div>
          </div> <!-- end menu -->
          
          <div id="navig">
               <div id="navig_right"></div>
               <?php pxSingleCatTree('<ol class="tree">%s</ol>'); ?>
               <div id="navig_left"></div>
          </div>
          
          <div id="desc">
               <div id="desc_head"></div>
               <div id="descontenair">
               <h2 class="category"><?php pxArtTitle(); ?></h2>
               <div id="art-description">
               <?php pxArtDescription(); ?>
               </div>
               </div>
          </div>
          
          <div id="desc_foot"></div>
          <div id="main_frame">
               <div id="content">
               <div class="resource">
               <h2 class="restitle"><?php pxArtPageTitle(); ?></h2>
               <div class="footer_titre"></div>
               <div class="modificated_article">
               <?php if (pxArtPageIsFirst()): ?>
               <div class="modif_left"></div>
               <p class="modificated"><?php echo __('The') ?> <a href="<?php pxArtPath(); ?>"><?php pxArtDateModification(__('%Y-%m-%d</a> at %H:%M'), '%s'); ?> <?php echo __('by') ?> <a class="email" href="<?php pxResAuthorEmail('mailto:%s'); ?>" title="<?php echo __('Email the author of this article') ?>"><?php pxResAuthor(); ?> </a><?php pxResCategories(__('<br />In %s')); ?>.</p>
               <div class="modif_right"></div>
               <?php else: ?>
               <div class="modif_left"></div>
               <p class="modificated"><?php echo __('The') ?> <a href="<?php pxArtPath(); ?>"><?php pxArtDateModification(__('%Y-%m-%d</a> at %H:%M'), '%s'); ?> <?php echo __('by') ?> <a class="email" href="<?php pxResAuthorEmail('mailto:%s'); ?>" title="<?php echo __('Email the author of this article') ?>"><?php pxResAuthor(); ?></a> <?php pxResCategories(__('<br />In %s')); ?>.</p>
               <div class="modif_right"></div>
               <?php endif; ?>
               </div>
               <?php pxArtListPages(__('<div id="pages"><div id="pages_head"></div><div id="pagescontenair"><div id="art-pages-list"><h3>Pages of the article</h3> %s</div></div><div id="pages_foot"></div></div>')); ?>
               <div class="resource_article">
               <?php pxArtPageContent(); ?>
               </div>
               <div class="commentaire">
               <?php include dirname(__FILE__).'/comments_inline.php'; ?>
               </div>
               <hr class="invisible"/>
               </div>
               </div>
          </div> <!-- end main -->
          </div>
     </div>
     <div class="contenair">
     <div id="menu_mask"></div>
     </div>
     
<?php include(dirname(__FILE__).'/inc/footer.php'); ?>
<?php
    $cache->endCache();
endif;
?>