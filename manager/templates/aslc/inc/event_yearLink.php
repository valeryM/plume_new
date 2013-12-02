<?php 
	$link = $annee;
	$href = '/?'.$queryPath.'&amp;Annee='.($annee).'&amp;Mois='.$mois.'';
?>
		<table border="0" cellspacing="0" cellpadding="0" >
			<tr>
				<td height="100%" width="60px" align="center" valign="middle" >
					<a class="button" href="<?php echo $href; ?>" onclick="this.blur();">
						<span style="font-weight:bold"><?php echo $link; ?></span>
					</a>
				</td>
			</tr>
		</table>
