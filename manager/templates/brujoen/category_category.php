<?php
if ($cache->processPage(3600)):
    pxTemplateInit('order_res_manual|res_per_page:10|remove_numbers');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php pxInfo('encoding'); ?>" />
<meta name="MSSmartTagsPreventParsing" content="TRUE" />
<title><?php pxInfo('name'); ?></title>
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
          
          <!-- menu -->
          <div id="col-content">
               <div class="col-titre"><h2 class="back"><?php echo __('Back to') ?></h2></div>
               <ul><li><a href="<?php pxParentCatPath(); ?>"><?php pxParentCatTitle('%s'); ?></a></li></ul>
               
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
          
          <!-- Navigation within the category tree -->
          <div id="navig">
               <div id="navig_right"></div>
               <?php pxSingleCatTree('<ol class="tree">%s</ol>'); ?>
               <div id="navig_left"></div>
          </div>
          
          <!-- Description of the category -->
          <div id="desc">
               <div id="desc_head"></div>
               <div id="descontenair">
               <?php pxSingleCatTitle('<h2 class="category">%s</h2>'); ?>
               <?php pxSingleCatDescription(); ?>
               <p class="modified"><?php pxSingleCatNbResources(__('No resources'), __('1 resource'), __('<strong>%s</strong> resources')); ?> <?php echo __('in this category.') ?></p>
               </div>
          </div>
          <div id="desc_foot"></div>
          
          <div id="main_frame">
               <div id="content">
               <?php while (!$res->EOF()): ?>
               <div class="resource">
               <h2 class="restitle"><a href="<?php pxResPath(); ?>"><?php pxResTitle('%s'); ?></a></h2>
               <div class="footer_titre"></div>
               <div class="modif_left"></div>
               <p class="modificated"><a class="date" href="<?php pxResPath(); ?>" title="<?php echo __('Read the whole article') ?>"></a> <?php echo __('The') ?> <?php pxResDateModification(__('%Y-%m-%d at %H:%M')); ?> <?php echo __('by') ?> <a class="email" href="<?php pxResAuthorEmail('mailto:%s'); ?>" title="<?php echo __('Email the author of this article') ?>"><?php pxResAuthor(); ?></a>. <?php pxResCategories(__('<br />In %s')); ?>.</p>
               <div class="modif_right"></div>
               <?php pxResDescription(); ?>
               </div>
               <?php
               $res->moveNext();
               endwhile; ?>
               <?php pxSingleCatNextPage(-1,__('<p><a href="%s">Previous Page</a></p>')); ?>
	       <?php pxSingleCatNextPage(1,__('<p><a href="%s">Next Page</a></p>')); ?>
               <hr class="invisible"/>
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