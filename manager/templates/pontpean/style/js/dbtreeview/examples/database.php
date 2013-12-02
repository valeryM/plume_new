<?php 

//to test this file
// 1. create a MySQL database and import the nace.sql.gz SQL file
// 2. create a credentials.php file that define DB_HOST, DB_USER, DB_PASSWORD, and DB_DATABASE

/**
 * Example.
 * @package    DBTreeView
 * @author     Rodolphe Cardon de Lichtbuer <rodolphe@wol.be>
 * @copyright  2007 Rodolphe Cardon de Lichtbuer
 * @license    http://www.opensource.org/licenses/lgpl-license.php LGPL
 */

define("TREEVIEW_LIB_PATH","../lib/dbtreeview");

require_once(TREEVIEW_LIB_PATH . '/dbtreeview.php');

//the file credentials is not included in the distribution.
//it only defines these constants:
//DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE
require_once("../credentials.php"); 

class MyHandler implements RequestHandler{
	
	//using 2 attribute : id=0, 1, 2, 3   root=1

	public function handleChildrenRequest(ChildrenRequest $req){
		
		$attributes = $req->getAttributes();
		
		if(!isset($attributes['code'])){
			die("error: attribute code not given");
		}
		$parentCode = $attributes['code'];
	
		$link = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD)
    		   or die("Unable to connect to database.");
		mysql_select_db(DB_DATABASE) or die("Could not select database");

		if(!mysql_query("SET CHARACTER SET utf8")){
			throw new Exception('Could not set character set UTF-8.');
		}
		

		$query = sprintf("SELECT * FROM nace WHERE parent='%s'", 
					mysql_escape_string($parentCode));
					
					
		$result = mysql_query($query) or die("Query failed");

		$nodes=array();
		

		while ($line = mysql_fetch_assoc($result)) {
			$code = $line["code"];
			$text = "<b>$code</b> : ".$line["description"];
			
		 $node = DBTreeView::createTreeNode(
				$text, array("code"=>$code));
	     $node->setURL(sprintf("javascript:alert(\"Open page for NACECODE %s\");", $code));
		
		//has children
		$query2 = sprintf("SELECT * FROM nace WHERE parent='%s' LIMIT 1", 
					mysql_escape_string($code));
					
		$result2 = mysql_query($query2) or die("Query failed");
		if(!mysql_fetch_assoc($result2)){
			//no children
			$node->setHasChildren(false);
			$node->setClosedIcon("doc.gif");
		}
		$nodes[] = $node;
		}

		// Lib?ration des r?sultats 
		mysql_free_result($result);

		// Fermeture de la connexion 
		mysql_close($link);
		
		$response = DBTreeView::createChildrenResponse($nodes);
		return $response;
	}
} //class TestListener


try{
	DBTreeView::processRequest(new MyHandler());
}catch(Exception $e){
	echo("Error:". $e->getMessage());
}

//head
print("<html>\n<head>\n");
printf("<script src=\"%s/treeview.js\" type=\"text/javascript\"></script>\n",
			TREEVIEW_LIB_PATH);
print('<link href="screen.css" rel="stylesheet" type="text/css" media="screen"/>');
printf('<link href="%s/treeview.css" rel="stylesheet" type="text/css" media="screen"/>'."\n",
			TREEVIEW_LIB_PATH);
print("</head>\n");
?>
<body>
<h1>Demonstration</h1>
<p>The tree below is an example of what you can do with <strong>PHP DBTreeView</strong>.</p>
<p>The content of this treeview is extractd from the NACEBEL codes of the belgian federal public service of economy. The content is in french (sorry...). </p>
<p>The treeview has <strong>more than 3800 nodes</strong> !</p>
<p>This useful tree download nodes on demand. When you clic on a '+' to see a branch, a request is sent to the server, without page refresh.</p>
<?php
$rootAttributes = array("code"=>"0");
$treeID = "treev1";
$tv = DBTreeView::createTreeView(
		$rootAttributes,
		TREEVIEW_LIB_PATH, 
		$treeID);
$tv->setRootHTMLText("NACEBEL codes");
$tv->setRootIcon("star.gif");
$tv->printTreeViewScript();
?>
</body>
</html>