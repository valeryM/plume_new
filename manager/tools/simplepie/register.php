<?php

/**
 * Plume CMS plugin for SimplePie ; a fast, easy-to-use RSS and Atom parser.
 *
 * @author Cécilia Gaudard <cilyia{at}gmail{dot}com>
 * @copyright 2007 Cécilia Gaudard
 * @version 1.0-RC1
 *
 * This plugin is largely inspired from the WordPress Plugin for SimplePie, 
 * which copyright informations are as following : 
 * @author Ryan Parman and Geoffrey Sneddon
 * @copyright 2006 Ryan Parman and Geoffrey Sneddon
 * @URI : http://simplepie.org/
 * 
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 */

if(!defined('PLUGINPATH')) define('PLUGINPATH', realpath(dirname(__FILE__).'/../').'/');
include_once(PLUGINPATH . 'simplepie/simplepie.inc');



// Snap into image mode, if necessary
if (isset($_GET['i']) && !empty($_GET['i'])) {
	$feed = new SimplePie();
	$feed->bypass_image_hotlink();
	$feed->init();
}

/*
echo pxGetFeed('http://example.com/feed.xml', 'items:5|shortdesc:200|showdate:j M Y');
*/

function pxGetFeed($input, $options='') {
	$argv=Array();
	$options = explode('|', $options);
	$argv['targetLink'] = '_blank';
	
	foreach($options as $option) {
		$opt = explode(':', trim($option));
		if (strtolower($opt[0]) == 'items') $argv['items']=$opt[1];
		else if (strtolower($opt[0]) == 'showdesc') $argv['showdesc']=$opt[1];
		else if (strtolower($opt[0]) == 'showdate') $argv['showdate']=$opt[1];
		else if (strtolower($opt[0]) == 'shortdesc') $argv['shortdesc']=$opt[1];
		else if (strtolower($opt[0]) == 'error') $argv['error']=$opt[1];
		else if (strtolower($opt[0]) == 'showtitle') $argv['showtitle']=$opt[1];
		else if (strtolower($opt[0]) == 'alttitle') $argv['alttitle']=$opt[1];
		else if (strtolower($opt[0]) == 'targetLink') $argv['targetLink']=$opt[1];
	}

	//echo $input;
	
	$feed = new SimplePie();
	$feed->set_feed_url($input);
	$feed->set_cache_location(PLUGINPATH . "simplepie/cache");
	//$feed->bypass_image_hotlink();
	$feed->set_image_handler(true);
    //$feed->bypass_image_hotlink_page();
    //$feed->set_image_handler(true);
	/*$feed->bypass_image_hotlink_page(PLUGINPATH . "simplepie/simplepie_image_handler.php");*/
	$success = $feed->init();

	if ($success && $feed->data) {
		//$flink = $feed->get_feed_link();
		$flink = $feed->get_link();
		//$ftitle = $feed->get_feed_title();
		$ftitle = $feed->get_title();

		$output='';
		$target='_blank';
		$output .= '<div class="simplepie">';
		if (!isset($argv['showtitle']) || empty($argv['showtitle']) || $argv['showtitle'] == "true") {
			if (isset($argv['targetLink']) && $argv['targetLink'] != '') $target = $argv['targetLink'];
			if (isset($argv['alttitle']) && !empty($argv['alttitle'])) {
				if ($ftitle != '' && $flink != '') $output .= "<h3><a target=\"$target\" href=\"$flink\" title=\"". $argv['alttitle'] ."\">" . $argv['alttitle'] . "</a></h3>";
				else if ($ftitle != '') $output .= "<h3>" . $argv['alttitle'] . "</h3>";
			}
			else {
				if ($ftitle != '' && $flink != '') $output .= "<h3><a target=\"$target\" href=\"$flink\" title=\"$ftitle\">$ftitle</a></h3>";
				else if ($ftitle != '') $output .= "<h3>$ftitle</h3>";
			}
		}
		$output .= '<ol>';

		$max = $feed->get_item_quantity();
		if (isset($argv['items']) && !empty($argv['items'])) $max = min($argv['items'], $feed->get_item_quantity());

		for($x=0; $x<$max; $x++) {
			$item = $feed->get_item($x);
			$link = $item->get_permalink();
			$title = StupefyEntities($item->get_title());
			$full_desc = StupefyEntities($item->get_description());
			$desc = $full_desc;

			if (isset($argv['shortdesc']) && !empty($argv['shortdesc'])) {
				$suffix = '...';
				$short_desc = trim(str_replace("\n", ' ', str_replace("\r", ' ', strip_tags(StupefyEntities($item->get_description())))));
				$desc = substr($short_desc, 0, $argv['shortdesc']);
				$lastchar = substr($desc, -1, 1);
				if ($lastchar == '.' || $lastchar == '!' || $lastchar == '?') $suffix='';
				$desc .= $suffix;
			}

			if (isset($argv['showdesc']) && !empty($argv['showdesc']) && $argv['showdesc']==='false') {
				if (isset($argv['showdate']) && !empty($argv['showdate'])) {
					$output .= "<li><a target=\"$target\" href=\"$link\" title=\"$title\">$title</a> <span class=\"date\">" . $item->get_date($argv['showdate']) . "</span></li>";
				} else {
					$output .= "<li><a target=\"$target\" href=\"$link\" title=\"$title\">$title</a></li>";
				}
			} else {
				if (isset($argv['showdate']) && !empty($argv['showdate'])) {
					$output .= "<li><strong><a target=\"$target\" href=\"$link\" title=\"$title\">$title</a> <span class=\"date\">" . $item->get_date($argv['showdate']) . "</span></strong><br />$desc</li>";
				} else {
					$output .= "<li><strong><a target=\"$target\" href=\"$link\" title=\"$title\">$title</a></strong><br />$desc</li>";
				}
			}
		}

		$output .= '</ol>';
		$output .= '</div>';
	}
	else {
		if (isset($argv['error']) && !empty($argv['error'])) $output = $argv['error'];
		else if (isset($feed->error)) $output = $feed->error;
	}

	return $output;
}

// SmartyPants 1.5.1 changes rolled in May 2004 by Alex Rosenberg, http://monauraljerk.org/smartypants-php/
function StupefyEntities($s = '') {
	$inputs = array('&#8211;', '&#8212;', '&#8216;', '&#8217;', '&#8220;', '&#8221;', '&#8230;', '&#91;', '&#93;');
	$outputs = array('-', '--', "'", "'", '"', '"', '...', '[', ']');
	$s = str_replace($inputs, $outputs, $s);
	return $s;
}

?>