<?php
// BlogBridge Library
// Copyright (c) 2006 Salas Associates, Inc.  All Rights Reserved.
//
// Use, modification or copying prohibited unless appropriately licensed
// under an express agreement with Salas Associates, Inc.
//
// Contact: R. Pito Salas
// Mail To: support@blogbridge.com
//
// $Id: backup_restore.php,v 1.1 2006/10/23 11:44:51 alg Exp $
//

require_once 'classes/BackupsManager.class.php';
require_once 'smarty_page.php';

// Check necessary permissions to do actions or show pages
check_perm('do_backups_restores');

$action = defPOST('action', '');

$messages = array();
if ($action == 'backup')
{
	$messages = BackupsManager::backup();
} else if ($action == 'delete' && isset($_POST['backup']))
{
	$messages = BackupsManager::delete($_POST['backup']);
} else if ($action == 'restore')
{
	$messages = BackupsManager::restore($_POST['backup']);
}

if (isset($messages['message'])) $smarty->assign('message', $messages['message']);
if (isset($messages['error'])) $smarty->assign('error', $messages['error']);

$dm = new DataManager();
$smarty->assign('nav', $dm->getFolderNavigationSideblock($uid, 1));
$dm->close();

$dict = array();
$full = array();
list_backups($dict, $full);

$smarty->assign('can_backup', is_writable(BACKUPS_DIR) && is_file(MYSQL_DUMP));
$smarty->assign('backups', $full);
$smarty->assign('backups_dict', $dict);

$smarty->assign('title', 'Backup and Restore');
$smarty->assign('content', 'backup_restore');
$smarty->assign('page', 'main');
$smarty->display('layout.tpl');

/** Makes the list of backups to their sizes. */
function list_backups(&$dict, &$full)
{
	if ($dh = opendir(BACKUPS_DIR))
	{
		$times = array();
		while ($file = readdir($dh))
		{
			if ($file[0] == '.') continue;
			
			$y = substr($file, 0, 4);
			$m = substr($file, 5, 2);
			$d = substr($file, 8, 2);
			$h = substr($file, 11, 2);
			$i = substr($file, 14, 2);
			$s = substr($file, 17, 2);

			$t = mktime($h, $i, $s, $m, $d, $y);
			if ($t > 0) $times[$file] = $t;
		}
		
		arsort($times);
		
		foreach ($times as $file => $t)
		{
			$date = strftime('%b %d, %Y %H:%M:%S', $t);
			$size = filesize(BACKUPS_DIR . '/' . $file) / 1024;
			
			$dict[$file] = $date;
			$full[] = array('name' => $file, 'date' => $date, 'size' => $size);
		}
		
		closedir($dh);
	}
}
?>


