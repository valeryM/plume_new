<?php 

/**
 * Example.
 * @package    DBTreeView
 * @author     Rodolphe Cardon de Lichtbuer <rodolphe@wol.be>
 * @copyright  2007 Rodolphe Cardon de Lichtbuer
 * @license    http://www.opensource.org/licenses/lgpl-license.php LGPL
 */

define("TREEVIEW_LIB_PATH","../lib/dbtreeview");

require_once(TREEVIEW_LIB_PATH . '/dbtreeview.php');

class MyHandler implements RequestHandler{
	
public function handleChildrenRequest(ChildrenRequest $req){
		
		$attributes = $req->getAttributes();

		if(!isset($attributes['code'])){
			die("error: attribute code not given");
		}
		$parentCode = $attributes['code'];
		$nodes = array();
		if($parentCode=="0"){
				$node = DBTreeView::createTreeNode("Node 1", array("code"=>"1"));
		        $node->setURL("javascript:alert(\"You clicked on node 1\");");
				$nodes[] = $node; //add node to nodes array
				$node = DBTreeView::createTreeNode("Node 2", array("code"=>"2"));
		        $node->setURL("javascript:alert(\"You clicked on node 2\");");
				$nodes[] = $node;
				$node = DBTreeView::createTreeNode("Node 3 (no child)", array("code"=>"3"));
		        $node->setURL("javascript:alert(\"You clicked on node 3\");");
				$nodes[] = $node;

		}
		if($parentCode=="1"){
				$node = DBTreeView::createTreeNode("Node 11", array("code"=>"11"));
		        $node->setURL("javascript:alert(\"You clicked on node 11\");");
				$nodes[] = $node; //add node to nodes array
				$node = DBTreeView::createTreeNode("Node 12", array("code"=>"12"));
		        $node->setURL("javascript:alert(\"You clicked on node 12\");");
				$nodes[] = $node;
				$node = DBTreeView::createTreeNode("Node 13", array("code"=>"13"));
		        $node->setURL("javascript:alert(\"You clicked on node 13\");");
				$nodes[] = $node;
		}
		if($parentCode=="2"){
				$node = DBTreeView::createTreeNode("Node 21", array("code"=>"21"));
		        $node->setURL("javascript:alert(\"You clicked on node 11\");");
				$nodes[] = $node; //add node to nodes array
				$node = DBTreeView::createTreeNode("Node 22", array("code"=>"22"));
		        $node->setURL("javascript:alert(\"You clicked on node 12\");");
				$nodes[] = $node;
				$node = DBTreeView::createTreeNode("Node 23", array("code"=>"23"));
		        $node->setURL("javascript:alert(\"You clicked on node 13\");");
				$nodes[] = $node;
		}
		$response = DBTreeView::createChildrenResponse($nodes);
		return $response;
	}
}
try{
	DBTreeView::processRequest(new MyHandler());
}catch(Exception $e){
	echo("Error:". $e->getMessage());
}

//head
print("<html>\n<head>\n");
printf("<script src=\"%s/treeview.js\" type=\"text/javascript\"></script>\n",
			TREEVIEW_LIB_PATH);
printf('<link href="%s/treeview.css" rel="stylesheet" type="text/css" media="screen"/>'."\n",
			TREEVIEW_LIB_PATH);
print('<link href="screen.css" rel="stylesheet" type="text/css" media="screen"/>');
print("</head>\n");
?>
<body>
<h1>Demonstration</h1>
<p>The tree below is an example of what you can do with <strong>PHP DBTreeView</strong>.</p>
<p>The content of this treeview is data hardcoded in the php script (not from a database).</p>
<p>This tree download nodes on demand. When you clic on a '+' to see a branch, a request is sent to the server, without page refresh.</p>
<?php
$rootAttributes = array("code"=>"0");
$treeID = "treev1";
$tv = DBTreeView::createTreeView(
		$rootAttributes,
		TREEVIEW_LIB_PATH, 
		$treeID);
$tv->printTreeViewScript();
?>
</body>
</html>
