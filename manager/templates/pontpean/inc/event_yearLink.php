<?php 
	$link = $annee;
	$href = '/?'.$queryPath.'&amp;Annee='.($annee).'&amp;Mois='.$mois.'';
?>
		<table  style="border:0;padding:0;border-collapse: collapse;"  >
			<tr>
				<td height="100%" width="60px" align="center" valign="middle" >
					<a class="button" href="#" onclick="this.blur();loadEvents('<?php echo $href; ?>');">
						<span style="font-weight:bold"><?php echo $link; ?></span>
					</a>
				</td>
			</tr>
		</table>
