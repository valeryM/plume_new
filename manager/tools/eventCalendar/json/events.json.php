<?php
header('Content-type:text/json');
$limit = null;
$year = date('Y');
$day = '';
$month = '';

$dir = dirname(dirname(dirname(dirname(__FILE__))));
include_once $dir.'/path.php';
include_once $dir.'/prepend.php';
require_once $dir.'/conf/configweb_default.php';

if (isset($_GET['limit']) && $_GET['limit']!=0) $limit = $_GET['limit'];
if (isset($_GET['year']) && $_GET['year']!='') $year = $_GET['year'];
if (isset($_GET['day']) && $_GET['day']!='') $day = $_GET['day'];
if (isset($_GET['month'])) $month = $_GET['month'];

if ($month!='false' && intval($month)>=0) 
	$month = intval($month)+1;
elseif ($month=='' || $month==false) 
	$month = intval($month)+1;
else
	$month = date('m');

$events = pxEventsList($year, $month, $day, $limit);

$result = array();
// default timezone
date_default_timezone_set('Europe/Paris');

while( !$events->EOF()) {
	$result[] =array( 
			'date' =>  ((date::unix($events->f('event_startdate')))*1000)."",  
			'type' => (date::unix($events->f('event_startdate')) < date::unix(date('YmdHis')) ) ? 'Past': 'future',
			'title' => $events->f('title'),
			'description' => html_entity_decode(text::truncate(text::parseContent($events->f('description'),'Text'),150),ENT_QUOTES,'UTF-8'),
			'url' => './?'.$events->f('category_path').$events->f('path'),
			);
	$events->moveNext();
}

echo json_encode($result);
?>