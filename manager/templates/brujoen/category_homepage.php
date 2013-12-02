<?php
if ($cache->processPage(3600)):
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
               <h2 class="categories"><?php echo __('Categories') ?></h2>
                    <?php pxPrimaryCategories('<ul id="top-categories">%s</ul>'); ?>

               <h2 class="news"><?php echo __('In short') ?></h2>
                    <ul>
                    <?php while (!$res->EOF()): ?>
                    <li><a href="<?php pxResPath(); ?>" title="<?php pxResTitle(); ?>"><?php pxResTitle(); ?></a></li>
                    <?php
                    $res->moveNext();
                    endwhile; ?>
                    </ul>

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
          
          <div id="desc">
               <div id="desc_head"></div>
               <div id="descontenair">
               <h2 class="category"><?php echo __('Welcome') ?></h2>
               <p class="description"><?php pxInfo('description'); ?></p>
               <p class="modified"><?php echo __('Enjoy your stay on this website!') ?></p>
               </div>
          </div>
          <div id="desc_foot"></div>
          
          <div id="main_frame">
               <div id="content">
               <?php while (!$last->EOF()): ?>
               <div class="resource">
               <h2 class="restitle"><a href="<?php pxLastResPath(); ?>" title="<?php pxLastResTitle('%s'); ?>"><?php pxLastResTitle('%s'); ?></a></h2>
               <div class="footer_titre"></div>
               <div class="modif_left"></div>
               <p class="modificated"><a class="date" href="<?php pxLastResPath(); ?>" title="<?php echo __('Read the whole article') ?>"></a> <?php echo __('The') ?> <a href="<?php pxLastResPath(); ?>" title="<?php echo __('Publish Date of:') ?> <?php pxLastResTitle('%s'); ?>"><?php pxLastResDateModification(__('%Y-%m-%d</a> at %H:%M')); ?><br /><?php echo __('By') ?> <a class="email" href="<?php pxLastResAuthorEmail('mailto:%s'); ?>" title="<?php pxLastResAuthor(); ?><?php echo __(', publisher of') ?> <?php pxLastResTitle('%s'); ?>"><?php pxLastResAuthor(); ?></a></p>
               <div class="modif_right"></div>
               <?php pxLastResDescription(); ?>
               </div>
               <?php
               $last->moveNext();
               endwhile;
               ?>
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