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

require_once dirname(__FILE__).'/class.l10n.php';

define('SEARCH_LOG', dirname(dirname(dirname(__FILE__))). '/logs/log_search.log');

class Search extends CError
{

    var $websiteId = ''; //Website id
    var $con = null; //DB connection
	var $log = array(); // info for log file
	
    /**
    Constructor

    @param object DB reference
    @param string Website id
    */
    function Search(&$db, $websiteId='')
    {
        $this->con = $db;
        $this->websiteId = $websiteId;
        return true;
        $text = date::stamp() .' - Init Search class'.chr(10);
		//file_put(SEARCH_LOG, $text);
    }

    /**
    Index a text string.

    @param string The text string to index
    @param string The identifier of the string (resource id for example)
    @param string The type of string
    @return bool Succes or not, if error an error message is set
    */
    function index($string, $id, $type='html')
    {
    	/*
        $text = date::stamp() .' - Search::index (string initial)'.chr(10);
        $text .= $string .chr(10);
		file_put(SEARCH_LOG, $text);
		*/
    	/*
		if ('html' == $type) {
            //To have an accurate number of occurences
            $string = $this->balance_string($string);
        }
        */
        $string = $this->clean_string($string);
        /*
        $text = date::stamp() .' - Search::index (string)'.chr(10);
        $text .= $string .chr(10);
		file_put(SEARCH_LOG, $text);
        */
        $words  = $this->explode_in_words($string, true);
        /*
        $text = date::stamp() .' - Search::index (words)'.chr(10);
        $text .= print_r($words,true).chr(10);
		file_put(SEARCH_LOG, $text);
		*/
        return $this->save_words($id, $words);
    }

    /**
     * Balance HTML string.
     *
     * Balance an HTML string by identification of words in headlines. Return a
     * string with more occurences of the important words.
     *
     * @param string String to balance
     * @return string Balanced string
     */
    function balance_string($string)
    {
        return $string;
    }

    /**
     * Explode in words.
     *
     * Explode in words a strings and give the number of occurence 
     * of each word.
     *
     * @param string String to be exploded
     * @param bool Remove the small words and numerical only (true)
     * @return array Associative array, key: word, value: occurence
     */
    public static function explode_in_words($string, $remove=true)
    {
        return Search::tokenize($string,false,config::f('search_min_size'));
    }

    /**
     * Save the indexed resource.
     *
     * @param string Resource id
     * @param array Words and occurences
     * @return bool Success
     */
    public function save_words($id, $words)
    {
        //save the words in the db
        return $this->db_save_words($id, $words);
    }

    /**
     * Add the words to the word table. The words are supposed to be clean.
     * Update the occurence table
     *
     * @param string Resource id
     * @param array words
     * @return bool Success
     */
    public function db_save_words($id, $words)
    {
        include_once dirname(__FILE__).'/lib.utils.php';

        $reqExists = 'SELECT * FROM '.$this->con->pfx.'searchwords 
                      WHERE word=\'%s\'';
        $reqIns    = 'INSERT INTO '.$this->con->pfx.'searchwords SET 
                      word=\'%s\'';
        $reqInsOcc = 'INSERT INTO '.$this->con->pfx.'searchocc SET
                      word_id=\'%s\', 
                      resource_id=\''.$this->con->esc($id).'\',
                      website_id=\''.$this->con->esc($this->websiteId).'\',
                      occ=\'%s\', pondocc=\'%s\'';
        $reqExistsOcc = 'SELECT * FROM '.$this->con->pfx.'searchocc
                      WHERE 
                      word_id=\'%s\' AND 
                      resource_id=\''.$this->con->esc($id).'\' AND
                      website_id=\''.$this->con->esc($this->websiteId).'\'';
        $reqUpdOcc = 'UPDATE '.$this->con->pfx.'searchocc SET
                      occ=occ+\'%s\', pondocc=pondocc+\'%s\' WHERE 
                      word_id=\'%s\' AND 
                      resource_id=\''.$this->con->esc($id).'\' AND
                      website_id=\''.$this->con->esc($this->websiteId).'\'';


        // Remove the previous indexation of the resource
        if (false === $this->remove_from_index($id)) {
            return false;
        }           
        // Add the new words (in the word table and in the occurence table)
        // Number of indexed words in the string
        $total = 0.0;
        foreach ($words as $word => $occ) {
            $total += (float) $occ;
        }
        foreach ($words as $word => $occ) {
            $req = sprintf($reqExists, $this->con->escapeStr($word));
            if (($rs = $this->con->select($req)) === false) {
                $this->setError('MySQL: '.$this->con->error(), 500);
                return false;
            }
            if ($rs->nbRow() == 0) {
                // need to add the word to the word table
                $req = sprintf($reqIns, $this->con->escapeStr($word));
                if (!$this->con->execute($req)) {
                    $this->setError('MySQL: '.$this->con->error(), 500);
                    return false;
                }
                if (false == ($wid = $this->con->getLastID())) {
                    $this->setError('MySQL: '.$this->con->error(), 500);
                    return false;
                }
            } else {
                $wid = $rs->f('word_id');
            }
            // Find if occurence already exists because of stupid
            // MySQL merging stuff in some particular colations.
            $req = sprintf($reqExistsOcc, $wid);
            if (($rs = $this->con->select($req)) === false) {
                $this->setError('MySQL: '.$this->con->error(), 500);
                return false;
            }
            if ($rs->nbRow() == 0) {
                // Add the word to the occurence table
                $req = sprintf($reqInsOcc, $wid, $this->con->escapeStr($occ), 
                               (float) $occ/$total*100.0);
                if (!$this->con->execute($req)) {
                    $this->setError('MySQL: '.$this->con->error(), 500);
                    return false;
                }
            } else {
                $req = sprintf($reqUpdOcc, $this->con->escapeStr($occ), 
                               (float) $occ/$total*100.0, $wid);
                if (!$this->con->execute($req)) {
                    $this->setError('MySQL: '.$this->con->error(), 500);
                    return false;
                }
            }
        }
        // Update the stats table
        $reqSel = 'SELECT * FROM '.$this->con->pfx.'search 
                   WHERE resource_id=\''.$this->con->escapeStr($id).'\'';
        $reqIns = 'INSERT INTO '.$this->con->pfx.'search SET 
                   resource_id=\''.$this->con->escapeStr($id).'\',
                   website_id=\''.$this->con->escapeStr($this->websiteId).'\', 
                   lastindex=\''.date::stamp().'\',
                   nbindex=\'1\'';
        $reqUpd = 'UPDATE '.$this->con->pfx.'search SET 
                   lastindex=\''.date::stamp().'\', nbindex=nbindex+1
                   WHERE resource_id=\''.$this->con->escapeStr($id).'\'';

        if (($rs = $this->con->select($reqSel)) === false) {
            $this->setError('MySQL: '.$this->con->error(), 500);
            return false;
        }
        if ($rs->nbRow() == 0) {
            $req = $reqIns;
        } else {
            $req = $reqUpd;
        }
        if (!$this->con->execute($req)) {
            $this->setError('MySQL: '.$this->con->error(), 500);
            return false;
        }
        return true;
    }
    
    /**
     * Remove a resource from the index. 
     *    
     * @param string Resource id
     * @return bool Succes
     */
    public function remove_from_index($id)
    {
        // Remove the previous indexation of the resource
        $req = 'DELETE FROM '.$this->con->pfx.'searchocc WHERE resource_id=\''
            .$this->con->escapeStr($id).'\'';
        if (!$this->con->execute($req)) {
            $this->setError('MySQL: '.$this->con->error(), 500);
            return false;
        }   
        return true;
    }
    
    /**
     * Clean index. 
     *
     * Clean the index of the words that are not used anymore.
     * This a SQL intensive function. 
     *    
     * @return int Number of word removed or false
     */
    public function clean_index()
    {
        //Get all the word ids.
        $i = 0;
        $req = 'SELECT word_id FROM '.$this->con->pfx.'searchwords';
        if (($rs = $this->con->select($req)) === false) {
            $this->setError('MySQL: '.$this->con->error(), 500);
            return false;
        }
        while (!$rs->EOF()) {
            $req =  'SELECT resource_id FROM '.$this->con->pfx.'searchocc ';
            $req .= 'WHERE word_id=\''.$this->con->escapeStr($rs->f('word_id'))
                .'\'';
            if (($rsocc = $this->con->select($req)) === false) {
                $this->setError('MySQL: '.$this->con->error(), 500);
                return false;
            }
            if ($rsocc->nbRow() == 0) {
                // the word is not used, delete it
                $req =  'DELETE FROM '.$this->con->pfx.'searchwords ';
                $req .= 'WHERE word_id=\''
                    .$this->con->escapeStr($rs->f('word_id')).'\'';
                if (!$this->con->execute($req)) {
                    $this->setError('MySQL: '.$this->con->error(), 500);
                    return false;
                }
                $i++;
            }
            $rs->moveNext();
        }
        return $i;
    }
    


    /**
     * Return the basic SQL query with place holders to generate 
     * the complete search query.
     *    
     * @param string Query string sent by the user
     * @param boolean to spécify if all words must be founded
     * @return string SQL basic query string
     */
    function create_search_query_string($query, $isLuckySearch = true, $type ='')
    {

        $query = $this->clean_string($query);
        $words = $this->explode_in_words($query, false);
        $kw = array();
        reset($words);
        $nbwords = 0;
        foreach ($words as $word => $occ) {
            $kw[] = $this->con->pfx.'searchwords.word LIKE \''.$this->con->escapeStr($word).'\'';
            $nbwords++;
        }
        $orstring = implode(' OR ', $kw);
        if (strlen($orstring) == 0) $orstring = '1=0';
        $sql  = 'SELECT '.$this->con->pfx.'resources.*, '
            .$this->con->pfx.'categories.*, COUNT(*) AS total, '."\n";
            
        if ($type == 'events' || $type == 'all')  {
        	$sql .= $this->con->pfx.'events.*, ';
        } 
        if ($type == 'news' || $type == 'all')  {
        	$sql .= $this->con->pfx.'news.*, ';
        }
        $sql .= 'SUM('.$this->con->pfx.'searchocc.occ) AS score '."\n";
        $sql .= 'FROM '.$this->con->pfx.'searchocc'."\n";
        $sql .= 'LEFT JOIN '.$this->con->pfx.'searchwords ON '
            .$this->con->pfx.'searchwords.word_id='.$this->con->pfx
            .'searchocc.word_id '."\n";
        $sql .= 'LEFT JOIN '.$this->con->pfx.'resources ON '
            .$this->con->pfx.'resources.resource_id='.$this->con->pfx
            .'searchocc.resource_id '."\n";
        $sql .= 'LEFT JOIN '.$this->con->pfx.'categoryasso ON '
            .$this->con->pfx.'categoryasso.identifier='.$this->con->pfx
            .'resources.identifier '."\n";
        $sql .= 'LEFT JOIN '.$this->con->pfx.'categories ON '
            .$this->con->pfx.'categoryasso.category_id='.$this->con->pfx
            .'categories.category_id '."\n";
            
        if ($type == 'news' || $type == 'all') {
            $sql .= ' LEFT JOIN '.$this->con->pfx.'news ON '.$this->con->pfx.'news.resource_id='.$this->con->pfx.'resources.resource_id ';
        } 
        if ($type == 'events' || $type == 'all')  {
            $sql .= ' LEFT JOIN '.$this->con->pfx.'events ON '.$this->con->pfx.'events.resource_id='.$this->con->pfx.'resources.resource_id ';
      	} 
            
        $sql .= 'WHERE ('.$orstring.')'."\n";
        $sql .= 'AND '.$this->con->pfx.'categoryasso.categoryasso_type=\''
            .PX_RESOURCE_CATEGORY_MAIN.'\' '."\n";
        $sql .= 'AND '.$this->con->pfx.'resources.website_id=\''
            .$this->websiteId.'\' %s '."\n";
        $sql .= 'GROUP BY '.$this->con->pfx
            .'searchocc.resource_id ';
           
        if ($isLuckySearch == false ) {
            $sql .= 'HAVING total=\''.$nbwords
            .'\' ORDER BY total DESC, score DESC';
        } else {
            $sql .= 'HAVING total>=\''.round($nbwords/3,0)
            .'\' ORDER BY total DESC, score DESC';
        }
        return $sql;
    }
    
    /**
     * Get the list of resources in a website with the last indexation time.
     *    
     * @param string Website to look for, if no set, all
     * @return mixed RecordSet or false if errors
     */
    public function get_indexed_resources_stats($website='')
    {
        $website = trim($website);
        $extra = '';
        if (!empty($website)) {
            $extra = 'WHERE '.$this->con->pfx.'resources.website_id=\''
                .$this->con->esc($website).'\''."\n";
        }
        $sql  = 'SELECT * FROM '.$this->con->pfx.'resources '."\n";
        $sql .= 'LEFT JOIN '.$this->con->pfx.'search 
           ON '.$this->con->pfx.'resources.resource_id='
           .$this->con->pfx.'search.resource_id '."\n";
        $sql .= $extra;
        $sql .= 'ORDER BY lastindex ASC';
        if (($rs = $this->con->select($sql)) === false) {
            $this->setError('MySQL: '.$this->con->error(), 500);
            return false;
        }
        return $rs;
    }


    /**
     * Given a string, cleaned from the not interesting characters,
     * returns an array with the words as index and the number of
     * times it was in the text as the value.
     *
     * @credits Tokenizer of DokuWiki to handle Thai and CJK words.
     *          http://www.splitbrain.org/projects/dokuwiki
     *
     * @param string Cleaned, lowercased and utf-8 encoded string.
     * @param bool Remove the accents (True)
     * @return array Word and number of occurences.
     */
    public static function tokenize($string, $remove_accents=True, $min_length = false)
    {
        if ($remove_accents) {
            $string = Search::removeAccents($string);
        }
        $asian1 = '[\x{0E00}-\x{0E7F}]'; // Thai
        $asian2 = '['.
                   '\x{2E80}-\x{3040}'.  // CJK -> Hangul
                   '\x{309D}-\x{30A0}'.
                   '\x{30FD}-\x{31EF}\x{3200}-\x{D7AF}'.
                   '\x{F900}-\x{FAFF}'.  // CJK Compatibility Ideographs
                   '\x{FE30}-\x{FE4F}'.  // CJK Compatibility Forms
                   ']';
        $asian3 = '['. // Hiragana/Katakana (can be two characters)
                   '\x{3042}\x{3044}\x{3046}\x{3048}'.
                   '\x{304A}-\x{3062}\x{3064}-\x{3082}'.
                   '\x{3084}\x{3086}\x{3088}-\x{308D}'.
                   '\x{308F}-\x{3094}'.
                   '\x{30A2}\x{30A4}\x{30A6}\x{30A8}'.
                   '\x{30AA}-\x{30C2}\x{30C4}-\x{30E2}'.
                   '\x{30E4}\x{30E6}\x{30E8}-\x{30ED}'.
                   '\x{30EF}-\x{30F4}\x{30F7}-\x{30FA}'.
                   ']['.
                   '\x{3041}\x{3043}\x{3045}\x{3047}\x{3049}'.
                   '\x{3063}\x{3083}\x{3085}\x{3087}\x{308E}\x{3095}-\x{309C}'.
                   '\x{30A1}\x{30A3}\x{30A5}\x{30A7}\x{30A9}'.
                   '\x{30C3}\x{30E3}\x{30E5}\x{30E7}\x{30EE}\x{30F5}\x{30F6}\x{30FB}\x{30FC}'.
                   '\x{31F0}-\x{31FF}'.
                   ']?';
        $asian = '(?:'.$asian1.'|'.$asian2.'|'.$asian3.')';
        $words = array();
        // handle asian chars as single words.
        $asia = @preg_replace('/('.$asian.')/u',' \1 ',$string);
        if (!is_null($asia)) {
            //will not be called if regexp failure
            $string = $asia;
        }

        $arr = preg_split('/\s+/', $string, -1, PREG_SPLIT_NO_EMPTY);

        foreach ($arr as $w) {
            $w = trim($w);
            if ($min_length !== false) {
            	if (mb_strlen($w,'UTF-8') >= $min_length) {
		            if (array_key_exists($w, $words)) {
		                $words[$w]++;
		            } else {
		                $words[$w] = 1;
		            }
            	}
            } else {
	            if (array_key_exists($w, $words)) {
	                $words[$w]++;
	            } else {
	                $words[$w] = 1;
	            }
            }
        }
        return $words;
    }

    /**
     * Clean a string from the HTML and the unnecessary
     * punctuation. Convert the string to lowercase.
     *
     * @info Require mbstring extension.
     *
     * @param string String.
     * @return string Cleaned lowercase string.
     */
    public static function clean_string($string)
    {
    	/*
        $text = date::stamp() .' - Search::clean_string (string initial)'.chr(10);
        $text .= $string .chr(10);
		file_put(SEARCH_LOG, $text);
    	*/
		$string = mb_strtolower($string,'UTF-8');
		/*
        $text = date::stamp() .' - Search::clean_string (string initial)'.chr(10);
        $text .= $string .chr(10);
		file_put(SEARCH_LOG, $text);
    	*/
    	$string = html_entity_decode($string,ENT_QUOTES,'UTF-8');
    	
        //$string = Search::html_entity_decode_utf8($string);
        /*
        $text = date::stamp() .' - Search::clean_string (html_entity_decode)'.chr(10);
        $text .= $string .chr(10);
		file_put(SEARCH_LOG, $text);
    	*/    	
    	$replace = array('&nbsp;','&eacute;','&grave;','&quot;','&lt;','&gt;', 'ndash', 'xmedia', 'mailto:', '––', '...');
    	$string = str_replace($replace,' ',$string);
		/*
        $text = date::stamp() .' - Search::clean_string (str_replace)'.chr(10);
        $text .= $string .chr(10);
		file_put(SEARCH_LOG, $text);
    	*/
    	$string = htmlspecialchars_decode($string);
    	/*
        $text = date::stamp() .' - Search::clean_string (htmlspecialchars_decode)'.chr(10);
        $text .= $string .chr(10);
		file_put(SEARCH_LOG, $text);
		*/        
        $string = str_replace('<?php', '', $string);
        $string = str_replace('?>', '', $string);
		$string = str_replace('%', '', $string);
        $string = strip_tags($string);
        $string = strtr($string, "\r\n\t", ' ');
        $string = strtr($string, '.&<>,;:(){}[]\\|*!?^_=/\'~`%$#"', ' ');
        
        return mb_strtolower($string, 'UTF-8');
    }

    /**
     * Remove the accentuated characters.
     *
     * Requires a string in lowercase, the removal is not perfect but
     * is better than nothing.
     *
     * @param string Lowercased string in utf-8.
     * @return string String with some of the accents removed.
     */
    public static function removeAccents($string)
    {
        $map = array(
                     'à'=>'a', 'ô'=>'o', 'ď'=>'d', 'ḟ'=>'f', 'ë'=>'e',
                     'š'=>'s', 'ơ'=>'o', 'ß'=>'ss', 'ă'=>'a', 'ř'=>'r', 
                     'ț'=>'t', 'ň'=>'n', 'ā'=>'a', 'ķ'=>'k', 'ŝ'=>'s', 
                     'ỳ'=>'y', 'ņ'=>'n', 'ĺ'=>'l', 'ħ'=>'h', 'ṗ'=>'p', 
                     'ó'=>'o', 'ú'=>'u', 'ě'=>'e', 'é'=>'e', 'ç'=>'c',
                     'ẁ'=>'w', 'ċ'=>'c', 'õ'=>'o', 'ṡ'=>'s', 'ø'=>'o', 
                     'ģ'=>'g', 'ŧ'=>'t', 'ș'=>'s', 'ė'=>'e', 'ĉ'=>'c',
                     'ś'=>'s', 'î'=>'i', 'ű'=>'u', 'ć'=>'c', 'ę'=>'e', 
                     'ŵ'=>'w', 'ṫ'=>'t', 'ū'=>'u', 'č'=>'c', 'ö'=>'oe', 
                     'è'=>'e', 'ŷ'=>'y', 'ą'=>'a', 'ł'=>'l', 'ų'=>'u', 
                     'ů'=>'u', 'ş'=>'s', 'ğ'=>'g', 'ļ'=>'l', 'ƒ'=>'f', 
                     'ž'=>'z', 'ẃ'=>'w', 'ḃ'=>'b', 'å'=>'a', 'ì'=>'i', 
                     'ï'=>'i', 'ḋ'=>'d', 'ť'=>'t', 'ŗ'=>'r', 'ä'=>'ae', 
                     'í'=>'i', 'ŕ'=>'r', 'ê'=>'e', 'ü'=>'ue', 'ò'=>'o',
                     'ē'=>'e', 'ñ'=>'n', 'ń'=>'n', 'ĥ'=>'h', 'ĝ'=>'g', 
                     'đ'=>'d', 'ĵ'=>'j', 'ÿ'=>'y', 'ũ'=>'u', 'ŭ'=>'u', 
                     'ư'=>'u', 'ţ'=>'t', 'ý'=>'y', 'ő'=>'o', 'â'=>'a', 
                     'ľ'=>'l', 'ẅ'=>'w', 'ż'=>'z', 'ī'=>'i', 'ã'=>'a', 
                     'ġ'=>'g', 'ṁ'=>'m', 'ō'=>'o', 'ĩ'=>'i', 'ù'=>'u', 
                     'į'=>'i', 'ź'=>'z', 'á'=>'a', 'û'=>'u', 'þ'=>'th', 
                     'ð'=>'dh', 'æ'=>'ae', 'µ'=>'u', 'ĕ'=>'e',
                     );
        return strtr($string, $map);
    }


    /* ===================================================================== *
     *                                                                       *
     *                Methods for rendering the pages.                       *
     *                                                                       *
     * Note: All standalone methods.                                         *
     * ===================================================================== */

    /**
     * Action to display a category.
     *
     * @param string Server query string
     * @return int Success code
     */
    public static function action($query)
    {
        $l10n = new l10n(config::f('lang'));
        $l10n->loadTemplate(config::f('lang'), config::f('theme_id'));
        // Easy access
        $GLOBALS['_PX_render']['last'] = '';
        $last =& $GLOBALS['_PX_render']['last']; 
        $GLOBALS['_PX_render']['website'] = '';
        $website =& $GLOBALS['_PX_render']['website']; 
        $GLOBALS['_PX_render']['res'] = '';
        $res =& $GLOBALS['_PX_render']['res']; 

        // Parse query string to find the matching article
        $con =& pxDBConnect();
        $s = new Search($con, config::f('website_id'));

        config::setVar('query_string', Search::parseQueryString($query));
        
        $sql = $s->create_search_query_string(config::f('query_string'));
        $extra = ' AND '.$con->pfx.'resources.publicationdate <= '
            .date::stamp().' AND '.$con->pfx.'resources.enddate >= '
            .date::stamp().' AND '.$con->pfx.'resources.status = '
            .PX_RESOURCE_STATUS_VALIDE;
        $sql = sprintf($sql, $extra);

        if (($res = $con->select($sql, 'ResourceSet')) === false) {
            $GLOBALS['_PX_render']['error']->setError('MySQL: '
                                                      .$con->error(), 500);
            
            config::setVar('query_string_origin', Search::parseQueryString($query));
            return 404;
        }

        $GLOBALS['_PX_render']['res']->autoSave(array('score', 'total'));
        $GLOBALS['_PX_render']['website'] = FrontEnd::getWebsite();

        header(FrontEnd::getHeader('search.php'));
        // Load the template
        include config::f('manager_path').'/templates/'
            .config::f('theme_id').'/search.php';
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
        if (preg_match('#^/search/(.*)$#i', $query, $match)) {
            $clean = $match[1];
            if (empty($clean)) {
                if (!empty($_GET['q'])) {
                    $clean = $_GET['q'];
                }
            }
            $clean = Search::clean_string($clean);
            $words = Search::explode_in_words($clean, true);
            $clean = '';
            foreach ($words as $k => $v) {
                $clean .= $k.' ';
            }
            $clean = trim($clean);
        } 
        return $clean;
    }

    /**
     * source: http://www.php.net/manual/en/function.html-entity-decode.php
     */
    function html_entity_decode_utf8($string)
    {
        static $trans_tbl;
        // replace numeric entities
        $string = preg_replace('~&#x([0-9a-f]+);~ei', 
                               'Search::code2utf(hexdec("\\1"))', $string);
        $string = preg_replace('~&#([0-9]+);~e', 'Search::code2utf(\\1)', 
                               $string);
        // replace literal entities
        if (!isset($trans_tbl)) {
            $trans_tbl = array();
            foreach (get_html_translation_table(HTML_ENTITIES) as $val=>$key)
                $trans_tbl[$key] = utf8_encode($val);
        }
        return strtr($string, $trans_tbl);
    }

    /** 
     * Returns the utf string corresponding to the unicode value (from
     * php.net, courtesy - romans@void.lv)
     */
    public static function code2utf($num)
    {
        if ($num < 128) return chr($num);
        if ($num < 2048) return chr(($num >> 6) + 192) . chr(($num & 63) + 128);
        if ($num < 65536) return chr(($num >> 12) + 224) . chr((($num >> 6) & 63) + 128) . chr(($num & 63) + 128);
        if ($num < 2097152) return chr(($num >> 18) + 240) . chr((($num >> 12) & 63) + 128) . chr((($num >> 6) & 63) + 128) . chr(($num & 63) + 128);
        return '';
    }
}
?>