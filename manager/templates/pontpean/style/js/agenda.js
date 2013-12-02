/**
 * Gestion de l'affichage pour l'agenda
 */

function filterIsSelected(id) {
	var statut = $("#"+id).attr("checked");
	/* Gestion des cases à cocher */
	if (statut == undefined && id == "flt_all") {
		$("#flt_all").attr("checked",true);
	} else if (statut == undefined && id != "flt_all") {
		/* Activer le filtre all si aucune autre sélection */
		if ($("#flt_asso").attr("checked")== undefined 
				&& $("#flt_commune").attr("checked")==undefined 
				&& $("#flt_near").attr("checked")==undefined
				&& $("#flt_culture").attr("checked")==undefined)
			$("#flt_all").attr("checked",true);
		
	} else if (statut=="checked" && id=="flt_all") {
		$("#flt_asso").attr("checked",false);
		$("#flt_commune").attr("checked",false);
		$("#flt_near").attr("checked",false);
		$("#flt_culture").attr("checked",false);
	} else if (statut == "checked" && id!="flt_all") {
		$("#flt_all").attr("checked",false);		
	}
	/* Gestion de l'affichage des infos */
	if ($("#flt_all").attr("checked")=="checked") {
		$(".event").show();
	} else {
		if ($("#flt_asso").attr("checked")=="checked" ) $(".asso").show("fast"); else $(".asso").hide();
		if ($("#flt_commune").attr("checked")=="checked" ) $(".commune").show("fast"); else $(".commune").hide();
		if ($("#flt_near").attr("checked")=="checked" ) $(".autour").show("fast"); else $(".autour").hide();
		if ($("#flt_culture").attr("checked")=="checked" ) $(".culture").show("fast"); else $(".culture").hide();
	}

}
