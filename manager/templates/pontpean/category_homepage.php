<?php 
if ($cache->processPage(180)):

pxTemplateInit('remove_numbers');
//pxGetLastResources();
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="<?php pxInfo('encoding'); ?>" />
	<!-- Set the viewport width to device width for mobile -->
	<meta name="viewport" content="width=device-width" />
	<meta name="MSSmartTagsPreventParsing" content="TRUE" />
	<?php include(dirname(__FILE__).'/inc/head-link.php'); ?>
	<?php include(dirname(__FILE__).'/inc/head-meta.php'); ?>
	<meta name="description" content="<?php pxSingleCatTitle('%s'); ?> - <?php pxMetasDescription(); ?>" />
	<meta name="DC.Description" content="<?php pxSingleCatTitle('%s'); ?> - <?php pxMetasDescription(); ?>" />
	<meta name="DC.source" content="<?php pxSingleCatPath()?>" scheme="URI" />
	<meta name="DC.Title" content="<?php pxSingleCatTitle('%s'); ?> - <?php pxInfo('name'); ?> (<?php echo $GLOBALS['_PX_render']['cat']->f('category_path'); ?>)" />
	<meta name="DC.Keywords" content="<?php echo pxSingleCatTitle('%s',true).' '.pxSingleCatPath('%s',false,true); ?>" />	
</head>

<body class="category_homepage homepage ">

	<div id="page">
		<?php include dirname(__FILE__).'/inc/banner.php'; ?>
		<div id="main">
		
			<div id="menuleft">
				<div id="Agenda">
					<div class="logo"></div>
					<div class="body_agenda">
						<?php 
							//Afficher les évènements
							$catList = pxArrayEventsCats(PX_CONFIG_EVENTS_LIST_AGENDA);
							$actus = getAllLastEventsFromCategories($catList['ids'], PX_CONFIG_TIMEBEFORE, PX_CONFIG_TIMEAFTER, 'content',PX_CONFIG_MAX_EVENTS);
							while (!$actus->EOF())  {
								$startDate = date::unix($actus->f('startdate'));
								//pxLastResAssociatedLink();
								$link = '&Annee='.date('Y',$startDate).'&Mois='.date('m',$startDate).'#'.$actus->f('path')
								?>
								<div class="event <?php  echo $catList['idTag'][$actus->f('category_id')]; ?>" >
									<a class="texteactu" href="/?<?php echo $actus->f('category_path').$link; ?>" >
										<span style="font-weight:bold;"><?php echo $actus->f('title'); ?></span><br/>
										<?php echo __(date('l',$startDate)).' '.date('d',$startDate).' '.__(date('F',$startDate)); ?>
									</a>
									<br/>
									<hr width="188px" class="sepActu" />
								</div>
								<?php 
								$actus->moveNext();
							}
							?>
					</div>
					<div class="bottom_agenda">
						<span class="seeAll" style="position:relative;left:5px;">
						<?php $category = pxGetMasterCategoriesByName(PX_CONFIG_EVENTS); ?>
							<a href="<?php echo $category->getPath(); //$category->f('category_path')?>">Tout l'agenda</a>
						</span>						
					</div>
				</div>
				<div class="divSeparatorSmall"></div>
				<div id="AgendaCulturel">
					<div class="logo"></div>
					<div class="texteActu">
						<div class="body_agenda">
						<?php 
							//Afficher les évènements
							$catList = pxArrayEventsCats(PX_CONFIG_EVENTS_CULTUREL);
							$actus = getAllLastEventsFromCategories($catList['ids'], PX_CONFIG_TIMEBEFORE, PX_CONFIG_TIMEAFTER, 'short_content',PX_CONFIG_MAX_EVENTS_CULTUREL);

							while (!$actus->EOF())  {
								$startDate = date::unix($actus->f('startdate'));
								//pxLastResAssociatedLink();
								$link = '&Annee='.date('Y',$startDate).'&Mois='.date('m',$startDate).'#'.$actus->f('path')
								?>
								<div class="event <?php  echo $catList['idTag'][$actus->f('category_id')]; ?>" >
									<a class="texteactuCulturel" href="/?/<?php echo $actus->f('category_path').$link; ?>" >
										<span style="font-weight:bold;"><?php echo $actus->f('title'); ?></span><br/>
										<?php echo __(date('l',$startDate)).' '.date('d',$startDate).' '.__(date('F',$startDate)); ?>
										<br/>
										<?php echo text::parseContent($actus->f('shortcontent'));?>
									</a>
								</div>
								<?php 
								$actus->moveNext();
							}							
						?>
						</div>					
					</div>
					<div class="logobas"></div>
					<div class="info1">
						<span class="seeAll" style="position:relative;left:5px;">
						<?php $category = FrontEnd::getCategory($catList['ids'][0]); ?>
							<a href="<?php echo $category->getPath();?>">Toute la saison culturelle</a>
						</span>
					</div>
					<div class="info2">
						<span class="seeAll" style="position:relative;left:5px;">							
							<a href="/?<?php echo PX_CONFIG_SALLES_CULTUREL; ?>">Location des salles</a>
						</span>
					</div>
				</div>
			</div>
			<div id="mainfloat">
				<div id="content">
					<div id="zoneMenuRoot">
                    	<div id="menu">
							<div class="menuPrincipal decouvrir">
								<a href="/?/Decouvrir/" title=""></a>
								<div class="image"></div>								
									<?php 
									pxAfficheTooltipCategories(PX_CONFIG_MASTER_1,1.1);
									?>									
							</div>
							<div class="menuPrincipal vivre">
								<a href="/?/Vivre/" title=""></a>
								<div class="image"></div>								
									<?php 
									pxAfficheTooltipCategories(PX_CONFIG_MASTER_3,1.1);
									?>								
							</div>
							<div class="menuPrincipal agir">
								<a href="/?/Agir/" title=""></a>
								<div class="image"></div>								
									<?php 
									pxAfficheTooltipCategories(PX_CONFIG_MASTER_2,1.1);
									?>								
							</div>
						</div>
                    </div>	
					<div class="news">
						<div id="News" style="display:block">
							<div class="titreNews"> </div>
							<div class="cadreNews" style="text-align:center;">
								<div id="slides" class="slides-default">
									<div class="slides-container">								
											<?php pxGetLastResources('','news,events',PX_CONFIG_CAT_NEWS,true); ?>
											<?php while (!$last->EOF() ): ?>
												<?php 
												if ($last->f('type_id') == 'events') {
													$contenu = text::parseContent($last->f('event_shortcontent'));
												} else {
													$contenu = text::parseContent($last->f('news_shortcontent'));
												}
												if (strpos($contenu,'class="titreAlaUne"')!=false)	{
												?>
												<div class="slides-content">
													<a href="<?php echo $last->getPath(); //$last->f('path'); ?>" title="">
													<?php 														
														echo $contenu;
													?>
													</a>
												</div>
												<?php 
												} //end if
												$last->moveNext();
											endwhile; ?>
									</div>
								</div>
								<div class="bandeauNews"></div>								
							</div>							
						</div>
					</div>
					<div><hr class="invisible" /></div>
					<div class="actus">					
						<div id="Actus">
							<div class="titreActus"> </div>
							<div class="divSeparator"> </div>
							<div class="cadreActus" style="text-align:center;">
								<?php 
									pxGetLastResources(5,'news',PX_CONFIG_CAT_ACTUS,true); 
									$nbActus=0;
								?>
								<?php while (!$last->EOF() ): ?>
									<div id="actu-<?php echo $nbActus++; ?>" class="rowActus">
										<span class="actu-title" ><?php pxLastResTitle(); ?></span>
										<span class="actu-shortContent"><?php pxLastNewsShortContent(); ?></span>
										<?php pxLastResAssociatedLink(); ?>
										<span class="actu-lireLaSuite" >
											<a href="<?php pxLastResPath();?>" >Lire la suite..</a>
										</span>
									</div>
									<?php
									$last->moveNext();
									if (!$last->EOF())
										?>
										<div class="separateurNews"> </div>
										<?php 
								endwhile; ?>							
							</div>
							<div class="linkActus"  >
								<span class="seeAll">
									<a href="/?<?php echo PX_CONFIG_ALL_ACTUS; ?>">Voir toute l'actualité</a>
								</span>
							</div>
						</div>
					</div>
				</div>
				<!-- end content -->

			</div>
			<!-- end mainfloat -->

			<div id="menuright">
				<div id="infoPratique">				
					<?php require('inc/links.php'); ?>
					<?php require('inc/rssInfos.php'); ?>
					<div class="divSeparator"></div>
					<div style="align:center; text-align:center; width:194px;">
						<img src="<?php pxInfo('filesurl'); ?>theme/<?php pxInfo('theme'); ?>/img/icons/logo_pontpean.png" alt="logo_pontpean"></img>
					</div>
				</div>
				</div>
			</div> 	<!-- end menuright -->

		</div> 	<!-- end main -->
	</div> 	<!-- end page -->
	<?php include(dirname(__FILE__).'/inc/footer.php'); ?>
</body>
</html>
<?php
    $cache->endCache();
endif;
?>
