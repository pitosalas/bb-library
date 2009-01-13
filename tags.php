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
// $Id: tags.php,v 1.1 2006/10/23 11:44:51 alg Exp $
//

require_once 'smarty_page.php';
require_once 'ajax.php';
require_once 'classes/TagsManager.class.php';

$dm = new DataManager();
$cmd = defPOST('cmd', defGET('cmd', ''));

if ($cmd == 'cloud')
{
	$smarty->assign('cloud', TagsManager::getTagsCloud());
	$smarty->assign('title', 'Tags');
	$smarty->assign('content', 'tags_cloud');
	$smarty->assign('login_redirect', smarty_url_tags($smarty));
} else if ($cmd == 'find')
{
	$objs = TagsManager::findObjectsByTag($_GET['tag']);

	$smarty->assign('tag', $_GET['tag']);
	$smarty->assign('folders', $objs['folders']);
	$smarty->assign('items', $objs['items']);
	$smarty->assign('people', $objs['people']);
	$smarty->assign('title', 'Tag');
	$smarty->assign('content', 'tag');	
	$smarty->assign('login_redirect', smarty_url_tags($smarty, $_GET['tag']));
} else
{
	// Check necessary permissions to do actions or show pages
	check_perm('manage_tags');

	$action = defPOST('action', defGET('action', ''));
	if ($action == 'delete')
	{
		$tags = $_POST['tag'];
		$dm->deleteTags($tags);
	} else if ($action == 'merge')
	{
		$tags = $_POST['tag'];
		$dm->mergeTags($tags);
	}
	
	$smarty->assign('xajax_javascript', $xajax->getJavascript(BASE_URL.'/xajax'));
	$smarty->assign('tags', $dm->getAllTagsWithCounters());

	$smarty->assign('title', 'Tags');
	$smarty->assign('content', 'tags');
}
$smarty->assign('nav', $dm->getFolderNavigationSideblock($uid, 1));
$dm->close();

$smarty->assign('page', 'main');
$smarty->display('layout.tpl');
?>
