<?php

/**
 * DBTreeView.
 * @package    DBTreeView
 * @author     Rodolphe Cardon de Lichtbuer <rodolphe@wol.be>
 * @copyright  2007 Rodolphe Cardon de Lichtbuer
 * @license    http://www.opensource.org/licenses/lgpl-license.php LGPL
 */

//tip for boolean : false == NULL but false !== NULL ( !(false===NULL))


/**
 * Treeview default configuration
 */
require_once("dbtreeview_config.php");


/**
 * Core DBTreeView class.
 * The static functions of this classes are used as factory for
 * TreeNode, ChildrenResponse and DBTreeView itself.
 * @package    DBTreeView
*/

class DBTreeView{
		
	private $id;

	//associative array
	private $rootAttributes;
	private $rootHTMLText;
	private $rootURL;
	private $rootURLTargetFrame;
	private $rootIcon;
	private $addIcon;
	private $substractIcon;
	private $emptyIcon;
	private $waitIcon;
	private $defaultOpenIcon;
	private $defaultClosedIcon;
	private $pathToLibrary;
	private $loadingMessage;
	
	/**
	 * The PHP script URL that receives the XML request
	 *
	 * @var string
	 */
	private $script;

	/**
	 * Creates a new treeview.
	 * @param string $id the id attribute of the treeview element.
	 * @param string $pathToLibrary the path to the dbtreeview directory
	 * @param array $rootAttributes the attributes of the root node.
	 * @return DBTreeView the new treeview.
	 */
	private function __construct(array $rootAttributes, $pathToLibrary, $id){
		$this->id = $id;
		$this->rootAttributes = $rootAttributes;
		$this->pathToLibrary = $pathToLibrary;
		$this->addIcon = $pathToLibrary . DEFAULT_ADD_ICON;
		$this->substractIcon = $pathToLibrary . DEFAULT_SUBSTRACT_ICON;
		$this->emptyIcon = $pathToLibrary . DEFAULT_EMPTY_ICON;
		$this->waitIcon = $pathToLibrary . DEFAULT_WAIT_ICON;
		$this->defaultOpenIcon = $pathToLibrary . DEFAULT_OPEN_ICON;
		$this->defaultClosedIcon = $pathToLibrary . DEFAULT_CLOSED_ICON;
		
	}
	
	/**
	 * Set the root HTML text.
	 * @param string $text the HTML text.
	 */
	public function setRootHTMLText($text){
		$this->rootHTMLText = $text;
	}
	/**
	 * Set the root icon URL.
	 * @param string $URL the URL.
	 */
	public function setRootIcon($URL){
		$this->rootIcon = $URL;
	}	
	/**
	 * Set the root URL.
	 * @param string $URL the URL.
	 * @param string $URLTargetFrame the optional target frame name.
	 */
	public function setRootURL($URL, $URLTargetFrame = NULL){
		$this->rootURL = $URL;
		$this->rootURLTargetFrame = $URLTargetFrame;
	}
	
	/**
	 * Set the URL of the 'add' icon.
	 * @param $URL the URL.
	 */
	public function setAddIcon($URL){
		$this->addIcon = $URL;
	}
	/**
	 * Set the URL of the 'substract' icon.
	 * @param $URL the URL.
	 */
	public function setSubstractIcon($URL){
		$this->substractIcon = $URL;
	}
	/**
	 * Set the URL of the 'empty' icon.
	 * @param $URL the URL.
	 */
	public function setEmptyIcon($URL){
		$this->emptyIcon = $URL;
	}
	/**
	 * Set the URL of the default 'open' icon.
	 * @param $URL the URL.
	 */
	public function setDefaultOpenIcon($URL){
		$this->defaultOpenIcon = $URL;
	}
	/**
	 * Set the URL of the default 'closed' icon.
	 * @param $URL the URL.
	 */
	public function setDefaultClosedIcon($URL){
		$this->defaultClosedIcon = $URL;
	}
	/**
	 * Set the URL of the 'wait' icon.
	 * @param $URL the URL.
	 */
	public function setWaitIcon($URL){
		$this->waitIcon = $URL;
	}
	
	/**
	 *  Set the URL of the PHP script that receives the XML requests.
	 *  By default this value is NULL (in this case, the script self is called).
	 */
	public function setScript($URL){
		$this->script = $URL;
	}
	
	/**
	 * Set the loading message.
	 * @param string $HTMLtext the loading message (HTML text).
	 */
	public function setLoadingMessage($HTMLText){
		$this->loadingMessage = $HTMLText;
	}	
	
	
	/**
	 * Creates a new treeview.
	 * @param string $id the id attribute of the treeview element.
	 * @param string $pathToLibrary the path to the dbtreeview directory without '/' at the end
	 * @param array $rootAttributes an associative array with the attributes of the root node.
	 * @return DBTreeView the new treeview.
	 */
	public static function createTreeView(array $rootAttributes, $pathToLibrary, $id){
		return new self($rootAttributes, $pathToLibrary, $id);
	}	

	/**
	 * Creates a response with the given children.
	 *
	 * @param array $nodes array of TreeNode objects.
	 * @return ChildrenResponse the response object.
	 */
	public static function createChildrenResponse(array $nodes){
		return XMLChildrenResponse::createInstance($nodes);
	}
	
	/**
	 * Escape a string to be printed in a JavaScript string.
	 * BUG : THIS FUNCTION IS INCOMPLETE : should transform \n, \t, ...
	 * @param string $string the string to be escaped
	 * @return string the escaped string.
	 */
	private static function escapeJSString($string){
		return addslashes($string);
	}
	/**
	 * Print the treeview <script> element in the current document.
	 */
	public function printTreeViewScript(){
		
		//start
		print("<script type=\"text/javascript\">\n");
		print("var attrs = new Array();\n");
		foreach($this->rootAttributes as $key => $value){
			print(sprintf("attrs[\"%s\"]=\"%s\";\n",
			   DBTreeView::escapeJSString($key), 
			   DBTreeView::escapeJSString($value)));
		}
		print("var params = new Object();\n");
		print("params.attributes = attrs;\n");
		if($this->id != NULL){
			print(sprintf("params.id = \"%s\";\n", 
			   DBTreeView::escapeJSString($this->id)));
		}
		if($this->rootHTMLText != NULL){
			print("var root = new Object();\n");
			print(sprintf("root.text = \"%s\";\n", 
			   DBTreeView::escapeJSString($this->rootHTMLText)));
			if($this->rootURL != NULL){
			   print(sprintf("root.URL = \"%s\";\n", 
			      DBTreeView::escapeJSString($this->rootURL)));
			   if($this->rootURLTargetFrame != NULL){
			   	  print(sprintf("root.URLTargetFrame = \"%s\";\n", 
			   	     DBTreeView::escapeJSString($this->rootURLTargetFrame)));
			   }
			}
			if($this->rootIcon != NULL){
				print(sprintf("root.icon = \"%s\";\n", 
				   DBTreeView::escapeJSString($this->rootIcon)));
			}
			print("params.root = root\n");
		}
		if($this->script != NULL){
			print(sprintf("params.script = %s;\n", 
			   DBTreeView::escapeJSString($this->script)));	
		}
		if($this->addIcon != NULL){
			print(sprintf("params.plusImgSrc = \"%s\";\n", 
			   DBTreeView::escapeJSString($this->addIcon)));
		}
		if($this->substractIcon != NULL){
			print(sprintf("params.minusImgSrc = \"%s\";\n", 
			   DBTreeView::escapeJSString($this->substractIcon)));
		}
		if($this->emptyIcon != NULL){
			print(sprintf("params.emptyImgSrc = \"%s\";\n", 
			   DBTreeView::escapeJSString($this->emptyIcon)));
		}
		if($this->waitIcon != NULL){
			print(sprintf("params.waitImgSrc = \"%s\";\n", 
			   DBTreeView::escapeJSString($this->waitIcon)));
		}
		if($this->defaultOpenIcon != NULL){
			print(sprintf("params.defaultImageOpen = \"%s\";\n", 
			   DBTreeView::escapeJSString($this->defaultOpenIcon)));
		}
		if($this->defaultClosedIcon != NULL){
			print(sprintf("params.defaultImageClosed = \"%s\";\n", 
			   DBTreeView::escapeJSString($this->defaultClosedIcon)));
		}
		if($this->loadingMessage != NULL){
			print(sprintf("params.loadingMessage = \"%s\";\n", 
			   DBTreeView::escapeJSString($this->loadingMessage)));
		}
		
?>

try{new DBTreeView.TreeView(params);}
catch(err){
  txt="There was an error on this page.\n\n"
  txt+="Error description: " + err + "\n\n"
  txt+="Click OK to continue.\n\n"
  alert(txt)
}
<?php

	print("</script>\n");
	}
		
	/**
	 * Creates a new node.
	 * @param string $text the HTML text of the node.
	 * @param array $attributes associative array indetifying the node. array('key'=>'value').
	 * @param string $URL the optional target URL.
	 * @param string $URLTargetFrame the optional target frame. 
	 * @return TreeNode the new node.
	 */
	public static function createTreeNode($text, $attributes, $URL=NULL, $URLTargetFrame=NULL){
		return XMLTreeNode::createInstance($text, $attributes, $URL, $URLTargetFrame);
	}
	
   /**
	 * Creates a response with the children of a parent node.
	 * 
	 * @param RequestHandler $hanlder the handler of the request.
	 * @param boolean $onlyRequest if true, throws an error if not a XML query. If false (default), returns silently if not a XML query.
	 * @return ChildrenResponse
	 */
	public static function processRequest(RequestHandler $hanlder, $onlyRequest = false){
			$stopOnError = true;
			//check header
			if(!$onlyRequest){
				if(function_exists("apache_request_headers")){
					$headers = apache_request_headers();
					$contentType = NULL;
					if(isset($headers['Content-Type'])){
						//example : Content-Type : text/xml;charset=UTF-8
						$contentTypeArray = explode(";",$headers['Content-Type'],2);
						$contentType = $contentTypeArray[0];
					}
					if($contentType != "application/xml" && $contentType != "text/xml"){
						return;
					}
				}
				else{
					$stopOnError = false;
				}
			}

			try{
			//error_reporting(0); //unactive reporting
			$doc = new DOMDocument();
			$data = file_get_contents("php://input");
			if($data==NULL || strlen($data)<1){
				throw new Exception("empty data input");
			}
			$doc->loadXML($data);
			//$doc->load("php://input"); //RAW POST DATA
/*
 * RAW POST DATA
 * 
 * For information, see 
 * http://www.php.net/manual/en/reserved.variables.php#reserved.variables.server
 * http://www.php.net/manual/en/wrappers.php.php 
 */
				$elem = $doc->getElementsByTagName(XMLChildrenRequest::TAG)->item(0);
				if($elem==NULL){
					throw new XMLException("Invalid XMLChildrenRequest");
				}
			}catch(Exception $e2){
				if($stopOnError){
					throw $e2;
				}else{
					//not apache, maybe not process
					return;
				}
			}
			$request = XMLChildrenRequest::getInstance($elem);
			$response = $hanlder->handleChildrenRequest($request);
			$responseDoc = new DOMDocument();
			$responseDoc->appendChild($response->toElement($responseDoc));

			header("Content-Type: application/xml");
			//$responseDoc->save("php://output");
			$responseXML = $responseDoc->saveXML();
			echo($responseXML);
			exit(0);
	}	
}


/**
 * ChildrenRequest Interface.
 * The children request contains the attributes of the parent node.
 * In the future, this interface could receive additional functions.
 * @package    DBTreeView
*/
interface ChildrenRequest{
	
	/**
	 * Returns the attributes of the parent node.
	 * 
	 * @return array an associative array {key => value}.
	 */
	public function getAttributes();
	
}

/**
 * ChildrenResponse Interface.
 * The children response is used by the processor to answer to the client requests.
 * In the future, this interface could receive additional functions.
 * @package    DBTreeView
*/
interface ChildrenResponse{
	
	
}

/**
 * Request Handler interface. 
 * @package   DBTreeView
 */
interface RequestHandler{
	
	/** 
	 * Returns a response for a children request.
     *
	 * @param ChildrenRequest the request.
	 * @return ChildrenResponse the reponse.
     */
	public function handleChildrenRequest(ChildrenRequest $request); 
}

 /**
  * Tree node.
  * 
  * @package    DBTreeView
  */
interface TreeNode{
/**
	 * Set the URL of the 'open' icon (expanded node)
	 * @param string $imageURL
	 */
	public function setOpenIcon($imageURL);
	/**
	 * Returns the URL of the 'open' icon (expanded node)
	 * @return string the image URL 
	 */
	public function getOpenIcon();
	/**
	 * Set the URL of the 'closed' icon (collapsed node)
	 * @param string $imageURL 
	 */
	public function setClosedIcon($imageURL);
	/**
	 * Returns the URL of the 'closed' icon (collapsed node)
	 * @return string the image URL 
	 */
	public function getClosedIcon();
	
	/**
	 * Get the HTML text of the node
	 * @return string
	 */
	public function getHTMLText();
	
	/**
	 * Returns true if the node should be open by default.
	 * @return boolean
	 */
	public function getIsOpenByDefault();

	/**
	 * Set if the node should be open by default.
	 * @param boolean $bool
	 */
	public function setIsOpenByDefault($bool);

	/**
	 * Set if the node has children. If undefined, consider the 
	 * node has got children.
	 * @param boolean $bool
	 */
	public function setHasChildren($bool);
	
	/**
	 * Returns false if the node has no child. Otherwise, it can
	 * have children (maybe not).
	 * @return boolean $bool
	 */
	public function getHasChildren();
	
	/** 
	 * Set the URL and target frame.
	 *
	 * @param string $URL the URL
	 * @param string $URLTargetFrame the target frame
	 */
	public function setURL($URL, $URLTargetFrame=NULL);
	
	/**
	 * Returns the URL, or null if undefined.
	 * @return string the URL, or null if undefined.
	 */
	public function getURL();
	
	/**
	 * Returns the target frame, or null if undefined.
	 * @return string the target frame, or null if undefined.
	 * 
	 */
	public function getURLTargetFrame();
	
	/**
	 * Set the children of this node.
	 * @param array $children an array of XMLTreeNode
	 */
	public function setChildren(array $children);


	/**
	 * Returns the children of this node
	 * @return array an array of XMLTreeNode.
	 */
	public function getChildren();

	/**
	 * Returns the attributes of this node.
	 * @return array an associative array.
	 */
	 public function getAttributes();

}

/**
 * XML definition of <Attribute>
 * @package    DBTreeView_XML
 */
class XMLAttribute{
	const TAG = "Attribute";
	const NAME_ATTR = "name";
	const VALUE_ATTR = "value";
	
	private $name;
	private $value;	
	/**
	 * Creates a new instance from an existing element.
	 * 
	 * @param DOMElement $elem the <Attribute> element.
	 * @return XMLAttribute the new instance.
	 */
	public static function getInstance(DOMElement $elem){
		if($elem->tagName != self::TAG){
			throw new XMLException("Invalid Attribute element.");
		}
		$name = $elem->getAttribute(self::NAME_ATTR);
		$value = $elem->getAttribute(self::VALUE_ATTR);
		return new self($name, $value);
	}
	
	/**
	 * Creates a new instance.
	 *
	 * @param string $name the attribute name.
	 * @param string $value the attribute value.
	 */
	public function __construct($name, $value){
		$this->name = $name;
		$this->value = $value;
	}
	
	
	/**
	 * Generates the DOM element
	 *
	 * @param DOMDocument $doc a valid document used to generate the element.
	 * @return DOMElement the <Attribute> element
	 */
	public  function  toElement(DOMDocument $doc){
		$elem = $doc->createElement(self::TAG);
		$elem->setAttribute(self::NAME_ATTR, $this->name);
		$elem->setAttribute(self::VALUE_ATTR, $this->value);
		return $elem;
	}
	
	/**
	 * Returns the attribute name.
	 * 
	 * @return string the name.
	 */
	public function getName(){
		return $this->name;
	}
		/**
	 * Returns the attribute value.
	 * 
	 * @return string the value.
	 */
	public function getValue(){
		return $this->value;
	}


	/**
	 * Convert an array of XMLAttribute to an associative array
	 */
	public static function XMLAttrs2Assoc(array $xmlAttrs){
		$attrs = array();
		foreach($xmlAttrs as $xmlAttr){
			$attrs[$xmlAttr->getName()]= $xmlAttr->getValue();
		}
		return $attrs;
	}
	/**
	 * Convert an associative array to an array of XMLAttribute
	 */
	public static function Assoc2XMLAttrs(array $assocArray){
		$xmlAttrs = array();
		foreach($assocArray as $key => $value){
			$xmlAttrs[] = new XMLAttribute($key, $value);
		}
		return $xmlAttrs;
	}
}

/**
 * XML Children request.
 * 
 * <p>Request message send by the client. For example,
 * when the user opens a branch, the following message could be :</p>
 * <code>
 * <childrenRequest>
 *   <attribute name="id" value="02">
 *   <attribute name="filter" value="groupB">
 * </childrenRequest>
 * </code>
 * @package    DBTreeView_XML
 */
class XMLChildrenRequest implements ChildrenRequest{

	const TAG = "ChildrenRequest";
	const VERSION_ATTR = "version";

	private $version;

	//associative array
	private $attributes;  

	/**
	 * Creates a new object.
	 * 
	 * @param string $version the protocol version
	 * @param string $attributes the attributes of the parent node (associative array)
	 * @return XMLChildrenRequest
	 */
	private function __construct($version, $attributes){
		$this->version = $version;
		$this->attributes =$attributes;
	}
	
	/**
	 * Create a new instance with parent node attributes
	 * 
	 * @param array $attributes the attributes of the parent node (associative array)
	 * @return XMLChildrenRequest
	 */
	public static function createInstance(array $attributes){
		$version = 1;
		$attributes = $attributes;
		return new self($version, $attributes);
	}
	
	/**
	 * Generates the <XMLChildrenRequest> element.
	 * 
	 * @param $doc a valid XML document
	 * @return DOMElement the <XMLChildrenRequest> element.
	 *  
	 */
	public function toElement(DOMDocument $doc){
		$elem = $doc->createElement(self::TAG);
		$elem->setAttribute(self::VERSION_ATTR, $this->version);
		$xmlAttrs = XMLAttribute::assoc2XMLAttrs($this->attributes);
		foreach($xmlAttrs as $xmlAttr){
			$attrElem = $xmlAttr->toElement($doc);
			$elem->appendChild($attrElem);
		}
		return $elem; 
		
	}
	
	/**
	 * Creates a new instance from an existing element.
	 * 
	 * @param $elem the <XMLChildrenRequest> element.
	 * @return XMLChildrenRequest
	 */
	public static function getInstance(DOMElement $elem){
		//check tag
		if($elem->tagName != self::TAG){
		throw new XMLException("Invalid ChildrenRequest");	
		}
		//check version
		$version = $elem->getAttribute(self::VERSION_ATTR);
		if($version != 1){
			throw new XMLException("Invalid version of <ChildrenRequest>.");
		}
				
		//retrieves attributes
		$attributes = array(); //associative array
		$childNodes = $elem->childNodes;
		for($i=0; $i<$childNodes->length; $i++){
			$node = $childNodes->item($i);
			if($node->nodeType != XML_ELEMENT_NODE){
				continue;
			}
			$attr = XMLAttribute::getInstance($node);
			$attributes[$attr->getName()] = $attr->getValue();
		}
		return new self($version, $attributes);

	}
	
	/** 
      * Returns the attributes of the parent node.
      * 
      * @return array an associative array
      */
	public function getAttributes(){
		return $this->attributes;
	}
	
	/** 
     * Returns the version.
     */
	public function getVersion(){
		return $this->version;
	}
		
}

/**
 * XML Children Response.
 * 
 * <p>Response message send by the server to the client. 
 * @package    DBTreeView_XML
 */
class XMLChildrenResponse implements ChildrenResponse{
	
	const TAG = "ChildrenResponse";
	const VERSION_ATTR = "version";
	
	private $version;
	private $nodes; // array(XMLTreeNode)
	
	/**
	 * Creates a new instance.
	 * 
	 * @param array $nodes an array of XMLTreeNode
	 * @param int $version the protocol version
	 */
	private function __construct($nodes, $version=1){
		 $this->version = $version;
		 $this->nodes = $nodes;
	}
	
	/**
	 * Creates a new instance with given nodes.
	 *
	 * @param array $nodes the children nodes (TreeNode)
	 * @return XMLChildrenResponse
	 */
	public static function createInstance($nodes){
		return new self($nodes);
	}
	
	/**
	 * Generates the XML element containing the response.
	 * 
	 * @param DOMDocument $doc a valid document used to generate the element.
	 * @return DOMElement the reponse
	 */
	public function toElement(DOMDocument $doc){
		$elem = $doc->createElement(self::TAG);
		$elem->setAttribute(self::VERSION_ATTR, $this->version);
		for($i=0;$i<count($this->nodes);$i++){
			$elem->appendChild($this->nodes[$i]->toElement($doc));
		}
		return $elem;
	}
	
	/**
	 * Returns the nodes
	 * @return array the nodes (array of XMLTreeNode)
	 */
	public function getNodes(){
		return $this->nodes;
	}
	
}

/**
 * XML Exception
 * 
 * This exception extends the Exception class, and is used
 * for XML parsing error.
 * @package    DBTreeView_XML
 * 
 */
class XMLException extends Exception 
{
    public function __construct($message, $code = 0) {
     parent::__construct($message, $code);
  }
   
}

/**
 * XML definition of a tree node.
 * @package    DBTreeView_XML
 */
class XMLTreeNode implements TreeNode{
	
	const TAG = "Node";
	const OPEN_ICON_ATTR = "openIcon";
	const CLOSED_ICON_ATTR = "closedIcon";
	const TEXT_ATTR = "text";
	const HAS_CHILDREN_ATTR = "hasChildren";
	const URL_ATTR  = "URL";
	const URL_TARGET_FRAME_ATTR = "URLTargetFrame";
	const IS_OPEN_BY_DEFAULT_ATTR = "isOpenByDefault";
	
	private $text;

	//associative array
	private $attributes;
	private $URL;
	private $URLTargetFrame;
	private $openIcon;
	private $closedIcon;
	private $hasChildren=true;
	private $children=array(); //array(XMLTreeNode), may be empty
	private $isOpenByDefault=true; //default value if missing
	
	/**
	 * Create an new instance
	 *
	 * @param $text string the node text
	 * @param $attributes array an associative array
	 */
	private function __construct($text, $attributes){
		$this->text = $text;
		$this->attributes = $attributes;
		if($attributes==NULL){
			throw new Exception("Attributes may not be NULL.");
		}
	}
	
	/**
	 * Creates a new instance from an existing DOM element.
	 * 
	 * @param DOMElement the <Node> element
	 * @return XMLTreeNode the new instance.
	 */
	public static function getInstance(DOMElement $elem){
		if($elem->tagName != TAG){
			throw new XMLException("Invalid <Node> element.");
		}
		$text = $elem->getAttribute(self::TEXT_ATTR);
		$URL = $elem->getAttribute(self::URL_ATTR);
		$URLTargetFrame = $elem->getAttribute(self::URL_TARGET_FRAME_ATTR); 
		$openIcon = $elem->getAttribute(self::OPEN_ICON_ATTR);
		$closedIcon = $elem->getAttribute(self::CLOSED_ICON_ATTR);
		$hasChildren = $elem->getAttribute(self::HAS_CHILDREN_ATTR);
		$isOpenByDefault = $elem->getAttribute(self::IS_OPEN_BY_DEFAULT_ATTR);
		
		$attributes = array();
		$children = array();
		$child = $elem->firstChild;
		while($child!=NULL){
			if($child->type == XML_ELEMENT_NODE){
				if($child->tagName == XMLAttribute::TAG){
					$xmlAttr = XMLAttribute::getInstance($child);
					$attributes[$xmlAttr->getName()] = $xmlAttr->getValue();
				}elseif($child->tagName == self::TAG){
					$children[] = self::getInstance($child); 
				}
			}
			$child=$child->nextSibling;
		}
		$treeNode = new self($text, $attributes);
		$treeNode->setURL($URL, $URLTargetFrame);
		$treeNode->setOpenIcon($openIcon);
		$treeNode->setClosedIcon($closedIcon);
		$treeNode->setHasChildren($hasChildren);
		$treeNode->setChildren($children);
		$treeNode->setIsOpenByDefault($isOpenByDefault);
		return $treeNode;
	}
	
	/**
	 * Generates the <Node> element.
	 * 
	 * @param DOMDocument $doc a valid document used to generate de element
	 * @return DOMElement the <Node> element. 
	 */
	public function toElement(DOMDocument $doc){
		$elem = $doc->createElement(self::TAG);
		$elem->setAttribute(self::TEXT_ATTR, $this->text);
		if($this->openIcon != NULL){
			$elem->setAttribute(self::OPEN_ICON_ATTR, $this->openIcon);
		}
		if($this->closedIcon != NULL){
			$elem->setAttribute(self::CLOSED_ICON_ATTR, $this->closedIcon);
		}
		if($this->URL != NULL){
			$elem->setAttribute(self::URL_ATTR, $this->URL);
		}
		if($this->URLTargetFrame != NULL){
			$elem->setAttribute(self::URL_TARGET_FRAME_ATTR, $this->URLTargetFrame);
		}
		$elem->setAttribute(self::HAS_CHILDREN_ATTR, 
					$this->hasChildren ? "true": "false");
		if($this->isOpenByDefault !== NULL){
			$elem->setAttribute(self::IS_OPEN_BY_DEFAULT_ATTR, 
					$this->isOpenByDefault ? "true":"false");
		}
		foreach($this->attributes as $key => $value){
			$xmlAttr = new XMLAttribute($key, $value);
			$elem->appendChild($xmlAttr->toElement($doc));
		}
		foreach($this->children as $child){
			$elem->appendChild($child->toElement($doc));
		}
		return $elem;
	}
	
	/**
	 * Creates a new instance with given text, attributes, and link.
	 * 
	 * @param string $text the HTML text of the node
	 * @param array $attributes an associative array.
	 * @param string $URL the target URL.
	 * @param string $URLTargetFrame the target frame.
	 * @return XMLTreeNode the new instance.
	 */ 
	public static function createInstance($text, $attributes, $URL=NULL, $URLTargetFrame=NULL){
		$treeNode = new self($text, $attributes);
		$treeNode->setURL($URL, $URLTargetFrame);
		return $treeNode;
	}
	
	/**
	 * Set the source of the 'open directory' icon
	 * @param string $imageURL 
	 */
	public function setOpenIcon($imageURL){
		$this->openIcon = $imageURL;
	}
	/**
	 * Get the source of the 'open directory' icon
	 * @return string the image URL 
	 */
	public function getOpenIcon(){
		return $this->openIcon;
	}
	/**
	 * Set the source of the 'closed directory' icon
	 * @param string $imageURL 
	 */
	public function setClosedIcon($imageURL){
		$this->closedIcon = $imageURL;
	}
	/**
	 * Get the source of the 'closed directory' icon
	 * @return string the image URL 
	 */
	public function getClosedIcon(){
		return $this->closedIcon;
	}
	
	/**
	 * Get the HTML text of the node
	 * @return string
	 */
	public function getHTMLText(){
		return $this->text;
	}
	
	/**
	 * Returns true if the node should be open by default.
	 * @return boolean
	 */
	public function getIsOpenByDefault(){
		return $this->isOpenByDefault;
	}
	/**
	 * Set if the node should be open by default.
	 * @param boolean $bool
	 */
	public function setIsOpenByDefault($bool){
		$this->isOpenByDefault = $bool;
	}
	
	/**
	 * Set if the node has children. If undefined, consider the 
	 * node has got children.
	 * @param boolean $bool
	 */
	public function setHasChildren($bool){
		$this->hasChildren = $bool;
	}
	
	/**
	 * Returns false if the node has no child. Otherwise, it can
	 * have children (maybe not).
	 * @param boolean $bool
	 */
	public function getHasChildren(){
		return $this->hasChildren;
	}
	
	/** 
	 * Set the URL and target frame.
	 *
	 * @param string $URL the URL
	 * @param string $URLTargetFrame the target frame
	 */
	public function setURL($URL, $URLTargetFrame=NULL){
		$this->URL = $URL;
		$this->URLTargetFrame = $URLTargetFrame;
	}
	
	/**
	 * Returns the URL, or null if undefined.
	 * @return string the URL, or null if undefined.
	 */
	public function getURL(){
		return $this->URL;
	}
	
	/**
	 * Returns the target frame, or null if undefined.
	 * @return string the target frame, or null if undefined.
	 * 
	 */
	public function getURLTargetFrame(){
		return $this->URLTargetFrame;
	}
	
	/**
	 * Returns the attributes associated to this node.
	 * @return array an associative array.
	 */
	public function getAttributes(){ 
		return $this->attributes;
	}
	
	/**
	 * Set the children of this node.
	 * @param array $children an array of XMLTreeNode
	 */
	public function setChildren(array $children){
		$this->children = $children;
	}
	
	/**
	 * Returns the children of this node
	 * @return array an array of XMLTreeNode.
	 */
	public function getChildren(){
		return $this->children;
	}
}


?>