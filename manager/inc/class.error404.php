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



class Error404
{
    /**
     * Action to display the 404 error.
     *
     * @param string Server query string
     * @return int Success code
     */
    public static function action($query)
    {
    	
    	$l10n = new l10n(config::f('lang'));
        $l10n->loadTemplate(config::f('lang'), config::f('theme_id'));
        
        config::setVar('category_page', 1);
        $GLOBALS['_PX_render']['last'] = '';
        $last =& $GLOBALS['_PX_render']['last']; 
        $GLOBALS['_PX_render']['website'] = '';
        $website =& $GLOBALS['_PX_render']['website']; 
        $GLOBALS['_PX_render']['res'] = '';
        $res =& $GLOBALS['_PX_render']['res']; 

        // Parse query string to find the matching article
        $con =& pxDBConnect();

        // Find possible matches in the DB for a category
		if ('/' != substr($query, 0, 1)) $query = '/'.$query;
		$r = 'SELECT * FROM '.$con->pfx.'categories
			    LEFT JOIN '.$con->pfx.'websites ON '.$con->pfx.'websites.website_id = '.$con->pfx.'categories.website_id
			    WHERE '.$con->pfx.'categories.website_id=\''.$con->escapeStr($GLOBALS['_PX_website_config']['website_id']).'\'
			    AND '.$con->pfx.'categories.category_path=\''.$con->escapeStr($query.'/').'\'';
		if (($rs = $con->select($r)) !== false) {
			if ($rs->f('category_id')) {
				//bingo the error was a missing / at the end for a category
				$con->close();
                if (config::f('url_format') == 'simple') {
                    $query = '/?'.$query;
                }
                $GLOBALS['_PX_redirect'] = $GLOBALS['_PX_website_config']['rel_url'].$query.'/';
                return 301;
			}
        }
		//need to see if a redirection is available.
		$r = 'SELECT * FROM '.$con->pfx.'smart404 WHERE
		      website_id=\''.$con->escapeStr($GLOBALS['_PX_website_config']['website_id']).'\' AND
		      oldpage=\''.$con->escapeStr($query).'\'';
		if (($rs = $con->select($r)) !== false) {
			if ($rs->f('newpage')) {
				//bingo
				$page = $rs->f('newpage');
				//try to update the count
				$r = 'UPDATE '.$con->pfx.'smart404 SET
				lastroutingdate=\''.date::stamp().'\', total=total+1
				WHERE website_id=\''.$con->escapeStr($GLOBALS['_PX_website_config']['website_id']).'\' AND
		        oldpage=\''.$con->escapeStr($query).'\' LIMIT 1';
				$con->execute($r); //we do not check if errors as we already know the page
				@$con->close();
                $GLOBALS['_PX_redirect'] = $page;
                return 301;
			}
		} 

		//log the error if log requested
		if (config::f('log404errors')) {
				$ref = (!empty($_SERVER['HTTP_REFERER'])) ? $_SERVER['HTTP_REFERER'] : '';
				$agent = (!empty($_SERVER['HTTP_USER_AGENT'])) ? substr($_SERVER['HTTP_USER_AGENT'],0, 250) : '';
				$r = 'INSERT INTO '.$con->pfx.'smart404logs SET
                website_id=\''.$con->escapeStr($GLOBALS['_PX_website_config']['website_id']).'\',
                page=\''.$con->escapeStr($query).'\', fromurl=\''.$con->escapeStr($ref).'\',
                date=\''.date::stamp().'\', useragent=\''.$con->escapeStr($agent).'\'';
				$con->execute($r); //we do not check if errors as it is not critical
        }


        $s = new Search($con, config::f('website_id'));

        $query = preg_replace('#^(/error404)#', '', $query);
        config::setVar('query_string', Error404::parseQueryString($query));

        $sql = $s->create_search_query_string(config::f('query_string'));
        $extra = ' AND '.$con->pfx.'resources.publicationdate <= '
            .date::stamp().' AND '.$con->pfx.'resources.enddate >= '
            .date::stamp().' AND '.$con->pfx.'resources.status = '
            .PX_RESOURCE_STATUS_VALIDE;
        $sql = sprintf($sql, $extra);

        if (($res = $con->select($sql, 'ResourceSet')) === false) {
            $GLOBALS['_PX_render']['error']->setError('MySQL: '
                                                      .$con->error(), 500);
            return 404;
        }

        $GLOBALS['_PX_render']['res']->autoSave(array('score', 'total'));
        $GLOBALS['_PX_render']['website'] = FrontEnd::getWebsite();

        header('Status: 404 Not Found');
        header(FrontEnd::getHeader('404.php'));
        // Load the template        
        include config::f('manager_path').'/templates/'
            .config::f('theme_id').'/404.php';
            
        return 200;
    }

    /**
     * Parse query string.
     *
     * @param string Query string
     * @return string Clean query string
     */
    public static function parseQueryString($query)
    {
        $clean = '';
        $query = Search::clean_string($query);
        $words = Search::explode_in_words($query, true);
        foreach ($words as $k => $v) {
            $clean .= $k.' ';
        }
        return trim($clean);
    }

}
?>
