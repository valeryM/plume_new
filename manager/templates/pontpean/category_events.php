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
	<?php include(dirname(__FILE__).'/inc/head-link.php'); ?>
	<?php include(dirname(__FILE__).'/inc/head-meta.php'); ?>
	<meta name="description" content="<?php pxSingleCatTitle('%s'); ?> - <?php pxMetasDescription(); ?>" />
	<meta name="DC.Description" content="<?php pxSingleCatTitle('%s'); ?> - <?php pxMetasDescription(); ?>" />
	<meta name="DC.source" content="<?php pxSingleCatPath(); ?>" scheme="URI" />
	<meta name="DC.Title" content="<?php pxSingleCatTitle('%s'); ?> - <?php pxInfo('name'); ?> (<?php echo $GLOBALS['_PX_render']['cat']->f('category_path'); ?>)" />
	<?php 
	$keywords = pxSingleCatTitle('%s',true).' '.pxSingleCatPath('%s',false,true). ' ';
	while (!$res->EOF() ) {
		$keywords .= pxResWordIndex($res->f('resource_id'),'1,5');
		$res->moveNext();
	}
	$res->moveStart();
	?>
	<meta name="DC.Keywords" content="<?php echo $keywords; ?>" />
</head>

<body class="category_events category">

	<div id="page">
		<?php require dirname(__FILE__).'/inc/banner2.php'; ?>
		<?php require dirname(__FILE__).'/inc/menu_top.php'; ?>
		<div id="main">
			
			<div id="mainleft">
				<div id="content">
					<?php 
					// Les catégories des évènements
					$catList = pxGetArrayEventsCats(PX_CONFIG_EVENTS_LIST_ALL);
					$catSelected = $GLOBALS['_PX_render']['cat'];
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
						//$m->res= $m->array_path['res'];
					}
					//echo print_r($array_path,true);
					// affichage du bandeau
					//echo $menu->afficheBandeauEvents($catList);
					?>
					<div id="agenda_bandeau" >
						<div style="vertical-align:bottom; height:90px">
							<form id="f_agenda" action="#" >
								<table  style="border:0;padding:0;border-collapse: collapse;width:800px;height:60px">
									<tr height="20px">
										<td>&nbsp;</td>
									</tr>
									<tr height="15px">
									<?php 
										$flt_used = '';
										if (isset($_GET['flt_used'])) {
											$flt_used = $_GET['flt_used'];
										}										
										foreach ($catList['idTag'] as $id=>$name) {
									?>
											<td>
											<?php 
											$cat = FrontEnd::getCategory($id);
											
											if ( $catSelected->f('category_id') == $id) {
												$checked = 'checked="checked"';
												if ($flt_used == '') $flt_used = $name;
											}  else 
												$checked='';
											?>
											<input name="flt_<?php echo $name; ?>" id="flt_<?php echo $name; ?>" <?php echo $checked; ?> type="checkbox" class="flt_agenda" onclick="filterIsSelected('flt_<?php echo $name; ?>')" value="0" />
											<label for="flt_<?php echo $name; ?>" ><?php echo $cat->f('category_name'); ?></label><br/>
											</td>
									<?php
										}
										
									?>
									<input type="hidden" id="flt_used" value="<?php echo $flt_used ?>" >
									</tr>
								</table>
							</form>
						</div>
					</div>
					<?php 
					// affichage du calendrier de navigation
					//echo $menu->afficheCalendrierNavigation($Annee,$Mois);
					//global $eventCat; //= $this->m->getMasterCategoriesByName(PX_CONFIG_EVENTS);
					
					$eventCat = pxGetMasterCategoriesByName(PX_CONFIG_EVENTS);
					$queryPath = $eventCat->f('category_path');
					
					$Month = array('Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Août','Septembre', 'Octobre' , 'Novembre','Décembre');
					// affiche le calendrier pour la navigation
					
					?>
					<div id="Calendrier" align="center">
						<table  style="border:0;padding:0;border-collapse: collapse;text-align:center" >
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
									<table  style="padding:0;border-collapse: collapse;">
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
												$rep .= '<a class="'.$class.'" href="#" onclick="this.blur();loadEvents(\''.$href.'\');"><span>'. $Month[$i].'</span></a></td>';
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
												$rep .= '<a class="'.$class.'" href="#" onclick="this.blur();loadEvents(\''.$href.'\');"><span>'. $Month[$i].'</span></a></td>';
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
					pxRenderEventsContent($catSelected, $catList, $Annee, $Mois);
					?>
				
				</div><!-- end content -->
	
			</div><!-- end mainfloat -->

			<div id="menuright">
				<div id="infoPratique">
					<?php require('inc/breadCrumbs.php'); ?>				
					<?php require('inc/links.php'); ?>
				</div>
			</div><!-- end menuright -->

		</div><!-- end main -->
		<script type="text/javascript" >
			var flt_checked= "";
			var cptChecked = 0, cptTotal = 0;
		
			function filterIsSelected(id) {
				var statut = $("#"+id).attr("checked");
				/* Gestion des cases à cocher */
				if (statut == undefined && id == "flt_all") {
					$("#flt_all").attr("checked",true);
					
				} else if (statut=="checked" && id=="flt_all") {
					$(".flt_agenda").each(function() {
						$(this).attr("checked",false);
					});
					$("#flt_all").attr("checked",true);
					
				} else { /*if (statut == undefined && id != "flt_all") {*/
					$("#flt_all").attr("checked",false);
					/* Activer le filtre all si aucune autre sélection */
					flt_checked = "";
					cptChecked = 0;
					cptTotal = 0;
					$(".flt_agenda").each(function() {
						if ($(this).attr("id") != "flt_all") cptTotal ++;
						if($(this).attr("checked") != undefined) {
							flt_checked = $(this).attr("id");
							cptChecked ++;
						}
					});
					if (cptTotal == cptChecked) {
						flt_checked= "";
						$(".flt_agenda").attr("checked", false);
					}
					if (flt_checked == "" ) $("#flt_all").attr("checked",true);
			/*
				} else if (statut == "checked" && id!="flt_all") {
					$("#flt_all").attr("checked",false);
			*/
				}
				

				$("#flt_used").val(id);
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

			function loadEvents(href) {
				var url = href+"&flt_used="+$("#flt_used").val();
				window.location = url;
				return false;
			}
			
		</script>
		<script type="text/javascript" src="<?php pxInfo('filesurl'); ?>theme/<?php pxInfo('theme')?>/js/jquery.scrollto.js" ></script>
		<!-- 
		<script type="text/javascript" src="<?php pxInfo('filesurl'); ?>theme/<?php pxInfo('theme')?>/js/agenda.js" ></script>
		-->

	</div><!-- end page -->
	<?php include(dirname(__FILE__).'/inc/footer.php'); ?>
</body>
</html>
<?php
    $cache->endCache();
endif;
?>