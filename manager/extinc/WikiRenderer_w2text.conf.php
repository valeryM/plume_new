<?php
/**
* Éléments de configuration pour WikiRenderer
*/


require_once dirname(__FILE__).'/WikiRenderer.conf.php';

class WikiRenderer_w2text extends WikiRendererConfig {
  /**
 	* @var array	liste des tags inline
   */
	var $inlinetags= array(
      'strong' =>array('__','__',      null,'wikibuild_emphase_w2t'),
      'em'     =>array('\'\'','\'\'',  null,'wikibuild_emphase_w2t'),
      'code'   =>array('@@','@@',      null,'wikibuild_code_w2t'),
      'q'      =>array('^^','^^',      array('lang','cite'),'wikibuild_q_w2t'),
      'cite'   =>array('{{','}}',      array('title'),'wikibuild_cite_w2t'),
      'acronym'=>array('??','??',      array('title'),'wikibuild_acronym_w2t'),
      'link'   =>array('[',']',        array('href','lang','title'),'wikibuild_link_w2t'),
		'image'  =>array('((','))', 		array('src','alt','align','longdesc'),'wikibuild_image_w2t'),
      'anchor' =>array('~~','~~',      array('id'),'wikibuild_anchor_w2t')
   );

   /**
   * liste des balises de type bloc autorisées.
   * Attention, ordre important (p en dernier, car c'est le bloc par defaut..)
   */
   var $bloctags = array('title_w2t'=>true, 'list_w2t'=>true,
   'pre_w2t'=>true, 'hr_w2t'=>true, 'blockquote_w2t'=>true,'definition_w2t'=>true,
   'table_w2t'=>true, 'p_w2t'=>true);



   var $simpletags = array('%%%'=>"\n");

   /**
    * @var	integer	niveau minimum pour les balises titres
    */
   var $minHeaderLevel=3;

   /**
    * indique le sens dans lequel il faut interpreter le nombre de signe de titre
    * true -> ! = titre , !! = sous titre, !!! = sous-sous-titre
    * false-> !!! = titre , !! = sous titre, ! = sous-sous-titre
    */
   var $headerOrder=false;

}


function wikibuild_emphase_w2t($contents, $attr){
   return $contents[0];
}

function wikibuild_code_w2t($contents, $attr){
   return '['.$contents[0].']';
}


function wikibuild_q_w2t($contents, $attr){
   if(count($contents) > 2)
      return '"'.$contents[0].'" ('.$contents[2].')';
   else
      return '"'.$contents[0].'"';
}

function wikibuild_cite_w2t($contents, $attr){
   if(count($contents) > 1)
      return '"'.$contents[0].'" ('.$contents[1].')';
   else
      return '"'.$contents[0].'"';

}

function wikibuild_acronym_w2t($contents, $attr){
   if(count($contents) > 1)
      return $contents[0].' ('.$contents[1].')';
   else
      return $contents[0];
}


function wikibuild_link_w2t($contents, $attr){
   $cnt=count($contents);
   $result='';

   if($cnt >1){
      if($cnt> count($attr))
         $cnt=count($attr)+1;
      if(strpos($contents[1],'javascript:')!==false) // for security reason
         $contents[1]='#';
      $result=$contents[0].' ('.$contents[1].')';
   }else{
      if(strpos($contents[0],'javascript:')!==false) // for security reason
         $contents[0]='#';
      $result=$contents[0];

   }
   return $result;

}

function wikibuild_anchor_w2t($contents, $attr){
   return '';
}

function wikibuild_image_w2t($contents, $attr){
   return '';
}



// ===================================== déclaration des différents bloc wiki
// on declare des blocs dérivant des blocs initiaux
// comme on n'autorise pas le texte preformaté (debutant par des espaces)
// on autorise des espaces en début de ligne pour les blocs.

/**
 * traite les signes de types liste
 */
class WRB_list_w2t extends WikiRendererBloc {

	var $type='list';
   var $regexp="/^([\*#-]+)(.*)/";

	function getRenderedLine(){
      return $this->_detectMatch[1].$this->_renderInlineTag($this->_detectMatch[2]);
	}
}


/**
 * traite les signes de types table
 */
class WRB_table_w2t extends WikiRendererBloc {
	var $type='table';
	var $regexp="/^\| ?(.*)/";
	var $_openTag="--------------------------------------------";
	var $_closeTag="--------------------------------------------\n";

	var $_colcount=0;

	function open(){
		$this->_colcount=0;
		return $this->_openTag;
	}


	function getRenderedLine(){

		$result=explode(' | ',trim($this->_detectMatch[1]));
		$str='';
      $t='';

		if((count($result) != $this->_colcount) && ($this->_colcount!=0))
			$t="--------------------------------------------\n";
		$this->_colcount=count($result);

		for($i=0; $i < $this->_colcount; $i++){
			$str.=$this->_renderInlineTag($result[$i])."\t| ";
		}
		$str=$t."| ".$str;

		return $str;
	}

}

/**
 * traite les signes de types hr
 */
class WRB_hr_w2t extends WikiRendererBloc {

   var $type='hr';
	var $regexp='/^={4,} *$/';
	var $_closeNow=true;

	function getRenderedLine(){
		return "=======================================================\n";
	}

}

/**
 * traite les signes de types titre
 */
class WRB_title_w2t extends WikiRendererBloc {
	var $type='title';
	var $regexp="/^(\!{1,3})(.*)/";
	var $_closeNow=true;
	var $_minlevel=1;
   var $_order=false;

	function WRB_title_w2t(&$wr){
      $this->_minlevel = $wr->config->minHeaderLevel;
      $this->_order = $wr->config->headerOrder;
		parent::WikiRendererBloc($wr);
	}

	function getRenderedLine(){
      if($this->_order){
		   $repeat= 4- strlen($this->_detectMatch[1]);
         if($repeat <1) $repeat=1;
      }else
         $repeat= strlen($this->_detectMatch[1]);
		return str_repeat("\n",$repeat)."\t".$this->_renderInlineTag($this->_detectMatch[2])."";
	}
}

/**
 * traite les signes de types pre (pour afficher du code..)
 */
class WRB_pre_w2t extends WikiRendererBloc {

   var $type='pre';
	var $regexp="/^ (.*)/";
	var $_openTag='';
	var $_closeTag='';

   function getRenderedLine(){
		return ' '.$this->_renderInlineTag($this->_detectMatch[1]);
	}

}

/**
 * traite les signes de type paragraphe
 */
class WRB_p_w2t extends WikiRendererBloc {
	var $type='p';
	var $regexp="/(.*)/";
}



/**
 * traite les signes de type blockquote
 */
class WRB_blockquote_w2t extends WikiRendererBloc {
	var $type='bq';
	var $regexp="/^(\>+)(.*)/";


	function getRenderedLine(){
      return $this->_detectMatch[1].$this->_renderInlineTag($this->_detectMatch[2]);
	}
}

/**
 * traite les signes de type blockquote
 */
class WRB_definition_w2t extends WikiRendererBloc {

	var $type='dfn';
   var $regexp="/^;(.*) : (.*)/i";

	function getRenderedLine(){
		$dt=$this->_renderInlineTag($this->_detectMatch[1]);
		$dd=$this->_renderInlineTag($this->_detectMatch[2]);
      return "$dt :\n\t$dd";
	}
}
?>
