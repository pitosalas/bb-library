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
// $Id: item_add.php,v 1.4 2007/04/27 13:56:45 alg Exp $
//

require_once 'smarty.php';
require_once 'ajax.php';
require_once 'sites/config.php';

// Check necessary permissions to do actions or show pages
check_perm('edit_content');

$userId = $_SESSION['user_id'];
$fid = defGET('fid', 1);

$dm = new DataManager();
$folder = $dm->getFolderViewInfo($fid);
$itemTypes = $dm->getItemTypes();
$my_folders = $dm->getMyFoldersViewInfo($userId, perm('edit_others_content'));
$user = $dm->getPersonInfo($userId);
$nav = $dm->getFolderNavigationSideblock($userId, $fid);
$authors = $dm->getAllAuthors();
$dm->close();

// Get the list of my folders
$smarty->assign("my_folders", $my_folders);

if (!isset($shortcut_ids))
{
	$shortcut_ids = array($folder->id);
	$shortcut_nam = array($folder->title);
} else $shortcut_nam = array();

$smarty->assign('folder', $folder);
$smarty->assign('shortcut_ids', $shortcut_ids);
$smarty->assign('shortcut_nam', $shortcut_nam);

$smarty->assign('nav', $nav);
$smarty->assign('user', $user);
$smarty->assign('owner', $user);
$smarty->assign('authors', $authors);

$smarty->assign('itemTypes', $itemTypes);
$smarty->assign('itemType_id', -1);

$smarty->assign('discovery_enabled', BBS_ENABLED);
$smarty->assign('xajax_javascript', $xajax->getJavascript(BASE_URL.'/xajax'));
$smarty->assign('action', 'add');
$smarty->assign('title', 'Add Item');
$smarty->assign('infoblock', array('title' => "Adding Item", 'description' => "Please specify all the necessary information about your new item."));
$smarty->assign('content', 'item_form');
$smarty->assign('page', 'main');
$smarty->display('layout.tpl');

?>
