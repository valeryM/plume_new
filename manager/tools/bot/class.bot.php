<?php
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
# The Initial Developer of the Original Code is
# Olivier Meunier.
# Portions created by the Initial Developer are Copyright (C) 2003
# the Initial Developer. All Rights Reserved.
#
# Contributor(s):
# - Sebastien Fievet
#
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
#
# ***** END LICENSE BLOCK ***** */

class bot
{
	var $con;
	var $site_id;
	
	
	/**
	*	constructeur de la class BOT
	*	permet de dŽfinir les variables de la classe
	*
	*	@param	(str) nom du site courant ("default")
	*/
	function bot($site_id)
	{
		if ($this->con === null)
			$this->con =& pxDBConnect();
		$this->site_id = $site_id;
	}
	
	function getResources($byYear=false, $byMonth=false, $byDay=false, $order='ASC')
	{
		$select =	'';
		$groupBy = '';
		$orderBy = '';

		if(!empty($byYear))
		{
			$select.= (strlen($select)>0 ? ', SUBSTRING(`creationdate`, 1, 4) AS myYear':'SELECT COUNT(*) as count, `subject`, SUBSTRING(`creationdate`, 1, 4) AS myYear');
			$groupBy.= (strlen($groupBy)>0 ? ' myYear':' GROUP BY myYear');
			$orderBy.= (strlen($orderBy)>0 ? ' myYear':' ORDER BY myYear').' '.$order;
		}
		if(!empty($byMonth))
		{
			$sql = 'MONTHNAME(CONCAT(SUBSTRING(`creationdate`, 1, 4),"-",SUBSTRING(`creationdate`, 5, 2),"-",SUBSTRING(`creationdate`, 7, 2))) AS myMonth
					, SUBSTRING(`creationdate`, 5, 2) AS myMonthNb';
			$select.= (strlen($select)>0 ? ', '.$sql:'SELECT COUNT(*) as count, `subject`, '.$sql);
			$groupBy.= (strlen($groupBy)>0 ? ', myMonthNb':' GROUP BY myMonthNb');
			$orderBy.= (strlen($orderBy)>0 ? ', myMonthNb':' ORDER BY myMonthNb').' ASC';
		}
		if(!empty($byDay))
		{
			$sql	=	'DAYNAME(CONCAT(SUBSTRING(`creationdate`, 1, 4),"-",SUBSTRING(`creationdate`, 5, 2),"-",SUBSTRING(`creationdate`, 7, 2))) AS myDay'.
						', SUBSTRING(`creationdate`, 7, 2) as myDayNb';
			$select.= (strlen($select)>0 ? ' , '.$sql:'SELECT COUNT(*) as count, `subject`, '.$sql);
			$groupBy.= (strlen($groupBy)>0 ? ', myDayNb':' GROUP BY myDayNb');
			$orderBy.= (strlen($orderBy)>0 ? ', myDayNb':' ORDER BY myDayNb').' ASC';
		}
		
		$sql = $select.
				' FROM '.$this->con->pfx.'resources
				LEFT JOIN '.$this->con->pfx.'categoryasso ON '.$this->con->pfx.'categoryasso.identifier='.$this->con->pfx.'resources.identifier
				LEFT JOIN '.$this->con->pfx.'categories ON '.$this->con->pfx.'categoryasso.category_id='.$this->con->pfx.'categories.category_id
				LEFT JOIN '.$this->con->pfx.'websites ON '.$this->con->pfx.'websites.website_id='.$this->con->pfx.'resources.website_id
				LEFT JOIN '.$this->con->pfx.'subtypes ON '.$this->con->pfx.'subtypes.subtype_id='.$this->con->pfx.'resources.subtype_id';
		
		$sql .= ' AND '.$this->con->pfx.'resources.website_id=\''.$this->con->esc(config::f('website_id')).'\''."\n";
		$sql .= ' AND '.$this->con->pfx.'resources.status=\''.PX_RESOURCE_STATUS_VALIDE.'\''."\n";
		$sql .= ' AND '.$this->con->pfx.'resources.publicationdate <= '.date::stamp();
		$sql .= ' AND '.$this->con->pfx.'resources.enddate >= '.date::stamp();
		$sql .= (strlen($groupBy)>0 ? $groupBy:'GROUP BY 1').(strlen($orderBy)>0 ? $orderBy:'');

		if (($rs = $this->con->select($sql)) === false)
		    return false;
		return $rs;
	}
}
?>