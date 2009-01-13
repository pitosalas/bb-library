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
// $Id: UpdatesChecker.class.php,v 1.3 2007/07/16 11:30:56 alg Exp $
//

require_once 'httpclient/HttpClient.class.php';
if (file_exists('version.php')) require_once ('version.php');

class UpdatesChecker
{
	/** Check for a new version and update the latest version property. */
	function check()
	{
		// Collect data & send request
		$last_version = UpdatesChecker::request();

		// Analyze response
		if ($last_version && 
			ereg("^[0-9]+(\.[0-9]+)+$", $last_version) && 
			FL_VERSION != $last_version)
		{
			// New version is available -- update database
			$db = new Database();
			$db->setAvailableVersion($last_version);
			$db->disconnect();
		}
	}

	/** Collect data and send the request. */
	function request()
	{
		// Collect data
		$db = new Database();
		$num_users = $db->getUsersCount();
		$num_folders = $db->getFoldersCount();
		$num_items = $db->getItemsCount();
		$last_login = $db->getLastLogin();
		$domain = $_SERVER['SERVER_NAME'];
		$db->disconnect();

		// Send request
		$last_version = HttpClient::quickPost(UPDATES_CHECK_URL, array (
			'users' => $num_users, 
			'folders' => $num_folders, 
			'items' => $num_items, 
			'last_login' => $last_login, 
			'domain' => $domain,
			'version' => FL_VERSION));

		return trim($last_version);
	}
}
?>