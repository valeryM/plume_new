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

require_once dirname(__FILE__).'/class.resourceset.php';

/**
 * paginator extends resourceset to provide pagination of
 * a resourceset.
 */
class Paginator extends ResourceSet
{
    /**
     * Number of resources per page.
     */
    var $res_per_page = 0;

    /**
     * Current page.
     */
    var $current_page = 1;

    /**
     * Total number of pages.
     */
    var $total_pages = 1;

    /**
     * Init the Paginator from an array.
     *
     * @param array Resource data ('')
     * @param int Number of resource per page ('')
     * @param int Current page number ('')
     */
    function Paginator($data='', $res_per_page='', $current_page='')
    {
        parent::ResourceSet($data);
        if ($res_per_page !== '') {
            $this->res_per_page = $res_per_page;
        }
        if ($current_page != '') {
            $this->current_page = $current_page;
        }
        if (!$this->isEmpty()) {
            if ($this->res_per_page > 0) {
                $this->move(($this->current_page-1) * $this->res_per_page);
            }
        }
    }

    /**
     * End of file. Returns true at the end of the page.
     *
     * @return bool Is at the end of the page.
     */
    function EOF()
    {
        return ($this->int_index == $this->int_row_count) 
            or ($this->int_index and ($this->int_index == $this->res_per_page * $this->current_page));
    }

}
?>
