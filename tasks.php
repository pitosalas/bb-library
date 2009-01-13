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
// $Id: tasks.php,v 1.1 2006/10/23 11:44:51 alg Exp $
//

require_once 'classes/TasksManager.class.php';
require_once 'smarty_page.php';

// Check necessary permissions to do actions or show pages
check_perm('manage_tasks');

$run = defGET('run', '');
if ($run != '')
{
	TasksManager::runTaskByName($run);
}

if (count($_POST) > 0)
{
	TasksManager::updateTasks($_POST);
}

$dm = new DataManager();
$smarty->assign('nav', $dm->getFolderNavigationSideblock($uid, 1));
$dm->close();

$smarty->assign('title', 'Tasks');
$smarty->assign('content', 'tasks');
$smarty->assign('pulse_link', BASE_URL . '/pulse');
$smarty->assign('tasks', TasksManager::getTasks());
$smarty->assign('periods', TasksManager::getTaskPeriods());
$smarty->assign('page', 'main');
$smarty->display('layout.tpl');

?>
