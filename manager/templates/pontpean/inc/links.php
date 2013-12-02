	<div class="search">
		<div class="searchZone">
			<input name="q" id="searchQ" type="text" 
				onblur="if(this.value=='') this.value='Votre recherche';"
				onclick="this.value='';" value="Votre recherche" size="30" />
			<div id="searchButton" class="searchButton" >
				<span></span>
			</div>
		</div>

	</div>
	<script type="text/javascript">
		$("#searchQ").keydown(function(event) {
			if (event.keyCode == 13) {
				$("#searchButton").trigger("click");
				return false;
			} 
		});
		$("#searchButton").click(function() {
			if($("#searchQ").val().length==0 || $("#searchQ").val()=="Votre recherche"){
				alert("Saisir un critère de recherche!");
				return false;
			} else if ($("#searchQ").val().length < <?php echo config::f('search_min_size');?>){
				alert("Le critère de recherche doit avoir au moins <?php echo config::f('search_min_size');?> caractères!");
				return false;
			} else {
				var href = "/search.php?q="+$("#searchQ").val();
				window.location=href;
				return false;
			}
		});
	</script>
	<div class="divSeparatorSmall"></div>
	<div style="border: 2px solid #D1E73D; width: 188px;">
		<?php pxLink::linkListByArea("Infos",'','<div>%s</div>','<div class="links %s" >%s</div>');?>
	</div>

