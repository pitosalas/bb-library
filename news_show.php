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
// $Id: news_show.php,v 1.2 2007/07/26 11:36:40 alg Exp $
//

require_once 'smarty_page.php';

// News item ID
$niid = defGET('niid', null);
$action = defGET('action', null);

$dm = new DataManager();

if ($action == 'edt_nitem' && $niid)
{
	// Check necessary permissions to do actions or show pages
	check_perm('manage_news');

	// Editing item
	$item = $dm->getNewsItem($niid);
	
	$smarty->assign('news_item', $item);
	$smarty->assign('content', 'news_form');
	
	$smarty->assign('infoblock', array(
		'title' => 'Editing Item: ' . $item['title'], 
		'description' => 'Please specify all the necessary information about your item.'));
	$smarty->assign('action', 'update');
} else if ($action == 'add_nitem')
{
	// Check necessary permissions to do actions or show pages
	check_perm('manage_news');

	// Adding new item
	$smarty->assign('content', 'news_form');
	
	$smarty->assign('infoblock', array(
		'title' => 'Adding News Item', 
		'description' => 'Please specify all the necessary information about your item.'));
	$smarty->assign('action', 'add');
} else
{
	// Deleting item
	if ($action == 'del_nitem' && $niid)
	{
		if (perm('manage_news')) $dm->deleteNewsItem($niid);
		$niid = null;
	}

	if ($niid)
	{
		$smarty->assign('news_item', $dm->getNewsItem($niid));
		$smarty->assign('content', 'news');
		$smarty->assign('login_redirect', smarty_url_news($smarty, $niid));
	} else
	{
		$news = $dm->getNewsItems(!perm('manage_news'));
		$smarty->assign('news_items', $news);
		$smarty->assign('content', 'news_list');
		$smarty->assign('login_redirect', smarty_url_news($smarty));
	}
}

$smarty->assign('nav', $dm->getFolderNavigationSideblock($uid, 1));
$smarty->assign('title', 'News');

$dm->close();

$smarty->assign('page', 'main');
$smarty->display('layout.tpl');
?>