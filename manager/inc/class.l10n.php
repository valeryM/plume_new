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

require_once dirname(__FILE__).'/class.config.php';

function __($str)
{
    $t = trim($str);
    if (isset($GLOBALS['_PX_locale'][$t])) {
        return $GLOBALS['_PX_locale'][$t];
    } elseif (config::f('locale_lang') != 'en' 
              && config::f('debug') === true) {
        $GLOBALS['_PX_debug_data']['untranslated'][] = $t;
    } else if (config::f('log')=== true) {
    	// en cas d'absence de la traduction et Log activée, 
    	// on enregistre le texte non traduit dans un fichier        
        $file = dirname(dirname(dirname(__FILE__))) .'/logs/untranslated.txt';
        $text = config::f('locale_lang').'-'.$t .chr(10).chr(13);
        //file_put_contents($file, $text , FILE_APPEND | LOCK_EX);
		file_put($file, $text);
    }
    return $t;
}


function file_put($file,$text)  {
	
	$f = fopen($file,'a+');
	fwrite($f,$text);
	fclose($f);
	
	//file_put_contents($file, $text, FILE_APPEND | LOCK_EX);
}

/**
 * Localization class.
 *
 * 2 letter ISO codes from http://www.oasis-open.org/cover/iso639a.html
 * The list of languages supported by ISO-8859-1 is coming from Wikipedia
 */
class l10n
{

    /** 
     * Constructor.
     * See loadDomain()
     *
     * @param string Language ('en')
     * @param string Domain ('plume')
     */
    function l10n($lang='en', $domain='plume')
    {
        $this->loadDomain($lang, $domain);
    }
    
    /**
     * Load a domain file.
     * A domain file is a .lang file in the main locale folder of plume.
     *
     * @param string Language ('en')
     * @param string Domain, without the .lang ('plume')
     * @return bool Success
     */
    function loadDomain($lang='en', $domain='plume')
    {
        config::setVar('locale_lang', $lang);
        if ('en' == $lang) {
            return true;
        }
        return $this->loadFile(config::f('manager_path').'/locale/'
                               .$lang.'/'.$domain.'.lang');
    }

    /**
     * Load the locale of a plugin in the translation array.
     * See loadDomain()
     * It does not set the 'locale_lang' variable, whereas the 
     * loadDomain() method is setting it.
     *
     * @param string Language ('en')
     * @param string Plugin name
     * @return bool Sucess
     */
    function loadPlugin($lang='en', $plugin)
    {
        if ('en' == $lang) {
            return true;
        }
        return $this->loadFile(config::f('manager_path').'/tools/'
                               .$plugin.'/locale/'.$lang.'/'.$plugin.'.lang');
    }
    
    /**
     * Load the locale of a template in the translation array.
     * See loadDomain()
     * It does not set the 'locale_lang' variable, whereas the 
     * loadDomain() method is setting it.
     *
     * @param string Language ('en')
     * @param string Plugin name
     * @return bool Sucess
     */
    function loadTemplate($lang='en', $template)
    {
        if ('en' == $lang) {
            return true;
        }
        return $this->loadFile(config::f('manager_path').'/templates/'
                               .$template.'/locale/'.$lang.'/'.$template.'.lang');
    }

    /**
     * Load a locale file
     *
     * @param string Complete path to the locale file
     * @param bool Get only the encoding of the file
     * @return mixed Bool for success or encoding string
    */
    public static function loadFile($file, $getencodingonly=false)
    {
        if (!empty($GLOBALS['_PX_locale_files'][$file])) {
            return true;
        }
        if (!file_exists($file)) {
            return false;
        }

        // Load optimized file if available
        $phpfile = substr($file, 0, -5).'.php';
        if (file_exists($phpfile) 
            && (@filemtime($file) < @filemtime($phpfile))) {
            include $phpfile;
            $GLOBALS['_PX_locale_files'][$file] = 'optimized';
            return true;
        }

        $lines = file($file);
        // the first line is the encoding of the file a la Python
        if ($getencodingonly && preg_match('/^#\s-\*-\scoding:\s(.*)\s-\*-/', $lines[0], $match)) {
            return strtolower($match[1]);
        }

        $count = count($lines);
        for ($i=1; $i<$count; $i++) {
            $tmp = (!empty($lines[$i+1])) ? trim($lines[$i+1]) : '';
            if (!empty($tmp) && ';' == substr($lines[$i],0,1)) {
                $GLOBALS['_PX_locale'][trim(substr($lines[$i],1))] = $tmp;
                $i++;
            }
        }
        $GLOBALS['_PX_locale_files'][$file] = true;
        return true;
    }
    
    /**
     * Optimize a locale. Convert the .lang in a .php file 
     * ready to be included. The optimized file is encoded 
     * with the current encoding.
     *
     * @param string Locale file to optimize
     * @return bool Success
     */
    public static function optimizeLocale($file)
    {
        if (!file_exists($file)) {
            return false;
        }
        $phpfile = substr($file, 0, -5).'.php';

        $lines = file($file);

        if (false === ($fp = @fopen($phpfile,'w'))) {
            return false;
        }
        fputs($fp, '<?php '."\n".'/* automatically generated file from: '
              .$file.'  */'."\n\n");
        $basename = basename($file);
        fputs($fp, 'if (basename($_SERVER[\'SCRIPT_NAME\']) == \''.$basename.'\') exit;'."\n\n");
        $count = count($lines);
        for ($i=1; $i<$count; $i++) {
            $tmp = (!empty($lines[$i+1])) ? trim($lines[$i+1]) : '';
            if (!empty($tmp) && ';' == substr($lines[$i],0,1)) {
                $string = '$GLOBALS[\'_PX_locale\'][\''
                    .str_replace("'", "\\'", trim(substr($lines[$i],1)))
                    .'\'] = \''.str_replace("'", "\\'", $tmp).'\';'."\n";
                fputs($fp, $string);
                $i++;
            }
        }
        fputs($fp, "\n".'?>');
        @fclose($fp);
        @chmod($phpfile, 0777);
        return true;
    }

    /**
     * Get the available locales for a plugin or a domain.
     *
     * @param string Plugin ('')
     * @param string Domain ('')
     * @return array List of 2 letter iso codes
     */
    public static function  getAvailableLocales($plugin='', $domain='')
    {
        if ('' == $plugin) {
            $rootdir = config::f('manager_path').'/locale';
        } else {
            $rootdir = config::f('manager_path').'/tools/'.$plugin.'/locale';
        }
        $locales = array();
        $locales[] = 'en'; //English is always available
        $current_dir = opendir($rootdir);
        if (!empty($domain)) {
            $domain .= '.lang';
        }
        while($entryname = readdir($current_dir)) {
            if (is_dir($rootdir.'/'.$entryname.'/') 
                and ($entryname != '.' and $entryname!='..') 
                and (2 == strlen($entryname))
                ) {
                $entryname = strtolower($entryname);
                if (empty($domain)) {
                    $locales[] = $entryname;
                } elseif (is_file($rootdir.'/'.$entryname.'/'.$domain)) {
                    $locales[] = $entryname;
                }
            }
        }
        closedir($current_dir);
        sort($locales);
        reset($locales);
        return $locales;
    }

    /**
     * Return the "best" accepted language from the list of available 
     * languages.
     *
     * Use $_SERVER['HTTP_ACCEPT_LANGUAGE'] if the accepted language is empty
     *
     * @param array Available languages in the system
     * @param string String of comma separated accepted languages ('')
     * @return string Language 2 letter iso code, default is 'en'
     */
    public static function getAcceptedLanguage($available, $accepted ='')
    {
        if (empty($accepted)) {
            if (!empty($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
                $accepted = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
            } else {
                return 'en';
            }
        }
        $acceptedlist = explode(',', $accepted);
        foreach ($acceptedlist as $lang) {
            //for the fr-FR en-US cases
            $lang = strtolower(substr($lang, 0, 2)); 
            if (in_array($lang, $available)) {
                return $lang;
            }
        }
        //no match found, English
        return 'en';
    }

    /**
     * Returns iso codes.
     *
     * @param bool Is the language the key in the array (false)
     * @return array The key is either the language or the iso code
     */
    public static function getIsoCodes($lang=false)
    {
        $res = array('aa' => 'Afar',
                     'ab' => 'Abkhazian',
                     'af' => 'Afrikaans',
                     'am' => 'Amharic',
                     'ar' => 'Arabic',
                     'as' => 'Assamese',
                     'ay' => 'Aymara',
                     'az' => 'Azerbaijani',
                     'ba' => 'Bashkir',
                     'be' => 'Byelorussian',
                     'bg' => 'Bulgarian',
                     'bh' => 'Bihari',
                     'bi' => 'Bislama',
                     'bn' => 'Bengali',
                     'bo' => 'Tibetan',
                     'br' => 'Breton',
                     'ca' => 'Catalan',
                     'co' => 'Corsican',
                     'cs' => 'Czech',
                     'cy' => 'Welsh',
                     'da' => 'Danish',
                     'de' => 'German',
                     'dz' => 'Bhutani',
                     'el' => 'Greek',
                     'en' => 'English',
                     'eo' => 'Esperanto',
                     'es' => 'Spanish',
                     'et' => 'Estonian',
                     'eu' => 'Basque',
                     'fa' => 'Persian',
                     'fi' => 'Finnish',
                     'fj' => 'Fiji',
                     'fo' => 'Faroese',
                     'fr' => 'French',
                     'fy' => 'Frisian',
                     'ga' => 'Irish',
                     'gd' => 'Scots gaelic',
                     'gl' => 'Galician',
                     'gn' => 'Guarani',
                     'gu' => 'Gujarati',
                     'ha' => 'Hausa',
                     'he' => 'Hebrew',
                     'hi' => 'Hindi',
                     'hr' => 'Croatian',
                     'hu' => 'Hungarian',
                     'hy' => 'Armenian',
                     'ia' => 'Interlingua',
                     'ie' => 'Interlingue',
                     'ik' => 'Inupiak',
                     'id' => 'Indonesian',
                     'is' => 'Icelandic',
                     'it' => 'Italian',
                     'iu' => 'Inuktitut',
                     'ja' => 'Japanese',
                     'jv' => 'Javanese',
                     'ka' => 'Georgian',
                     'kk' => 'Kazakh',
                     'kl' => 'Greenlandic',
                     'km' => 'Cambodian',
                     'kn' => 'Kannada',
                     'ko' => 'Korean',
                     'ks' => 'Kashmiri',
                     'ku' => 'Kurdish',
                     'ky' => 'Kirghiz',
                     'la' => 'Latin',
                     'ln' => 'Lingala',
                     'lo' => 'Laothian',
                     'lt' => 'Lithuanian',
                     'lv' => 'Latvian;lettish',
                     'mg' => 'Malagasy',
                     'mi' => 'Maori',
                     'mk' => 'Macedonian',
                     'ml' => 'Malayalam',
                     'mn' => 'Mongolian',
                     'mo' => 'Moldavian',
                     'mr' => 'Marathi',
                     'ms' => 'Malay',
                     'mt' => 'Maltese',
                     'my' => 'Burmese',
                     'na' => 'Nauru',
                     'ne' => 'Nepali',
                     'nl' => 'Dutch',
                     'no' => 'Norwegian',
                     'oc' => 'Occitan',
                     'om' => 'Afan (oromo)',
                     'or' => 'Oriya',
                     'pa' => 'Punjabi',
                     'pl' => 'Polish',
                     'ps' => 'Pashto;pushto',
                     'pt' => 'Portuguese',
                     'qu' => 'Quechua',
                     'rm' => 'Rhaeto-romance',
                     'rn' => 'Kurundi',
                     'ro' => 'Romanian',
                     'ru' => 'Russian',
                     'rw' => 'Kinyarwanda',
                     'sa' => 'Sanskrit',
                     'sd' => 'Sindhi',
                     'sg' => 'Sangho',
                     'sh' => 'Serbo-croatian',
                     'si' => 'Singhalese',
                     'sk' => 'Slovak',
                     'sl' => 'Slovenian',
                     'sm' => 'Samoan',
                     'sn' => 'Shona',
                     'so' => 'Somali',
                     'sq' => 'Albanian',
                     'sr' => 'Serbian',
                     'ss' => 'Siswati',
                     'st' => 'Sesotho',
                     'su' => 'Sundanese',
                     'sv' => 'Swedish',
                     'sw' => 'Swahili',
                     'ta' => 'Tamil',
                     'te' => 'Telugu',
                     'tg' => 'Tajik',
                     'th' => 'Thai',
                     'ti' => 'Tigrinya',
                     'tk' => 'Turkmen',
                     'tl' => 'Tagalog',
                     'tn' => 'Setswana',
                     'to' => 'Tonga',
                     'tr' => 'Turkish',
                     'ts' => 'Tsonga',
                     'tt' => 'Tatar',
                     'tw' => 'Twi',
                     'ug' => 'Uigur',
                     'uk' => 'Ukrainian',
                     'ur' => 'Urdu',
                     'uz' => 'Uzbek',
                     'vi' => 'Vietnamese',
                     'vo' => 'Volapuk',
                     'wo' => 'Wolof',
                     'xh' => 'Xhosa',
                     'yi' => 'Yiddish',
                     'yo' => 'Yoruba',
                     'za' => 'Zhuang',
                     'zh' => 'Chinese',
                     'zu' => 'Zulu');
        if ($lang) {
            $res = array_flip($res);
            ksort($res); //order by lang
        }
        return $res;
    }

    /**
     * Returns the list of western iso codes.
     */
    public static function getIsoWestern()
    {
        return array('af', 'sq', 'eu', 'ca', 'da', 'nl', 'en', 'fo', 'fi', 
                     'fr', 'de', 'is', 'ga', 'it', 'no', 'pt', 'rm', 'gd',
                     'es', 'sv', 'sw');
    }
}


/**
 * Error handling class.
 */
class CError
{
    var $error = array(); /**< Current errors. */


    /**
     * Reset the errors
     */
    function resetError()
    {
        $this->error = array();
    }

    /**
     * Set an error.
     *
     * By convention the number is 4xx if the error is coming from
     * the user or 5xx if coming from the system (database error for ex.)
     *
     * @param string Error message
     * @param int Error number (0)
     */
    function setError($msg, $no=0)
    {
        $this->error[] = array($no,$msg);
    }

    
    /**
     * Returns the errors.
     *
     * @param bool Errors as HTML list (false)
     * @param bool Show numbers (true)
     * @return mixed array of errors, HTML list, or false if no errors
     */
    function error($html=false, $with_nb=true)
    {
        if (count($this->error) > 0) {
            if (!$html) {
                return $this->error;
            } else {
                $res = '<ul>'."\n";
                foreach($this->error as $v) {
                    $res .= '<li>'.
                        (($with_nb) ? 
                         '<span class="errno">'.$v[0].'</span> - ' : 
                         '').
                        '<span class="errmsg">'.$v[1].'</span></li>'."\n";
                }
                return $res."</ul>\n";
            }
        } else {
            return false;
        }
    }

    /**
     * Helper function to set the error from the DB.
     *
     * @param string Error message from the DB
     */
    function setDbError($db_error_msg)
    {
        $this->setError(__('DB error:').' '.$db_error_msg, 500);
    }

    /**
     * Bulk set errors.
     *
     * Used when you went to recopy the errors of one object into
     * another. You can call that way:
     * $object->bulkSetErrors($otherobject->error());
     *
     * @param array List of errors
     * @return bool Success
     */
    function bulkSetError($errors)
    {
        if (!is_array($errors)) {
            return false;
        }
        foreach ($errors as $e) {
            $this->setError($e[1], $e[0]);
        }
        return true;
    }

}
?>
