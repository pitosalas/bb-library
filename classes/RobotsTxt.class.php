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
// $Id: RobotsTxt.class.php,v 1.1 2007/07/10 09:50:04 alg Exp $
//

require_once 'Database.class.php';

/**
 * Generator of robots.txt file.
 */
class RobotsTxt
{
	/**
	 * Returns the text of robots.txt
	 */
	function get($site_path = SITE_PATH)
	{
		$f = $site_path . 'robots.txt';
		if (file_exists($f))
		{
			$content = file_get_contents($f);
		} else
		{
			$content = RobotsTxt::generate();

			// Save the content to the content file
			if ($h = fopen($f, 'w'))
			{
				fwrite($h, $content);
				fclose($h);
			}
		}
		
		return $content;
	}

	/**
	 * Removes the robots.txt file that leads to regeneration.
	 */
	function flush($site_path = SITE_PATH)
	{
		@unlink($site_path . 'robots.txt');
	}
		
	/**
	 * Generates the robots.txt file.
	 */
	function generate()
	{
		$db = new Database();
		
		$l = array();
		
		// All user agents
		$l []= "User-agent: *";
		
		// The list of individual files we don't want to be indexed
		$l []= "/news.xml";
		$l []= "/top10.opml";
		$l []= "/top100.opml";
		$l []= "/search";
		
		// List all XML feeds for items
		$is = $db->getAllItemIDs();
		foreach ($is as $i) $l []= "/item/$i.xml";
		
		// List all OPMLs for folders
		$fs = $db->getAllFolderIDs();
		foreach ($fs as $f) $l []= "/folder/$f.opml";

		$db->disconnect();
		return join("\nDisallow: ", $l);		
	}
}
?>