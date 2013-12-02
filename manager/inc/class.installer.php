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

define('PX_TEST_GOOD', 1);
define('PX_TEST_WARNING', 2);
define('PX_TEST_BAD', 3);

/**
 * Class managing the installation process.
 *
 * 
 */
class Installer extends CError
{
    var $pages = array(); /**< Pages in the installation. */
    var $page_order = array(); /**< Order in the pages. */
    var $current_page = '';
    var $validated_steps = array(); /**< Steps that went through. */
    var $conf_exists = false; /**< Does the config.php exists. */
    var $answers = array(); /**< The answers to the questions. */
    var $tests = array(); /**< Results of the tests. */

    /**
     * Constructor.
     */
    function Installer()
    {
        $this->loadConfigFile();
        $this->load();
    }

    /**
     * Save the answers in the session.
     * 
     * @return bool Success
     */
    function save()
    {
        $_SESSION['install_ans'] = $this->answers;
        $_SESSION['install_tests'] = $this->tests;
        return true;
    }

    /**
     * Load the answers from the session.
     *
     * @return bool Success
     */
    function load()
    {
        if (isset($_SESSION['install_ans'])) {
            $this->answers = $_SESSION['install_ans'];
        }
        if (isset($_SESSION['install_tests'])) {
            $this->tests = $_SESSION['install_tests'];
        }
        return true;
    }


    /**
     * Add a page in the installer.
     *
     * Set the cursor to the current page. 
     *
     * @param string Id of the page. It must be unique
     * @param string Title of the page ('')
     * @param string Description of the page ('')
     * @return bool Success
     */
    function addPage($id, $title='', $desc='')
    {
        if (isset($this->pages[$id])) {
            $this->setError(sprintf(__('The page %s already exists in the installer'), $id), 500);
            return false;
        }
        $this->page_order[] = $id;
        $this->current_page = $id;
        
        $this->pages[$id]['title'] = $title;
        $this->pages[$id]['description'] = $description;
        
        return true;
    }

    /**
     * Add a question to the current page.
     *
     * @param string Id of the question. Must be unique.
     * @param string The question
     * @param string Type of answer ('text'), 'combobox', 'password', 
     *                              'textarea'
     * @param mixed Values for answers, combobox values with default choice
     *              or simple text string ('').
     * @param string Help id, to display corresponding help ('')
     * @return bool Success
     */
    function addQuestion($id, $question, $type='text', $answer='', $helpid='')
    {
        $this->pages[$this->current_page]['q'][] = array($id, $question, $type,
                                                         $answer, $helpid);
        return true;
    }


    /**
     * Add a test to the current page.
     *
     * To validate a page, all the tests of the page are run. A test has
     * 3 possible results success, failure or warning.
     *
     * The test function is used to run tests but also to perform installation
     * action. The test is not "run" at the time the test is added, but when
     * when the runPageTests() or runAllTests() are called.
     * A test function must have this declaration:
     * array(test result, message) = func(&$installer instance, $messageok,
     *                                    $messagebad, $messagewrning)
     * Through the Installer instance a test function can access the answers
     * to all the questions given during the installation procedure.
     *
     * @param string Test id. Must be unique.
     * @param string Test function to call.
     * @param string Good message ('')
     * @param string Bad message ('')
     * @param string Warning message ('')
     * @return bool Success of the test addition
     */
    function addTest($id, $func, $good='', $bad='', $warn='')
    {
        if (isset($this->tests[$id])) {
            $this->setError(sprintf(__('The test %s is already defined. Each test id must be unique.'), $id), 500);
            return false;
        }
        if (!function_exists($func)) {
            $this->setError(sprintf(__('The test function %s is not defined.'), 
                                    $func), 500);
            return false;

        }
        $this->pages[$this->current_page]['t'][] 
            =  array($id, $func, $good, $bad, $warn);
        $this->tests[$id] = array(null, ''); // test not run yet
        return true;
    }

    /**
     * Run the tests of a given page.
     *
     * @param string Id of the page
     * @param int Return false on warning (PX_TEST_WARNING) or only errors 
     *            PX_TEST_BAD
     * @return bool Success
     */
    function runPageTests($pageid, $level=PX_TEST_WARNING)
    {
        if (!isset($this->pages[$pageid]) 
            || !isset($this->pages[$pageid]['t'])) {
            $this->setError(sprintf(__('The page %s is not available.'), $pageid), 500);
        }
        $success = true;
        foreach ($this->pages[$pageid]['t'] as $test) {
            // Here $test[1] is a function that is called
            $res = $test[1]($this, $test[2], $test[3], $test[4]);
            if ($res[0] >= $level) {
                $success = false;
            }
            $this->tests[$test[0]] = $res;
        }
        return $success;
    }


    /**
     * Get percentage done.
     *
     * @return int Percentage of the steps done.
     */
    function getPercentage()
    {
        $n = count($this->pages);
        for ($i=0; $i<$n; $i++) {
            if ($this->page_order[$i] == $this->current_step) {
                return (int) ($i / $n);
            }
        }
        return 0;
    }

    /**
     * Get next page id.
     *
     * @return mixed Id of the next page or false 
     */
    function getNextPage()
    {
        $n = count($this->pages);
        for ($i=0; $i<$n; $i++) {
            if ($this->page_order[$i] == $this->current_step) {
                if (isset($this->page_order[$i+1])) {
                    return $this->page_order[$i+1];
                }
            }
        }
        return false;
    }

    /**
     * Get previous page id.
     *
     * @return mixed Id of the previous page or false
     */
    function getPrevPage()
    {
        $n = count($this->pages);
        for ($i=0; $i<$n; $i++) {
            if ($this->page_order[$i] == $this->current_step) {
                if ($i>1) {
                    return $this->page_order[$i-1];
                }
            }
        }
        return false;
    }

    /**
     * Get an answer.
     *
     * @param string What to get
     * @return string The answer
     */
    function getAns($what)
    {
        if (isset($this->answers[$what])) {
            return $this->answers[$what];
        }
        return '';
    }

    /**
     * Set an answer.
     *
     * @param string What to set
     * @param string The answer
     */
    function setAns($what, $ans)
    {
        $this->answers[$what] = $ans;
    }

    /**
     * Load config file if exists.
     *
     * @return bool Success to load it.
     */
    function loadConfigFile()
    {
        if (file_exists(config::f('manager_path').'/conf/config.php')) {
            include_once config::f('manager_path').'/conf/config.php';
            $this->conf_exists = true;
            return true;
        } else {
            $this->conf_exists = false;
            return false;
        }
    }

    /**
     * Return the HTML of the questions.
     *
     * @param string Page id
     * @return string HTML of the page content
     */
    function getPageQuestions($id)
    {
        if (!isset($this->pages[$id])) {
            $this->setError(sprintf(__('The page %s is not available.'), $id), 500);
            return '';
        }
        $html = '';
        foreach ($this->pages[$id]['q'] as $q) {
            $html .= $this->getQuestionHtml($q);
        }
        return $html;
    }

    /**
     * Return the HTML of a question
     *
     * @param array Definition of the question
     * @return string HTML of the question
     */
    function getQuestionHtml($q)
    {
        $id = $q[0];
        $question = $q[1];
        $type = $q[2];
        $answer = $q[3];

        $html = '<p><label for="'.$id.'">'.$question.'</label> ';
        switch ($type) {
        case 'combobox':
            $html .= form::combobox($id, $answer['choices'], 
                                    $this->getAns($id), $answer['default']);
            break;
        case 'textarea':
            $html .= form::textArea($id, 60, 5, $this->getAns($id), '',
                                    'style="width:100%"');
            break;
        case 'password':
            $html .= form::passwordField($id, 50, 255, $this->getAns($id));
            $html .= '</p><p><label for="'.$id.'-confirm">'
                .__('Please confirm the password:').'</label> ';
            $html .= form::passwordField($id.'-confirm', 50, 255, 
                                         $this->getAns($id.'-confirm'));
            break;
        case 'text':
        default:
            $html .= form::textField($id, 50, 255, $this->getAns($id));
        }
        $html .= '</p>'."\n\n";
        return $html;
    }

}

/**
 * Ce que doit faire l'installer.
 * - stocker les reponses de l'utilisateur
 * - trouver/demander la langue de l'utilisateur
 * - pouvoir se "serialiser" dans une session
 * - collecte et contrôle des informations dans tous les étapes
 * - écriture réelle uniquement à la fin avec possibilité de répéter une
 * étape foireuse
 * - explication avan et après de ce qui va se faire
 * - possibilité de retoure en arrière et de passage d'une étape à l'autre.
 * - affichage des étapes dans un "menu" sur le côté
 * - possibilités d'un scénario selon l'hébergeur ?? (depuis fichier xml)
 * 
 */
/*
- Pour chaque page les boutons [<<Previous] [Next>>]
- Stockage des infos via des champs "hidden" pour éviter les problèmes
avec les sessions (free.fr)
- On garde les différentes pages .php, ce n'est pas un problème


*/
?>
