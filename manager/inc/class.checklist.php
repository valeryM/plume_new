<?php
/* -*- tab-width: 4; indent-tabs-mode: nil; c-basic-offset: 4 -*- */
/*
# ***** BEGIN LICENSE BLOCK *****
# This file is part of Plume CMS, a website management application.
# Copyright (C) 2001-2005 Loic d'Anterroches and contributors.
#
# Plume CMS is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.
#
# Plume CMS is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
#
# ***** END LICENSE BLOCK ***** */


/**
 * @class checklist
 *
 * Class to run series of tests/checks.
 * A result of a test can be good, bad or warning.
 */

class CheckList
{
    var $tests = array();
    
    /**
     * Create a new checklist with a test.
     *
     * @see addtest
     */
    function CheckList($id='', $res=0, $good='', $bad='', $warning='')
    {
        $this->addTest($id, $res, $good, $bad, $warning);
    }

    /**
     * Add a new test
     *
     * @param string id of the test
     * @param int result of the test (0=bad, 1=good, 2=warning)
     * @param string String in case of good result
     * @param sring String in case of bad result
     * @param string String in case of warning
     * @return bool valid or invalid test
     */
    function addTest($id='', $res=0, $good='', $bad='', $warning='')
    {
        if (empty($id)) {
            return false;
        }
        $string = $good;
        if (2 === $res) {
            $string = $warning;
        } elseif (false === $res or 0 === $res) {
            $string = $bad;
            $res = 0;
        } else {
            $res = 1;
        }
        $this->tests[] = array(
                               'id' => $id,
                               'res' => $res,
                               'mess' => $string
                               );
        return true;
    }
    
    /** 
     * Compatibility with the dotclear checklist 
     */
    function addItem($id='', $res=0, $good='', $bad='') 
    {
        if (NULL === $res) $res = 1;
        return $this->addTest($id, $res, $good, $bad);
    }

    /**
     * Check the tests, 2 levels all good or good and warnings.
     *
     * @param int Level 1=all good, 2=warnings ok (default)
     * @return bool global result of the tests
     */
    function checkAll($level=2)
    {
        foreach ($this->tests as $test) {
            if (0 === $test['res']) return false;
            if (1 === $level && 2 === $test['res']) return false;
        }
        return true;
    }

    /**
     * Get the an html list with the results of the tests.
     *
     * @param string Path to the images (no trailing slash)
     * @param string Good image ('check_on.png')
     * @param string Bad image ('check_off.png')
     * @param string Warning image ('check_wait.png')
     * @return string HTML code
     */
    function getHtml($path, $gi='check_on.png', 
                     $bi='check_off.png', $wi='check_wait.png')
    {
        reset($this->tests);
        $o = '<ul class="checklist">'."\n";
        foreach ($this->tests as $test) {
            $img = $path.'/'.$gi;
            $string = $test['mess'];
            if (2 === $test['res']) {
                $img = $path.'/'.$wi;
            } elseif (false == $test['res']) {
                $img = $path.'/'.$bi;
            }
            $o .= '<li><img src="'.$img.'" /> '.$string.'</li>'."\n";
        }
        $o .= '</ul>'."\n\n";
        return $o;
    }
}
?>
