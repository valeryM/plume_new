<?php 
$rootPath = dirname(dirname(dirname(__FILE__)));
require_once $rootPath.'/path.php';
require_once $_PX_config['manager_path'].'/prepend.php';
auth::checkAuth(PX_AUTH_NORMAL);

$m = new Manager();
$lang = $m->user->lang;
$_px_theme = $m->user->getTheme();
?>
<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=<?php echo strtolower($GLOBALS['_PX_config']['encoding']); ?>" />
		<title>elFinder - PLUME CMS</title>
		<script type="text/javascript" src="<?php echo $_PX_website_config['rel_url'];?>/manager/tools.js"> </script>
		<link rel="stylesheet" type="text/css"href="<?php echo $_PX_website_config['rel_url'];?>/manager/js/themes/base/jquery.ui.all.css" />
		<link rel="stylesheet" type="text/css" href="<?php echo $_PX_website_config['rel_url'];?>/manager/themes/<?php echo $_px_theme; ?>/style.css" />
		<!-- Fonctions Jquery ui -->
		<script type="text/javascript" src="<?php echo $_PX_website_config['rel_url'];?>/manager/js/jquery.last.min.js"></script>
		<script type="text/javascript" src="<?php echo $_PX_website_config['rel_url'];?>/manager/js/ui/jquery-ui.custom.min.js"></script>
		<script type="text/javascript" src="<?php echo $_PX_website_config['rel_url'];?>/manager/js/jquery-migrate-1.2.1.js"></script>
		<!-- elFinder CSS (REQUIRED) -->
		<link rel="stylesheet" type="text/css" media="screen" href="<?php echo $_PX_website_config['rel_url'];?>/manager/tools/elfinder/css/elfinder.min.css">
		<link rel="stylesheet" type="text/css" media="screen" href="<?php echo $_PX_website_config['rel_url'];?>/manager/tools/elfinder/css/theme.css">

		<!-- elFinder JS (REQUIRED) -->
		<script type="text/javascript" src="<?php echo $_PX_website_config['rel_url'];?>/manager/tools/elfinder/js/elfinder.min.js"></script>

		<!-- elFinder translation (OPTIONAL) -->
		<script type="text/javascript" src="<?php echo $_PX_website_config['rel_url'];?>/manager/tools/elfinder/js/i18n/elfinder.<?php echo $lang;?>.js"></script>

		<!-- elFinder initialization (REQUIRED) -->
		<script type="text/javascript" charset="utf-8">
		
			function getUrlParam(paramName) {
				var reParam = new RegExp('(?:[\?&]|&amp;)'+paramName+'=([^&]+)','i');
				var match = window.location.search.match(reParam);
				
				return (match && match.length > 1) ? match[1] : '';
			}

			$().ready(function() {
				var funcNum = getUrlParam('CKEditorFuncNum');

				var opts = {
			    		lang: "<?php echo $lang; ?>",      // language (OPTIONAL)
						url : "<?php echo $_PX_website_config['rel_url'].'/manager/tools/elfinder/php/connector.php'; ?>",  //  connector URL (REQUIRED)
						commands : [
							"reload", "home", "up", "back", "forward", "quicklook", 
							"download", "rm", "duplicate", "rename", "mkdir", "mkfile", "upload", "copy", 
							"cut", "paste", "extract", "archive", "search", "info", "view", "help",
							"resize", "sort"],
						commandsOptions: {
							getfile : {
								// send only URL or URL+path if false
								onlyURL  : true,							
								// allow to return multiple files info
								multiple : false,							
								// allow to return folders info
								folders  : false,
								// action after callback (close/destroy)
								oncomplete : "close",
							},
						},
						uiOptions : {
							// toolbar configuration
							toolbar : [
								["back", "forward"],
								["reload"],
								["home", "up"],
								["mkdir", "mkfile", "upload"],
								[/*"open",*/ "download", "getfile"],
								["info"],
								["quicklook"],
								["copy", "cut", "paste"],
								["rm"],
								["duplicate", "rename", "edit", "resize"],
								["extract", "archive"],
								["search"],
								["view"],
								["help"]
							],
						},
						contextmenu : {
							// navbarfolder menu
							navbar : [/*"open", "|",*/ "copy", "cut", "paste", "duplicate", "|", "rm", "|", "info"],
						
							// current directory menu
							cwd    : ["reload", "back", "|", "upload", "mkdir", "mkfile", "paste", "|", "info"],
						
							// current directory file menu
							files  : [
									"getfile", "|",/*"open",*/ "quicklook", "|", "download", "|", "copy", "cut", "paste", "duplicate", "|",
									"rm", "|", /*"edit",*/ "rename", "resize", "|", "archive", "extract", "|", "info"
								]						
						},
						handlers : {
							// auto extract / resize files on upload
							upload : function(event, instance) {
								var uploadedFiles = event.data.added;
								var archives = ["application/zip", "application/x-gzip", "application/x-tar", "application/x-bzip2"];
								for (i in uploadedFiles) {
									var file = uploadedFiles[i];
									if (jQuery.inArray(file.mime, archives) >= 0) {
										instance.exec("extract", file.hash);
									}
									/*
									if (file.size> maxFileSize ) {
										file.tmb="1";
									}
									*/
								}
							},
						
							/*open   : function(event) { console.log(event.data); }*/
						},
						allowShortcuts : false,
						loadTmbs : 2,
						debug : ["error","warning", "event-destroy"],
						/* Callback function for "getfile" command. Required to use elFinder with WYSIWYG editors, external callbacks.
						For more info how to use this function refer to wiki WYSIWYG integrations examples
						*/						
						getFileCallback : function(file) {
							window.opener.CKEDITOR.tools.callFunction(funcNum, file);
							window.close();
							}, 
						resizable : false,
						height:450,				
					    };
			    
				var elf = $('#elfinder').elfinder(opts).elfinder('instance');
				window.height = 550;
			});

		</script>
	</head>
	<body>
		<!-- Element where elFinder will be created (REQUIRED) -->
		<div id="elfinder"></div>
	</body>
</html>
