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
// $Id: folder_js.php,v 1.1 2006/10/23 11:44:51 alg Exp $
//

require_once 'classes/DataManager.class.php';
require_once 'functions.php';

if (isset($_GET['fid']))
{
	$sort = getSort();
	$order = getOrder();
	$limit = getLimit();
	$showauthor = defGET('showauthor', '0');
	$textRLLink = defGET('textRLLink', '0');
	$newDuring = defGET('newDuring', '0');
	
	// Shallow means there will be no sub-folders, but links to embedded lists only
	$shallow = isset($_GET['shallow']) && (bool)$_GET['shallow'];
	
    $dm = new DataManager();
    $items = $dm->getFolderJS($_GET['fid'], $sort, $order, $limit);
    $dm->close();

	$now = mktime();
	$newAfter = $now - $newDuring * 3600 * 24;
	
	outputItemsJS($items, $showauthor, $textRLLink, $newAfter);    
}

/** Returns valid sort order. */
function getSort()
{
	$sort = defGET('sort', 'title');
	if ($sort != 'title' && $sort != 'date') $sort = 'title';
	if ($sort == 'date') $sort = 'created';
	
	return $sort;
}

/** Returns sorting direction. */
function getOrder()
{
	$order = defGET('order', 'asc');
	if ($order != 'asc' && $order != 'desc') $order = 'asc';
	
	return $order;
}

/** Returns maximum number of items to output. */
function getLimit()
{
	$limit = defGET('limit', '-1');
	if (!is_numeric($limit)) $limit = -1;
	
	return $limit;
}

/**
 * Outputs items.
 */
function outputItemsJS($items, $showauthor, $textRLLink, $newAfter)
{
	$out = '<ul>';
    foreach ($items as $item) $out .= outputItemJS($item, $showauthor, $textRLLink, $newAfter);
	$out .= '</ul>';
	
	echo 'document.write(\''. addslashes($out) . '\');';
}

/**
 * Outputs single item.
 */
function outputItemJS($item, $showauthor, $textRLLink, $newAfter)
{
	$title = $item['title'];
	$url = $item['url'];
	$xmlUrl = $item['xmlUrl'];
	$text = $item['description'];
	$owner = $item['author'];
	$date = $item['created'];
	
	$out = '<li>';
	$out .= '<a class=\'title\' href=\'' . $url . '\' title=\''.escape4JS($text).'\'>' . escape4JS($title) . '</a>';
	$out .= '<a href=\''.$xmlUrl.'\'>';
	
	// Reading List link
	if ($textRLLink == 1)
	{
		$out .= '[RL]';
	} else
	{
		$out .= '<img src=\''.IMAGES_URL.'/spacer.gif\' border="0">';
	}
	$out .= '</a>';

	// "New" sign
	if ($date > $newAfter)
	{
		$out .= '<img src=\''.IMAGES_URL.'/spacer.gif\' border="0" class="new">';
	}

	if ($showauthor) $out .= '<div class=\'author\'>by <span class=\'name\'>'.escape4JS($owner).'</span></div>';
	$out .= '</li>';
	
	return $out;
}
?>
