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

require_once dirname(__FILE__).'/../extinc/class.recordset.php';

/**
 * resourceset extends recordset to store dynamically a set of
 * resources. These resources can be news, articles, etc...
 * The goal is to provide a convinient way to iterate through
 * different type of resources.
 * An instance of recordset is created from a query against the
 * `resources` table joined to the `categories` to be able to
 * have the current category information. 
 * @see /manager/extinc/class.mysql.php for more details.
 * It relies on the resources to have some predefined methods.
 */
class ResourceSet extends recordset
{
    /**
     * Store the current resource object in the stack.
     */
    var $cur = null;

    /**
     * Store the fields to save.
     */
    var $auto_save = array();

    /**
     * Init the resourceset from an array.
     * If not empty, load the current resource object.
     */
    function ResourceSet($data='')
    {
        parent::recordset($data);
        if (!$this->isEmpty()) {
            $this->loadCurrent();
        }

    }

    /**
     * Auto save for load some fields.
     *
     * When loading a resource from the data in the current list
     * the list special data is not used in the object. For example
     * if the list of resources is coming from a search, the score
     * is lost. The auto save feature is used to recover such fields.
     *
     * @param array Fields to save
     * @return bool True
     */
    function autoSave($fields)
    {
        $this->auto_save = $fields;
        $this->recoverFields();
        return true;
    }

    /**
     * Recover the fields set by autoSave().
     *
     * @return bool True
     */
    function recoverFields()
    {
        if (!is_null($this->cur)) {
            foreach ($this->auto_save as $field) {
                $this->cur->setField($field, parent::f($field));
            }
        }
        return true;
    }

    /**
     * Access to data in the current resource.
     *
     * @param string Field to retrieve
     * @return mixed Field data
     */
    function f($field)
    {
    	if (isset($this->cur))
        	return $this->cur->f($field);
    	else
    		return '';
    }

    /**
     * Access to extended data in the current resource.
     *
     * @param string Extended resource
     * @param string Field to retrieve
     * @return mixed Field data
     */
    function extf($ext, $field)
    {
        if (isset($this->cur->$ext) && !empty($this->cur->$ext)) {
            return $this->cur->$ext->f($field);
        } else {
            //try to access not available extended data 
            //this is a problem, trigger a warning as most likely the code
            //has a problem
            /*
            trigger_error('Extended data "'.$ext
                          .'" not available for the current resource', 
                          E_USER_WARNING);
                          */
            return '';
        }
    }

    /**
     * Get the path of the resource.
     *
     * @string string Force the type of path ('')
     * @return string Path
     */
    function getPath($type='')
    {
    	if (isset($this->cur))
        	return $this->cur->getPath($type);
    	else
    		return '';
    }

    /**
     * From the data at the current index, load the
     * corresponding resource object into $this->cur
     */
    function loadCurrent()
    {
        $data = array();
        if (false === ($data[0] = $this->getRow())) {
            // this should never happen
            trigger_error('No data available to load a resource object '
                          .parent::f('resource_id'), 
                          E_USER_WARNING); 
            return false;
        }
        $type = parent::f('type_id');
        if (!class_exists($type)) {
            // we suppose here that the class file has been loaded before
            $type = 'Resource';
        }     
        $this->cur = new $type($data);
        if (false !== ($res = MemStorage::get($this->cur->f('resource_id')))) {
            $this->cur = $res;
        } else {
            $this->cur->load(); 
            $this->recoverFields();
            MemStorage::save($this->cur);
        }
    }

    /**
     * Move to the next resource.
     * 
     * @return bool false if no next resource
     */
    function moveNext()
    {
        if (parent::moveNext() && !$this->EOF()) {
            $this->loadCurrent();
            return true;
        } else {
            return false;
        }
    }


    /**
     * Get content of a field as text.
     * & is encoded as &amp;
     *
     * @param string Field to get
     * @return string Content
     */
    function getTextContent($field)
    {
        return $this->cur->getTextContent($field);
    }


    /**
     * Move to a given index.
     * 
     * @param int Index to move to
     * @return bool false if no index
     */
    function move($index)
    {
        if ($this->int_index == $index) {
            return true;
        }
        if (parent::move($index)) {
            $this->loadCurrent();
            return true;
        } else {
            return false;
        }
    }


    /**
     * Iterate through extended data.
     *
     * @param string Extended data
     * @return bool The move was a success or not
     */
    function extMoveNext($ext)
    {
        if (!empty($this->cur->$ext)) {
            return $this->cur->$ext->moveNext();
        } else {
            trigger_error('Extended data "'.$ext.'" not available for the current resource.', E_USER_WARNING);
            return false;
        }
    }

    /**
     * Check the end of extended data.
     *
     * @param string Extended data
     * @return bool At the end of the data
     */
    function extEOF($ext)
    {
        if (!empty($this->cur->$ext)) {
            return $this->cur->$ext->EOF();
        } else {
            trigger_error('Extended data "'.$ext.'" not available for the current resource.', E_USER_WARNING);
            return true; //Force to be at the end of the data
        }
    }

}


/**
 * Global storage of resources. Used by the ResourceSet to avoid reloading
 * many times a resource within a page.
 */
class MemStorage
{
    /**
     * Returns a resource or false if not available
     *
     * @param int Resource id
     * @return mixed Resource object or false
     */
    public static function get($res_id)
    {
        if (isset($GLOBALS['_PX_mem_storage'][$res_id])) {
            return $GLOBALS['_PX_mem_storage'][$res_id];
        } else {
            return false;
        }
    }

    /**
     * Save the resource.
     *
     * @param Object Resource object
     * @return true
     */
    public static function save($res)
    {
        if (!isset($GLOBALS['_PX_mem_storage'])
            or count($GLOBALS['_PX_mem_storage']) > 20) {
            $GLOBALS['_PX_mem_storage'] = array();
        }
        $GLOBALS['_PX_mem_storage'][$res->f('resource_id')] = $res;
        return true;
    }
}
?>
