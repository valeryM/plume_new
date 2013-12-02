<div id="MenuSousRubrique">
	<?php 
	
	$cat =  $GLOBALS['_PX_render']['cat']->f('category_id');
	$category = FrontEnd::getCategory($cat);
	
	$path = $category->f('category_path');
	//echo 'path:'.$path;
	
	$arrayPath = explode('/',$category->f('category_path'));
	//echo print_r($arrayPath,true);
	// récupère la 1ère catégorie
	$catMaster = $GLOBALS['_PX_render']['mcat'];
	// récupère la catégorie parent de la catégorie active
	$catChild = $GLOBALS['_PX_render']['mchildcat'];
	//$catParent = FrontEnd::getCategory('/'.$arrayPath[1].'/'.$arrayPath[2].'/');
	if (is_object($catChild)) {
		//echo 'catMaster:'.$catMaster->f('category_id').' '.$catMaster->getPath();
		//echo 'catChild:'.$catChild->f('category_id').' '.$catChild->getPath();
		$classCat  = str_ireplace('/','' ,$catMaster->f('category_path'));
		?>
		<div class="<?php echo $classCat; ?>">
			<span class="categoryMaster" ><?php echo $catMaster->f('category_name'); ?></span>
			<?php 
			if (strstr($path, $catMaster->f('category_path'),0)!==false) 
				$subcatClass = 'sousCategorieSelected sousCategorieSelected'.$classCat;
			else 
				$subcatClass = '';
			?>
			<span class="categoryParent <?php echo $subcatClass; ?>">
			<?php echo $catChild->f('category_name'); ?>
			</span>
		</div>
		<div class="menuSousCategorieActive" >
			<?php 
		
			$listeMenus = pxArrayCategory($catChild->f('category_id'),'ORDER BY category_position');
			$firstLine=true;
			$rep='';
			$classSeparateur='noSeparateur';
			//echo print_r($listeMenus,true);
			//exit;
			foreach($listeMenus as $level) {
				if (!$firstLine) 
					$classSeparateur = 'separateur';
				
				$firstLine = false;
				?>
				<div class="<?php echo $classSeparateur; ?>">
					<?php 
					// affichage de la sous-rubrique sélectionnée
					// active la sous-rubrique si elle correspond à la sélection
					if ( strstr($path, $level['path'],0)!==false )
						$classSelected = "Selected";
					else
						$classSelected="";
					
					?>
					<div class="sousCategorie<?php echo $classSelected; ?> sousCategorie<?php echo $classSelected.$classCat; ?>" >
						<a title="<?php echo $level['title']; ?>" href="<?php echo $level['url']; ?>" >
							<span><?php echo $level['name']; ?></span>
						</a>
					</div>
					<?php 
					foreach($level['sublevel'] as $idx => $subLevel) {
						if (strstr($path, $subLevel['path'],0)!==false )
							$classSub = "Selected";
						else 
							$classSub = "";
						?>
						<div class="sousCategorie sousCategorie<?php echo $classSub.$classCat; ?> subCategorie <?php echo $classSub; ?>">
							<a title="<?php echo $subLevel['title']; ?>"  href="<?php  echo $subLevel['url']; ?>" >
								<span><?php echo $subLevel['name']; ?></span>
							</a>
						</div>
					<?php 
					} // fin de la boucle sur sublevel
					?>
				</div>
					
			<?php 		
			} // fin de la boucle dans les catégories ($subCat)
		?>
		</div>
	<?php } // endif?>
</div>
<div class="divSeparator"></div>