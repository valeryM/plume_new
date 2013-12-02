<?php
/*
# ***** BEGIN LICENSE BLOCK *****
# Version: MPL 1.1/GPL 2.0/LGPL 2.1
#
# The contents of this file are subject to the Mozilla Public License Version
# 1.1 (the "License"); you may not use this file except in compliance with
# the License. You may obtain a copy of the License at
# http://www.mozilla.org/MPL/
#
# Software distributed under the License is distributed on an "AS IS" basis,
# WITHOUT WARRANTY OF ANY KIND, either express or implied. See the License
# for the specific language governing rights and limitations under the
# License.
#
# The Original Code is Plume CMS.
#
# The Initial Developer of the Original Code is
# Loic d'Anterroches
# 
# Portions created by the Initial Developer are Copyright (C) 2004
# the Initial Developer. All Rights Reserved.
#
# Contributor(s):
# Olivier Meunier.
#
# Alternatively, the contents of this file may be used under the terms of
# either the GNU General Public License Version 2 or later (the "GPL"), or
# the GNU Lesser General Public License Version 2.1 or later (the "LGPL"),
# in which case the provisions of the GPL or the LGPL are applicable instead
# of those above. If you wish to allow use of your version of this file only
# under the terms of either the GPL or the LGPL, and not to allow others to
# use your version of this file under the terms of the MPL, indicate your
# decision by deleting the provisions above and replace them with the notice
# and other provisions required by the GPL or the LGPL. If you do not delete
# the provisions above, a recipient may use your version of this file under
# the terms of any one of the MPL, the GPL or the LGPL.
#
# ***** END LICENSE BLOCK ***** */

/**
 * Class to manage the thumbnails
 * It provides also a static method to get the gd library version. 
 */
class thumbnail 
{
    var $_thumbdir = ''; /**< Path to the thumbnail folder. */
    var $_thumbfile = ''; /**< Path to the last created thumbnail. */

    /**
     * Init the thumbnail class. 
     */
    function thumbnail($thumbdir = '')
    {
	$this->_thumbdir = $thumbdir;
    }

    /**
     * Get the name of a thumbnail from the image name, the image
     * name can include a path this is just a md5() operation.
     * If the height and the width are given, the info is used to
     * generate the name, else not.
     */
    function getName($img, $w=0, $h=0)
    {
	$name = md5($img);
	if ($w>0 && $h>0) {
	    $name .= '-'.$w.'x'.$h;
	}
	$name .= '.jpg';
	return $name;
    }
 
    /**
     * Get the path to the thumbnail.
     */
    function getPath($img, $w=0, $h=0)
    {
	return $this->_thumbdir.'/'.$this->getName($img, $w, $h);
    }

    /**
     * Get the size of the last created thumbnail
     * return the same results as the builtin getimagesize PHP function.
     */
    function getSize() 
    {
	if (!empty($this->_thumbfile)) {
	    return getimagesize($this->_thumbfile);
	} else {
	    return false;
	}
    }

    /**
     * Create thumbnail of an image, proportions are kept.
     *
     * @param string Path to source image
     * @param string Path to the thumbnail image
     * @param int Max width of the thumbnail
     * @param int Max height of the thumbnail
     * @return bool Success or not
     */
    function create($src, $dst, $w=120, $h=200)
    {
	if (!file_exists($src)) {
	    return false;
	}
    
	if (($size = @getimagesize($src)) === false) {
	    return false;
	}
	
	$type = $size[2];
	$H = $size[1];
	$W = $size[0];
    
	if ($type == '1') {
	    $function = 'imagecreatefromgif';
	} elseif ($type == '2') {
	    $function = 'imagecreatefromjpeg';
	} elseif ($type == '3') {
	    $function = 'imagecreatefrompng';
	} else {
	    return false;
	}
	if (!function_exists($function)) {
	    return false;
	}

	if (($img = @$function($src)) == false) {
	    return false;
	}

	// get the zoom factors and the thumbnail height and width
	$rB = $H/$W;
	$rS = $h/$w;
	if (($H > $h) && ($W > $w)) {
	    if ($rB > $rS) {
		$height = $h;
		$width  = $height/$rB;
	    } else {
		$width = $w;
		$height = $width*$rB; 
	    }
	} elseif ($H > $h) {
	    $height = $h;
	    $width  = $height/$rB;
	} elseif ($W > $w) {
	    $width = $w;
	    $height = $width*$rB; 
	} else {
	    $height = $H;
	    $width  = $W;
	} 

	$zx = $W/$width;
	$zy = $H/$height;

	if (thumbnail::gd_version() >= 2) {
	    if ( ($img2 = imagecreatetruecolor(round($width),round($height)))  === false) {
		return false;
	    }
	} else {
	    if ( ($img2 = ImageCreate(round($width),round($height)))  === false) {
		return false;
	    }	
	}

	$this->_resampleBicubic($img2,$img,0,0,0,0,$width,$height,$zx,$zy);

	if (@imagejpeg($img2,$dst,80) === false) {
	    return false;
	}
	imagedestroy($img2);
	if (@file_exists($dst)) {
	    $this->_thumbfile = $dst;
	    return true;
	} else {
	    $this->_thumbfile = '';
	    return false;
	}
    }
    
    /**
     * Get the current GD version. Need the output buffering functions.
     */
    function gd_version() 
    {
	static $gd_version_number = null;
	if ($gd_version_number === null) {
	    // Use output buffering to get results from phpinfo()
	    // without disturbing the page we're in.  Output
	    // buffering is "stackable" so we don't even have to
	    // worry about previous or encompassing buffering.
	    ob_start();
	    phpinfo(8);
	    $module_info = ob_get_contents();
	    ob_end_clean();
	    if (preg_match("/\bgd\s+version\b[^\d\n\r]+?([\d\.]+)/i",
			   $module_info,$matches)) {
		$gd_version_number = $matches[1];
	    } else {
		$gd_version_number = '0';
	    }
	}
	return $gd_version_number;

    }

    /* ========================================================================
     *   Private functions, should not be called from outside of the class.
     * ========================================================================
     */

    /**
     * Resample the image 
     * http://www.php.net/manual/en/function.imagecopyresized.php
     */
    private function _resampleBicubic(&$dst, &$src, $dstx, $dsty, $srcx, $srcy, $w, $h, $zoomX, $zoomY = '')
    {
	if (!$zoomY) {
	    $zoomY = $zoomX;
	}
	
	$palsize = ImageColorsTotal($src);
	
	for ($i = 0; $i<$palsize; $i++) {
	    $colors = ImageColorsForIndex($src, $i);
	    ImageColorAllocate($dst, $colors['red'], $colors['green'], $colors['blue']);
	}
	
	$zoomX2 = (int)($zoomX/2);
	$zoomY2 = (int)($zoomY/2);
	
	$dstX = imagesx($dst);
	$dstY = imagesy($dst);
	$srcX = imagesx($src);
	$srcY = imagesy($src);
	
	for ($j = 0; $j<($h-$dsty); $j++) {
	    $sY = (int)($j*$zoomY)+$srcy;
	    $y13 = $sY+$zoomY2;
	    $dY = $j+$dsty;
	    
	    if (($sY >= $srcY) or ($dY >= $dstY) or ($y13 >= $srcY)) {
		break 1;
	    }
	    
	    for ($i = 0; $i<($w-$dstx); $i++) {
		$sX = (int)($i*$zoomX)+$srcx;
		$x34 = $sX+$zoomX2;
		$dX = $i+$dstx;
		
		if (($sX >= $srcX) or ($dX >= $dstX) or ($x34 >= $srcX)) {
		    break 1;
		}
	    
		$c1 = ImageColorsForIndex($src, ImageColorAt($src, $sX, $y13));
		$c2 = ImageColorsForIndex($src, ImageColorAt($src, $sX, $sY));
		$c3 = ImageColorsForIndex($src, ImageColorAt($src, $x34, $y13));
		$c4 = ImageColorsForIndex($src, ImageColorAt($src, $x34, $sY));
	    
		$r = ($c1['red']+$c2['red']+$c3['red']+$c4['red'])/4;
		$g = ($c1['green']+$c2['green']+$c3['green']+$c4['green'])/4;
		$b = ($c1['blue']+$c2['blue']+$c3['blue']+$c4['blue'])/4;
	    
		ImageSetPixel($dst, $dX, $dY, ImageColorClosest($dst, $r, $g, $b));
	    }
	}
    }
}
?>
