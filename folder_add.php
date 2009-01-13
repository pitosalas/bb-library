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
// $Id: folder_add.php,v 1.3 2007/04/27 13:56:45 alg Exp $
//

require_once 'smarty.php';

// Check necessary permissions to do actions or show pages
check_perm('edit_content');

$userId = $_SESSION['user_id']; 
$fid = defGET('fid', 1);

$dm = new DataManager();
$folder = $dm->getFolderViewInfo($fid);
$viewTypes = $dm->getViewTypes();
$my_folders = $dm->getMyFoldersViewInfo($userId, perm('edit_others_content'));
$nav = $dm->getFolderNavigationSideblock($userId, $fid);
$user = $dm->getPersonInfo($userId);
$authors = $dm->getAllAuthors();
$dm->close();

if (!isset($shortcut_ids))
{
	$shortcut_ids = array($folder->id);
	$shortcut_nam = array($folder->title);
} else $shortcut_nam = array();

$smarty->assign('my_folders', $my_folders);
$smarty->assign('shortcut_ids', $shortcut_ids);
$smarty->assign('shortcut_nam', $shortcut_nam);

// Prepare view types
$smarty->assign('viewTypes', $viewTypes);
$smarty->assign('viewType_id', -1);

$smarty->assign('nav', $nav);
$smarty->assign('user', $user);
$smarty->assign('owner', $user);
$smarty->assign('authors', $authors);

$smarty->assign('action', 'add');
$smarty->assign('title', 'Add Folder');
$smarty->assign('infoblock', array('title' => 'Adding Folder', 'description' => 'Please specify all the necessary information about your new folder.'));
$smarty->assign('content', 'folder_form');
$smarty->assign('page', 'main');
$smarty->display('layout.tpl');
?>