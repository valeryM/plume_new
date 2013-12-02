<?php
$rootPath = dirname(__FILE__).'../../../../../';
require_once $rootPath.'manager/path.php';
require_once $rootPath.'manager/prepend.php';
auth::checkAuth(PX_AUTH_NORMAL);

$m = new Manager();
$m->user->load();

error_reporting(0); // Set E_ALL for debuging

include_once dirname(__FILE__).DIRECTORY_SEPARATOR.'elFinderConnector.class.php';
include_once dirname(__FILE__).DIRECTORY_SEPARATOR.'elFinder.class.php';
include_once dirname(__FILE__).DIRECTORY_SEPARATOR.'elFinderVolumeDriver.class.php';
include_once dirname(__FILE__).DIRECTORY_SEPARATOR.'elFinderVolumeLocalFileSystem.class.php';
// Required for MySQL storage connector
// include_once dirname(__FILE__).DIRECTORY_SEPARATOR.'elFinderVolumeMySQL.class.php';
// Required for FTP connector support
// include_once dirname(__FILE__).DIRECTORY_SEPARATOR.'elFinderVolumeFTP.class.php';


/**
 * Simple function to demonstrate how to control file access using "accessControl" callback.
 * This method will disable accessing files/folders starting from  '.' (dot)
 *
 * @param  string  $attr  attribute name (read|write|locked|hidden)
 * @param  string  $path  file path relative to volume root directory started with directory separator
 * @return bool|null
 **/
function access($attr, $path, $data, $volume) {
	return strpos(basename($path), '.') === 0       // if file/folder begins with '.' (dot)
		? !($attr == 'read' || $attr == 'write')    // set read+write to false, other (locked+hidden) set to true
		:  null;                                    // else elFinder decide it itself
}

$current_dir = config::f('xmedia_root');
//pour l'utilisateur admin ou root
if (!auth::asLevel(PX_AUTH_ADVANCED))  {
	$current_dir .= '/'.$m->user->getPref('xmedia_current_dir');
}
//echo 'config:'.config::f('xmedia_root');
//echo 'current_dir:'.$current_dir;
/*
if (!empty($_SESSION['xmedia_current_dir'])) {
	$current_dir = $_SESSION['xmedia_current_dir'];
} else {
	$current_dir = $m->user->getPref('xmedia_current_dir');
}
*/
$tmbUrl = $_PX_website_config['rel_url_files'].'/'.$m->user->getPref('xmedia_current_dir').'/.tmb/';
if (isset($_SESSION['elFinder']) && isset($_REQUEST['target'])) {
	// find target in the array
	$path='';
	if(isset($_SESSION['elFinder']['options'])) {
		$data = $_SESSION['elFinder']['options'];
		//$arrayPath = explode('\\',$data['path']);
		$arrayPath = array_slice(explode('\\',$data['path']),1);
		$path = implode('/',$arrayPath);
	} 
	elseif (isset($_SESSION['elFinder']['tree']))
		$data = $_SESSION['elFinder']['tree'];
	//echo $data['name'];
	if (isset($data['name']) &&  $data['name']!='') $path.='/'. $data['name'];
	//$tmbUrl = $_PX_website_config['rel_url_files'].'/'.$m->user->getPref('xmedia_current_dir'). $data['name'].'/.tmb/';
	//$tmbUrl = $_PX_website_config['rel_url_files'].'/'.$m->user->getPref('xmedia_current_dir').$path.'/.tmb';
	//$tmbUrl = $current_dir;
	//$tmbUrl = $_PX_website_config['rel_url_files'].'/'.$m->user->getPref('xmedia_current_dir').'.tmb';
	//$tmbUrl = $_PX_website_config['rel_url_files'].'/.tmb';
	//$tmpPath = '.tmb';
	//$tmbUrl = '';
	/*
	if ($data['hash'] == $_REQUEST['target']) {
		$tmbUrl = $_PX_website_config['rel_url_files'].'/'.$m->user->getPref('xmedia_current_dir') .'/'. $data['name'].'/.tmb/';
	}
	*/
	
}

$opts = array(
	'debug' => false,
	'roots' => array(
		array(
			'driver' => 'LocalFileSystem',   // driver for accessing file system (REQUIRED)
			'path' => $current_dir,         // path to files (REQUIRED)
			'tmbDirName'=> '.tmb',
			/*'tmbURL' => $tmbUrl,*/
			'tmbPathLocal' => true,
			'URL' => $_PX_website_config['rel_url_files'].'/'.$m->user->getPref('xmedia_current_dir'), // URL to files (REQUIRED)
			'accessControl' => 'access'             // disable and hide dot starting files (OPTIONAL)
		)
	)
);


// run elFinder
$connector = new elFinderConnector(new elFinder($opts));
$connector->run();

