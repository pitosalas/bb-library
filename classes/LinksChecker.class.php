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
// $Id: LinksChecker.class.php,v 1.8 2007/08/01 15:11:17 alg Exp $
//

if (file_exists('sites/config.php')) require_once ('sites/config.php');

require_once 'Item.class.php';
require_once 'Database.class.php';
require_once 'httpclient/HttpClient.class.php';

/** Link checking period. */
define('LINK_CHECK_PERIOD', 60 * 60 * 24 * 2);
/** The period of receiving letters. */
define('LETTER_PERIOD', 60 * 60 * 24 * 7);

/**
 * Checks links in the database for validity.
 */
class LinksChecker
{
	/**
	 * Starts checking.
	 */
	function check()
	{
		$items = LinksChecker::db_get_items();

		// Touch everything to avoid double checks
		foreach ($items as $item) LinksChecker::db_touch_item($item); 
		
		foreach ($items as $item)
		{
			// Get the link of the item
			$link = $item->siteURL;
			if ($link->type_id != 4 && strlen(trim($item->dataURL)) > 0)
			{
				$link = $item->dataURL;
			}
			
			if (strlen(trim($link)) > 0)
			{
				$code = LinksChecker::get_link_response($link);
				
				$is_error = ($code < 200 || $code >= 400);

				// Update the database
				LinksChecker::db_update_item($item, $code, $is_error);
			}
		}
	}

	/**
	 * Gets the response for the link.
	 */
	function get_link_response($link)
	{
		// Parse the link
        $bits = parse_url($link);
        $host = $bits['host'];
        $port = isset($bits['port']) ? $bits['port'] : 80;
        $path = isset($bits['path']) ? $bits['path'] : '/';
        if (isset($bits['query'])) $path .= '?'.$bits['query'];

        $client = new HttpClient($host, $port);
        $client->setUserAgent('Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.4) Gecko/20070515 Firefox/2.0.0.4');

		// Access the page
		$client->head($path);
		$code = (int)$client->getStatus();
		
		return $code;
	}
		
	// ------------------------------------------------------------------------
	// Letters
	// ------------------------------------------------------------------------

	/** Fetches all invalid stuff, breaks into groups and sends to owners and co. */
	function deliver_mail()
	{
		if (strlen(trim(LC_MAIL_SUBJECT)) == 0) return;

		$recepients = LinksChecker::db_get_recepients();
		if (strlen(trim($recepients)) == 0) return;
		    
		// Get the list of bad items
		$items = LinksChecker::db_get_bad_items();

		if (count($items) == 0) return;
				
		// Group items by owners
		$owners = array();
		foreach ($items as $item)
		{
			// Get or create the list of items belonging to an owner
			if (!isset($owners[$item['owner_id']]))
			{
				$owner = array();
			} else $owner = $owners[$item['owner_id']];
			
			// Add this item
			$owner []= $item;
			
			// Save the list
			$owners[$item['owner_id']] = $owner;
		}
		
		$txt = '';
		foreach ($owners as $ownerid => $items)
		{
			$ownerName = $items[0]['ownerName'];
			$ownerEmail = $items[0]['ownerEmail'];
			
			$txt .= "$ownerName ($ownerEmail)\n\n";
			$bfolder = '';
						
			foreach ($items as $item)
			{
				$title = $item['title'];
				$link = BASE_URL . '/item/' . $item['id'];
				$badLink = $item['type_id'] == 4 ? $item['siteUrl'] : $item['dataUrl'];
				$created = date("r", $item['created']);
				$badSince = date("r", $item['checkFailureTime']);
				$folder = $item['folderTitle'];
				$folderLink = BASE_URL . '/folder/' . $item['folderId'];

				if ($folder != $bfolder)
				{
					$bfolder = $folder;
					$txt .= "  Folder    : $folder ($folderLink)\n\n";
				}

				$txt .= "    Item      : $title ($link)\n";
				$txt .= "    Bad Link  : $badLink\n";
				$txt .= "    Reason    : " . LinksChecker::code_to_reason($item['checkCode']) . "\n";
				$txt .= "    Created   : $created\n";
				$txt .= "    Bad Since : $badSince\n";
				$txt .= "\n";
			}
		}
		
		// Send
		if (strlen($txt) > 0)
		{
			mail($recepients, LC_MAIL_SUBJECT, $txt);
		}
	}

	/**
	 * Converts the code into the reason.
	 */
	function code_to_reason($code)
	{
		$reason = 'Unknown';
		
		$group = (int)($code / 100);
		
		if ($group == 3)
		{
			$reason = 'Moved';
		} else if ($group == 4)
		{
			$reason = 'Not Found';
		} else if ($group == 5)
		{
			$reason = 'Server Error';
		}
		
		return $reason . " (HTTP Code: $code)";
	}
		
	// ------------------------------------------------------------------------
	// Database
	// ------------------------------------------------------------------------

	/**
	 * Gets the next items for checking.
	 */
	function db_get_items()
	{
		$items = array();
		
		$period = LINK_CHECK_PERIOD;
		$now = mktime();
		
		$db = new Database();
		$rows = $db->_query_rows("SELECT * FROM Item WHERE checkLastTime IS NULL OR checkLastTime + $period < $now OR checkCode = 0 ORDER BY checkLastTime ASC");
		foreach ($rows as $row) $items []= $db->_row2Item($row);
		$db->disconnect();
		
		return $items;
	}
	
	/**
	 * Touches the item so that it isn't picked up by other process.
	 */
	function db_touch_item($item)
	{
		$now = mktime();
		$id = $item->id;
		
		$db = new Database();
		$db->_query("UPDATE Item SET checkLastTime = $now WHERE id = $id");
		$db->disconnect();
	}
	
	/**
	 * Updates the state of the item.
	 */
	function db_update_item($item, $code, $is_error)
	{
		$id = $item->id;

		$db = new Database();
		$db->_query("UPDATE Item SET checkCode = $code WHERE id = $id");
		if ($is_error)
		{
			$db->_query("UPDATE Item SET checkFailureTime = checkLastTime WHERE id = $id AND checkFailureTime IS NULL");
		} else
		{
			$db->_query("UPDATE Item SET checkFailureTime = NULL WHERE id = $id");
		}
		$db->disconnect();
	}
	
	/**
	 * Returns the list of recepients as a string.
	 */
	function db_get_recepients()
	{
		$db = new Database();
		$rec = $db->getApplicationProperty('lc_recepients');
		$db->disconnect();

		return $rec;
	}
	
	/**
	 * Returns the list of bad items with additonal fields:
	 *  - ownerName, ownerEmail
	 *  - folderTitle, folderId
	 */
	function db_get_bad_items()
	{
		$now = mktime();
		$period = LETTER_PERIOD;
		
		// Query items
		$db = new Database();
		$rows = $db->_query_rows(
			"SELECT i.*, p.fullName ownerName, p.email ownerEmail, f.id folderId, f.title folderTitle " .
			"FROM Item i ".
			"  LEFT JOIN Person p ON i.owner_id=p.id ".
			"  LEFT JOIN Folder_Item fi ON i.id=fi.item_id ".
			"  LEFT JOIN Folder f ON fi.folder_id=f.id ".
			"WHERE (checkCode < 200 OR checkCode >= 300) AND checkCode != 0 AND ".
			"  (checkLastLetterTime IS NULL OR checkLastLetterTime + $period < $now) ".
			"GROUP BY i.id ".
			"ORDER BY owner_id, folderId");

		// Touch items
		$db->_query(
			"UPDATE Item " .
			"SET checkLastLetterTime = $now ".
			"WHERE (checkCode < 200 OR checkCode >= 300) AND checkCode != 0 AND ".
			"  (checkLastLetterTime IS NULL OR checkLastLetterTime + $period < $now) ");

		$db->disconnect();
		
		return $rows;
	}
}
?>