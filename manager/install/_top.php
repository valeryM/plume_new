<?php
header('Content-Type: text/html; charset='.strtolower($GLOBALS['_PX_config']['encoding']));  
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
 "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $_PX_config['encoding']; ?>" />
<title><?php echo __('PLUME CMS Installation'); ?></title>
<style type="text/css">
<!--
body {
	background : #fcfcfc url("../themes/default/images/fond.jpg");
	font-family : Verdana,Arial,Helevetica,sans-serif;
	color : #000;
	font-size : 0.8em;
	margin : 2em 8% 2em 8%;
}

h1,h2, h3, h4 {
	font-family: "Trebuchet MS",Trebuchet,Arial,Helvetica,sans-serif;
}

h1 {
	color: #069;
}

h2, h3 {
	color: #f60;
}

ul.checklist li 
{
	list-style-type: none;
}

a { color : #039; }

html*a:hover, html*a:focus { text-decoration : none; }

#main { background:#fff;color:#000;border:2px solid #999;padding:1em; }

.install { border-collapse : collapse; }

.install td { padding : 0.5em 1em 0.2em 0; }

.install .t { white-space: nowrap; }

.important { font-weight: bold; color: #f00; }

input, textarea, option, select {
	
	background: #eef3f5;
	color: #000;
	font-family: Verdana,Arial,Helvetica,sans-serif;
	font-size: 1em;
    margin-top: 2px;
}
input, textarea {
	border: 1px inset #000;
}

label, span.label {
	display: block;
}

input.submit {
	
	border-style: outset;
	background: #d2e0e6 url("images/degrade_bleu.png") repeat-x 0 100%;
	font-weight: bold;
}

input.submit:hover, input.submit:focus {
	background: #fc3 url("images/degrade_orange.png") repeat-x 0 100%;
}

input.submit:active {
	border-style: inset;
}

p.field {
	clear: left;
	margin: 0;
	padding: 1em 0 0 0;
}

label.float, span.label {
	position: relative;
	float: left;
	width: 25%;
	padding-right: 0.5em;
}


.small {
	font-size: 0.8em;
}

.install-info {
	float: right;
	position: relative;
	border: 1px solid #ccc;
	-moz-border-radius: 8px;
	margin: 0;
	padding: 8px 8px 8px 8px;
	white-space: nowrap;
	background-color: #eceade;
    z-index: 5;
}
-->
</style>
</head>

<body>

<div id="main">
<h1><?php echo __('PLUME CMS Installation'); ?></h1>

<p class="install-info"><?php echo sprintf(__('%s%% of the installation completed'),$_px_p); ?></p>

