<?php
require(dirname(dirname(dirname(__FILE__))) . '/plume/manager/conf/configweb_default.php');

// <!-- This is all the XHTML ImageFlow needs -->
echo '<div class="imageflowContainer">';
echo '<div id="myImageFlow" class="imageflow" >';
// Lister les images situées dans le dossier PX_CONFIG_PORTFOLIO
$imageRoot = $_PX_website_config['document_root'] . PX_CONFIG_PORTFOLIO;
// si le dossier existe
if (file_exists($imageRoot)) {
	$DirContent = dir($imageRoot);
	$images = array();
	
	// Boucle sur le contenu du dossier
	while(false !== ($entry = $DirContent->read()))  {
		// ce n'est pas une répertoire
		
		if (!is_dir($imageRoot.'/'.$entry))  {
			
			// vérification du type de fichier (image) et n'est pas un reflet
			if (false !== stristr(PX_CONFIG_IMAGE_EXT,pathinfo($entry,PATHINFO_EXTENSION)) && ( substr($entry,0,5)!== 'refl_')  ) {
				// c'est 1 image !
				$images[] = $entry; //'.PX_CONFIG_PORTFOLIO.'/'.$entry.'"
				$longdesc = 'javascript:LightBoxDefine.displayImage(\''.PX_CONFIG_PORTFOLIO.'/'.$entry.'\')';
				echo '<img src="'.PX_CONFIG_PORTFOLIO.'/'.$entry.'" longdesc="'.$longdesc.'" height="100px" alt="'.$entry.'" />';
				echo "\n";
			}
		}
	}
	// unset
	$DirContent->close();
}
echo '</div>';
echo '</div>';
echo '<div id="gallery" style="display:none">
		<ul>
			<li>
				<a class="imageGallery" href="vide" title="">
					<img class="imageGallery" src="" width="45" height="45" alt="" />
				</a>
			</li>
		</ul>
	</div>';

?>