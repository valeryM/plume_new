<?php
header("Content-type: text/xml");
header("Content-Disposition: attachment; filename=\"database.xml\"");
header('Expires: 0');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Pragma: public');
/*
 * 
<database>
	<action id="articles" label="Create table %s" string="{{PREFIX}}articles">
		<test eq="neq" value="{{PREFIX}}articles" label="Table %s exists"
		string="{{PREFIX}}articles">SHOW TABLES LIKE '{{PREFIX}}articles'</test>
		CREATE TABLE {{PREFIX}}articles (
		page_id int(10) unsigned NOT NULL auto_increment,
		resource_id int(10) unsigned NOT NULL default '0',
		page_number int(10) unsigned NOT NULL default '0',
		page_title varchar(250) NOT NULL default '',
		page_content longtext NOT NULL,
		page_creationdate bigint(20) unsigned NOT NULL default '0',
		page_modifdate bigint(20) unsigned NOT NULL default '0',
		PRIMARY KEY	(page_id),
		KEY resource_id (resource_id)
		) {{TYPE}} {{CHARSET}}
	</action>
</database>
 */

require_once dirname(__FILE__).'/prepend.php';
require_once dirname(dirname(__FILE__)).'/conf/config.php';
$m = new Manager();

if (!isset($con)) 
	$con =& pxDBConnect();
	
$xml =  '<?xml version="1.0" encoding="'.$_PX_config['encoding'].'"?>
<!-- 
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
# The Original Code is PLUME CMS.
#
# The Initial Developer of the Original Code is
# loic d\'Anterroches.
# Portions created by the Initial Developer are Copyright (C) 2003
# the Initial Developer. All Rights Reserved.
#
# Contributor(s):
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
# ***** END LICENSE BLOCK *****
-->
<database>'."\n\n";

$prefix = $_PX_config['db']['table_prefix'];

// récupère la liste des tables de la base de données
$tables = $con->getTablesList();
$colname= 'Tables_in_'.$_PX_config['db']['db_database'];
while (!$tables->EOF() )  {
	// pour chaque table ayant le préfixe
	// lecture de la structure
	$tablename = $tables->f($colname);
	if(strstr($tablename, $prefix) !== false) {
		// préfixe trouvé
		// construction des propriétés xml
		$subname = str_replace($prefix,'',$tablename);
		$xml .= "\t".'<action id="'.$subname.'" label="Create table %s" string="{{PREFIX}}'.$subname.'">'."\n";
		// lecture de la structure
		$sqlDef = $con->getTableDefinition($tablename, 'array');
		//echo print_r($sqlDef,true);
			while (!$sqlDef->EOF() )  {
				
				$sqlCreate = $sqlDef->f('create table');
				$sqlCreate = str_replace($tablename,'{{PREFIX}}'.$subname,$sqlCreate);
				$sqlCreate = str_replace('`','',$sqlCreate);
				$sqlCreate = str_replace("\n  ","\n\t\t", $sqlCreate);
				$sqlCreate = str_replace('DEFAULT', 'default', $sqlCreate);
				$sqlCreate = str_replace('AUTO_INCREMENT', 'auto_increment', $sqlCreate);
				$pos = strpos($sqlCreate,'ENGINE=');
				if ($pos >0) {
					$sqlCreate= substr($sqlCreate, 0,$pos-1).' {{TYPE}} {{CHARSET}}';
				}
				$xml .= "\t\t".'<test eq="neq" value="{{PREFIX}}'.$subname.'" label="Table %s exists" '."\n";
				$xml .= "\t\t".'string="{{PREFIX}}'.$subname.'">SHOW TABLES LIKE \'{{PREFIX}}'.$subname.'\'</test>'."\n";
				$xml .= "\t\t".$sqlCreate."\n";
			/*
			 * 
			<action id="articles" label="Create table %s" string="{{PREFIX}}articles">
				<test eq="neq" value="{{PREFIX}}articles" label="Table %s exists"
				string="{{PREFIX}}articles">SHOW TABLES LIKE '{{PREFIX}}articles'</test>
					CREATE TABLE {{PREFIX}}articles (
					page_id int(10) unsigned NOT NULL auto_increment,
					resource_id int(10) unsigned NOT NULL default '0',
					page_number int(10) unsigned NOT NULL default '0',
					page_title varchar(250) NOT NULL default '',
					page_content longtext NOT NULL,
					page_creationdate bigint(20) unsigned NOT NULL default '0',
					page_modifdate bigint(20) unsigned NOT NULL default '0',
					PRIMARY KEY	(page_id),
					KEY resource_id (resource_id)
					) {{TYPE}} {{CHARSET}}
			</action>
			 */
				$sqlDef->moveNext();

		}
		$xml .= "\t".'</action>'."\n";
	}
	$xml .= "\n";
	$tables->moveNext();	
}
$xml .= "\n".'</database>'."\n";

echo $xml;

exit;