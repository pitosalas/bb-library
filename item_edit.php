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
// $Id: item_edit.php,v 1.3 2007/03/16 11:56:21 alg Exp $
//

require_once 'smarty.php';
require_once 'ajax.php';
require_once 'sites/config.php';

// Check necessary permissions to do actions or show pages
check_perm('edit_content');

$userId = $_SESSION['user_id'];
$iid = defGET('iid', 1);

$dm = new DataManager();
$item = $dm->getItemEditInfo($iid);
$my_folders = $dm->getMyFoldersViewInfo($userId, perm('edit_others_content'));
$itemTypes = $dm->getItemTypes();
$parent_id = 1;
if ($item->folders['ids'][0]) $parent_id = $item->folders['ids'][0];
$nav = $dm->getFolderNavigationSideblock($userId, $parent_id);
$user = $dm->getPersonInfo($userId);
$owner = $dm->getPersonInfo($item->owner_id);
$authors = $dm->getAllAuthors();
$dm->close();

// Get the list of my folders
$smarty->assign('my_folders', $my_folders);

$smarty->assign('item', $item);
$smarty->assign('shortcut_ids', $item->folders['ids']);
$smarty->assign('shortcut_nam', $item->folders['titles']);

$smarty->assign('itemTypes', $itemTypes);
$smarty->assign('itemType_id', $item->type_id);

$smarty->assign('nav', $nav);
$smarty->assign('user', $user);
$smarty->assign('owner', $owner);
$smarty->assign('authors', $authors);

$smarty->assign('discovery_enabled', BBS_ENABLED);
$smarty->assign('xajax_javascript', $xajax->getJavascript(BASE_URL.'/xajax'));
$smarty->assign('action', 'edit');
$smarty->assign('title', 'Edit Item');
$smarty->assign('infoblock', array('title' => 'Editing Item', 'description' => 'Please specify all the necessary information about your item.'));
$smarty->assign('content', 'item_form');
$smarty->assign('page', 'main');
$smarty->display('layout.tpl');

?>
