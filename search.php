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
// $Id: search.php,v 1.2 2006/11/13 11:09:21 alg Exp $
//

require_once 'smarty_page.php';
require_once 'classes/SearchManager.class.php';

$search = defPOST('search', '');
$advanced = (bool) defPOST('advanced', false);

// When searching from the quick form the defaults are 'true'
$def = !$advanced;

// Read parameters
$typeFeeds = (bool) defPOST('typeFeeds', $def);
$typeFolders = (bool) defPOST('typeFolders', $def);
$typePeople = (bool) defPOST('typePeople', $def) && perm('manage_users');

$zoneTitle = (bool) defPOST('zoneTitle', $def);
$zoneDescription = (bool) defPOST('zoneDescription', $def);
$zoneTags = (bool) defPOST('zoneTags', $def);
$zoneSiteURL = (bool) defPOST('zoneSiteURL', $def);
$zoneDataURL = (bool) defPOST('zoneDataURL', $def);

if (strlen(trim($search)) > 0)
{
    // Perform search
    // If at least one type and one zone are specified, do search
    if (($typeFeeds || $typeFolders || $typePeople) && 
        ($zoneTitle || $zoneDescription || $zoneTags || $zoneSiteURL || $zoneDataURL))
    {
        $results = SearchManager::search($search, $typeFeeds, $typeFolders, $typePeople, 
            $zoneTitle, $zoneDescription, $zoneTags, $zoneSiteURL, $zoneDataURL);
    } else $results = array();
    
    $smarty->assign_by_ref('results', $results);
}

$dm = new DataManager();
$smarty->assign('nav', $dm->getFolderNavigationSideblock($uid, 1));
$dm->close();

$smarty->assign('search', $search);
$smarty->assign('typeFeeds', $typeFeeds);
$smarty->assign('typeFolders', $typeFolders);
$smarty->assign('typePeople', $typePeople);
$smarty->assign('zoneTitle', $zoneTitle);
$smarty->assign('zoneDescription', $zoneDescription);
$smarty->assign('zoneTags', $zoneTags);
$smarty->assign('zoneSiteURL', $zoneSiteURL);
$smarty->assign('zoneDataURL', $zoneDataURL);

$smarty->assign('content', 'search');
$smarty->assign('title', 'Search');
$smarty->assign('login_redirect', smarty_function_url(array('page' => 'search'), $smarty));

$smarty->assign('page', 'main');
$smarty->display('layout.tpl');

?>