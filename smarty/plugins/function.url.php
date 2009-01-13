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
// $Id: function.url.php,v 1.4 2007/08/23 17:04:11 alg Exp $
//

/**
 * Returns URL for the node (folder or item).
 * @param folder or item -- node element.
 * @param type -- normal, or opml (for folder), or rss (for item).
 */
function smarty_function_url($params, &$smarty)
{
    if (isset($params['page']))
    {
    	// Direct page request
    	$page = $params['page'];
    	if ($page == 'home') $url = FOLDER_URL . 1; else
    	if ($page == 'profile') $url = USER_URL . $_SESSION['user_id']; else
    	if ($page == 'users') $url = USER_URL; else
    	if ($page == 'organizations') $url = ORGANIZATION_URL; else
    	if ($page == 'upload_opml') $url = BASE_URL . '/opml_upload_form.php'; else
    	if ($page == 'csv_import') $url = BASE_URL . '/csv_import.php'; else
    	if ($page == 'preferences') $url = BASE_URL . '/prefs_show.php'; else
    	if ($page == 'search') $url = BASE_URL . '/search'; else
    	if ($page == 'tasks') $url = BASE_URL . '/tasks'; else
    	if ($page == 'top100')
    	{
    		$url = BASE_URL . '/top100';
    		if (isset($params['type']) && $params['type'] == 'opml') $url .= '.opml';
    	} else
    	if ($page == 'top10')
    	{
    		$url = BASE_URL . '/top10.opml';
    	} else
    	if ($page == 'news')
    	{
    		$url = NEWS_URL;
    		$type = isset($params['type']) ? $params['type'] : null; 
    		if ($type == 'rss') $url .= '.xml'; else
    		if ($type == 'add_nitem') $url .= '?action=add_nitem';
    	} else
    	if ($page == 'backup_restore') $url = BASE_URL . '/backup_restore.php'; else
    	if ($page == 'tags_cloud') $url = BASE_URL . '/tags_cloud'; else 
    	if ($page == 'tags') $url = BASE_URL . '/tags.php'; 

		// Add action    	
    	if (isset($params['action'])) $url .= '?action=' . $params['action'];  
    } else if (isset($params['tag']))
    {
    	$url = BASE_URL . '/tag/' . urlencode($params['tag']);
    } else
    {
	    $type = 'normal'; 
	    $folder_title = false;
	    $item_title = false;
	    
	    if (isset($params['type'])) $type = $params['type'];
	
	    if (isset($params['folder']))
	    {
	    	$fid = $params['folder']->id;
	    	$folder_title = $params['folder']->title;
	    }
	    if (isset($params['folder_id'])) $fid = $params['folder_id'];
	    if (isset($params['item']))
	    {
	    	$iid = $params['item']->id;
	    	$item_title = $params['item']->title;
	    }
		if (isset($params['news'])) $niid = $params['news']['id'];
			    
	    $fidP = (isset($fid) ? '&fid=' . $fid : '');
	    $pfid = (isset($params['parent']) ? '&pfid=' . $params['parent']->id : '');
	    
	    if ($type == 'opml')
	    {
	    	if (isset($fid))
	    	{
	    		$f = $params['folder'];
	    		
	    		if ($f->dynamic == 0) $url = folder_url($fid, $folder_title) . '.opml'; else
	    	 	if ($f->opml_url != '') $url = $f->opml_url; else
	    	 	$url = '';
	    	} else if (isset($iid))
	    	{
	    		$i = $params['item'];
	    		
				if ($i->dynamic == 0) $url = item_url($iid, $item_title) . '.opml'; else
				$url = $i->dataURL;
	    	}
	    } else
	    if ($type == 'rss')
	    {
	    	$i = $params['item'];
	    	$real = (bool)(isset($params['real']) && $params['real']);
	    		
			if ($i->dynamic == 0 && !$real) $url = item_url($iid, $item_title) . '.xml'; else
			$url = $i->dataURL;
	    } else
	    if ($type == 'realrss') $url = $params['item']->dataURL; else
	    if ($type == 'img') $url = ITEM_URL . $iid . '.jpg'; else
	    if ($type == 'site') $url = $params['item']->siteURL; else
	    if ($type == 'add_folder') $url = FOLDER_URL . $fid . "?action=add_folder"; else
	    if ($type == 'edt_folder') $url = FOLDER_URL . $fid . "?action=edt_folder"; else
	    if ($type == 'del_folder') $url = FOLDER_URL . $fid . "?action=del_folder" . $pfid; else
	    if ($type == 'opml_update') $url = FOLDER_URL . $fid . "?action=opml_update"; else
	    if ($type == 'add_item') $url = FOLDER_URL . $fid . "?action=add_item" . $fidP; else
	    if ($type == 'edt_item') $url = ITEM_URL . $iid . "?action=edt_item" . $fidP; else
	    if ($type == 'del_item') $url = ITEM_URL . $iid . "?action=del_item" . $fidP; else
		if ($type == 'edt_nitem') $url .= NEWS_URL . '/' . $niid . '?action=edt_nitem'; else
		if ($type == 'del_nitem') $url .= NEWS_URL . '/' . $niid . '?action=del_nitem'; else
		{
	    	// No type specified
	        if (isset($iid)) $url = item_url($iid, $item_title); else
	        if (isset($fid)) $url = folder_url($fid, $folder_title); else
	        if (isset($niid)) $url = NEWS_URL . '/' . $niid; else
			if (isset($params['org'])) $url = ORGANIZATION_URL . $params['org']; else
	        if (isset($params['person'])) $url = USER_URL . $params['person']->id;
	    }
    }
	
    if (!isset($url)) print_r($params);
        
    return $url;
}

function smarty_url_folder($folder, &$smarty)
{
	return smarty_function_url(array('folder' => $folder), $smarty);
}

function smarty_url_item($item, &$smarty)
{
	return smarty_function_url(array('item' => $item), $smarty);
}

function smarty_url_news(&$smarty, $nitem = null)
{
	return ($nitem == null)
		? smarty_function_url(array('page' => 'news'), $smarty)
		: smarty_function_url(array('news' => array('id' => $nitem)), $smarty);
}

function smarty_url_tags(&$smarty, $tag = null)
{
	return ($tag == null)
		? smarty_function_url(array('page' => 'tags_cloud'), $smarty)
		: smarty_function_url(array('tag' => $tag), $smarty);
}

?>
