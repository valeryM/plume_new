<?php
/* -*- tab-width: 4; indent-tabs-mode: nil; c-basic-offset: 4 -*- */
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
# The Original Code is DotClear Weblog.
#
# The Initial Developer of the Original Code is
# Olivier Meunier.
# Portions created by the Initial Developer are Copyright (C) 2003
# the Initial Developer. All Rights Reserved.
#
# Contributor(s):
#    Lo�c d'Anterroches
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

require_once dirname(__FILE__).'/../inc/class.l10n.php';

class recordset extends CError
{
    var $arry_data=array(); // tableau contenant les donn�es
    var $int_index; //index pour parcourir les enregistrements
    // les enregistrements commencent � l'index 0
        
    var $int_row_count=0; // nombre d'enregistrements
    var $int_col_count=0; // nombre de colonnes
    var $int_row_count_total=0; // Nombre d'enregistrements a l'initialisation
        
    function recordset($data='')
    {
        $this->int_index = 0;
    
        if (is_array($data)) {
            $this->arry_data = $data;
            $this->int_row_count = count($this->arry_data);
            $this->int_row_count_total = $this->int_row_count;
        
            if ($this->int_row_count == 0) {
                $this->int_col_count = 0;
            } else {
                $this->int_col_count = count($this->arry_data[0]);
            }
        }
    }
    
    function f($c)
    {
        if (!empty($this->arry_data)) {
            if (is_integer($c)) {
                $T = array_values($this->arry_data[$this->int_index]);
                return (isset($T[($c)])) ? $T[($c)] : false;
            } else {
            	if (!isset($this->arry_data[$this->int_index][$c])) $c = strtolower($c);
                
                if (isset($this->arry_data[$this->int_index][$c])) {
                    if (!is_array($this->arry_data[$this->int_index][$c])) {
                        return trim($this->arry_data[$this->int_index][$c]);
                    } else {
                        return $this->arry_data[$this->int_index][$c];
                    }
                } else {
                    return false;
                }
            }
        }
    }

    /**
     * Insert a new record at the end.
     * Set the cursor at the newly created position.
     *
     * @return int New index
     */
    function insert()
    {
        $this->arry_data[$this->int_row_count] = array();
        $this->int_row_count += 1; 
        $this->moveEnd();
        return ($this->int_row_count-1);
    }


    function field($c)
    {
        return $this->f($c);
    }
        
    function setField($c,$v)
    {
        $c = strtolower($c);
        $this->arry_data[$this->int_index][$c] = $v;
    }
        
    function moveStart()
    {
        $this->int_index = 0;
        return true;
    }
        
    function moveEnd()
    {
        $this->int_index = ($this->int_row_count-1);
        return true;
    }
    
    function moveNext()
    {
        if (!empty($this->arry_data) && !$this->EOF()) {
            $this->int_index++;
            return true;
        } else {
            return false;
        }
    }
        
    function movePrev()
    {
        if (!empty($this->arry_data) && $this->int_index > 0) {
            $this->int_index--;
            return true;
        } else {
            return false;
        }
    }
        
    function move($index)
    {
        if (!empty($this->arry_data) 
            && $this->int_index >= 0 
            && $index < $this->int_row_count) {
            $this->int_index = $index;
            return true;
        } else {
            return false;
        }
    }
        
    function BOF()
    {
        return ($this->int_index == -1 || $this->int_row_count == 0);
    }
        
    function EOF()
    {
        return ($this->int_index == $this->int_row_count);
    }
        
    function isEmpty()
    {
        return ($this->int_row_count == 0);
    }
        
    // Donner le tableau de donn�es
    function getData()
    {
        return $this->arry_data;
    }

    /**
     * Get the current row of data.
     */
    function getRow()
    {
        if (!$this->BOF() && !$this->EOF()) {
            return $this->arry_data[$this->int_index];
        }
        return false;
    }

    // Nombre de lignes
    function nbRow()
    {
        return $this->int_row_count;
    }
    
    // Nombre de lignes a l'initialisation
    function nbRowTotal()
    {
        return $this->int_row_count_total;
    }    
    
    // Nombre de colonnes a l'initialisation
    function nbCol()
    {
        return count($this->arry_data[$this->int_index]);
    }  
    
    function getField($f) {
    	return $this->arry_data[$this->int_index][$f];
    }
    /**
     * Get ids of resources with a prefix or not.
     *
     * @return array ids
     * @param  string key for ids
     * @param  string prefix for ids ('')
     */
    function getIDs($key, $str='')
    {
        $res = array();
        foreach ($this->arry_data as $k => $v) {
            $res[] = $str.$v[$key];
        }
        return $res;
    }

    
    function getIndex()
    {
        return $this->int_index;                 
    }
}
?>
