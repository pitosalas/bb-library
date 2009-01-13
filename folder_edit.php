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
// $Id: folder_edit.php,v 1.4 2007/04/03 08:19:08 alg Exp $
//

require_once 'smarty.php';

// Check necessary permissions to do actions or show pages
check_perm('edit_content');

$userId = $_SESSION['user_id']; 

if (isset($_GET['fid']))
{
    $fid = $_GET['fid'];

    $dm = new DataManager();

    $folder = $dm->getFolderEditInfo($fid);
    $viewTypes = $dm->getViewTypes();
    $nav = $dm->getFolderNavigationSideblock($userId, $fid);
    $user = $dm->getPersonInfo($userId);
    $owner = $dm->getPersonInfo($folder->owner_id);
    $authors = $dm->getAllAuthors();
    
    // Root folder can't have any parents
    if ($fid != 1)
    {
        // Get the list of my folders
        $perm_edit_others = perm('edit_others_content');
        $my_folders = $dm->getMyFoldersViewInfo($userId, $perm_edit_others);
        unset_folder($my_folders, $fid);
        foreach ($folder->directChildren as $id) unset_folder($my_folders, $id);
        $smarty->assign('my_folders', $my_folders);

		$assignable_folders = $my_folders[MY_FOLDERS];
		if ($perm_edit_others && isset($my_folders[OTHERS_FOLDERS]))
		{
			$assignable_folders = $assignable_folders + $my_folders[OTHERS_FOLDERS];
		}
		
        // Prepare shortcut ids
        $shortcut_ids = array();
        $shortcut_nam = array();
        $shortcuts = $dm->getFolderShortcutParents($fid);
        foreach ($shortcuts as $shortcut)
        {
        	$shortcut_ids[] = $shortcut->id;
        	$shortcut_nam[] = $shortcut->title;
        }
        $smarty->assign('shortcut_ids', $shortcut_ids);
        $smarty->assign('shortcut_nam', $shortcut_nam);
    }
    
    $dm->close();

    $smarty->assign('viewTypes', $viewTypes);
    $smarty->assign('viewType_id', $folder->viewType_id);
        
    $smarty->assign('action', 'edit');
    $smarty->assign('folder', $folder);
    $smarty->assign('nav', $nav);
    $smarty->assign('user', $user);
    $smarty->assign('owner', $owner);
    $smarty->assign('authors', $authors);
    
    $smarty->assign('title', 'Edit Folder');
    $smarty->assign('infoblock', array('title' => 'Editing Folder', 'description' => 'Please update the information and confirm.'));
    $smarty->assign('content', 'folder_form');
    $smarty->assign('page', 'main');
    $smarty->display('layout.tpl');
} else
{
	$fid = 1;
	include('folder_show.php');
}
?>