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

$m->l10n->loadPlugin($m->user->lang, 'smart404');
$is_log = (!empty($_REQUEST['op']) && $_REQUEST['op']=='log');
$env  = (!empty($_GET['env'])) ? $_GET['env'] : 1;
$con =& pxDBConnect();

/* ============================================================== *
 *              Functions needed for the lists                    *
 * ============================================================== */
require_once $_PX_config['manager_path'].'/extinc/class.lum.php';

function line_red($data, $i)
{
    global $_px_theme;
    $delete = sprintf('<a href="tools.php?p=smart404&amp;op=red&amp;del=%s" title="'.__('Remove').'" onclick="return window.confirm(\''. __('Are you sure you want to delete this redirection?').'\');"><img src="themes/'.$_px_theme.'/images/delete.png" alt="'.__('Remove').'"/></a>',
                      urlencode($data['old']));
    if (strlen($data['new']) > 50) {
        $data['new'] = '<span title="'.$data['new'].'">...'
            .substr($data['new'],-47).'</span>';
    }
    if (strlen($data['old']) > 50) {
        $data['old'] = '<span title="'.$data['old'].'">...'
            .substr($data['old'],-47).'</span>';
    }
    return '<tr><td>'.$data['old'].'</td><td>'.$data['new']
        .'</td><td>'.$data['last'].'</td><td>'.$data['total']
        .'</td><td>'.$delete.'</td></tr>'."\n";
}

function line_log_all($data, $i)
{
    global $_px_ptheme;
    $details = sprintf('<a href="tools.php?p=smart404&amp;op=log&amp;page=%s" title="'.__('View details').'"><img src="tools/smart404/themes/'.$_px_ptheme.'/details.png" alt="'.__('Details').'"/></a>',
                       urlencode($data['page']));
    if (strlen($data['page']) > 50) {
        $data['page'] = '<span title="'.$data['page'].'">...'
            .substr($data['page'],-47).'</span>';
    }
    return '<tr><td>'.$data['page'].'</td><td>'.$data['date']
        .'</td><td>'.$data['total'].'</td><td>'.$details.'</td></tr>'."\n";
}

function line_log_details($data, $i)
{
    if (strlen($data['from']) > 40) {
        $data['from'] = '<span title="'.$data['from'].'">'
            .substr($data['from'],0,37).'...</span>';
    } elseif (strlen($data['from']) == 0) {
        $data['from'] = '...';
    }
    if (strlen($data['useragent']) > 40) {
        $data['useragent'] = '<span title="'.$data['useragent'].'">'
            .substr($data['useragent'],0,37).'...</span>';
    }
    return '<tr><td>'.$data['date'].'</td><td>'.$data['from']
        .'</td><td>'.$data['useragent'].'</td></tr>'."\n";
}

/* ================================== *
 *          Process block             *
 * ================================== */
if ($is_log) {
    $px_submenu->addItem( __('Redirections'),'tools.php?p=smart404','',false);
    /* ========================================
     *   Get the log of errors or purge the logs
     * ========================================*/
    $is_page_view = false;
    if (!empty($_REQUEST['purge'])) {
        //the purge is simple to implement :)
        $sql = 'DELETE FROM '.$con->pfx.'smart404logs 
           WHERE website_id=\''.$_SESSION['website_id'].'\'';
        if (!$con->execute($sql)) {
            $m->setError('MySQL : '.$con->error(), 500);
        } else {
            $msg =  __('The error log of this website has been successfully purged.');
            header('Location: tools.php?p=smart404&op=red&msg='.urlencode($msg));
            exit;
        }
    } else {
        //2 cases for the logs:
        //  - default = list all the errors grouped by error pages 
        //    and with number of hits DESC for the order
        //  - page = list the details of a page (from, user agent) 
        //    order by date DESC
        $log_list = array();
        if (!empty($_REQUEST['page'])) {
            $is_page_view = true;
            $px_submenu->addItem(__('404 error log'), 
                                 'tools.php?p=smart404&amp;op=log',
                                 '', false);
            $sql = 'SELECT * FROM '.$con->pfx.'smart404logs 
              WHERE website_id=\''.$_SESSION['website_id'].'\'  AND
              page LIKE \''.$con->escapeStr($_REQUEST['page']).'\' 
              ORDER BY date DESC';
            if (false !== ($rs = $con->select($sql))) {
                $i = 0;
                while (!$rs->EOF()) {
                    $log_list[$i]['date'] = date(__('Y/m/d&\nb\sp;H:i:s'),
                                                 date::unix($rs->f('date')));
                    $log_list[$i]['from'] = $rs->f('fromurl');
                    $log_list[$i]['useragent'] = $rs->f('useragent');
                    $i++;
                    $rs->moveNext();
                }
            }
        } else {
            $sql = 'SELECT *, COUNT(*) AS total, MAX(date) AS lastdate 
              FROM '.$con->pfx.'smart404logs
              WHERE website_id=\''.$_SESSION['website_id'].'\' 
              GROUP BY page ORDER BY total DESC';
            if (false !== ($rs = $con->select($sql))) {
                $i = 0;
                while (!$rs->EOF()) {
                    $log_list[$i]['date'] = date(__('Y/m/d&\nb\sp;H:i:s'),
                                                 date::unix($rs->f('lastdate')));
                    $log_list[$i]['page'] = $rs->f('page');
                    $log_list[$i]['total'] = $rs->f('total');
                    $i++;
                    $rs->moveNext();
                }
            }
    
        }
    }
} else {
    $px_submenu->addItem( __('404 error log'),
                          'tools.php?p=smart404&amp;op=log',
                          '', false);
    /* ========================================
     *   Get the redirections or add/remove one
     * ========================================*/
    if (empty($_GET['del'])) {
        //get the redirections
        $red_list = array();
        $sql = 'SELECT * FROM '.$con->pfx.'smart404 
           WHERE website_id=\''.$_SESSION['website_id'].'\' 
           ORDER BY lastroutingdate DESC';
        if (false !== ($rs = $con->select($sql))) {
            $i = 0;
            while (!$rs->EOF()) {
                $red_list[$i]['old'] = $rs->f('oldpage');
                $red_list[$i]['new'] = $rs->f('newpage');
                $red_list[$i]['last'] = date(__('Y/m/d&\nb\sp;H:i:s'),
                                             date::unix($rs->f('lastroutingdate')));
                $red_list[$i]['total'] = $rs->f('total');
                $i++;
                $rs->moveNext();
            }
        }
    }

    if (!empty($_GET['del'])) {
        //Remove a redirection
        $sql = 'DELETE FROM '.$con->pfx.'smart404 
          WHERE website_id=\''.$_SESSION['website_id'].'\' 
          AND oldpage LIKE \''.$con->escapeStr($_GET['del']).'\' LIMIT 1';
        if (!$con->execute($sql)) {
            $m->setError('MySQL : '.$con->error(), 500);
        } else {
            $msg = __('The redirection has been deleted successfully.');
            header('Location: tools.php?p=smart404&op=red&msg='.urlencode($msg));
            exit;
        }
    }

    if (!empty($_POST['save'])) {
        //Add a redirection
        $px_old = (!empty($_POST['s_old'])) ? trim($_POST['s_old']) : '';
        $px_new = (!empty($_POST['s_new'])) ? trim($_POST['s_new']) : '';
        //Quick check
        $error = false;
        if (!preg_match('#^/#', $px_old)) {
            $m->setError(__('The old page must start with <strong>/</strong>.'), 400);
            $error = true;
        }
        if (!preg_match('#^http://#', $px_new)) {
            $m->setError(__('The new page must start with <strong>http://</strong>.'), 400);
            $error = true;
        }
        if (!$error) {
            $sql = 'INSERT INTO '.$con->pfx.'smart404 SET 
                website_id=\''.$_SESSION['website_id'].'\', 
                oldpage=\''.$con->escapeStr($px_old).'\',
                newpage=\''.$con->escapeStr($px_new).'\',
                lastroutingdate=\''.date::stamp().'\'';
            if (!$con->execute($sql)) {
                $m->setError('MySQL : '.$con->error(), 500);
            } else {
                $msg =  __('The redirection has been added successfully.');
                header('Location: tools.php?p=smart404&op=red&msg='.urlencode($msg));
                exit;
            }
        }
    }

}

/*==============================================================================
 Display block
==============================================================================*/
?>
<h1><?php  echo __('Smart 404 Errors'); ?></h1>

<?php 
if ($is_log):
    /* ========================================
     *     Display the log of errors
     * ========================================*/
    ?>
<h2><?php echo __('Log of the pages not found'); ?></h2>
<?php

if ($is_page_view) {
    echo '<p>'.sprintf(__('Detailled log for the page <em>%s</em>.'), htmlspecialchars($_REQUEST['page'])).'</p>'."\n";
    echo '<p>'.sprintf(__('<a href="%s">Create a redirection</a> to avoid this error.'), 'tools.php?p=smart404&amp;old='.urlencode($_REQUEST['page']).'#create').'</p>'."\n";
    $objLum = new lum($env, 'line_log_details', $log_list, 0, 20);
    $objLum->htmlHeader = '<table class="clean-table">'."\n".'<tr><th>'.__('Date').'</th><th>'.__('From').'</th><th>'.__('User Agent').'</th></tr>'."\n";
} else {
    echo '<p><a href="tools.php?p=smart404&amp;op=log&amp;purge=toilettes" '.
        'onclick="return window.confirm(\''. __('Are you sure you want to purge all the logs?').'\')">'. __('Purge the logs.').'</a></p>';

    $objLum = new lum($env, 'line_log_all', $log_list, 0, 20);
    $objLum->htmlHeader = '<table class="clean-table">'."\n".'<tr><th>'.__('Page').'</th><th>'.__('Date').'</th><th colspan="2">'.__('Count').'</th></tr>'."\n";
}
$objLum->htmlLineStart = '';
$objLum->htmlColStart = '';
$objLum->htmlColEnd = '';
$objLum->htmlLineEnd = '';
$objLum->htmlFooter = '</table>'."\n";

$objLum->htmlLinksStart = '<p class="small">';
$objLum->htmlLinksEnd = '</p>';

$objLum->htmlCurPgStart = '<strong>';
$objLum->htmlCurPgEnd = '</strong>';

$objLum->htmlPrev =  __('&laquo; previous page');
$objLum->htmlNext =  __('&raquo; next page');
$objLum->htmlPrevGrp = '...';
$objLum->htmlNextGrp = '...';

$objLum->htmlEmpty = '<p><strong>'. __('No errors.').'</strong></p>';
$objLum->htmlLinksLib =  __('page(s):');

echo $objLum->drawLinks();
echo $objLum->drawPage();
echo $objLum->drawLinks();

?>
<?php else:
/*========================================
 Display the redirections
========================================*/
$px_old = (empty($px_old) && !empty($_GET['old'])) ? $_GET['old'] : '/';
$px_new = (!empty($px_new)) ? $px_new : 'http://';

?>
<h2><?php echo __('Current redirections'); ?></h2>

<?php

$objLum = new lum($env, 'line_red', $red_list, 0, 20);

$objLum->htmlHeader = '<table class="clean-table">'."\n".'<tr><th>'.__('Old page').'</th><th>'.__('Redirection').'</th><th>'.__('Last').'</th><th colspan="2">'.__('Count').'</th></tr>'."\n";
$objLum->htmlLineStart = '';
$objLum->htmlColStart = '';
$objLum->htmlColEnd = '';
$objLum->htmlLineEnd = '';
$objLum->htmlFooter = '</table>'."\n";

$objLum->htmlLinksStart = '<p class="small">';
$objLum->htmlLinksEnd = '</p>';

$objLum->htmlCurPgStart = '<strong>';
$objLum->htmlCurPgEnd = '</strong>';

$objLum->htmlPrev =  __('&laquo; previous page');
$objLum->htmlNext =  __('&raquo; next page');
$objLum->htmlPrevGrp = '...';
$objLum->htmlNextGrp = '...';

$objLum->htmlEmpty = '<p><strong>'. __('No redirection for the moment.').'</strong></p>';
$objLum->htmlLinksLib =  __('page(s):');

echo $objLum->drawLinks();
echo $objLum->drawPage();
echo $objLum->drawLinks();


?>

<h2 id="create"><?php echo __('Create a redirection'); ?></h2>

<form action="tools.php" method="post" id="formPost">
<p class="field"><label class="nowrap" for="s_old" style="display:inline"><strong><?php  echo __('Old page:'); ?></strong></label>
<?php  echo form::textField('s_old', 50, 200, $px_old, '', ''); ?><br />
<?php  echo __('The old page starts with <strong>/</strong> like <strong>/folder/oldpage.html</strong>'); ?>
</p>
<p class="field"><label class="nowrap" for="s_new" style="display:inline"><strong><?php  echo __('New page:'); ?></strong></label>
<?php  echo form::textField('s_new', 50, 200, $px_new, '', ''); ?><br />
<?php  echo __('Provide the page where you want to send people looking for the old page.'); ?>
</p>

<p class="button">
<?php echo form::hidden('p','smart404');  ?>
<?php echo form::hidden('op','red');  ?>
<input name="save" type="submit" class="submit" value="<?php  echo __('Create the redirection'); ?>" accesskey="s" />
</p>
</form>


<?php endif; ?>