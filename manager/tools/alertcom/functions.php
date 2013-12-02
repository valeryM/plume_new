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
# ***** END LICENSE BLOCK ***** */

/*Function to get Webmaster e-mail (getWebmaster())*/
function getWebmaster()
{
    $con =& pxDBConnect();
   
    $sql = sprintf('SELECT %s.* FROM %s INNER JOIN %s WHERE level = 9 and website_id = \'%s\'',
                    $con->pfx.'users', $con->pfx.'users', $con->pfx.'grants',
                    $GLOBALS['_PX_website_config']['website_id']);
    $res = $con->select($sql);
   
    return $res;
}

function emailListTesting($email_list)
{
        $tab_email = explode(",", $email_list);
        foreach ($tab_email as $email)
        { 
                if (!preg_match("!^[a-zA-Z0-9._-]+@[a-zA-Z0-9._-]{2,}\.[a-zA-Z]{2,4}$!", trim($email)))
                {
                return true;
                break;
                }
        }
}
?>