<?php

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
# ***** END LICENSE BLOCK *****

// Change if you have a different path.
$_PX_config['manager_path'] = dirname(__FILE__).'/manager';
// Change to match the id, instead of configweb_default.php you will have
// configweb_idofthesite.php

include_once $_PX_config['manager_path'].'/inc/lib.utils.php';
include_once $_PX_config['manager_path'].'/conf/configweb_default.php';

include_once $_PX_config['manager_path'].'/conf/config.php';

?>
