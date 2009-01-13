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
// $Id: Database.class.php,v 1.34 2007/09/26 12:48:45 alg Exp $
// 

if (file_exists('sites/config.php')) require_once ('sites/config.php');

require_once ('Folder.class.php');
require_once ('Item.class.php');
require_once ('Person.class.php');

class Database {
	var $link;

	/**
	 * Connects to database.
	 */
	function connect() {
		if ($this->link != null)
			return;

		$this->link = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD, true) or die("Failed to connect to database:\n".mysql_error());
		mysql_select_db(DB_NAME, $this->link) or die("Failed to select the database:\n".mysql_error());
	}

	/**
	 * Disconnects from database.
	 */
	function disconnect() {
		if ($this->link != null) {
			mysql_close($this->link);
			$this->link = null;
		}
	}

	/**
	 * Returns home folder object.
	 */
	function findHomeFolder() {
		return $this->findFolderByID(1);
	}

	/**
	 * Returns the list of all folders from database.
	 */
	function getAllFolders() {
		$folders = array ();
		$res = $this->_query("SELECT id, title FROM Folder ORDER BY title");

		if ($res) {
			while ($row = mysql_fetch_assoc($res))
				$folders[$row['id']] = $row['title'];
			mysql_free_result($res);
		}

		return $folders;
	}

	/**
	 * Returns the list of IDs of all folders from database.
	 */
	function getAllFolderIDs() {
		$ids = array ();
		$res = $this->_query("SELECT id FROM Folder");

		if ($res) {
			while ($row = mysql_fetch_assoc($res)) $ids []= $row['id'];
			mysql_free_result($res);
		}

		return $ids;
	}

	/**
	 * Returns the list of IDs of all items from database.
	 */
	function getAllItemIDs() {
		$ids = array ();
		$res = $this->_query("SELECT id FROM Item");

		if ($res) {
			while ($row = mysql_fetch_assoc($res)) $ids []= $row['id'];
			mysql_free_result($res);
		}

		return $ids;
	}

	/**
	 * Returns the list of all authors and librarians.
	 */
	function getAllAuthors() {
		$authors = array ();
		$res = $this->_query('SELECT t.id, p.id, fullName FROM Person p LEFT JOIN AccountType t ON p.type_id=t.id WHERE edit_content = \'Y\' ORDER BY fullName');
		if ($res) {
			while ($row = mysql_fetch_assoc($res))
				$authors[$row['id']] = $row['fullName'];
			mysql_free_result($res);
		}

		return $authors;
	}

	/**
	 * Returns folder found by ID.
	 */
	function findFolderByID($folderId) {
		$folder = null;

		$result = $this->_query("SELECT * FROM Folder WHERE id = ".$folderId);
		if ($result) {
			if ($row = mysql_fetch_array($result))
				$folder = $this->_row2Folder($row);
			mysql_free_result($result);
		}

		return $folder;
	}

	/**
	 * Looks up the array of folder by given opml url.
	 */
	function getOPMLFolderIDsByURL($url) {
		return $this->_query_ids('SELECT id FROM Folder WHERE opml_url = \''.$this->_escapeSQL($url).'\'');
	}

	/**
	 * Returns the list of folder IDs of OPML folders to update. 
	 */
	function getOPMLFolderIDsToUpdate($maximum_update_time) {
		return $this->_query_ids('SELECT id FROM Folder WHERE opml_url != \'\' AND dynamic = 0 AND opml_last_updated < '.$maximum_update_time);
	}

	/**
	 * Returns the parent folder for the given folder.
	 */
	//    function findParentFolder($folderId)
	//    {
	//        $parent = null;
	//
	//        $result = $this->_query("SELECT f2.* FROM Folder f LEFT JOIN Folder f2 ON f.parent_id=f2.id WHERE f.id=" . $folderId);
	//        if ($result)
	//        {
	//            if ($row = mysql_fetch_assoc($result)) $parent = $this->_row2Folder($row);
	//            mysql_free_result($result);
	//        }
	//        
	//        return $parent;
	//    }

	/**
	 * Returns path to root from the given folder. The first element is root folder.
	 */
	//    function getPathToRoot($folder)
	//    {
	//        $parents = array();
	//        
	//        while (isset($folder->parent_id) && $folder = $this->findFolderById($folder->parent_id))
	//        {
	//            array_unshift($parents, $folder);
	//        }
	//                
	//        return $parents;
	//    }

	/**
	 * Returns the list of parent folders for the given item.
	 */
	function findParentFoldersOfItem($itemId, $noRoot = false) {
		$parents = array ();

		$result = $this->_query("SELECT f.* FROM Folder f JOIN Folder_Item fi ON f.id=fi.folder_id "."WHERE fi.item_id=".$itemId. ($noRoot ? " AND f.id <> 1" : "")." ORDER BY title");

		if ($result) {
			while ($row = mysql_fetch_assoc($result))
				$parents[] = $this->_row2Folder($row);
			mysql_free_result($result);
		}

		return $parents;
	}

	/** Counts sub-folders of the folder. */
	function countSubFolders($folderId) {
		$count = 0;

		$res = $this->_query('SELECT COUNT(*) FROM FolderShortcut WHERE parent_id='.$folderId);
		if ($res) {
			if ($row = mysql_fetch_array($res))
				$count = $row[0];
			mysql_free_result($res);
		}

		return $count;
	}

	/**
	 * Returns the list of sub-folders of a given folder.
	 */
	function findSubFolders($folderId) {
		$folders = array ();

		$result = $this->_query('SELECT f.* '.'FROM Folder f JOIN FolderShortcut ff ON f.id=ff.folder_id '.'WHERE ff.parent_id='.$folderId.' '.'ORDER BY ord, title');

		if ($result) {
			while ($row = mysql_fetch_array($result))
				$folders[] = $this->_row2Folder($row);
			mysql_free_result($result);
		}

		return $folders;
	}

	/**
	 * Returns true if some folder has any children.
	 */
	function hasChildren($folderId) {
		$result = $this->_query('SELECT 1 FROM FolderShortcut fs WHERE fs.parent_id = '.$folderId.' UNION '.'SELECT 1 FROM Folder_Item fi WHERE fi.folder_id = '.$folderId);

		$has = $result && mysql_num_rows($result) > 0;

		mysql_free_result($result);

		return $has;
	}

	/**
	 * Returns the array of all direct children (folders) for the given folder down to leaves.
	 */
	function findFolderDirectChildren($folderId) {
		$children = array ();

		$res = $this->_query('SELECT folder_id, count(*) cnt FROM FolderShortcut WHERE parent_id='.$folderId.' GROUP BY folder_id HAVING cnt = 1');
		if ($res) {
			while ($row = mysql_fetch_array($res)) {
				$children[] = $row[0];
				$children = array_merge($children, $this->findFolderDirectChildren($row[0]));
			}

			mysql_free_result($res);
		}

		return $children;
	}

	/**
	 * Returns the list of tags for a given folder.
	 */
	function findFolderTags($folderId) {
		$tags = array ();

		$result = $this->_query("SELECT t.* FROM Tag t JOIN Folder_Tag f ON t.id=f.tag_id WHERE f.folder_id=".$folderId);
		if ($result) {
			while ($row = mysql_fetch_array($result)) $tags[] = $row['name'];
			mysql_free_result($result);
		}

		return $tags;
	}

	function findFolderShortcutParents($folderId) {
		$shortcuts = array ();

		$result = $this->_query('SELECT f.* FROM Folder f JOIN FolderShortcut s ON f.id=s.parent_id WHERE s.folder_id='.$folderId);
		if ($result) {
			while ($row = mysql_fetch_assoc($result))
				$shortcuts[] = $this->_row2Folder($row);
			mysql_free_result($result);
		}

		return $shortcuts;
	}

	/**
	 * Returns the list of folders owned by this person.
	 */
	function findPersonFolders($pid)
	{
		$my_folders = array();

		$folders = $this->_query("SELECT * From Folder where owner_id=$pid");

		if ($folders && mysql_num_rows($folders) > 0)
		{
			$folder_ids = array();
			while ($row = mysql_fetch_array($folders))
			{
				$folder = $this->_row2Folder($row);
				$folder->item_count = 0;
				$folder->folder_count = 0;
				$my_folders[$folder->id]= $folder;
				$folder_ids[] = $folder->id;
			}
			mysql_free_result($folders);

			$folder_ids = implode(',', $folder_ids);
			
			// Count folders
			$cnt_folders = array();
			$ch_folders = $this->_query("SELECT parent_id, COUNT(*) cnt FROM FolderShortcut WHERE parent_id IN ($folder_ids) GROUP BY parent_id");
			while ($row = mysql_fetch_array($ch_folders))
			{
				$f = $my_folders[$row[0]];
				$f->folder_count = $row[1];
				$my_folders[$row[0]] = $f;
			}
			mysql_free_result($ch_folders);
			
			// Count items
			$ch_items = $this->_query("SELECT folder_id, COUNT(*) cnt FROM Folder_Item WHERE folder_id IN ($folder_ids) GROUP BY folder_id");
			while ($row = mysql_fetch_array($ch_items))
			{
				$f = $my_folders[$row[0]];
				$f->item_count = $row[1];
				$my_folders[$row[0]] = $f;
			}
			mysql_free_result($ch_items);
		}

		$my_folders = array_values($my_folders);
		
		return $my_folders;
	}

	/**
	 * Returns the list of folders owned by this person or others if they are assignable.
	 */
	function findAssignableFolders($pid, $perm_edit_others_content)
	{
		$my_folders = array();
		$others_folders = array();

		$result = $this->_query('SELECT id, title, owner_id FROM Folder WHERE dynamic = 0 ' .
			'AND (opml_url = \'\' OR opml_url IS NULL) ' . 
			($perm_edit_others_content ? '' : 'AND owner_id=' . $pid) .
			' ORDER BY title');

		if ($result)
		{
			while ($row = mysql_fetch_array($result))
			{
				$id = $row['id'];
				$title = $row['title'];
				$owner = $row['owner_id'];
				
				if ($owner == $pid)
				{
					$my_folders[$id]= $title;
				} else
				{
					$others_folders[$id]= $title;
				}
			}
			mysql_free_result($result);
		}
		
		// Create the result array
		$folders = array(MY_FOLDERS => $my_folders);
		if ($perm_edit_others_content && count($others_folders)) $folders[OTHERS_FOLDERS] = $others_folders;
		
		return $folders;
	}

	/**
	 * Returns the array of all items belonging to the given folder.
	 */
	function findFolderItems($folderId, $offs = 0, $limit = -1) {
		$items = array ();

		$result = $this->_query("SELECT i.* FROM Item i JOIN Folder_Item f ON i.id=f.item_id WHERE f.folder_id = ".$folderId." ORDER BY ord, title ". ($limit > -1 ? 'LIMIT '.$offs.', '.$limit : ''));

		if ($result) {
			while ($row = mysql_fetch_array($result))
				$items[] = $this->_row2Item($row);
			mysql_free_result($result);
		}

		return $items;
	}

	/**
	 * Returns OPML of the folder.
	 */
	function getFolderOPML($fid) {
		$opml = null;

		$res = $this->_query('SELECT opml FROM Folder WHERE id='.$fid);
		if ($res) {
			if ($row = mysql_fetch_array($res))
				$opml = $row[0];
			mysql_free_result($res);
		}

		return $opml;
	}

	/**
	 * Sets folder OPML.
	 */
	function setFolderOPML($fid, $opml) {
		$this->_query('UPDATE Folder SET opml=\''.$this->_escapeSQL($opml).'\' WHERE id = '.$fid);
	}

	function setTagsIfNotSet($fid, $tags) {
		$old_tags = $this->findFolderTags($fid);
		if (count($old_tags) == 0) {
			$this->_setFolderTags($fid, $tags, true);
		}
	}

	/**
	 * Adds a folder to database, sets its ID field and returns as the result.
	 */
	function addFolder($folder, $shortcuts, $tags = null) {
		$this->_query('INSERT INTO Folder (viewType_id, owner_id, title, description, created, '.
			'viewTypeParam, opml, opml_url, opml_user, opml_password, opml_updates_period, opml_last_updated, '.
			'opml_last_error, dynamic, ord, autoTags, show_in_nav_bar) '.
			'VALUES ('.$folder->viewType_id.', '.
			$folder->owner_id.', \''.
			$this->_escapeSQL($folder->title).'\', \''.
			$this->_escapeSQL($folder->description).'\', '.
			$folder->created.', \''.
			$this->_escapeSQL($folder->viewTypeParam).'\', \''.
			$this->_escapeSQL($folder->opml).'\', \''.
			$this->_escapeSQL($folder->opml_url).'\', \''.
			$this->_escapeSQL($folder->opml_user).'\', \''.
			$this->_escapeSQL($folder->opml_password).'\', '.
			$folder->opml_updates_period.', '.
			$folder->opml_last_updated.', \''.
			$this->_escapeSQL($folder->opml_last_error).'\', '.
			$folder->dynamic.',' .
			($folder->order == '' ? 99999 : $folder->order).','.
			(int)$folder->autoTags.','.
			(int)$folder->show_in_nav_bar.
			')');

		$folder->id = mysql_insert_id($this->link);

		if ($folder->id > 0) {
			$this->_setFolderTags($folder->id, $tags, true);
			$this->_setFolderShortcuts($folder->id, $shortcuts, true);
		}

		return $folder;
	}

	/**
	 * Updates folder record, its parent associations and tags.
	 */
	function updateFolder($folder, $shortcuts, $tags = null) {
		$this->_query('UPDATE Folder SET '.
			'viewType_id='.$folder->viewType_id.', '.
			'owner_id='.$folder->owner_id.', '.
			'title=\''.$this->_escapeSQL($folder->title).'\', '.
			'description=\''.$this->_escapeSQL($folder->description).'\', '.
			'viewTypeParam=\''.$this->_escapeSQL($folder->viewTypeParam).'\', '.
			'opml=\''.$this->_escapeSQL($folder->opml).'\', '.
			'opml_url=\''.$this->_escapeSQL($folder->opml_url).'\', '.
			'opml_user=\''.$this->_escapeSQL($folder->opml_user).'\', '.
			'opml_password=\''.$this->_escapeSQL($folder->opml_password).'\', '.
			'opml_updates_period='.$folder->opml_updates_period.', '.
			'opml_last_updated='.$folder->opml_last_updated.', '.
			'opml_last_error=\''.$this->_escapeSQL($folder->opml_last_error).'\', '.
			'dynamic='.$folder->dynamic.', '.
			'ord='.($folder->order == '' ? 99999 : $folder->order).', '.
			'autoTags='.(int)$folder->autoTags.', '.
			'show_in_nav_bar='.(int)$folder->show_in_nav_bar.' '.
			'WHERE id='.$folder->id);

		$this->_setFolderTags($folder->id, $tags);
		$this->_setFolderShortcuts($folder->id, $shortcuts);
	}

	/**
	 * Updates the opml_last_updated time.
	 */
	function updateOPMLFolderLastUpdated($fid, $time) {
		$this->_query('UPDATE Folder SET opml_last_updated='.$time.' WHERE id='.$fid);
	}

	/**
	 * Adds a item to database, sets its ID field and returns as the result.
	 */
	function addItem($item, $shortcuts, $tags = null) {
		$this->_query('INSERT INTO Item (type_id, owner_id, title, description, created, siteUrl, dataUrl, dynamic, ord, iTunesURL, useITunesURL, usePlayButtons, showPreview, autoTags, show_in_nav_bar) '.
			'VALUES ('.
			$item->type_id.', '.
			$item->owner_id.', \''.
			$this->_escapeSQL($item->title).'\', \''.
			$this->_escapeSQL($item->description).'\', '.
			$item->created.', \''.
			$this->_escapeSQL($item->siteURL).'\', \''.
			$this->_escapeSQL($item->dataURL).'\', '.
			$item->dynamic.', '. 
			($item->order == '' ? 99999 : $item->order).', \''.
			$this->_escapeSQL($item->itunesURL) . '\', '.
			(int)$item->useITunesURL.', '.  
			(int)$item->usePlayButtons.', '.  
			(int)$item->showPreview.', '.
			(int)$item->autoTags.', '.  
			(int)$item->show_in_nav_bar.  
			')');

		$item->id = mysql_insert_id($this->link);

		if ($item->id > 0) {
			$this->_setItemTags($item->id, $tags, true);
			$this->_setItemFolders($item->id, $shortcuts, true);
		}

		return $item;
	}

	/**
	 * Updates item record, its parent associations and tags.
	 */
	function updateItem($item, $shortcuts, $tags = null) {
		$this->_query("UPDATE Item SET ".
			'type_id='.$item->type_id.', '.
			'owner_id='.$item->owner_id.', '.
			'title=\''.$this->_escapeSQL($item->title).'\', '.
			'description=\''.$this->_escapeSQL($item->description).'\', '.
			'siteURL=\''.$this->_escapeSQL($item->siteURL).'\', '.
			'dataURL=\''.$this->_escapeSQL($item->dataURL).'\', '.
			'iTunesURL=\''.$this->_escapeSQL($item->itunesURL).'\', '.
			'useITunesURL='.(int)$item->useITunesURL.', ' .
			'usePlayButtons='.(int)$item->usePlayButtons.', ' .
			'showPreview='.(int)$item->showPreview.', ' .
			'dynamic='.$item->dynamic.', '.
			'ord='. ($item->order != null ? $item->order : 99999).', '.
			'autoTags='.(int)$item->autoTags.', '.
			'show_in_nav_bar='.(int)$item->show_in_nav_bar.' '.
			'WHERE id='.$item->id);

		$this->_setItemTags($item->id, $tags);
		$this->_setItemFolders($item->id, $shortcuts);
	}

	/**
	 * Deletes item if only it's not root (home) folder.
	 */
	function deleteItem($itemId, $folderId = -1) {
			// If folder specified then remove given folder-item link
	if ($folderId != -1) {
			$this->_query('DELETE FROM Folder_Item WHERE item_id='.$itemId.' AND folder_id='.$folderId);
			$this->_deleteUnlinkedItems();
		} else {
			$this->_query('DELETE FROM Item WHERE id='.$itemId);
		}

		$this->_cleanupTags();
	}

	/**
	 * Deletes folder if only it's not root (home) folder.
	 */
	function deleteFolder($folderId, $parentId = -1) {
		if ($folderId != 1) {
			if ($parentId != -1) {
				$this->_query('DELETE FROM FolderShortcut WHERE folder_id='.$folderId.' AND parent_id='.$parentId);
			} else {
				$this->_query('DELETE FROM Folder WHERE id='.$folderId);
			}
			$this->_deleteUnlinkedItems();
			$this->_deleteUnlinkedFolders();

			$this->_cleanupTags();
		}
	}

	/**
	 * Deletes every sub-folder and item in the given folder.
	 */
	function deleteFolderContents($fid) {
		$this->_query('DELETE FROM FolderShortcut WHERE parent_id = '.$fid);
		$this->_query('DELETE FROM Folder_Item WHERE folder_id = '.$fid);
		$this->_deleteUnlinkedItems();
		$this->_deleteUnlinkedFolders();
	}

	function _deleteUnlinkedFolders() {
		$this->_query('DELETE Folder FROM Folder LEFT JOIN FolderShortcut ON id=folder_id WHERE parent_id IS NULL AND id != 1');
	}

	function _deleteUnlinkedItems() {
		$this->_query('DELETE Item FROM Item LEFT JOIN Folder_Item ON id=item_id WHERE folder_id IS NULL');
	}

	function _setItemTags($itemId, $tags, $new = false) {
		if (!$new)
			$this->_query("DELETE FROM Item_Tag WHERE item_id=$itemId");

		if ($tags && is_array($tags)) {
			foreach ($tags as $tag) {
				$tag = strtolower(trim($tag));
				if (strlen($tag) != 0) {
					$tagId = $this->_getTagId($tag);
					$this->_query("INSERT INTO Item_Tag (item_id, tag_id) VALUES (".$itemId.",".$tagId.")");
				}
			}
		}
	}

	function _setFolderTags($folderId, $tags, $new = false) {
		if (!$new)
			$this->_query("DELETE FROM Folder_Tag WHERE folder_id=".$folderId);

		if ($tags && is_array($tags)) {
			foreach ($tags as $tag) {
				$tag = strtolower(trim($tag));
				if (strlen($tag) != 0) {
					$tagId = $this->_getTagId($tag);
					$this->_query("INSERT INTO Folder_Tag (folder_id, tag_id) VALUES (".$folderId.",".$tagId.")");
				}
			}
		}
	}

	function _setPersonTags($personId, $tags, $new = false) {
		if (!$new)
			$this->_query("DELETE FROM Person_Tag WHERE person_id=".$personId);

		if ($tags && is_array($tags)) {
			foreach ($tags as $tag) {
				$tag = strtolower(trim($tag));
				if (strlen($tag) != 0) {
					$tagId = $this->_getTagId($tag);
					$this->_query("INSERT INTO Person_Tag (person_id, tag_id) VALUES (".$personId.",".$tagId.")");
				}
			}
		}
	}
	
	/** Marks the user as accepted license. */
	function acceptLicense($pid, $license, $time)
	{
		$license = $this->_escapeSQL($license);
		$this->_query("UPDATE Person SET license_accepted=$time, license_text='$license' WHERE id=$pid");
	}

	/**
	 * Returns ID of the tag. If tag is missing, creates it.
	 */
	function _getTagId($tag) {
		$tagId = $this->_findTagId($tag);
		if ($tagId == -1) {
			$this->_query("INSERT INTO Tag (name) VALUES ('".$this->_escapeSQL($tag)."')");
			$tagId = mysql_insert_id($this->link);
		}

		return $tagId;
	}

	/**
	 * Returns ID of the tag or -1 if not found.
	 */
	function _findTagId($tag) {
		$id = -1;
		$result = $this->_query("SELECT id FROM Tag WHERE name='".$this->_escapeSQL($tag)."'");
		if ($result) {
			if ($row = mysql_fetch_row($result))
				$id = $row[0];
			mysql_free_result($result);
		}

		return $id;
	}

	function _setItemFolders($itemId, $shortcutIds, $new = false) {
		if (!$new)
			$this->_query("DELETE FROM Folder_Item WHERE item_id=".$itemId);

		if ($shortcutIds) {
			if (!is_array($shortcutIds))
				$shortcutIds = array ($shortcutIds);
			foreach ($shortcutIds as $sid) {
				$this->_query("INSERT INTO Folder_Item (item_id, folder_id) VALUES (".$itemId.", ".$sid.")");
			}
		}
	}

	function _setFolderShortcuts($folderId, $shortcutIds, $new = false) {
		if (!$new)
			$this->_query('DELETE FROM FolderShortcut WHERE folder_id='.$folderId);

		if ($shortcutIds) {
			if (!is_array($shortcutIds))
				$shortcutIds = array ($shortcutIds);
			foreach ($shortcutIds as $sid) {
				$this->_query('INSERT INTO FolderShortcut (folder_id, parent_id) VALUES ('.$folderId.', '.$sid.')');
			}
		}
	}

	function _cleanupTags() {
		// TODO: remove tags we no longer need
	}

	function getAllTagsWithCounters() {
		$tags = array ();

		// Read the list of tags
		$res = $this->_query('SELECT id, name FROM Tag ORDER BY name');
		if ($res) {
			while ($row = mysql_fetch_array($res))
				$tags[$row[0]] = array ('id' => $row[0], 'name' => $row[1], 'folders' => 0, 'items' => 0, 'users' => 0);
			mysql_free_result($res);

			// Fetch folder counts
			$res = $this->_query('SELECT tag_id, count(*) FROM Folder_Tag GROUP BY tag_id');
			if ($res) {
				while ($row = mysql_fetch_array($res))
					$tags[$row[0]]['folders'] = $row[1];
				mysql_free_result($res);
			}

			// Fetch item counts
			$res = $this->_query('SELECT tag_id, count(*) FROM Item_Tag GROUP BY tag_id');
			if ($res) {
				while ($row = mysql_fetch_array($res))
					$tags[$row[0]]['items'] = $row[1];
				mysql_free_result($res);
			}

			// Fetch user counts
			$res = $this->_query('SELECT tag_id, count(*) FROM Person_Tag GROUP BY tag_id');
			if ($res) {
				while ($row = mysql_fetch_array($res))
					$tags[$row[0]]['users'] = $row[1];
				mysql_free_result($res);
			}
		}

		return $tags;
	}

	/**
	 * Deletes all the tags from the list.
	 */
	function deleteTags($tags) {
		$this->_query('DELETE FROM Tag WHERE id IN ('.join(',', $tags).')');
	}

	/**
	 * Merges all the tags from the list so that they point to the last.
	 */
	function mergeTags($tags)
	{
		if (count($tags) < 2) return;

		$main_id = $tags[count($tags) - 1];
		$old_ids = array_slice($tags, 0, count($tags) - 1);
		$old_idss = join(',', $old_ids);

		// Add mappings
		// 1. Delete all mappings from source tags we are going to update
		$this->_query("DELETE FROM TagsMapping WHERE from_tag IN (SELECT name FROM Tag WHERE id IN ($old_idss))");
		// 2. Update destination from all tags we merging into the main to the main
		$this->_query("UPDATE TagsMapping SET to_tag = (SELECT name FROM Tag WHERE id = $main_id) WHERE to_tag IN (SELECT name FROM Tag WHERE id IN ($old_idss))");
		// 3. Insert source - destination mappings
		$this->_query("INSERT INTO TagsMapping (to_tag, from_tag) SELECT f.name, t.name FROM Tag f, Tag t WHERE f.id=$main_id AND t.id IN ($old_idss)");
		
		// Update tags
		$this->_query('UPDATE Folder_Tag SET tag_id = '.$main_id.' WHERE tag_id IN ('.$old_idss.')');
		$this->_query('UPDATE Item_Tag SET tag_id = '.$main_id.' WHERE tag_id IN ('.$old_idss.')');
		$this->_query('UPDATE Person_Tag SET tag_id = '.$main_id.' WHERE tag_id IN ('.$old_idss.')');
		$this->deleteTags($old_ids);
	}

	/**
	 * Renames tag. Returns TRUE if merging of tags took place.
	 */
	function renameTag($tid, $name) {
		$merged = false;

		// Check if tag with this name already exists
		$existingTagId = $this->_findTagId($name);
		if ($existingTagId == -1) {
			$this->_query('UPDATE Tag SET name=\''.$this->_escapeSQL($name).'\' WHERE id='.$tid);
		} else
			if ($existingTagId != $tid) {
				$this->mergeTags(array ($tid, $existingTagId));
				$merged = true;
			}

		return $merged;
	}

	// --- Items ----------------------------------------------------------------------------------

	/**
	 * Returns item object by its ID.
	 */
	function findItemByID($itemId) {
		$item = null;

		$result = $this->_query("SELECT * FROM Item WHERE id=".$itemId);
		if ($result && $row = mysql_fetch_assoc($result)) {
			$item = $this->_row2Item($row);
		}

		mysql_free_result($result);

		return $item;
	}

	/**
	 * Returns the list of tags for a given item.
	 */
	function findItemTags($itemId) {
		$tags = array ();

		$result = $this->_query("SELECT t.* FROM Tag t JOIN Item_Tag i ON t.id=i.tag_id WHERE i.item_id=".$itemId);
		while ($result && $row = mysql_fetch_assoc($result))
			$tags[] = $row['name'];

		mysql_free_result($result);

		return $tags;
	}

	/**
	 * Returns the list of folders associated with an item.
	 */
	function findItemFolders($iid) {
		$ids = array();
		$titles = array();

		$result = $this->_query("SELECT f.id, f.title FROM Folder_Item fi LEFT JOIN Folder f ON fi.folder_id=f.id WHERE item_id=$iid");
		if ($result) {
			while ($row = mysql_fetch_array($result)) {
				$ids []= $row[0];
				$titles []= $row[1];
			}
			mysql_free_result($result);
		}

		return array('ids' => $ids, 'titles' => $titles);
	}

	// --- People ---------------------------------------------------------------------------------

	/**
	 * Registers login of the user.
	 */
	function registerLogin($pid) {
		$this->_query('UPDATE Person SET last_login='.mktime().' WHERE id='.$pid);
	}

	/**
	 * Returns person by ID.
	 */
	function findPersonByID($personId) {
		$person = null;

		$result = $this->_query("SELECT * FROM Person WHERE id=".$personId);
		if ($result) {
			if ($row = mysql_fetch_assoc($result))
				$person = $this->_row2Person($row);
			mysql_free_result($result);
		}

		return $person;
	}

	/**
	 * Finds person by name.
	 */
	function findPersonByName($username) {
		$person = null;

		$result = $this->_query("SELECT * FROM Person WHERE userName='".$this->_escapeSQL($username).'\'');
		if ($result) {
			if ($row = mysql_fetch_assoc($result))
				$person = $this->_row2Person($row);
			mysql_free_result($result);
		}

		return $person;
	}

	/**
	 * Returns the list of tags for a given person.
	 */
	function findPersonTags($personId) {
		$tags = array ();

		$result = $this->_query("SELECT t.* FROM Tag t JOIN Person_Tag p ON t.id=p.tag_id WHERE person_id=".$personId);
		while ($result && $row = mysql_fetch_assoc($result)) {
			$tags[] = $row['name'];
		}

		if ($result)
			mysql_free_result($result);

		return $tags;
	}
	/**
	 * Returns permissions collection for a given account type.
	 */
	function getPermissions($account_type_id) {
		$perms = array ();

		$res = $this->_query('SELECT * FROM AccountType WHERE id='.$account_type_id);
		if ($res) {
			if ($row = mysql_fetch_assoc($res)) {
				foreach ($row as $perm => $val) {
					if ($val == 'Y')
						$perms[] = $perm;
				}
			}

			mysql_free_result($res);
		}

		return $perms;
	}

	// --- Organizations --------------------------------------------------------------------------

	/**
	 * Fetches information about given organization.
	 */
	function findOrganizationById($oid) {
		$org = null;

		$res = $this->_query('SELECT o.id, o.title, o.recommendations_folder_id, f.title as recommendations_folder_title, COUNT(p.id) AS users '.'FROM Organization o LEFT JOIN Folder f ON f.id = o.recommendations_folder_id '.'LEFT JOIN Person p ON p.organization_id = o.id '.'WHERE o.id='.$oid.' '.'GROUP BY o.id');

		if ($res) {
			$org = mysql_fetch_assoc($res);
			mysql_free_result($res);
		}

		return $org;
	}

	/**
	 * Returns the list of all organizations in the system.
	 */
	function getOrganizations() {
		$orgs = array ();

		$res = $this->_query('SELECT COUNT(p.id) as users '.'FROM Person p WHERE organization_id IS NULL');

		if ($res && $row = mysql_fetch_assoc($res)) {
			$orgs[] = array ('id' => null, 'title' => 'Default', 'recommendations_folder_id' => null, 'recommendations_folder_title' => null, 'users' => $row['users']);

			mysql_free_result($res);
		}

		$res = $this->_query('SELECT o.id, o.title, o.recommendations_folder_id, f.title as recommendations_folder_title, COUNT(p.id) AS users '.'FROM Organization o LEFT JOIN Folder f ON f.id = o.recommendations_folder_id '.'LEFT JOIN Person p ON p.organization_id = o.id '.'GROUP BY o.id '.'ORDER BY o.title');

		if ($res) {
			while ($row = mysql_fetch_assoc($res))
				$orgs[] = $row;

			mysql_free_result($res);
		}

		return $orgs;
	}

	/**
	 * Adds organization with given properties.
	 */
	function addOrganization($title, $recommendations_folder_id) {
		$this->_query('INSERT INTO Organization (title, recommendations_folder_id) '.'VALUES (\''.$this->_escapeSQL($title).'\', '. (isset ($recommendations_folder_id) ? $recommendations_folder_id : 'null').')');

		return mysql_insert_id($this->link);
	}

	/**
	 * Updates organization record.
	 */
	function updateOrganization($oid, $title, $recommendations_folder_id) {
		$this->_query('UPDATE Organization SET '.'title=\''.$this->_escapeSQL($title).'\', '.'recommendations_folder_id='. (isset ($recommendations_folder_id) ? $recommendations_folder_id : 'null').' '.'WHERE id='.$oid);
	}

	/**
	 * Deletes organization.
	 */
	function deleteOrganization($orgId) {
		$this->_query('DELETE FROM Organization WHERE id='.$orgId);
	}

	// --- Users ----------------------------------------------------------------------------------

	/**
	 * Returns the list of all users.
	 */
	function getAllUsers() {
		$people = array ();

		$res = $this->_query('SELECT * FROM Person ORDER BY fullName');
		if ($res) {
			while ($row = mysql_fetch_assoc($res))
				$people[] = $this->_row2Person($row);
			mysql_free_result($res);
		}

		return $people;
	}

	/**
	 * Returns the user by its ID.
	 */
	function findUserById($pid) {
		$person = null;

		$res = $this->_query('SELECT * FROM Person WHERE id='.$pid);
		if ($res) {
			if ($row = mysql_fetch_assoc($res))
				$person = $this->_row2Person($row);
			mysql_free_result($res);
		}

		return $person;
	}

	/**
	 * Adds person record.
	 */
	function addPerson($person, $tags = null) {
		$this->_query('INSERT INTO Person (fullName, email, userName, passwd, type_id, organization_id, description, home_page, no_ads) VALUES (\''.
			$this->_escapeSQL($person->fullName).'\', '.
			'\''.$this->_escapeSQL($person->email).'\', '.
			'\''.$this->_escapeSQL($person->userName).'\', '.
			'\''.$this->_escapeSQL($person->password).'\', '.
			$person->type_id.', '. 
			($person->organization_id && $person->organization_id != -1 ? $person->organization_id : 'null').', '.
			'\''.$this->_escapeSQL($person->description).'\', '.
			'\''.$this->_escapeSQL($person->home_page).'\', ' .
			(int)$person->no_ads . ')');

		$id = mysql_insert_id($this->link);

		$this->_setPersonTags($id, $tags, true);

		return $id;
	}

	/**
	 * Removes person by given ID.
	 */
	function deletePerson($pid) {
		$this->_query('DELETE FROM Person WHERE id='.$pid);
	}

	/**
	 * Updates person record.
	 */
	function updatePerson($person, $tags = null) {
		$this->_query('UPDATE Person SET '.
			'fullName=\''.$this->_escapeSQL($person->fullName).'\''.
			',email=\''.$this->_escapeSQL($person->email).'\''. 
			($person->userName ? ',userName=\''.$this->_escapeSQL($person->userName).'\'' : ''). 
			($person->password ? ',passwd=\''.$this->_escapeSQL($person->password).'\'' : '').
			',description=\''.$this->_escapeSQL($person->description).'\''.
			',home_page=\''.$this->_escapeSQL($person->home_page).'\''. 
			(isset($person->type_id) && $person->type_id != -1 ? ',type_id='.$person->type_id : ''). 
			(isset($person->organization_id) ? ',organization_id='. ($person->organization_id != -1 ? $person->organization_id : 'null') : '').
			',no_ads='.(int)$person->no_ads .
			' WHERE id='.$person->id);

		$this->_setPersonTags($person->id, $tags);
	}

	/**
	 * Converts database row into the person object.
	 */
	function _row2Person($row) {
		$person = new Person();
		$person->id = $row['id'];
		$person->userName = $row['userName'];
		$person->type_id = $row['type_id'];
		$person->fullName = $row['fullName'];
		$person->email = $row['email'];
		$person->password = $row['passwd'];
		$person->description = $row['description'];
		$person->home_page = $row['home_page'];
		$person->organization_id = $row['organization_id'];
		$person->last_login = $row['last_login'];
		$person->license_accepted = $row['license_accepted'];
		$person->no_ads = $row['no_ads'];

		return $person;
	}

	/**
	 * Returns the list of recommendataions or NULL.
	 */
	function getRecommendations($orgId) {
		$recs = null;

		$res = $this->_query('SELECT recommendations_folder_id FROM Organization WHERE id='.$orgId);
		if ($res) {
			if ($row = mysql_fetch_array($res)) {
				$fid = $row[0];
				if ($fid) {
					$recs = new Folder();
					$recs->items = $this->findFolderItems($fid);
					$recs->subfolders = $this->findSubFolders($fid);
				}
			}

			mysql_free_result($res);
		}

		return $recs;
	}

	// --- Bookmarking ----------------------------------------------------------------------------

	/**
	 * Adds a bookmark to the list of the user.
	 */
	function addBookmark($pid, $fid) {
		$res = $this->_query('SELECT COUNT(*) FROM Bookmark WHERE person_id='.$pid.' AND folder_id='.$fid);
		if ($res) {
			if ($row = mysql_fetch_array($res)) {
				if ($row[0] == 0)
					$this->_query('INSERT INTO Bookmark (person_id, folder_id) VALUES ('.$pid.', '.$fid.')');
			}

			mysql_free_result($res);
		}
	}

	/**
	 * Removes a bookmark from the list of the user.
	 */
	function removeBookmark($pid, $fid) {
		$this->_query('DELETE FROM Bookmark WHERE person_id='.$pid.' AND folder_id='.$fid);
	}

	/**
	 * Returns the list of folders, bookmarked by the given user.
	 */
	function getBookmarks($pid) {
		$folders = array ();

		$res = $this->_query('SELECT f.* FROM Bookmark b JOIN Folder f ON b.folder_id=f.id WHERE person_id='.$pid.' ORDER BY title');
		if ($res) {
			while ($row = mysql_fetch_assoc($res))
				$folders[] = $this->_row2Folder($row);
			mysql_free_result($res);
		}

		return $folders;
	}

	// --- Dictionaries ---------------------------------------------------------------------------

	/**
	 * Returns all available view types.
	 */
	function getViewTypes() {
		return $this->_getDictionary('ViewType');
	}

	/**
	 * Returns all available item types.
	 */
	function getItemTypes() {
		return $this->_getDictionary('ItemType');
	}

	/**
	 * Returns all available account types.
	 */
	function getAccountTypes() {
		return $this->_getDictionary('AccountType');
	}

	/**
	 * Returns all organizations.
	 */
	function getOrganizationsDict() {
		return $this->_getDictionary('Organization', 'title');
	}

	/**
	 * Returns all items from the given dictionary.
	 */
	function _getDictionary($dictionary, $nameField = 'name') {
		$items = null;

		$result = $this->_query('SELECT id, '.$nameField.' FROM '.$dictionary.' ORDER BY '.$nameField);
		if ($result) {
			$items = array ();
			while ($row = mysql_fetch_array($result))
				$items[$row[0]] = $row[1];

			mysql_free_result($result);
		}

		return $items;
	}

	/**
	 * Registers item access and returns the data URL or NULL if item wasn't found.
	 */
	function processItemAccess($iid) {
		$dataURL = null;

		$res = $this->_query('SELECT accessCount, dataURL FROM Item WHERE id='.$iid);
		if ($res) {
			if ($row = mysql_fetch_array($res)) {
				$count = $row[0];
				$dataURL = $row[1];

				$this->_query('UPDATE Item SET accessCount='. ($count +1).' WHERE id='.$iid);
			}

			mysql_free_result($res);
		}

		return $dataURL;
	}

	/**
	 * Returns the list of top items.
	 */
	function getTopItems($max) {
		$items = array ();

		$res = $this->_query('SELECT *, SUM(accessCount) as accessCnt FROM Item GROUP BY dataURL ORDER BY accessCnt DESC, title ASC'. ($max > -1 ? ' LIMIT '.$max : ''));
		if ($res) {
			while ($row = mysql_fetch_assoc($res))
				$items[] = $this->_row2Item($row);
			mysql_free_result($res);
		}

		return $items;
	}

	// --- News -----------------------------------------------------------------------------------

	/**
	 * Returns a news item.
	 */
	function getNewsItem($niid) {
		$item = null;
		$res = $this->_query('SELECT n.id, n.title, n.created, n.txt, p.fullname, n.public, n.folder_id, f.owner_id folder_owner_id, n.author_id '.
			'FROM NewsItem n LEFT JOIN Person p ON n.author_id = p.id '.
			'LEFT JOIN Folder f ON n.folder_id=f.id '. 
			'WHERE n.id = '.$niid);

		if ($res) {
			if ($row = mysql_fetch_assoc($res)) {
				$item = $this->_row2NewsItem($row);
			}

			mysql_free_result($res);
		}

		return $item;
	}

	/**
	 * Returns the list of all news items.
	 */
	function getNewsItems($onlyPublic = true, $max = -1)
	{
		$items = null;

		$res = $this->_query('SELECT n.id, n.title, n.created, n.txt, p.fullname, n.public, n.folder_id, f.owner_id folder_owner_id, n.author_id '.
			'FROM NewsItem n LEFT JOIN Person p ON n.author_id = p.id ' .
			'LEFT JOIN Folder f ON n.folder_id=f.id '. 
			($onlyPublic ? 'WHERE public = 1 ' : '').
			'ORDER BY created DESC'. 
			($max > 0 ? ' LIMIT '.$max : ''));

		if ($res)
		{
			while ($row = mysql_fetch_assoc($res))
			{
				$items[] = $this->_row2NewsItem($row);
			}

			mysql_free_result($res);
		}

		return $items;
	}

	/**
	 * Deletes news item.
	 */
	function deleteNewsItem($niid) {
		$this->_query('DELETE FROM NewsItem WHERE id = '.$niid);
	}

	/**
	 * Updates the item with given information.
	 */
	function updateNewsItem($niid, $title, $text, $public) {
		$this->_query('UPDATE NewsItem SET '.'title=\''.$this->_escapeSQL($title).'\', '.'txt=\''.$this->_escapeSQL($text).'\', '.'public='. ($public ? 1 : 0).' '.'WHERE id = '.$niid);
	}

	/**
	 * Adds new item and returns its ID.
	 */
	function addNewsItem($title, $text, $public, $date, $uid, $fid = false)
	{
		$this->_query('INSERT INTO NewsItem (title, txt, public, created, author_id, folder_id) '.
			'VALUES (\''.
			$this->_escapeSQL($title).'\', \''.
			$this->_escapeSQL($text).'\', '.
			($public ? 1 : 0).', '.
			$date.', '.
			$uid.', '.
			($fid ? $fid : 'NULL').')');

		return mysql_insert_id($this->link);
	}

	// --- Properties -----------------------------------------------------------------------------

	/**
	 * Returns application properties.
	 */
	function getApplicationProperties() {
		$props = array ();
		$res = $this->_query('SELECT * FROM ApplicationProperty');
		if ($res) {
			while ($row = mysql_fetch_assoc($res))
				$props[$row['name']] = $row['value'];
			mysql_free_result($res);
		}
		return $props;
	}

	/** Returns the application property value. */
	function getApplicationProperty($name) {
		return $this->_query_value("SELECT value FROM ApplicationProperty WHERE name='$name'");
	}
	
	/**
	 * Updates application properties.
	 */
	function updateApplicationProperties($props) {
		$this->_start_transaction();
		foreach ($props as $name => $val) {
			$this->_query('DELETE FROM ApplicationProperty WHERE name=\''.$this->_escapeSQL($name).'\'');
			$this->_query('INSERT INTO ApplicationProperty (name, value) VALUES (\''.$this->_escapeSQL($name).'\', \''.$this->_escapeSQL($val).'\')');
		}
		$this->_commit();
	}

	/**
	 * Updates single application property.
	 */
	function updateApplicationProperty($name, $value) {
		$this->_start_transaction();
		$this->_query('DELETE FROM ApplicationProperty WHERE name=\''.$this->_escapeSQL($name).'\'');
		$this->_query('INSERT INTO ApplicationProperty (name, value) VALUES (\''.$this->_escapeSQL($name).'\', \''.$this->_escapeSQL($value).'\')');
		$this->_commit();
	}

	function _start_transaction() {
		$this->_query('START TRANSACTION');
	}

	function _commit() {
		$this->_query('COMMIT');
	}

	function _rollback() {
		$this->_query('ROLLBACK');
	}

	// --- Java Script ----------------------------------------------------------------------------

	function selectFolderItemsJS($fid, $sort, $order, $limit) {
		$res = $this->_query('(SELECT title, f.description, f.id, 0 type_id, 1 as is_folder, created, fullName author, NULL as dataUrl '.
			'FROM Folder f, FolderShortcut ff, Person p WHERE f.id=ff.folder_id and ff.parent_id='.$fid.
			' and f.owner_id=p.id) UNION '.
			'(SELECT title, i.description, i.id, i.type_id, 0 as is_folder, created, fullName author, dataUrl '.
			'FROM Item i, Folder_Item f, Person p WHERE i.id=f.item_id and f.folder_id='.$fid.
			' and i.owner_id=p.id) ORDER BY '.$sort.' '.$order. 
			($limit > -1 ? ' LIMIT '.$limit : ''));

		if ($res) {
			$items = array ();
			while ($row = mysql_fetch_assoc($res))
				$items[] = $row;

			mysql_free_result($res);
		}

		return $items;
	}

	/**
	 * Selects all folders and items created recently.
	 */
	function selectNewAdditionsJS($limit) {
		$lim = $limit > -1 ? ' LIMIT '.$limit : '';

		$res = $this->_query('(SELECT title, description, id, type_id, 0 as is_folder, created '.'FROM Item '.'ORDER BY created desc '.$lim.') UNION '.'(SELECT title, description, id, 0 type_id, 1 as is_folder, created '.'FROM Folder '.'ORDER BY created desc '.$lim.') '.'ORDER BY created desc '.$lim);

		if ($res) {
			$items = array ();
			while ($row = mysql_fetch_assoc($res))
				$items[] = $row;

			mysql_free_result($res);
		}

		return $items;
	}

	function findItems($search, $title, $description, $tags, $siteURL, $dataURL) {
		$escSearch = $this->_escapeSQL($search);

		$cond = array ('0=1');
		if ($title)
			$cond[] = 'title LIKE \'%'.$escSearch.'%\'';
		if ($description)
			$cond[] = 'description LIKE \'%'.$escSearch.'%\'';
		if ($siteURL)
			$cond[] = 'siteURL LIKE \'%'.$escSearch.'%\'';
		if ($dataURL)
			$cond[] = 'dataURL LIKE \'%'.$escSearch.'%\'';

		$cond_sql = implode(' OR ', $cond);
		$query = 'SELECT i.* FROM Item i WHERE '.$cond_sql.' GROUP BY dataURL';

		if ($tags) {
			$tags_query = 'SELECT i.* FROM Item i, Item_Tag it, Tag t '.'WHERE t.name=\''.$escSearch.'\' AND i.id=it.item_id AND t.id=it.tag_id GROUP BY dataURL';

			$query = '('.$query.') UNION ('.$tags_query.') ';
		}

		$query .= ' ORDER BY ord, title';

		$items = array ();
		$res = $this->_query($query);
		if ($res) {
			while ($row = mysql_fetch_assoc($res))
				$items[] = $this->_row2Item($row);
			mysql_free_result($res);
		}

		return $items;
	}

	function findFolders($search, $title, $description, $tags, $opmlURL) {
		$escSearch = $this->_escapeSQL($search);

		$cond = array ('0=1');
		if ($title)
			$cond[] = 'title LIKE \'%'.$escSearch.'%\'';
		if ($description)
			$cond[] = 'description LIKE \'%'.$escSearch.'%\'';
		if ($opmlURL)
			$cond[] = 'opml_url LIKE \'%'.$escSearch.'%\'';

		$cond_sql = implode(' OR ', $cond);
		$query = 'SELECT f.* FROM Folder f WHERE '.$cond_sql;

		if ($tags) {
			$tags_query = 'SELECT f.* FROM Tag t, Folder_Tag ft, Folder f '.'WHERE t.name=\''.$escSearch.'\' AND t.id=ft.tag_id AND ft.folder_id=f.id';

			$query = '('.$query.') UNION ('.$tags_query.')';
		}

		$query .= ' ORDER BY title';

		$items = array ();
		$res = $this->_query($query);
		if ($res) {
			while ($row = mysql_fetch_assoc($res))
				$items[] = $this->_row2Folder($row);
			mysql_free_result($res);
		}

		return $items;
	}

	/** Returns folders with 'show_in_nav_bar' flag set. */
	function findNavigationObjects()
	{
		$objects = array();
		
		// Select folders
		$res = $this->_query('SELECT * FROM Folder WHERE show_in_nav_bar = 1 AND id <> 1 ORDER BY title');
		if ($res) {
			while ($row = mysql_fetch_assoc($res))
				$objects[] = $this->_row2Folder($row);
			mysql_free_result($res);
		}

		// Select items
		$res = $this->_query('SELECT * FROM Item WHERE show_in_nav_bar = 1 ORDER BY title');
		if ($res) {
			while ($row = mysql_fetch_assoc($res))
				$objects[] = $this->_row2Item($row);
			mysql_free_result($res);
		}
		
		return $objects;
	}
	
	function findPeople($search, $name, $description, $tags) {
		$escSearch = $this->_escapeSQL($search);

		$cond = array ('0=1');
		if ($name) {
			$cond[] = 'username LIKE \'%'.$escSearch.'%\'';
			$cond[] = 'fullName LIKE \'%'.$escSearch.'%\'';
		}
		if ($description)
			$cond[] = 'description LIKE \'%'.$escSearch.'%\'';

		$cond_sql = implode(' OR ', $cond);
		$query = 'SELECT f.* FROM Person f WHERE '.$cond_sql;

		if ($tags) {
			$tags_query = 'SELECT p.* FROM Tag t, Person_Tag pt, Person p '.'WHERE t.name=\''.$escSearch.'\' AND t.id=pt.tag_id AND pt.person_id=p.id';

			$query = '('.$query.') UNION ('.$tags_query.')';
		}

		$query .= ' ORDER BY fullName';

		$items = array ();
		$res = $this->_query($query);
		if ($res) {
			while ($row = mysql_fetch_assoc($res))
				$items[] = $this->_row2Person($row);
			mysql_free_result($res);
		}

		return $items;
	}

	/** Sets the latest available verson (on the server). */
	function setAvailableVersion($version)
	{
		$this->updateApplicationProperty('available_version', $version);
	}
	
	// --------------------------------------------------------------------------------------------
	// Statistics
	// --------------------------------------------------------------------------------------------

	function getUsersCount()
	{
		return $this->_query_value('SELECT COUNT(*) FROM Person');
	}
	
	function getFoldersCount()
	{
		return $this->_query_value('SELECT COUNT(*) FROM Folder');
	}
	
	function getItemsCount()
	{
		return $this->_query_value('SELECT COUNT(*) FROM Item');
	}
	
	function getLastLogin()
	{
		return $this->_query_value('SELECT MAX(last_login) FROM Person');
	}
	
	// --------------------------------------------------------------------------------------------

	/**
	 * Converts single database row into news item.
	 */
	function _row2NewsItem($row)
	{
		$item = null;

		if (isset ($row['id']))
		{
			$item = array (
				'id' => $row['id'],
				'title' => $row['title'],
				'date' => $row['created'],
				'text' => $row['txt'],
				'author' => $row['fullname'],
				'public' => $row['public'],
				'folder_id' => $row['folder_id'],
				'author_id' => $row['author_id'],
				'folder_owner_id' => $row['folder_owner_id']);
		}

		return $item;
	}

	/**
	 * Converts single database row into folder object.
	 */
	function _row2Folder($row) {
		$folder = null;

		if (isset ($row['id'])) {
			$folder = new Folder();
			$folder->id = $row['id'];
			$folder->title = $row['title'];
			$folder->description = $row['description'];
			$folder->created = $row['created'];
			$folder->owner_id = $row['owner_id'];
			$folder->viewType_id = $row['viewType_id'];
			$folder->viewTypeParam = $row['viewTypeParam'];

			$folder->opml = $row['opml'];
			$folder->opml_url = $row['opml_url'];
			$folder->opml_user = $row['opml_user'];
			$folder->opml_password = $row['opml_password'];
			$folder->opml_updates_period = $row['opml_updates_period'];
			$folder->opml_last_updated = $row['opml_last_updated'];
			$folder->opml_last_error = $row['opml_last_error'];
			$folder->dynamic = $row['dynamic'];
			
			$folder->order = $row['ord'] == 99999 ? '' : $row['ord'];
			$folder->autoTags = (bool)$row['autoTags'];
			$folder->show_in_nav_bar = (bool)$row['show_in_nav_bar'];
		}

		return $folder;
	}

	/**
	 * Converts single database row into item object.
	 */
	function _row2Item($row) {
		$item = new Item();
		$item->id = $row['id'];
		$item->title = $row['title'];
		$item->description = $row['description'];
		$item->created = $row['created'];
		$item->owner_id = $row['owner_id'];
		$item->siteURL = $row['siteUrl'];
		$item->dataURL = $row['dataUrl'];
		$item->type_id = $row['type_id'];

		$item->dynamic = $row['dynamic'];

		$item->technoInlinks = $row['technoInlinks'];
		$item->technoRank = $row['technoRank'];
		$item->order = $row['ord'] == 99999 ? '' : $row['ord'];
		
		$item->itunesURL = $row['itunesUrl'];
		$item->useITunesURL = (bool)$row['useITunesURL'];
		$item->showPreview = (bool)$row['showPreview'];
		$item->usePlayButtons = (bool)$row['usePlayButtons'];

		$item->autoTags = (bool)$row['autoTags'];
		$item->show_in_nav_bar = (bool)$row['show_in_nav_bar'];

		return $item;
	}

	function _escapeSQL($str) {
		if (get_magic_quotes_gpc())
			$str = stripslashes($str);

		// mysql_real_escape_string doesn't work on one of our test servers -- returning empty string
		return mysql_escape_string($str);
	}

	/**
	 * Performs the query.
	 */
	function _query($query) {
		if ($this->link == null)
			$this->connect();

		$result = mysql_query($query, $this->link);
		if (mysql_errno($this->link) != 0) {
			echo "<strong>Query:</strong> ".$query."</br>";
			echo mysql_error($this->link)."</br>";
			$this->_backtrace(debug_backtrace());
		}

		return $result;
	}

	/**
	 * Gets a single value.
	 */
	function _query_value($query)
	{
		$val = false;
		
		$res = $this->_query($query);
		
		if ($res) {
			$arr = mysql_fetch_array($res);
			$val = $arr[0];
			mysql_free_result($res);
		}
		
		return $val;
	}

	/**
	 * Fetches rows from the query.
	 */	
	function _query_rows($query)
	{
		$rows = array();
		
		$res = $this->_query($query);
		
		if ($res)
		{
			while ($row = mysql_fetch_assoc($res)) $rows []= $row;
			mysql_free_result($res);
		}
		
		return $rows;
	}
	
	/**
	 * Queries IDs. Returns the list of IDs.
	 */
	function _query_ids($select)
	{
		$ids = array ();

		$res = $this->_query($select);
		if ($res) {
			while ($row = mysql_fetch_array($res))
				$ids[] = $row[0];
			mysql_free_result($res);
		}

		return $ids;
	}

	function _backtrace($backtrace) {
		foreach ($backtrace as $call) {
			echo '<strong>'.$call['file'].'</strong> line: '.$call['line'].' '.$call['function'].'()<br>';
		}
	}
}
?>