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
// $Id: folder_submit.php,v 1.7 2007/09/21 14:42:33 alg Exp $
//

require_once 'smarty.php';
require_once 'classes/RobotsTxt.class.php';

// Check necessary permissions to do actions or show pages
check_perm('edit_content');

// This info comes from the session
$lastFolderId = 1;
$userId = $_SESSION['user_id']; 

$action = defPOST('action', 'form');
$add_more = defPOST('addMore', '0');

// Collect information
$folder = new Folder();
$folder->title = $_POST['title'];
$folder->description = $_POST['description'];
$folder->viewType_id = $_POST['viewType_id'];
$folder->owner_id = $_POST['owner_id'];
$folder->created = mktime();
$folder->opml_url = trim($_POST['opmlURL']);
$folder->opml_user = $_POST['opmlUser'];
$folder->opml_password = $_POST['opmlPassword'];
$folder->order = $_POST['order'];
$folder->autoTags = isset($_POST['autoTags']) && (bool)$_POST['autoTags'];
$folder->show_in_nav_bar = isset($_POST['show_in_nav_bar']) && (bool)$_POST['show_in_nav_bar'];

$shortcut_ids = null;
if (isset($_POST['shortcuts']) && strlen($_POST['shortcuts']) > 0) $shortcut_ids = explode(',', $_POST['shortcuts']);
$tags = isset($_POST['tags']) ? $_POST['tags'] : '';

$update_opml_folder = false;
$dm = new DataManager();
if ($action == 'add')
{
    // Add the folder to database
    $folder = $dm->addFolder($folder, $shortcut_ids, $tags);
    $fid = $folder->id;
	$update_opml_folder = $folder->is_opml_folder();
	
	RobotsTxt::flush();
} else if ($action == 'edit')
{
    // Submit changes
    $folder->id = $_POST['fid'];
	$old_folder = $dm->getFolderViewInfo($folder->id);
    $dm->updateFolder($folder, $shortcut_ids, $tags);
    $fid = $folder->id;

	if ($folder->is_opml_folder())
	{
		if (!$old_folder->is_opml_folder() || $folder->opml_url != $old_folder->opml_url)
		{
			$dm->deleteFolderContents($fid);
			$update_opml_folder = true;
		}
	} else
	{
		if ($old_folder->is_opml_folder())
		{
			$dm->deleteFolderContents($fid);
		}
	}
	
	RobotsTxt::flush();
}
$dm->close();

if ($update_opml_folder)
{
	// Fork new update process
	require_once 'classes/Pinger.class.php';
	Pinger::ping_link(BASE_URL . '/opml_ping?fid=' . $folder->id);
	$fid = $folder->id;
}

if ($action == 'add' && $add_more == 1)
{
	include('folder_add.php');
} else
{
	include('folder_show.php');
}
?>