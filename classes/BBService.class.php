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
// $Id: BBService.class.php,v 1.2 2007/01/03 13:53:26 alg Exp $
//

if (file_exists('sites/config.php')) require_once('sites/config.php');
require_once('xmlrpc/IXR_Library.inc.php');

/**
 * Service integration library.
 */
class BBService
{
	/**
	 * Contacts the service and grabs the discovery information.
	 * 
	 * There are several versions of reply:
	 *  -- NULL - discovery is in progress
	 *  -- empty array - nothing is known
	 *  -- array with some fields - known fields
	 */
	function discover($url)
	{
		if (!BBS_ENABLED) return array();
		
		$url = BBService::fix_url($url);
		
		$reply = null;
		$client = new IXR_Client(BBS_URL);
		
		if (!$client->query('meta.getBlogByUrlInUtf8', $url))
		{
			$reply = array('error', $client->getErrorMessage());
		} else
		{
			// Reply format:
			// code				- return code:
			//						0 - successful (check the fields for data)
			//						1 - processing, check later (no other fields)
			//						2 - invalid URL or URL pointing to non-discoverable
			//							place. Fields may be or may not be present.
			// title			- title of the blog.
			// author			- author of the blog.
			// description		- description of the blog.
			// htmlUrl			- root blog URL.
			// dataUrl			- data URL.
			// inboundLinks		- number of links pointing to this blog.
			// category			- category of the blog.
			// location			- publisher's location.
			
			$reply = $client->getResponse();
			
			if (isset($reply['code']) && $reply['code'] == 1) $reply = null;
		}
		
		return $reply;
	}
	
	/**
	 * Fixes URL to look correct for the service.
	 */
	function fix_url($url)
	{
		if ($url == null) return null;
		
		$url = trim($url);
		if (strlen($url) == 0)
		{
			$url = null;
		} else if (preg_match("/^feed:/i", $url))
		{
			$url = preg_replace("/^feed:(\/+)?/i", '', $url);
			$url = BBService::fix_url($url);
		} else if (!preg_match("/^\w+:/i", $url))
		{
			$url = 'http://' . $url;
		}

		return $url;
	}
}
?>