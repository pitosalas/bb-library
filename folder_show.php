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
// $Id: folder_show.php,v 1.5 2007/08/23 17:30:48 alg Exp $
//

require_once 'classes/DataManager.class.php';
require_once 'classes/MetaTags.class.php';
require_once 'classes/Generator.class.php';
require_once 'smarty_page.php';

// Folder ID
if (!isset($fid)) $fid = defGET('fid', 1);

$dm = new DataManager();

$folder = $dm->getFolderViewInfo($fid);
if ($folder)
{
    require_once 'ajax.php';

    $pid = $folder->owner_id;
    $owner = $dm->getOwnerSideblock($pid);
    
    if (trim($folder->description) == '' && $dm->_is_generate_tags_and_descriptions())
    {
    	$folder->description = Generator::folder_description($folder->title, $owner->fullName, $owner->tags);
    }
    
	MetaTags::set_folder_tags($smarty, $folder, $owner);

    $smarty->assign('folder', $folder);
    $smarty->assign('nav', $dm->getFolderNavigationSideblock($uid, $fid));
    $smarty->assign('owner', $owner);
    $smarty->assign('path', $dm->getPath($fid));
    
    if (isset($user))
    {
	    $smarty->assign('recommendations',
	    	$dm->getRecommendations($user->organization_id));	    
    }
    
    $smarty->assign('viewTypes', $dm->getViewTypes());
    $smarty->assign('viewType', $folder->viewType_id);

    $smarty->assign('title', $folder->title);
    $smarty->assign('content', 'folder_view');
    $smarty->assign('xajax_javascript', $xajax->getJavascript(BASE_URL.'/xajax'));

	// News
	if ($folder->id == 1)
	{
		// Root folder -- we need news
		$smarty->assign('news_items', $dm->getNewsItems(true, (int) $app_props['news_box_items']));
	}
	
	// Login redirect
	$smarty->assign('login_redirect', smarty_url_folder($folder, $smarty));	
} else
{
    $smarty->assign('nav', $dm->getFolderNavigationSideblock($uid, 1));
    $smarty->assign('title', 'Folder Not Found');
    $smarty->assign('content', 'folder_not_found');
}

$dm->close();

$smarty->assign('page', 'main');
$smarty->display('layout.tpl');
?>
