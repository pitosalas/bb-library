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
// $Id: ajax.server.php,v 1.4 2007/08/23 17:04:11 alg Exp $
//

/**
 * Loads the contents of some folder (sub-folders, items) and outputs them as HTML into
 * proper page node specified by GUID. 
 */
function loadFolderTreeItems($fid, $guid)
{
    require_once 'classes/DataManager.class.php';
    require_once 'session.php';

    $dm = new DataManager();
    $folder = $dm->getFolderTreeInfo($fid);
    $app_props = $dm->getApplicationProperties();
    $dm->close();
    
    $resp = new xajaxResponse();

    if ($folder)
    {
        require_once 'smarty.php';
        
        $html = _folders_tree($folder, perm('edit_content'),
        	$_SESSION['user_id'], $smarty, false, perm('edit_others_content'),
        	$app_props['direct_feed_urls']);
        
        $resp->addAssign('f' . $guid, 'innerHTML', $html);
    } else
    {
        $resp->addClear('f' . $guid, 'innerHTML');
    }
    
    return $resp;
}

/**
 * Adds bookmark for the folder to the list of current user.
 */
function addBookmark($fid)
{
    require_once 'classes/DataManager.class.php';
    require_once 'session.php';

	$uid = $_SESSION['user_id'];
	
    $dm = new DataManager();
    $dm->addBookmark($uid, $fid);
    $bookmarks = $dm->getBookmarks($uid);
    $dm->close();
    
    require_once 'smarty.php';
	$smarty->assign('bookmarks', $bookmarks);
	$out = $smarty->fetch('inclusions/bookmarks.tpl');
	
    $resp = new xajaxResponse();
    $resp->addAssign('bookmarks', 'innerHTML', $out);
    
    return $resp;
}

/**
 * Removes a bookmark.
 */
function removeBookmark($fid)
{
    require_once 'classes/DataManager.class.php';
    require_once 'session.php';

	$uid = $_SESSION['user_id'];
	
    $dm = new DataManager();
    $dm->removeBookmark($uid, $fid);
    $bookmarks = $dm->getBookmarks($uid);
    $dm->close();
    
    require_once 'smarty.php';
	$smarty->assign('bookmarks', $bookmarks);
	$out = $smarty->fetch('inclusions/bookmarks.tpl');
	
    $resp = new xajaxResponse();
    $resp->addAssign('bookmarks', 'innerHTML', $out);
    
    return $resp;
}

/**
 * Renames the tag specified by ID into something.
 */
function renameTag($tid, $name)
{
	require_once 'classes/DataManager.class.php';
	
	$dm = new DataManager();
	$merged = $dm->renameTag($tid, $name);
	$dm->close();
	
	$resp = new xajaxResponse();
	if ($merged) $resp->addRedirect(BASE_URL . '/tags.php');
	return $resp;
}

/**
 * Returns the preview of a feed.
 */
function getFeedPreview($iid, $div_id, $reload, $collapsed = 1)
{
	// Reading item info
	require_once 'classes/DataManager.class.php';
	$dm = new DataManager();
	$item = $dm->getItemViewInfo($iid);
	$dm->close();
	
	// Fetching
	$feed = null;
	if ($item != null && $item->dataURL != null && trim($item->dataURL) != '')
	{
		require_once 'classes/FeedParser.class.php';
		$feed = FeedParser::parse($item->dataURL, (bool)$reload);
	}
	
	// Output
	require_once 'smarty.php';
	if ($feed != null) $smarty->assign('feed', $feed);
	$smarty->assign('collapsed', $collapsed);
	$smarty->assign('usePlayButtons', (bool)$item->usePlayButtons);
	$out = $smarty->fetch('feed_preview.tpl');

    $resp = new xajaxResponse();
    $resp->addAssign($div_id, 'innerHTML', $out);
    $resp->addScript('install_click_handlers();');
    
    return $resp;
}

/** Discovers blog using BB Service. */
function discoverBlog($url, $status_id, $title_id, $descr_id, $siteUrl_id, $xmlUrl_id)
{
    $resp = new xajaxResponse();

	require_once 'classes/BBService.class.php';
	$reply = BBService::discover($url);

	if ($reply == null)
	{
		$status = 'Discovery is in progress. Try again...'; 
	} else if (isset($reply['error']))
	{
		$status = 'Error: ' . $reply['error'];
	} else if ($reply['code'] == 0)
	{
		// Success
		$status = 'Discovery completed';
		$resp->addAssign($title_id, 'value', $reply['title']);
		$resp->addAssign($descr_id, 'value', $reply['description']);
		$resp->addAssign($siteUrl_id, 'value', $reply['htmlUrl']);
		$resp->addAssign($xmlUrl_id, 'value', $reply['dataUrl']);
	} else
	{
		// Unknown
		$status = 'Location is unknown';
	}

    $resp->addAssign($status_id, 'innerHTML', $status);
    
    return $resp;
}

/**
 * Posts announcement for the given folder by the given user.
 * The announcement has a title and a text.
 */
function postAnnouncement($fid, $uid, $title, $text)
{
    require_once 'classes/Database.class.php';
    
    $db = new Database();
    $db->addNewsItem($title, $text, true, mktime(), $uid, $fid);
    $db->disconnect();

	$resp = new xajaxResponse();
	return $resp;
}

require_once 'ajax.php';
$xajax->processRequests();
?>
