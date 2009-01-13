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
// $Id: opml_upload_form.php,v 1.3 2007/08/17 12:31:34 alg Exp $
//

require_once 'smarty_page.php';

// Check necessary permissions to do actions or show pages
check_perm('edit_content');

// This is from session
$uid = $_SESSION['user_id'];
$fid = defGET('fid', null);

if (isset($error)) $smarty->assign('error', $error);

$dm = new DataManager();
$my_folders = $dm->getMyFoldersViewInfo($uid, perm('edit_others_content'));
$nav = $dm->getFolderNavigationSideblock($uid, 1);
$dm->close();

// Count my-folders
$cnt = 0;
foreach ($my_folders as $fg) $cnt += count($fg);

$smarty->assign("folder_id", $fid ? $fid : -1);
$smarty->assign("my_folders", $my_folders);
$smarty->assign("my_folders_count", $cnt);
$smarty->assign("nav", $nav);

$smarty->assign("title", "Upload OPML");
$smarty->assign("infoblock", array('title' => "Uploading OPML", 'description' => "Please choose the folder to upload data in and OPML file."));
$smarty->assign("content", "opml_upload_form");
$smarty->assign("page", "main");
$smarty->display("layout.tpl");
?>
