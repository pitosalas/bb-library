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
// $Id: TagsManager.class.php,v 1.4 2007/09/21 15:52:10 alg Exp $
//

require_once 'Database.class.php';

define('CLOUD_STEPS', 5);

/** Tags Management. */
class TagsManager
{
	/**
	 * Users TagsMapping database table to replace tags with their mapped
	 * replacements.
	 */
	function map($tags)
	{
		if ($tags != null && is_array($tags) && count($tags) > 0)
		{
			$db = new Database();
		
			$tagss = join("', '", $tags);
			$res = $db->_query("SELECT from_tag, to_tag FROM TagsMapping WHERE from_tag IN ('$tagss')");
			if ($res)
			{
				$mapping = array();
			
				while ($row = mysql_fetch_array($res))
				{
					$mapping[$row[0]] = $row[1];
				}
				mysql_free_result($res);
				
				if (count($mapping) > 0)
				{
					foreach ($tags as $i => $t)
					{
						if (array_key_exists($t, $mapping))
						{
							$tags[$i] = $mapping[$t];
						}
					}
				} 
			}
			
			$db->disconnect();
		}
				
		return $tags;
	}
	
	/**
	 * Returns normalized associative array of tags and normalized counts in range [0; 5].
	 */
	function getTagsCloud()
	{
		return TagsManager::_normalizeCloud(TagsManager::_getTagsCloudRaw());
	}
	
	/**
	 * Returns the associated array of tags and counts. Tags are all lower-cased.
	 */
	function _getTagsCloudRaw()
	{
		$cloud = array();
		
		$db = new Database();
		
		$res = $db->_query(
			'(SELECT t.name, count(*) cnt ' .
				'FROM Tag t LEFT JOIN Folder_Tag f ON t.id=f.tag_id ' .
				'WHERE f.folder_id IS NOT NULL GROUP BY t.id HAVING cnt > 0) UNION ' .
			'(SELECT t.name, count(*) cnt ' .
				'FROM Tag t LEFT JOIN Item_Tag i ON t.id=i.tag_id ' .
				'WHERE i.item_id IS NOT NULL GROUP BY t.id HAVING cnt > 0) UNION ' .
			'(SELECT t.name, count(*) cnt ' .
				'FROM Tag t LEFT JOIN Person_Tag p ON t.id=p.tag_id ' .
				'WHERE p.person_id IS NOT NULL GROUP BY t.id HAVING cnt > 0) ' .
				'ORDER BY name');
		
		if ($res)
		{
			while ($row = mysql_fetch_array($res))
			{
				$tag = $row[0];
				$cnt = $row[1];
				$old_cnt = isset($cloud[$tag]) ? $cloud[$tag] : 0;
				$cloud[$tag] = $old_cnt + $cnt;
			}			
			mysql_free_result($res);
		}
		
		$db->disconnect();

		return $cloud;
	}
	
	/**
	 * Finds all folders, items and people by the given tag.
	 */
	function findObjectsByTag($tag)
	{
		$res = array();
		
		$db = new Database();
		$res['folders'] = TagsManager::_findFoldersByTag($tag, $db);
		$res['items'] = TagsManager::_findItemsByTag($tag, $db);
		$res['people'] = TagsManager::_findPeopleByTag($tag, $db);
		$db->disconnect();
		
		return $res;
	}
	
	/**
	 * Returns the list of folders by given tag.
	 */
	function _findFoldersByTag($tag, &$db)
	{
		$folders = array();
		
		$res = $db->_query('SELECT f.id, f.title ' .
			'FROM Tag t LEFT JOIN Folder_Tag ft ON t.id=ft.tag_id ' .
				'LEFT JOIN Folder f ON ft.folder_id=f.id ' .
			'WHERE t.name = \'' . $db->_escapeSQL($tag) . '\' AND f.id IS NOT NULL ' .
			'ORDER BY title');

		if ($res)
		{
			while ($row = mysql_fetch_array($res))
			{
				$id = $row[0];
				$title = $row[1];
				
				$folder = new Folder(0, $title);
				$folder->id = $id;
				
				$folders[] = $folder;
			}
			mysql_free_result($res);
		}

		return $folders;
	}
	
	/**
	 * Returns the list of items by given tag.
	 */
	function _findItemsByTag($tag, &$db)
	{
		$items = array();
		
		$res = $db->_query('SELECT i.id, i.title, i.dataUrl ' .
			'FROM Tag t LEFT JOIN Item_Tag it ON t.id=it.tag_id ' .
				'LEFT JOIN Item i ON it.item_id=i.id ' .
			'WHERE t.name = \'' . $db->_escapeSQL($tag) . '\' AND i.id IS NOT NULL ' .
			'ORDER BY title');

		if ($res)
		{
			while ($row = mysql_fetch_array($res))
			{
				$item = new Item(0, $row[1]);
				$item->id = $row[0];
				$item->dataURL = $row[2];
				
				$items[] = $item;
			}
			mysql_free_result($res);
		}
					
		return $items;
	}
	
	/**
	 * Returns the list of people by given tag.
	 */
	function _findPeopleByTag($tag, &$db)
	{
		$people = array();
		
		$res = $db->_query('SELECT p.id, p.userName, p.fullName ' .
			'FROM Tag t LEFT JOIN Person_Tag pt ON t.id=pt.tag_id ' .
				'LEFT JOIN Person p ON pt.person_id=p.id ' .
			'WHERE t.name = \'' . $db->_escapeSQL($tag) . '\' AND p.id IS NOT NULL ' .
			'ORDER BY fullName');

		if ($res)
		{
			while ($row = mysql_fetch_array($res))
			{
				$id = $row[0];
				$username = $row[1];
				$fullname = $row[2];
				
				$person = new Person($username, $fullname);
				$person->id = $id;
				
				$people[] = $person;
			}
			mysql_free_result($res);
		}
					
		return $people;
	}
	
	/**	Normalizes the cloud and returns steps instead of counts in range [0; 4]. */
	function _normalizeCloud(&$cloud)
	{
		if ($cloud == null || !is_array($cloud)) return $cloud;
		
		// Find maximum
		$max = 0;
		foreach ($cloud as $tag => $cnt)
		{
			if ($cnt > $max) $max = $cnt;
		}
		
		$cnt_in_step = $max / CLOUD_STEPS;
		foreach ($cloud as $tag => $cnt)
		{
			$cloud[$tag] = round($cnt / $cnt_in_step);
		}
		
		return $cloud;
	}
}
?>