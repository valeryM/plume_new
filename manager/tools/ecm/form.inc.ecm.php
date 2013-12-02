<?php

$cats = $m->getCategories();
$arry_cat = array();
$arry_cat_js = array();
while (!$cats->EOF()) {
	$name =  $cats->f('category_name');
	$name .= ' ('.$cats->f('category_path').')';
	if (isGhostCat($cats->f('category_path'), $cats->f('category_isGhost')))
		$name .= ' ['. __('Hidden category').']';
	$arry_cat[$name] = $cats->f('category_id');
	$cats->moveNext();
}

$array_templates = $m->getTemplates('category');


/*=================================================
	 add/edit a category
=================================================*/


/*	Preview of the content if some content is available	*/
if (strlen($cat->getUnformattedContent('category_description')))
{
	echo '<div class="preview">';
	echo '<h3>'.$cat->getTextContent('category_name').'</h3>';
	echo $cat->getFormattedContent('category_description', 'html');
	echo "<hr class='invisible' id=\"zoubida\"/></div>\n\n";
}

//	Script permettant de construire la liste des catégories disponibles
/*
echo	'<script type="text/javascript">'."\n".
		"<!--\n".
			"var js_pathlist = new Array();\n";
			$cats->moveStart();
			while (!$cats->EOF()) {	
				echo "js_pathlist[".$cats->f('category_id')."] = '".$cats->f('category_path')."';\n";
				$cats->moveNext();
			}
echo	"//-->\n".
		"</script>\n";
*/
echo '<script type="text/javascript">'."\n".
		"var js_pathlist = new Array();\n";
$cats->moveStart();
$cat_id=$cats->f('category_id');
echo 'var cat_id='.$cat_id.';'."\n";
echo 'function setPath(el) { '."\n"
		.'if (el.form.cat_id.value=="allcat" || el.form.cat_id.value=="") el.form.cat_id.value=0;'."\n"
		.'el.form.c_parentid.value=el.form.cat_id.value;'."\n"
		.'setUrl("c_name", "c_path", "cat", js_pathlist);'."\n"
		.'}'."\n";

echo "js_pathlist[0] = '".$cats->f('category_path')."';\n";
while (!$cats->EOF()) {
	echo "js_pathlist[".$cats->f('category_id')."] = '".$cats->f('category_path')."';\n";
	$cats->moveNext();
}
echo "</script>\n";



//	Début du formulaire
echo '<form action="tools.php?p=ecm" method="post" id="formPost" name="formPost">'."\n";
echo	'<p>'."\n";

//	SI on édite pas la catégorie mère (/home) OU SI on a pas de catégorie sélectionnée (cas de l'ajout d'une nouvelle catégorie)
//	ALORS on génère la comboBox permettant de sélectionner la catégorie "mère" de celle que nous éditons
//echo $cat->f('category_id');
if (($cat->f('category_path') != '/' && strlen($cat->f('category_id'))==0 ) || $cat->f('category_id')=='')
{
	echo	'<span class="nowrap"><label for="c_parentid" style="display:inline">'.__('Parent category:').'</label> ';
//	echo		form::combobox('c_parentid', $arry_cat, $cat->f('category_parentid'), '', '', 'onChange="catChangePath(js_pathlist);"');
//	echo	'</span>'."\n";
/*
	if ($location !=='') {
	if (false !== strrpos($location,'.')) {
		$cat_id = substr($location, strrpos($location,'.')+1);
	} else $cat_id = $location;
*/
	$idx = $cat->f('category_id');
	$location = $idx;
	while (($rs = $m->getCategory($idx)) !== false) {
		if (!$rs->EOF() && $rs->f('category_parentid') != $rs->f('category_id')) {
			$idx = $rs->f('category_parentid');
			$location = $idx . '.' .$location;
		} else break;
	}
	
	if ($cat->f('category_parentid') == '') {
		$sel = $cat_id;
	} else  {
		$sel = $cat->f('category_parentid');
	}
	
	echo '<input size="3" name="location" type="hidden" style="height:20px" value="'.$location.'">';
	//echo '<input type="hidden" name="c_parentid" value="'.$sel.'" onchange="catChangePath(js_pathlist);" >';
	//echo '<input type="hidden" name="cat_id" value="'.$sel.'" onchange="setPath($(\'input[name=c_name]\'))">';	
	echo form::hidden('cat_id', $cat->f('category_parentid'),true);
	echo form::hidden('c_parentid', $cat->f('category_parentid'),true);
	echo '</span>';
}
//	SINON on génère un champs caché contenant la valeur de l'id de l'élément parent
else {		//c_parentid					// category_parentid
	echo form::hidden('cat_id', $cat->f('category_parentid'),true);
	echo form::hidden('c_parentid', $cat->f('category_parentid'),true);
	//echo '<input type="hidden" name="c_parentid" value="'.$sel.'" onchange="catChangePath(js_pathlist);" >';
	//echo '<input type="hidden" name="cat_id" value="'.$sel.'" onchange="setPath($(\'input[name=c_name]\'))">';
	
}
//	ComboBox permettant de sélectionner le format d'édition de la catégorie
echo		'<span class="nowrap">'.
				'<label for="c_format"  style="display:inline">'.__('Format:').'</label>'."\n";
echo			form::combobox('c_format', array('HTML'=>'html','Wiki'=>'wiki'), $cat->getContentFormat('category_description'), $m->user->getPref('content_format'));
echo		'</span>'."\n";
echo	'</p>'."\n";

//	Titre de la catégorie
echo	'<p>'.
			'<label for="c_name"><strong>'.__('Title:').'</strong></label> ';
echo		form::textField('c_name', 30, 255, $cat->f('category_name'), '', 'style="width:100%" onkeyup="setPath(this)"'); //onkeyup="setUrl(\'c_name\', \'c_path\', \'cat\', js_pathlist)"
echo	'</p>'."\n".
		'<p>'."\n";
echo		'<span  id="insert-img" class="bloc-droite"><img src="themes/'.$_px_theme.'/images/ico_image.png" alt="" /> ';
echo			'<strong><a href="xmedia.php" onclick="popup(this.href+\'?mode=popup\'); return false;">'.__('Insert an image or a file').'</a></strong>'.
			'</span>'."\n";
//	Description de la catégorie
echo		'<label for="c_description"><strong>'.__('Description:').'</strong></label>'."\n";
echo		form::textArea('c_description', 60, $m->user->getPref('category_textarea'), $cat->getUnformattedContent('category_description'), '', 'style="width:100%"');
echo		"\n".
			'<span id="size-control" class="size-control"> '
				.'<input type="image" alt="'.__('shrink textarea').'" name="decrease" value="-" src="themes/'.$_px_theme.'/images/ico_shrink.png" accesskey="-" class="size-control" /> '
				.'<input type="image" alt="'.__('grow textarea').'" name="increase" value="+" src="themes/'.$_px_theme.'/images/ico_grow.png" accesskey="+" class="size-control" /> ';
echo		'</span>'."\n";
echo	"</p>\n";

//	Mot clés de la catégorie (tags)
echo	'<p>'.
			'<label for="c_keywords">'.__('Keywords:').'</label> ';
echo		form::textArea('c_keywords', 60, 2, $cat->f('category_keywords'), '', 'style="width:100%"');
echo	"</p>\n";

//	Chemin d'accès de la catégorie
echo	'<p>'.
			'<label for="c_path"><strong>'.__('Path <span class="small">(/category/ or /cat/subcat/)</span>:').'</strong></label> ';
if ($cat->f('category_path') == '/' && strlen($cat->f('category_id'))) {
	echo	'<strong>/</strong>';
} else {
	echo	form::textField('c_path', 30, 255, $cat->f('category_path'), '', 'style="width:100%"');
}
echo	"</p>\n".

//	Combobox du template pour la catégorie
		"<p>\n";
echo		'<span class="nowrap"> ';
echo			'<label for="c_template" style="display:inline"><strong>'.__('Template:').'</strong></label> ';
echo			form::combobox('c_template', $array_templates, $cat->f('category_template'));
echo		'</span>'."\n";
echo	'</p>'."\n";

//	Boutons d'actions
//	Visualiser / Enregistrer
echo	'<p>'.
			'<input name="preview" type="submit" class="submit" value="'.__('Visualize [v]').'" accesskey="v" />&nbsp; '.
			'<input name="save" type="submit" class="submit" value="'.__('Save [s]').'" accesskey="s" /> ';
//	Transformer contenu wiki en xHTML
if (strlen($cat->f('category_id')) && $cat->getContentFormat('category_description') == 'wiki')
	echo '&nbsp;<input name="transform" type="submit" class="submit" value="'. __('Transform in XHTML').'" />';
//	Supprimer
/*
if (strlen($cat->f('category_id')) && $cat->f('category_path') != '/')  {
	echo	'&nbsp;'.'
			<input name="delete" type="submit" class="submit" value="'.  __('Delete').
				'" onclick="return window.confirm(\''.addslashes( __('Are you sure you want to delete this category?')).'\')"'.
			' />';
}
*/

//	Champs cachés permettan de savoir quelles actions déclancher
//	Champs caché pour l'action "editer"
if (strlen($cat->f('category_id')))
{
	echo	form::hidden('action','modify');
	echo	form::hidden('category_id', $cat->f('category_id'));
}
//	Champs caché pour l'action "ajouter"
else
	echo	form::hidden('action','add');

echo		form::hidden('quirk_category_path', strlen($cat->f('category_path')));
echo	'</p>';
echo	'</form>'."\n\n";
//	Aide formatage wiki
echo	'<h3>'.
			'<a onclick="openCloseSpan(\'wikihelp\',0); return false" href="#"><img alt="'.__('show/hide').'" id="img_wikihelp" src="themes/'.$_px_theme.'/images/plus.png" /></a>'.
			__('Wiki syntax').
		'</h3>'."\n";
echo	'<div id="wikihelp" style="display: none;"> ';
echo		$m->getHelp('wiki-inline'); 
echo	'</div>'."\n";
echo	'<script type="text/javascript"><!--'."\n".'openCloseSpan(\'wikihelp\',-1);'."\n".'//--></script>';
?>