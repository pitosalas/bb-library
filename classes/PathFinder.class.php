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
// $Id: PathFinder.class.php,v 1.2 2006/11/10 14:19:42 alg Exp $
//

require_once 'Database.class.php';

/**
 * Finds the shortest path between two folders. 
 */
class PathFinder
{	
	/**
	 * Finds path between two folders. If the second folder isn't specified,
	 * it looks for the path between the first folder and the root.
	 * 
	 * Returns NULL if the path doesn't exist or the array of folders where
	 * first element is the target folder and the last is the source.
	 */
	function find_shortest_path($source_id, $target_fid = 1, $is_source_folder = true)
	{
		$path = null;
		$shortest_path = PathFinder::find_path($source_id, $target_fid, $is_source_folder);
		
		if ($shortest_path != null)
		{
			$db = new Database();

			$path = array();
			foreach ($shortest_path as $fid)
			{
				$path[] = $db->findFolderByID($fid);
			}

			if (!$is_source_folder) $path[] = $db->findItemByID($source_id);
			
			$db->disconnect();
		}
		
		return $path;
	}
	
	/**
	 * Recursive function for finding the path between two folders.
	 * Returns the list of IDs between them. 
	 */
	function find_path($source_id, $target_fid, $is_source_folder = true, $visited_fids = array())
	{
		if ($is_source_folder)
		{
			$visited_fids[] = $source_id;
			if ($target_fid == $source_id) return array($target_fid);
		}

		// Try looking up the path to speedup the discovery		
		$shortest_path = PathFinder::lookup_shortest_path($source_id, $target_fid, $is_source_folder);
		if ($shortest_path != null) return $shortest_path;

		// Path isn't available. Discover.
		$visible = PathFinder::visible_from($source_id, $is_source_folder);
		if (count($visible) > 0)
		{
			foreach ($visible as $fid)
			{
				if ($fid == $target_fid)
				{
					// If we see the target directly, finish the search
					$shortest_path = array($target_fid);
					break;
				} else if (!in_array($fid, $visited_fids))
				{
					// If we see some node we haven't visited before,
					// try it.
					$path = PathFinder::find_path($fid, $target_fid, true, $visited_fids);
					
					if ($path != null &&
						($shortest_path == null ||
						count($shortest_path) > count($path)))
					{
						// If the target is visible through this node
						// and it's shorter than we've seen by now,
						// record this as a shorter path.
						$shortest_path = $path;
					}
				}
				
			}

			// Add this node to the path before reporting.
			if ($shortest_path != null && $is_source_folder) $shortest_path[] = $source_id;
		}

		// Record the path in the dictionary,
		// so we could look it up later for speedup
		PathFinder::record_shortest_path($source_id, $target_fid, $shortest_path, $is_source_folder);
		
		return $shortest_path;
	}
	
	/**
	 * Returns the list of IDs of folders visible from the given.
	 */
	function visible_from($id, $is_source_folder = true)
	{
		$db = new Database();
		$ids = $db->_query_ids($is_source_folder 
			? 'SELECT parent_id FROM FolderShortcut WHERE folder_id = ' . $id
			: 'SELECT folder_id FROM Folder_Item WHERE item_id = ' . $id);
		$db->disconnect();
		
		return $ids;
	}
	
	// ------------------------------------------------------------------------
	// Performance tools
	// ------------------------------------------------------------------------
	
	function record_shortest_path($source_id, $target_fid, $path, $is_source_folder = true)
	{
		$mpath = $path == null || count($path) == 0 ? 'NULL' : '"' . implode(',', $path) . '"';

		$db = new Database();
		$db->_query('DELETE FROM ShortestPaths WHERE source_id = ' . $source_id . ' AND target_id = ' . $target_fid . ' AND is_source_folder = ' . ($is_source_folder ? 1 : 0));
		$db->_query('INSERT INTO ShortestPaths (source_id, is_source_folder, target_id, path) VALUES (' . $source_id . ', ' . ($is_source_folder ? 1 : 0) . ', ' . $target_fid . ', ' . $mpath . ')');
		$db->disconnect();
	}
	
	function lookup_shortest_path($source_id, $target_fid, $is_source_folder = true)
	{
		$path = null;
		
		$db = new Database();
		$res = $db->_query('SELECT path FROM ShortestPaths WHERE source_id = ' . $source_id . ' AND target_id = ' . $target_fid . ' AND is_source_folder = ' . ($is_source_folder ? 1 : 0));
		if ($res)
		{
			if ($row = mysql_fetch_array($res)) $path = $row['path'];
			mysql_free_result($res);
		}
		$db->disconnect();
		
		return $path == null || strlen(trim($path)) == 0 ? null : explode(',', $path);
	}
	
	/**
	 * This should be called whenever the linking between
	 * folders changes.
	 */
	function invalidate_cache($folders = true, $itemid = false)
	{
		$db = new Database();
		$db->_query('DELETE FROM ShortestPaths WHERE is_source_folder = ' . ($folders ? '1' : '0' . ($itemid ? ' AND source_id = ' . $itemid : '')));
		$db->disconnect();
	}
}
?>