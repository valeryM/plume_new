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
require_once dirname(__FILE__).'/../extinc/class.recordset.php';

/**
 * Storage of a website.
 *
 * A website is not like the other resources as it needs some files
 * on disc to run (the configuration, the templates).
 * The result is that it is a little more complex to manage, thus this
 * Website class has more methods than for example the News class.
 * On the other side, it is still possible to use the standard methods
 * like set(), check(), commit(), remove() with all the ugly work
 * being hidden in background.
 */
class Website extends recordset
{
    /**
     * Constructor
     *
     * @param Array Data from a possible SQL query ('')
     */
    function Website($data='')
    {
        parent::recordset($data);
    }


    /**
     * Generate an id for a website from its web address.
     *
     * Not always unique.
     *
     * @param string Website address
     * @return string Id
     */
    function generateId($address)
    {
        return substr(preg_replace('/[^A-Za-z0-9]/', '', $address), 4); 
    }

    /**
     * Set the data of the website.
     *
     * @param string Name
     * @param string Description
     * @param string Format of the description
     * @param string Lang of the site 
     * @param string Website address
     * @param string Website path on the file system
     * @param string Website xmedia address
     * @param string Website xmedia path on the file system
     * @return bool True
     */
    function set($name, $description, $format, $lang, $address,
                 $path, $xmedia, $xmedia_path, $img_path)
    {
        $this->setField('website_name', trim($name));
        $this->setField('website_description', '='.$format."\n".trim($description));
        $this->setField('website_lang', $lang);

        $address = preg_replace('#(/)+$#', '', trim($address));
        $this->setField('website_url', $address);

        $path = preg_replace('#(/)+$#', '', trim($path));
        $this->setField('website_path', files::real_path($path));

        $xmedia = preg_replace('#(/)+$#', '', trim($xmedia));
        $this->setField('website_xmedia_url', $xmedia);

        $xmedia_path = preg_replace('#(/)+$#', '', trim($xmedia_path));
        $this->setField('website_xmedia_path', files::real_path($xmedia_path));
        
      	$this->setField('website_img',trim($img_path));
      	
        return true;
    }

    /**
     * Check if the given website is valid.
     *
     * In case of failure the error message is set.
     *
     * @return bool Success
     */
    function check()
    {
        if ((strlen($this->f('website_xmedia_path')) == 0)
            or !file_exists($this->f('website_xmedia_path'))) {
            $this->setError(sprintf(__('The provided file and image folder %s is not available. Please check the folder you gave.'), 
                                    $this->f('website_xmedia_path')), 
                            400);
        }
        if ((strlen($this->f('website_path')) == 0)
            or !file_exists($this->f('website_path'))) {
            $this->setError(sprintf(__('The document root folder of the website %s is not available. Please check the folder you gave.'), 
                                    $this->f('website_path')), 
                            400);
        }
        if (2 != strlen($this->f('website_lang'))) {
            $this->setError(__('The website language must be composed of the 2 ISO standard letters, for example de (German), fr (French), jp (Japanese).'), 
                            400);
        }
        if (0 == strlen($this->f('website_url'))) {
            $this->setError(__('You must give a website address.'), 400);
        }
        if (0 == strlen($this->f('website_xmedia_url'))) {
            $this->setError(__('You must give the URL to the file and image folder.'), 400);
        }
        if (0 != strlen($this->f('website_url')) and 
            !preg_match('#^(http|https)://#i', $this->f('website_address'))) {
            $this->setError(__('The website address must start with http(s)://.'), 400);
        }
        if (0 == strlen($this->f('website_xmedia_url')) and
            !preg_match('#^(http|https)://#i', $this->f('website_xmedia'))) {
            $this->setError(__('The URL to the file and image folder must start with http(s)://.'), 400);
        }
        if (0 == strlen(text::getRawContent($this->f('website_description')))) {
            $this->setError(__('You must provide a website description.'),
                            400);
        }
        if (0 == strlen($this->f('website_name'))) {
            $this->setError(__('You must give a website name.'), 400);
        }

        if (false !== $this->error(true, false)) {
            return false;
        }
        return true;
    }

    /**
     * Check the rights on the files and folders.
     *
     * As some hosting environments are not providing the necessary rights
     * to write files and create folders, the check of those rights must be
     * done separately to have the possibility to install without touching
     * the files, and then do the file installation by hand.
     *
     * @return bool Success
     */
    function checkFileRights()
    {
        if ((strlen($this->f('website_xmedia_path')) != 0)
            && file_exists($this->f('website_xmedia_path')) 
            && !is_writable($this->f('website_xmedia_path'))) {
            $this->setError(sprintf(__('The system has no write access to the file and image folder %s. Check the permissions on the server.'), 
                                    $this->f('website_xmedia_path')), 
                            400);
        }
        if ((strlen($this->f('website_path')) != 0)
            && file_exists($this->f('website_path')) 
            && !is_writable($this->f('website_path'))) {
            $this->setError(sprintf(__('The system has no write access to the document root folder %s. Check the permissions on the server.'), 
                                    $this->f('website_path')), 
                            400);
        }
        if (!is_writable(dirname(__FILE__).'/../conf/')) {
            $this->setError(__('The system has no write access to the configuration folder manager/conf/. Check the permissions on the server.'), 400);
        }
        if (!is_writable(dirname(__FILE__).'/../templates/')) {
            $this->setError(__('The system has no write access to the template folder manager/templates/. Check the permissions on the server.'), 400);
        }

        if (false !== $this->error(true, false)) {
            return false;
        }
        return true;
    }

    /**
     * Save the website.
     *
     * It is possible to only save the data into the database without touching
     * the configuration files.
     * As during the process many files need to be updated a log is created and
     * it is possible to have the list of files to move/update by hand if
     * needed and not the rights to do it.
     *
     * @param &array Associative array with the log of the process
     * @param bool Save only in the database (True)
     * @return bool Success
     */
    function commit(&$log, $only_in_db=true)
    {
        $log = array();

        if (false == $this->check()) {
            return false;
        }
        if (!$only_in_db and false == $this->checkFileRights()) {
            return false;
        }

        // Check if update or new website
        $update = true;
        if (0 == strlen($this->f('website_id'))) {
            $this->setField('website_id', 
                            $this->generateId($this->f('website_url')));
            $update = false;
        }
        $website_exists = $this->website_exists($this->f('website_id'));
        if ($website_exists === false) {
            return false;
        }
        if ($new_site and $website_exists == $this->f('website_id')) {
            $this->setError(sprintf(__('Adding a new website with id %s, but a website with this id already exists.'), $this->f('website_id')), 500);
            return false;
        }
        if ($website_exists === '') {
            $update = false;
        }

        $this->getConnection();

        if ($update) {
            $req = 'UPDATE '.$this->con->pfx.'websites SET ';
        } else {
            $req = 'INSERT INTO '.$this->con->pfx.'websites SET
                 website_id =\''.$this->con->esc($this->f('website_id')).'\',
                 website_startdate = \''.date::stamp().'\', ';
        }
        $req .= 'website_name = \''.$this->con->esc($this->f('website_name')).'\',
             website_url = \''.$this->con->esc($this->f('website_url')).'\',
             website_reurl = \''.$this->con->esc($this->f('website_reurl')).'\',
             website_path = \''.$this->con->esc($this->f('website_path')).'\',
             website_xmedia_reurl = \''.$this->con->esc($this->f('website_xmedia_reurl')).'\',
             website_xmedia_path = \''.$this->con->esc($this->f('website_xmedia_path')).'\',
             website_description = \''.$this->con->esc($this->f('website_description')).'\' ';
        if ($update) {
            $req .= 'WHERE website_id =\''.$this->con->esc($this->f('website_id')).'\'';
        }

        if (!$this->con->execute($req)) {
            $this->setError('MySQL: '.$this->con->error(), 500);
            return false;
        }


        if (!$update) {
            include_once dirname(__FILE__).'/lib.auth.php';
            // As this user adds the site, give him root access to it
            $insReq = 'INSERT INTO '.$this->con->pfx.'grants SET
                website_id =\''.$this->con->escapeStr($id).'\',
                user_id = \''.$this->con->escapeStr($this->user->_userid).'\',
                level = \''.PX_AUTH_ADMIN.'\'';

            if (!$this->con->execute($insReq)) {
                $this->setError('MySQL: '.$this->con->error(), 500);
                return false;
            }

            // Add the root category
            $hp_title = __('Home Page');
            $hp_desc = __('Root category, that plays the role of homepage.');
            $hp_kw = __('homepage, index, default');
            $insReq='INSERT INTO '.$this->con->pfx.'categories SET
                website_id=\''.$this->con->escapeStr($id).'\',
                category_name=\''.$this->con->escapeStr($hp_title).'\',
                category_description=\''.$this->con->escapeStr($hp_desc).'\',
                category_keywords=\''.$this->con->escapeStr($hp_kw).'\',
                category_path=\'/\',
                category_publicationdate=\''.timestamp().'\',
                category_creationdate=\''.timestamp().'\',
                category_enddate=99991231235959,
                category_template=\'category_homepage.php\',
                category_type=\'default\',
                category_cachetime=86400';
            if (!$this->con->execute($insReq)) {
                $this->setError('MySQL : '.$this->con->error(), 500);
                return false;
            }
            if (false == ($catid = $this->con->getLastID())) {
                $this->setError('MySQL: '.$this->con->error(), 500);
                return false;
            }
            $updReq = 'UPDATE '.$this->con->pfx.'categories SET 
                category_parentid=\''.$this->con->escapeStr($catid).'\'
                WHERE category_id = \''.$this->con->escapeStr($catid).'\'';

            if (!$this->con->execute($updReq)) {
                $this->setError('MySQL: '.$this->con->error(), 500);
                return false;
            }
            // Add 2 default subtypes

            if (false === $this->saveType('', 'articles', __('Article'), 
                                          'resource_article.php', 3600, '', '',
                                          $id)) {
                return false;
            }
            if (false === $this->saveType('', 'news', __('News'), 
                                          'resource_news.php', 3600, '1', '', 
                                          $id)) {
                return false;
            }

        }

    }

    /**
     * Check if a website with a given id exists.
     *
     * @param string Id
     * @return mixed Id of the website if exists, empty string if not existing
     *               false if error
     */
    function website_exists($id)
    {
        $this->getConnection();
        if (($rs = $this->con->select(SQL::getWebsite($id))) !== false) {
            if ($rs->nbRow() > 0) {
                return $rs->f('website_id');
            } else {
                return '';
            }
        } else {
            $this->setError('MySQL: '.$this->con->error(), 500);
            return false;
        }

    }


//if (false) {

    /**
     * Save a site or create a new one. If $id is empty, a new site 
     * is created and the log of the creation is set in &$log_new_site. 
     * The log is pure HTML ready for display.
     */
    function saveSite($id, $name, $description, $sitelang, $website_address, $website_path, $xmedia_name, &$log_new_site, $force_new_id='')
    {
        include_once dirname(__FILE__).'/../extinc/class.configfile.php';
        include_once dirname(__FILE__).'/class.checklist.php';
        global $_PX_config;

        $update = (empty($id)) ? false : true;
        $xmedia_path = '';
        if (!empty($website_path) && !empty($xmedia_name))
            $xmedia_path = files::real_path($website_path).'/'.$xmedia_name;
        $parsedurl = parse_url($website_address);

        if ($update) {
            // check the data if $update
            if (0 == strlen(trim($id))) {
                $this->setError( __('Error: Internal error, please report your actions leading to this error message.'),500);
            }
            if (preg_match('/[^A-Za-z0-9]/', $id)) {
                $this->setError( __('Error: Internal error, please report your actions leading to this error message.'),500);
            }
        }

        // get the website with this id
        if (empty($id)) {
            if (!empty($force_new_id)) $id = $force_new_id; //to be able to force the first site with the "default" id.
            else $id = substr(preg_replace('/[^A-Za-z0-9]/', '', $website_address), 4); //new id from the address (without http but the s)
        }

        $site = $this->getSites($id);

        if ($update) {
            if (!file_exists(dirname(__FILE__).'/../conf/configweb_'.$id.'.php') || !is_writable(dirname(__FILE__).'/../conf/configweb_'.$id.'.php')) {
                $this->setError(sprintf( __('Error: The configuration file %s is not writeable.'), files::real_path(dirname(__FILE__).'/../conf/').'/configweb_'.$id.'.php'), 500);
                return false;
            }
            if ($site->nbRow() == 0) {
                $this->setError( __('This site is not available.') , 400);
                return false;
            }

        } else {
            // check if this website already exists
            if ($site->nbRow() >= 1) {
                $this->setError( __('Error: Id already used.') , 400);
                return false;
            }
            // need to create a new file
            // copy paste the config_default.php
            if (!is_writable(dirname(__FILE__).'/../conf/')) {
                $this->setError(sprintf( __('Error: The configuration folder %s is not writeable.'),
                                         files::real_path(dirname(__FILE__).'/../conf/')), 500);
                return false;
            }
            $source_file      = dirname(__FILE__).'/../conf/configweb_default.copy.php';
            $destination_file = dirname(__FILE__).'/../conf/configweb_'.$id.'.php';
            if (file_exists($destination_file)) {
                @unlink ($destination_file);
            }
            if (!copy($source_file, $destination_file)) {
                $this->setError( __('Error: Impossible to create the configuration file.') , 500);
                return false;
            }
            @chmod($destination_file, 0666);

        }
        // open file for edition of the data
        $cfg = new configfile(dirname(__FILE__).'/../conf/configweb_'.$id.'.php');
        $cfg->prefix = '_PX_website_config';
        $cfg->editVar('website_id',    (string) $id);
        $cfg->editVar('xmedia_root',   (string) $xmedia_path);
        $cfg->editVar('domain',        (string) $domain);
        $cfg->editVar('rel_url',       (string) $reurl);
        $cfg->editVar('rel_url_files', (string) $xmedia_reurl);
        $cfg->editVar('secure',        (bool)   $secure);
        $cfg->editVar('lang',          (string) $sitelang);

        if (!$cfg->saveFile()) {
            $this->setError( __('Error: Impossible to create the configuration file.') , 500);
            return false;
        }
        // no update or add in the db
        if ($update) {
            $insReq = 'UPDATE '.$this->con->pfx.'websites SET ';
        } else {
            $insReq = 'INSERT INTO '.$this->con->pfx.'websites SET
                                          website_id =\''.$this->con->escapeStr($id).'\',
                                          website_startdate = \''.timestamp().'\', ';
        }
        $securestring = $secure ? 's' : '';
        $insReq .= 'website_name   = \''.$this->con->escapeStr($name).'\', ';
        $insReq .= 'website_url    = \''.$this->con->escapeStr('http'.$securestring.'://'.$domain.$reurl).'\', ';
        $insReq .= 'website_reurl  = \''.$this->con->escapeStr($reurl).'\', ';
        $insReq .= 'website_path   = \''.$this->con->escapeStr('').'\', ';
        $insReq .= 'website_xmedia_reurl   = \''.$this->con->escapeStr($xmedia_reurl).'\', ';
        $insReq .= 'website_xmedia_path   = \''.$this->con->escapeStr($xmedia_path).'\', ';
        $insReq .= 'website_description  = \''.$this->con->escapeStr($description).'\' ';
        if ($update) {
            $insReq .= 'WHERE website_id =\''.$this->con->escapeStr($id).'\'';
        }

        if (!$this->con->execute($insReq)) {
            $this->setError('MySQL : '.$this->con->error(), 500);
            return false;
        }


        if (!$update) {
            include_once dirname(__FILE__).'/lib.auth.php';
            // As this user adds the site, give him root access to it
            $insReq = 'INSERT INTO '.$this->con->pfx.'grants SET
                                          website_id =\''.$this->con->escapeStr($id).'\',
                                          user_id    = \''.$this->con->escapeStr($this->user->_userid).'\',
                                          level      = \''.PX_AUTH_ADMIN.'\'';

            if (!$this->con->execute($insReq)) {
                $this->setError('MySQL : '.$this->con->error(), 500);
                return false;
            }

            // Add the root category
            $hp_title = __('Home Page');
            $hp_desc = __('Root category, that plays the role of homepage.');
            $hp_kw = __('homepage, index, default');
            $insReq='INSERT INTO '.$this->con->pfx.'categories SET
                                website_id=\''.$this->con->escapeStr($id).'\',
                                category_name=\''.$this->con->escapeStr($hp_title).'\',
                                category_description=\''.$this->con->escapeStr($hp_desc).'\',
                                category_keywords=\''.$this->con->escapeStr($hp_kw).'\',
                                category_path=\'/\',
                                category_publicationdate=\''.timestamp().'\',
                                category_creationdate=\''.timestamp().'\',
                                category_enddate=99991231235959,
                                category_template=\'category_homepage.php\',
                                category_type=\'default\',
                                category_cachetime=86400';
            if (!$this->con->execute($insReq)) {
                $this->setError('MySQL : '.$this->con->error(), 500);
                return false;
            }
            if (false == ($catid = $this->con->getLastID())) {
                $this->setError('MySQL : '.$this->con->error(), 500);
                return false;
            }
            $updReq = 'UPDATE '.$this->con->pfx.'categories SET category_parentid=\''.$this->con->escapeStr($catid).'\'
                                        WHERE category_id = \''.$this->con->escapeStr($catid).'\'';

            if (!$this->con->execute($updReq)) {
                $this->setError('MySQL : '.$this->con->error(), 500);
                return false;
            }
            // Add 2 default subtypes

            if (false === $this->saveType('', 'articles', __('Article'), 'resource_article.php', 3600, '', '', $id)) {
                return false;
            }
            if (false === $this->saveType('', 'news', __('News'), 'resource_news.php', 3600, '1', '', $id)) {
                return false;
            }
                        
            // All the database related work is done. The creation is a success, we may have error to copy the 
            // files, but they are not erros, only "warnings".
            // 1- Create the xmedia/thumb folder
            // 2- Create the xmedia/theme folder
            // 3- Create the manager/templates/$id folder
            // 4- Copy folder manager/templates/_dist/default/theme into folder created in 2
            // 5- Copy folder manager/templates/_dist/default/templates into folder created in 3
            // 6- If id != 'default' copy from the 'default' document root config.php index.php prepend.php rss.php search.php
            //    into new document root
            // 7- Edit config.php for $_PX_config['manager_path'] and to load the good config file.

            include_once dirname(__FILE__).'/class.files.php';
            include_once dirname(__FILE__).'/class.checklist.php';
            $f = new files();
            $checklist = new checklist();
            // 1- Create the xmedia/thumb folder
            $checklist->addTest('thumb-folder', files::is_success($f->createfolder($xmedia_path.'/thumb', 0777)) ? 1 : 2,
                                sprintf(__('Thumbnail folder %s created successfully.'), files::real_path($xmedia_path.'/thumb')),
                                '' /* no error */,
                                sprintf(__('Unable to create the thumbnail folder %s.'), $xmedia_path.'/thumb'));

            // 2- Create the xmedia/theme folder
            $checklist->addTest('theme-folder', files::is_success($f->createfolder($xmedia_path.'/theme', 0777)) ? 1 : 2,
                                sprintf(__('Theme folder %s created successfully.'), files::real_path($xmedia_path.'/theme')),
                                '' /* no error */,
                                sprintf(__('Unable to create the theme folder %s.'), $xmedia_path.'/theme'));

            // 3- Create the manager/templates/$id folder
            $checklist->addTest('template-folder', files::is_success($f->createfolder(files::real_path(dirname(__FILE__).'/../templates/'.$id), 0777)) ? 1 : 2,
                                sprintf(__('Template folder %s created successfully.'), files::real_path(dirname(__FILE__).'/../templates/'.$id)),
                                '' /* no error */,
                                sprintf(__('Unable to create the template folder %s.'), dirname(__FILE__).'/../templates/'.$id));

            // 4- Copy folder manager/templates/_dist/default/theme into folder created in 2
            $checklist->addTest('content-theme-folder',
                                files::is_success($f->copyfolder(files::real_path(dirname(__FILE__).'/../templates/_dist/default/theme'),
                                                                 files::real_path($xmedia_path.'/theme'))) ? 1 : 2,
                                sprintf(__('Theme files successfully copied from %s to the theme folder.'), files::real_path(dirname(__FILE__).'/../templates/_dist/default/theme')),
                                '' /* no error */,
                                sprintf(__('Unable to copy the theme files from %s to the theme folder.'), files::real_path(dirname(__FILE__).'/../templates/_dist/default/theme')));

            // 5- Copy folder manager/templates/_dist/default/templates into folder created in 3
            $checklist->addTest('content-template-folder',
                                files::is_success($f->copyfolder(files::real_path(dirname(__FILE__).'/../templates/_dist/default/templates'),
                                                                 files::real_path(dirname(__FILE__).'/../templates/'.$id))) ? 1 : 2,
                                sprintf(__('Templates files successfully copied from %s to the template folder.'), files::real_path(dirname(__FILE__).'/../templates/_dist/default/templates')),
                                '' /* no error */,
                                sprintf(__('Unable to copy the templates files from %s to the template folder.'), files::real_path(dirname(__FILE__).'/../templates/_dist/default/templates')));

            // 6- If id != 'default' copy from the 'default' document root config.php index.php prepend.php rss.php search.php
            //    into new document root
            if ('default' != $id) {
                $checklist->addTest('config-php',
                                    files::is_success($f->copyfile(files::real_path(dirname(__FILE__).'/../../config.php'), $website_path.'/config.php')) ? 1 : 2,
                                    sprintf(__('Config file successfully copied from %s to the document root folder.'), files::real_path(dirname(__FILE__).'/../../config.php') ),
                                    '' /* no error */,
                                    sprintf(__('Unable to copy the config file from %s to the document root folder.'), files::real_path(dirname(__FILE__).'/../../config.php') ));

                $checklist->addTest('index-php',
                                    files::is_success($f->copyfile(files::real_path(dirname(__FILE__).'/../../index.php'), $website_path.'/index.php')) ? 1 : 2,
                                    sprintf(__('Index file successfully copied from %s to the document root folder.'), files::real_path(dirname(__FILE__).'/../../index.php') ),
                                    '' /* no error */,
                                    sprintf(__('Unable to copy the index file from %s to the document root folder.'), files::real_path(dirname(__FILE__).'/../../index.php') ));

                $checklist->addTest('prepend-php',
                                    files::is_success($f->copyfile(files::real_path(dirname(__FILE__).'/../../prepend.php'), $website_path.'/prepend.php')) ? 1 : 2,
                                    sprintf(__('Prepend file successfully copied from %s to the document root folder.'), files::real_path(dirname(__FILE__).'/../../prepend.php') ),
                                    '' /* no error */,
                                    sprintf(__('Unable to copy the prepend file from %s to the document root folder.'), files::real_path(dirname(__FILE__).'/../../prepend.php') ));

                $checklist->addTest('rss-php',
                                    files::is_success($f->copyfile(files::real_path(dirname(__FILE__).'/../../rss.php'), $website_path.'/rss.php')) ? 1 : 2,
                                    sprintf(__('Rss file successfully copied from %s to the document root folder.'), files::real_path(dirname(__FILE__).'/../../rss.php') ),
                                    '' /* no error */,
                                    sprintf(__('Unable to copy the rss file from %s to the document root folder.'), files::real_path(dirname(__FILE__).'/../../rss.php') ));

                $checklist->addTest('search-php',
                                    files::is_success($f->copyfile(files::real_path(dirname(__FILE__).'/../../search.php'), $website_path.'/search.php')) ? 1 : 2,
                                    sprintf(__('Search file successfully copied from %s to the document root folder.'), files::real_path(dirname(__FILE__).'/../../search.php') ),
                                    '' /* no error */,
                                    sprintf(__('Unable to copy the search file from %s to the document root folder.'), files::real_path(dirname(__FILE__).'/../../search.php') ));

                // 7- Edit config.php for $_PX_config['manager_path'] and to load the good config file.
                // open file for edition of the data
                $cfg = new configfile($website_path.'/config.php');
                $cfg->prefix = '_PX_config';
                $cfg->editVar('manager_path',    (string) files::real_path($_PX_config['manager_path']));
                $edit_config_success = 1;
                if (!$cfg->saveFile()) {
                    $edit_config_success = 2;
                } else {
                    if (!file_exists($website_path.'/config.php') or !is_writable($website_path.'/config.php')) {
                        $edit_config_success = 2;
                    } else {
                        $config_file = @join('', @file($website_path.'/config.php'));
                        $config_file = preg_replace('/configweb\_([A-Za-z0-9]+)\.php/', 'configweb_'.$id.'.php', $config_file);
                        $open = @fopen($website_path.'/config.php', 'w');
                        @fwrite($open, $config_file);
                        @fclose($open);
                    }
                }
                $checklist->addTest('edit-config-php', $edit_config_success,
                                    sprintf(__('Config file %s successfully updated.'), files::real_path($website_path.'/config.php') ),
                                    '' /* no error */,
                                    sprintf(__('Unable to update the config file %s.'), files::real_path($website_path.'/config.php') ));

            } //end of if not 'default'
            $path = ('default' != $id) ? 'themes/'.$GLOBALS['_px_theme'].'/images' : '../themes/default/images';
            $log_new_site = $checklist->getHtml($path);

        }

        return true;

    }

    function delSite($id)
    {
        if (preg_match('/[^A-Za-z0-9]/', $id)) {
            $this->setError( __('Error: Invalid id, it must contain only letters and digits.'),400);
            return false;
        }

        // get the website with this id
        $site = $this->getSites($id);
        if ($site->nbRow() == 0) {
            $this->setError( __('This site is not available.') , 400);
            return false;
        }
        $date = $this->getEarlierDate('m', '', '', $id);
        if (strlen($date) == 14) {
            $this->setError( __('Error: The site can only be deleted if empty.') , 400);
            return false;
        }


        if (     !file_exists(dirname(__FILE__).'/../conf/configweb_'.$id.'.php')
                 || !is_writable(dirname(__FILE__).'/../conf/configweb_'.$id.'.php')) {

            $this->setError(sprintf( __('Error: The configuration file %s is not writeable.'),
                                     files::real_path(dirname(__FILE__).'/../conf/').'/configweb_'.$id.'.php'), 500);
            return false;
        }


        $delReq = 'DELETE FROM '.$this->con->pfx.'websites WHERE website_id =\''.$this->con->escapeStr($id).'\'';
        if (!$this->con->execute($delReq)) {
            $this->setError('MySQL : '.$this->con->error(), 500);
            return false;
        }
        $delReq = 'DELETE FROM '.$this->con->pfx.'grants WHERE website_id =\''.$this->con->escapeStr($id).'\'';
        if (!$this->con->execute($delReq)) {
            $this->setError('MySQL : '.$this->con->error(), 500);
            return false;
        }
        $delReq = 'DELETE FROM '.$this->con->pfx.'userprefs WHERE website_id =\''.$this->con->escapeStr($id).'\'';
        if (!$this->con->execute($delReq)) {
            $this->setError('MySQL : '.$this->con->error(), 500);
            return false;
        }
        @unlink(dirname(__FILE__).'/../conf/configweb_'.$id.'.php');
        return true;
    }


}
?>
