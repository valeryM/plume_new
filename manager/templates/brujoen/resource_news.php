<?php
if ($cache->processPage(180)):
    pxTemplateInit();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php pxInfo('encoding'); ?>" />
<meta name="MSSmartTagsPreventParsing" content="TRUE" />
<title><?php pxNewsTitle('%s'); ?></title>
<?php include(dirname(__FILE__).'/inc/head-link.php'); ?>
<?php pxHeadLinks(); ?>
<meta name="description" content="<?php pxNewsTitle('%s'); ?>" />
<?php include(dirname(__FILE__).'/inc/head-meta.php'); ?>
</head>

<body class="news">

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
               <h2 class="category"><?php echo __('News') ?></h2>
               <p class="modified"><?php echo __('Latest news in short.') ?></p>
               </div>
          </div>
          
          <div id="desc_foot"></div>
          <div id="main_frame">
               <div id="content">
               <div class="news-infos">
               <h2 class="restitle"><?php pxNewsTitle('%s'); ?></h2>
               <div class="footer_titre"></div>
               <div class="modif_left"></div>
               <p class="modificated"><?php echo __('The') ?> <a href="<?php pxNewsPath(); ?>"><?php pxNewsDateCreation(__('%Y-%m-%d</a> at %H:%M')); ?> <?php echo __('by') ?> <a class="email" href="<?php pxNewsAuthorEmail('mailto:%s'); ?>"><?php pxNewsAuthor(); ?></a><br /> <?php pxNewsCategories(__('In %s')); ?>.</p>
               <div class="modif_right"></div>
               <?php pxNewsContent(); ?>
               <?php include dirname(__FILE__).'/comments_inline.php'; ?>
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