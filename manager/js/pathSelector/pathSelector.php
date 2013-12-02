<?php
header('Content-Type: text/html; charset=UTF-8');
$managerPath = dirname(dirname(dirname(__FILE__)));
require_once $managerPath.'/path.php';
require_once $managerPath.'/prepend.php';

$level_id=0;
$typeAction="";

if (!empty($_REQUEST['value'])) 
	$level_id = $_REQUEST['value'];
if (!empty($_POST['value'])) 
	$level_id = $_POST['value'];
if (!empty($_GET['value'])) 
	$level_id = $_GET['value'];

if (empty($level_id) || $level_id=='' ) {  
	$level_id=0;
}

//$_SESSION['location'] =$level;
$m = new Manager();

if (strpos($level_id, '.')!==false) {
	$level_id = substr(strrchr($level_id,'.'),1);
} 

$rsCat = $m->getCategoriesLightFromParent($level_id,'','',false);

$cat=array();
while(!$rsCat->EOF()) {
	$cat[] = array("value"=>$rsCat->f('category_id'),"label" => htmlspecialchars($rsCat->f('category_name')));
	$rsCat->moveNext();
}

echo JSONencode($cat);
//echo json_encode($cat);
//echo array_to_json($cat);
exit;
?>