<?php
header('Content-Type: text/plain');
require_once 'path.php';
include_once dirname(__FILE__).'/inc/class.files.php';
include_once dirname(__FILE__).'/inc/class.l10n.php';

/* Put # in front of the next 3 lines. */
echo "You need to open the file getlocale.php and put a # in front of \n";
echo "lines 8, 9 and 10 to use this script.\n";
exit;



class getlocale 
{
	var $lang = '';
    var $possible = array('plume', 'install');
	var $what = '';
	var $name = '';
    var $stats = array();

	function getlocale($lang)
	{
		$this->lang = $lang;
	}
	
	function checkinput()
	{
		
		if (empty($this->lang)) {
            echo "You need to provide a lang.\n";
            return false;
        }
		if (!preg_match('/^[a-z]{2,3}(_src)*$/', $this->lang)) {
			echo 'Error: Bad lang '.$this->lang."\n";
			return false;
		}
		return true;
	}
	
	function run()
	{
	    if (!$this->checkinput()) {
			return false;
		}
        $this->getPluginList();
        $this->getThemeList();
        foreach ($this->possible as $k => $entry) {
            list($what, $name) = explode(':', $entry);
            $this->what = $what;
            $this->name = $name;
            $this->parse();
        }
		echo "Done\n";
	}

    function stats()
    {
        echo "\n\n";
		echo '+-----------------------------------------------------------------------------------+'."\n";
		echo '| Statistics                                                                        |'."\n";
		echo '+-----------------------------------------------------------------------------------+'."\n";
        foreach ($this->stats as $k => $v) {
            echo 'File           - '.$k."\n";
            echo 'New strings    - '.$v[0]."\n";
			echo 'Strings kept   - '.$v[1]."\n";
			echo 'Old strings    - '.$v[2]."\n\n";
        }
    }

    function getPluginList()
    {
		global $_PX_config;
        $base = $_PX_config['manager_path'].'/tools/';
        $d = dir($base);
		while (($entry = $d->read()) !== false) {
			if ($entry != '.' && $entry != '..' 
                && is_dir($base.$entry)
                && (file_exists($base.$entry.'/desc.xml') or 
                    file_exists($base.$entry.'/register.php'))) {
				$this->possible[] = 'tools'.':'.$entry;
			}
		}
        @$d->close();
    }
    
    function getThemeList()
    {
        global $_PX_config;
        $base = $_PX_config['manager_path'].'/templates/';
        $d = dir($base);
        while (($entry = $d->read()) !== false) {
            if ($entry != '.' && $entry != '..' 
                && is_dir($base.$entry)
                && file_exists($base.$entry.'/desc.xml')) {
                $this->possible[] = 'theme'.':'.$entry;
            }
        }
        @$d->close();
    }

	function usage()
	{
		$r  = '+-----------------------------------------------------------------------------------+'."\n";
		$r .= '| Usage: getlocale.php?lang=(fr|dk|sk|new|...)                                      |'."\n";
        $r .= '| If you are creating a new locale, you need first to add the locale folders.       |'."\n";
		$r .= '+-----------------------------------------------------------------------------------+'."\n";
		return $r;
	}

	function backupFile($file)
	{
		if (file_exists($file) && !file_exists($file.'.bak')) {
			echo 'Backup file    - '.files::real_path($file).'.bak'."\n";
			files::copyfile($file, $file.'.bak');
		}
		return true;
	}

	/* -----------------------------------------------------------------------
	                             Parse functions
	----------------------------------------------------------------------- */
	function parse()
	{
		global $_PX_config;
		if (empty($this->what) || empty($this->lang)) return false;
        $this->cleanLocale();
		switch ($this->what) {
		case 'plume':
			$basedir = $_PX_config['manager_path'];
			$langfile = $_PX_config['manager_path'].'/locale/'.$this->lang.'/plume.lang';
			$exclude = '#(/tools/|/install/|/templates/|getlocale\.php|\.svn)#';
			break;
		case 'install':
			$basedir = $_PX_config['manager_path'].'/install';
			$langfile = $_PX_config['manager_path'].'/locale/'.$this->lang.'/install.lang';
			$exclude = '#\.svn#';
			break;
		case 'tools':
			$basedir = $_PX_config['manager_path'].'/tools/'.$this->name;
			$langfile = $_PX_config['manager_path'].'/tools/'.$this->name.'/locale/'.$this->lang.'/'.$this->name.'.lang';
			$exclude = '#\.svn#';
			break;
		case 'theme':
			$basedir = $_PX_config['manager_path'].'/templates';
			$langfile = $_PX_config['manager_path'].'/templates/'.$this->name.'/locale/'.$this->lang.'/'.$this->name.'.lang';
			$exclude = '#\.svn#';
			break;
		case 'all':
			echo 'Error: "all" argument only accepted for conversion'."\n";
			return false;
			break;
        default:
			return false;
			break;
		}
		$this->parseFiles($basedir, $exclude);
		$this->generateLangFile($langfile);
		return true;
	}

	function parseFiles($folder, $exclude='')
	{
		echo 'Parse files'."\n";
		$files = array();
		files::listfiles($folder, $files, '/\.php|\.xml$/i'); //only php/xml files

		foreach ($files as $file) {
			$file = files::real_path($file);
			$parse = true;
			if (!empty($exclude) and preg_match($exclude, $file)) {
				$parse = false;
			}
			if ($parse && (false === $this->parseFile($file))) echo ' Error'."\n";
		}
		echo 'Statistics'."\n";
		echo 'Parsed files   - '.count($files)."\n";
		echo 'Unique strings - '.count($GLOBALS['_PX_parsed_locale'])."\n";
		return true;
	}
	
	function generateLangFile($langfile)
	{
		$i = $j = $k = 0;
		$this->backupFile($langfile);
		echo 'Generate       - '.files::real_path($langfile)."\n";
		if (!file_exists($langfile)) {
			//very fast :o)
			$file=fopen($langfile,'w');
			fputs($file, '# -*- coding: utf-8 -*-'."\n".'# (C) 2006 your name <your @ email>'."\n");
			foreach ($GLOBALS['_PX_parsed_locale'] as $key => $val) {
				fputs($file, ';'.trim($key)."\n\n\n");
				$i++;
			}
			fclose($file);
			echo 'New strings   - '.$i."\n";
		} else {
			$this->loadFile($langfile);
			//Need to put the new strings without removing the previous order of strings
			//We just remove the lines that are not here anymore
			$file=@fopen($langfile,'w');
			@fputs($file, $GLOBALS['_PX_locale_header']); //keep previous headers
			foreach ($GLOBALS['_PX_locale'] as $orig => $trans) {
				if (!empty($GLOBALS['_PX_parsed_locale'][trim($orig)])) {
					@fputs($file, ';'.trim($orig)."\n");
					@fputs($file, trim($trans)."\n\n");
					unset($GLOBALS['_PX_parsed_locale'][trim($orig)]); //already put in the file
					unset($GLOBALS['_PX_locale'][trim($orig)]); //already put in the file
					$j++;
				}
			}
			//put the new strings
			foreach ($GLOBALS['_PX_parsed_locale'] as $key => $val) {
				@fputs($file, ';'.trim($key)."\n\n\n");
				$i++;
			}
			//put the strings not used anymore, can be usefull if only one letter changed in the original
			foreach ($GLOBALS['_PX_locale'] as $orig => $trans) {
				@fputs($file, '#;'.trim($orig)."\n");
				@fputs($file, '#'.trim($trans)."\n\n");
				$k++;
			}
			@fclose($file);
			echo 'New strings    - '.$i."\n";
			echo 'Strings kept   - '.$j."\n";
			echo 'Old strings    - '.$k."\n";
            $this->stats[$langfile] = array($i, $j, $k);
		}
		return true;
	}


	function loadFile($file)
	{
		if (!file_exists($file)) {
			return false;
		}
		$lines = file($file);
		$count = count($lines);
		$GLOBALS['_PX_locale_header'] = $lines[0].$lines[1];
		for ($i=2; $i<$count; $i++) {
			if (';' == substr($lines[$i],0,1)) {
				$GLOBALS['_PX_locale'][trim(substr($lines[$i],1))] = trim($lines[$i+1]).' ';
				$i++;
			}
		}
		return true;
	}
	
	function cleanLocale()
	{
		$GLOBALS['_PX_locale'] = array();
		$GLOBALS['_PX_parsed_locale'] = array();
		$GLOBALS['_PX_locale_header'] = '';
	}


	function parseFile($file)
	{
		echo files::real_path($file);
		if (!file_exists($file)) {
			return false;
		}
		$lines = file($file);
		$count = count($lines);
		$k = 0;
		for ($i=0; $i<$count; $i++) {
			if ($n = preg_match_all('/__\(\'(.*)\'\)|<label>(.*)<\/label\>|<desc>(.*)<\/desc>|label="(.*)"|/U', $lines[$i], $m, PREG_PATTERN_ORDER)) {
				$z = count($m);
				for ($j=0; $j<$n; $j++) {
					for ($y=1; $y<$z; $y++) {
						if (!empty($m[$y][$j])) {
							$GLOBALS['_PX_parsed_locale'][trim($m[$y][$j])] = true;
							$k++;
						}
					}
				}
			}
		}
		echo  ' ['.$count.' lines - '.$k.' strings]'."\n";
		return $k; //true;
	}

	/* -----------------------------------------------------------------------------
	                             Conversion functions
	----------------------------------------------------------------------------- */
	function convert()
	{
		switch ($this->what) {
		case 'plume':
		case 'install':
			$basedir = $GLOBALS['_PX_config']['manager_path'].'/locale/'.$this->lang;
			break;
		case 'help':
			$basedir = $GLOBALS['_PX_config']['manager_path'].'/help/'.$this->lang;
			break;
		case 'theme':
			$basedir = $GLOBALS['_PX_config']['manager_path'].'/templates/'.$this->what.'/locale/'.$this->lang;
			break;
		default:
			$basedir = $GLOBALS['_PX_config']['manager_path'].'/tools/'.$this->what.'/locale/'.$this->lang;
			break;
		}

		return $this->convertFolder($basedir);
	}


	function convertHelpFile($src, $dst)
	{
		echo 'Convert file   - '.files::real_path($src)."\n";
		if (!function_exists('iconv')) {
			echo 'Error: iconv module needed for the conversion.'."\n";
			return false;
		}
		$this->backupFile($dst);
		$lines = file($src);

		if (preg_match('/<\?xml\s+version="1.0"\s+encoding="(.*)"\?>\s*/iU', $lines[0], $match)) {
			$convert = true;
			$src_encoding = strtolower(trim($match[1]));
		} else {
			echo 'Error: The line: "'.$lines[0].'" is not an XML declaration, no conversion made.'."\n";
			return false;
		}
		$lines[0] = '<?xml version="1.0" encoding="utf-8"?>'."\n";
		$content = iconv($src_encoding, 'utf-8', join('', $lines));
		echo 'Conversion     - '.$src_encoding.' to utf-8'."\n";
		$f = fopen($dst,'w');
		fputs($f, $content);
		fclose($f);
		echo 'Lines          - '.count($lines)."\n";
		return true;
	}

	function convertLangFile($src, $dst)
	{
		echo 'Convert file   - '.files::real_path($src)."\n";
		if (!function_exists('iconv')) {
			echo 'Error: iconv module needed for the conversion.'."\n";
			return false;
		}
		$this->backupFile($dst);
		$src_encoding = l10n::loadFile($src, true);
		echo 'Conversion     - '.$src_encoding.' to utf-8'."\n";
		$lines = file($src);
		$count = count($lines);
		$file = fopen($dst,'w');
		fputs($file, '# -*- coding: utf-8 -*-'."\n");
		for ($i=1; $i<$count; $i++) {
			fputs($file, iconv($src_encoding, 'utf-8', trim($lines[$i]))."\n");
		}
		fclose($file);
		echo 'Lines          - '.$count."\n";
		return true;
	}

	/**
	It will convert a XX_src folder in the right XX folder. It can
	get a XX_src folder as a source or a XX folder as long as if a
	XX folder is given the corresponding XX_src folder exists.
	If $create == true the XX folder will be created if not
	already there.
	*/
	function convertFolder($folder, $create=true)
	{
		//Check if the folder ends in "_src" if not try to find
		//the corresponding "_src" folder
		$src_folder = '';
		$dst_folder = '';
		$folder = preg_replace('#(/)+$#', '', $folder);
		if (preg_match('/_src$/', $folder)) {
			$src_folder = files::real_path($folder);
			$dst_folder = files::real_path(substr($folder, 0, -4));
		} else {
			$src_folder = files::real_path($folder . '_src');
			$dst_folder = files::real_path($folder);
		}
		if (!file_exists($src_folder)) {
			echo  'No source folder: '.$src_folder."\n";
			return false;
		}
		if ($create && !file_exists($dst_folder)) {
			echo 'Create destination folder: '.$dst_folder;
			if (files::is_success($code = files::createfolder($dst_folder))) {
				echo ' - ok'."\n";
			} else {
				echo ' - error ('.$code.')'."\n";
				return false;
			}
		}
		//now we have the source and the destination folder, just need to get
		//the content of each of them and convert them. They can be .html or
		//.lang files
		$files = array();
		files::listfiles($src_folder, $files, '/\.lang|\.html$/i', false /* not to get the subfolders */);
		foreach ($files as $file) {
			$name = basename($file);
			$dst_file = $dst_folder.'/'.$name;
			$src_file = $file;
			if (preg_match('/\.lang$/', $name)) {
				$this->convertLangFile($src_file, $dst_file);
			} elseif (preg_match('/\.html$/', $name)) {
				$this->convertHelpFile($src_file, $dst_file);
			}
		}
		return true;
	}

}


$lang = '';
if (!empty($_GET['lang'])) $lang = trim($_GET['lang']);
$c = new getlocale($lang);
echo $c->usage();
$c->run();
$c->stats();
?>
