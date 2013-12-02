<?php
header('Content-type: text/html');

$dir = dirname(dirname(dirname(__FILE__)));
include_once dirname($dir).'/config.php';
include_once dirname($dir).'/prepend.php';

require_once $dir.'/conf/configweb_default.php';

$current_dir = $_POST['url'];
$up_dir = config::f('xmedia_root');

if (!empty($current_dir)) {
	$current_dir = str_replace('\\','',$current_dir);
	$current_dir = str_replace('..','',$current_dir);
	$current_dir = preg_replace( '#[^A-Za-z0-9\-\_\/]#', '', $current_dir);
	$current_dir = preg_replace( '#(/)+#', '/', $current_dir);
	$current_dir = preg_replace( '#^(/)+#', '', $current_dir);
	$current_dir = preg_replace( '#(/)+$#', '', $current_dir);
	if (!empty($current_dir))
		$current_dir .= '/';
} else {
	$current_dir = '';
}
// vérification du dossier
if (is_dir($up_dir.'/'.$current_dir)) {

}
$rel = config::f('rel_url_files');
//echo $rel;
//echo $current_dir;
//retire $rel du début du chemin crrent_dir;
$current_dir = str_replace($rel,'','/'.$current_dir);
$directory = dir($up_dir.'/'.$current_dir);
$response = '<div class="slides-container" >'."\n";
while(false !== ($entry = $directory->read()))  {
	
	if($entry != '.' && $entry != '..' && $entry != '.htaccess' && $entry != '.htpasswd' && $entry != 'thumb' ) {
		// si c'est une image (gif, png, jpg, jpeg)
		$detailEntry = explode('.', $entry);
		if (count($detailEntry)==2 && strpos('.gif .jpg .jpeg .png',$detailEntry[1]) >0 ) {
			$response .= '<div class="slides-content"><img src="'.$rel.$current_dir.$entry.'" style="border:0; padding:0; margin:auto;"/></div>'."\n";
			//$response .= '<div class="bandeauNews" ></div>'."\n";
		}
	}
}
$response .= '</div>'."\n";
$directory->close();
unset($directory);

echo $response;
?>