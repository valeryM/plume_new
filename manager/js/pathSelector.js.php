<?php
// Contruction du script pour le chemin d'accès aux catégories
if (!empty($_SESSION['valuesPath'])) $valuesPath=$_SESSION['valuesPath'];
//$valuesPath[]['separator'] = '" "';
?>
$(function(){
	// function to activate the plugin pathSelector
	// url must have the initial parameter ?
	var param = $("input[name=location]").attr('value');
	var url = "<?php echo $_PX_website_config['rel_url'];?>/manager/js/pathSelector/pathSelector.php";
	
	/*$("input[name=location]").pathSelectorAjaxOptions(url, <?php echo $valuesPath; ?>) ;*/
	$("input[name=location]").pathSelector(url, <?php echo $valuesPath; ?>) ;
	
	/* Receive notifications when the value of path selector changes.*/
	$("span.pathSelector").bind("mouseout",function(event) {
		//we could show newVal
		var idx = ($("input[name=location]").attr("value"));
		idx= idx.substring(idx.lastIndexOf('.')+1);
		if (idx=='1') idx = 'allcat';
		$("input[name=cat_id]").attr("value",idx) ;
		$("input[name=c_name]").trigger("keyup");
	});
	
	$("form button.filterButton").click(function() {
		var idx = ($("input[name=location]").attr("value"));
		idx= idx.substring(idx.lastIndexOf('.')+1);
		if (idx=='1') idx = 'allcat';
		$("input[name=cat_id]").attr("value",idx) ;	
	});
		
	// function for the filter form, reset the filter
	$("form button.resetButton").click(function() {
		$("input[name=location]").attr("value","1");
		$("input[name=cat_id]").attr("value","allcat");
	});

	// function for the filter form, into tools ecm
	// reset the filter
	$("form button.resetButtonEcm").click(function() {
		$("input[name='location']").attr("value","1");
		$("input[name='cat_id']").attr("value","allcat");
	});		
	
	$("span.pathSelector").trigger("mouseout");		
});

