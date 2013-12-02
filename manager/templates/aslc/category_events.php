<?php
if ($cache->processPage(180)):
   
pxTemplateInit('order_res_manual|res_per_page:10|remove_numbers');
//pxGetLastResources();
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="<?php pxInfo('encoding'); ?>" />
	<!-- Set the viewport width to device width for mobile -->
	<meta name="viewport" content="width=device-width" />
	<meta name="MSSmartTagsPreventParsing" content="TRUE" />
	<title><?php pxSingleCatTitle('%s'); ?> - <?php pxInfo('name'); ?></title>
	<?php include(dirname(__FILE__).'/inc/head-link.php'); ?>
	<meta name="description" content="<?php pxSingleCatTitle('%s'); ?> - <?php pxMetasDescription(); ?>" />
	<?php include(dirname(__FILE__).'/inc/head-meta.php'); ?>
	<meta name="DC.Title" content="<?php pxSingleCatTitle('%s'); ?> - <?php pxInfo('name'); ?>" />
</head>

<body class="category">

	<div id="page">
		<?php include dirname(__FILE__).'/inc/banner.php'; ?>
		<?php include dirname(__FILE__).'/inc/menu_top.php'; ?>
		<div id="main">
			<?php pxSingleCatTree('<ol class="tree">%s</ol>'); ?>
			
			<div id="mainfloat">
				<div id="content">
					<?php 
					// Les catégories des évènements
					//$catList = pxGetArrayEventsCats(PX_CONFIG_EVENTS_LIST_ALL);
					$eventCat = $GLOBALS['_PX_render']['cat'];
					$catList = pxGetArrayEventsCats('all='.$eventCat->f('category_name'));
					//$catList = Array($eventCat);
					$array_path = array();
					$Annee = date('Y');	// Année par défaut
					$Mois = date('m');	// Mois par défaut
					if (isset($_GET['Annee'])) $Annee = $_GET['Annee'];
					if (isset($_GET['Mois'])) $Mois = $_GET['Mois'];
					
					if ($Annee==date('Y') && $Mois == date('m') & $res->nbRowTotal()==1) {
						// une resource sélectionnée ? on charge les infos qui correspondent
						//$res = $m->getResource($m->array_path['res'][0],'',false);
						$Annee = date('Y',date::unix($res->f('publicationdate')));
						$Mois = date('m',date::unix($res->f('publicationdate')));
						//echo 'année:'.$Annee.' mois:'.$Mois;
					} else {
						//$resources = $m->getResourcesFromCat($idCat);
						while (!$res->EOF()) {
							$array_path['res'][] = $res->f('resource_id');
							$res->moveNext();
						}
					}

					// affichage du calendrier de navigation
					$queryPath = $eventCat->f('category_path');					
					$Month = array('Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Août','Septembre', 'Octobre' , 'Novembre','Décembre');
					// affiche le calendrier pour la navigation
					
					?>
					<div id="Calendrier" align="center">
						<table  style="border:0;padding:0;border-collapse: collapse;test-align:center;" >
							<tr>
								<td valign="middle">
									<?php 
									//affiche l'année -1
									$annee = $Annee-1;
									$mois = 12;
									require('inc/event_yearLink.php');
									?>
								</td>
								<td >
									<table style="padding:0;border-collapse: collapse;">
										<tr>
											<td colspan="6" align="center" valign="middle">
												<h3>Calendrier des évènements&nbsp;<?php echo $Annee; ?></h3>
											</td>
										</tr>
										<tr>
										<?php 
											
											// boucle sur le 1er semestre
											$rep = '';
											for ($i=0;$i<6;$i++)  {
												$rep .= '<td class="EventMonth" width="80px" align="center" valign="middle">';
												$href = '/?'.$queryPath.'&amp;Annee='.$Annee.'&amp;Mois='.($i+1);
												$class = 'button';
												if ($Mois == $i+1 ) $class='buttonSel';
												$rep .= '<a class="'.$class.'" href="'.$href.'" onclick="this.blur();"><span>'. $Month[$i].'</span></a></td>';
											}
											echo $rep;
										?>
										</tr>
										<tr>
										<?php 
											// boucle sur le 2nd semestre
											$rep = '';
											for ($i=6;$i<12;$i++)  {
												$rep .= '<td class="EventMonth" width="80px" align="center" valign="middle">';
												$href = '/?'.$queryPath.'&amp;Annee='.$Annee.'&amp;Mois='.($i+1);
												$class = 'button';
												if ($Mois == $i+1 ) $class='buttonSel';
												$rep .= '<a class="'.$class.'" href="'.$href.'" onclick="this.blur();"><span>'. $Month[$i].'</span></a></td>';
											}
											echo $rep;					
										?>
										</tr>
									</table>
								</td>
								<td valign="middle">
									<?php 
										//affiche l'année -1
										$annee = $Annee+1;
										$mois = 1;
										require('inc/event_yearLink.php');
									?>
								</td>
							</tr>
						</table>
					</div>
					<?php 
					
					//$menu->afficheEventsContent($catList, $Annee, $Mois);
					$eventsContent = pxRenderEventsContent($eventCat, $catList, $Annee, $Mois, true);
					echo $eventsContent;
					?>
				
				</div><!-- end content -->
	
			</div><!-- end mainfloat -->

			<div id="menuright">
				<div class="col-content">
					<?php pxSingleCatTitle('<h2 class="category">%s</h2>'); ?>
					<div class="pxSingleCatDescription">
						<?php pxSingleCatDescription(); ?>
					</div>
					<?php include(dirname(__FILE__).'/inc/calendar-events.php'); ?>
					<h2>
						<?php echo __('Links'); ?>
					</h2>
					<?php pxLink::linkList(); ?>
				</div><!-- col-content -->
			</div><!-- end menuright -->
			
		</div><!-- end main -->
		<script type="text/javascript" >
			var flt_checked= "";
		
			function filterIsSelected(id) {
				var statut = $("#"+id).attr("checked");
				/* Gestion des cases à cocher */
				if (statut == undefined && id == "flt_all") {
					$("#flt_all").attr("checked",true);
				} else if (statut == undefined && id != "flt_all") {
					/* Activer le filtre all si aucune autre sélection */
					flt_checked = "";
					$(".flt_agenda").each(function() {
						if($(this).attr("checked") != undefined) flt_checked = $(this).attr("id");
						});
					if (flt_checked == "" ) $("#flt_all").attr("checked",true);
				} else if (statut=="checked" && id=="flt_all") {
					$(".flt_agenda").each(function() {
						$(this).attr("checked",false);
						});
					$("#flt_all").attr("checked",true);
			
				} else if (statut == "checked" && id!="flt_all") {
					$("#flt_all").attr("checked",false);
				}
				/* Gestion de l affichage des infos */
				if ($("#flt_all").attr("checked")=="checked") {
					// affiche tout
					$(".event").show();
				} else {
					$(".flt_agenda").each(function() {
							if ($(this).attr("checked")=="checked") {
								$("."+$(this).attr("id").substring(4)).show("fast");
							} else {
								$("."+$(this).attr("id").substring(4)).hide();
							}
						});
				}
		
			}
			
		</script>
		<script type="text/javascript" src="<?php pxInfo('filesurl'); ?>theme/<?php pxInfo('theme')?>/js/jquery.scrollto.js" ></script>

		<?php 
			if (strpos( $eventsContent,'fullcalendar')>0)  {
				pxFullCalendar();
			}
			if (strpos($eventsContent,'pdfviewer')>0) {
				pxPdfViewer();
			}
		?>
	</div><!-- end page -->
	<?php include(dirname(__FILE__).'/inc/footer.php'); ?>
</body>
</html>
<?php
    $cache->endCache();
endif;
?>