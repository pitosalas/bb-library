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
// $Id: opml_ping.php,v 1.2 2007/01/03 13:53:26 alg Exp $
//

require_once 'sites/config.php';
require_once 'opml.php';
require_once 'classes/DataManager.class.php';
require_once 'classes/Pinger.class.php';

// Number of seconds between automatic updates (minimum)
define('OPML_FOLDER_UPDATE_PERIOD_SEC', 24*3600);

set_time_limit(600);

$fid = defGET('fid', -1);
if ($fid != -1)
{
	$dm = new DataManager();
	$folder = $dm->getFolderViewInfo($fid);

	if (isset($folder->opml_url) && $folder->dynamic == 0)
	{
		$dm->updateOPMLFolderLastUpdated($fid);
		$folder = prepare_opml_folder($folder->opml_url, $folder->owner_id);
		if ($folder != null) $dm->updateOPMLFolder($fid, $folder);
	}
	
	$dm->close();
} else if (isset($_GET['url']))
{
	$url = defGET('url', null);
	if ($url)
	{
		$dm = new DataManager();
		$fids = $dm->getOPMLFolderIDsByURL($url);

		foreach ($fids as $fid)
		{
			// Asynchronously call update procedure
			Pinger::ping_link(BASE_URL . '/opml_ping?fid=' . $fid);
		}
		$dm->close();
	}
} else
{
	// Update all OPML folders requiring updates
	$maximum_update_time = mktime() - OPML_FOLDER_UPDATE_PERIOD_SEC;
	echo 'Updating before ' . ($maximum_update_time) . "<br>\n";
	
	$dm = new DataManager();
	$fids = $dm->getOPMLFolderIDsToUpdate($maximum_update_time);

	foreach ($fids as $fid)
	{
		$link = BASE_URL . '/opml_ping?fid=' . $fid;
		echo 'Updating FID=' . $fid . ' LINK=' . $link . "<br>\n";
		
		// Asynchronously call update procedure
		Pinger::ping_link($link);
	}
	$dm->close();
}
?>
