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
// $Id: item_submit.php,v 1.9 2007/09/26 12:48:45 alg Exp $
//

require_once 'smarty.php';
require_once 'classes/RobotsTxt.class.php';

// Check necessary permissions to do actions or show pages
check_perm('edit_content');

// This info comes from the session
$lastFolderId = 1;
$userId = $_SESSION['user_id']; 

$action = defPOST('action', '');

// Collect information
$item = new Item();
$item->title = $_POST['title'];
$item->description = $_POST['description'];
$item->owner_id = $_POST['owner_id'];
$item->type_id = $_POST['itemType_id'];
$item->created = mktime();
$item->siteURL = $_POST['siteURL'];
$item->dataURL = $_POST['dataURL'];
$item->order = $_POST['order'];
$item->itunesURL = $_POST['itunesURL'];
$item->useITunesURL = isset($_POST['useITunesURL']);
$item->usePlayButtons = isset($_POST['usePlayButtons']);
$item->showPreview = isset($_POST['showPreview']);
$item->autoTags = isset($_POST['autoTags']);
$item->show_in_nav_bar = isset($_POST['show_in_nav_bar']) && (bool)$_POST['show_in_nav_bar'];

$shortcuts = null;
if (isset($_POST['shortcuts'])) $shortcut_ids = explode(',', $_POST['shortcuts']);
$tags = isset($_POST['tags']) ? $_POST['tags'] : '';

$dm = new DataManager();
if ($action == 'add')
{
    // Add the item to database
    $item = $dm->addItem($item, $shortcut_ids, $tags);
	
	RobotsTxt::flush();
} else if ($action == 'edit')
{
    // Submit changes
    $item->id = $_POST['iid'];
    $dm->updateItem($item, $shortcut_ids, $tags);
	
	RobotsTxt::flush();
}
$dm->close();

if ($action == 'add' && defPOST('addMore', '0') == 1)
{
	include('item_add.php');
} else
{
	$iid = $item->id;
	include('item_show.php');
}
?>