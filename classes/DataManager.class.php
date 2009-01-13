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
// $Id: DataManager.class.php,v 1.19 2007/09/26 12:48:45 alg Exp $
//

require_once 'Database.class.php';
require_once 'PathFinder.class.php';
require_once 'Generator.class.php';
require_once 'TagsManager.class.php';

define('PAGE_ITEMS', 16);

class DataManager
{
    var $db;

    function DataManager()
    {
        $this->db = new Database();
    }
    
    function close()
    {
        $this->db->disconnect();
    }
    
    /**
     * Looks for user information by username and password.
     */
    function getPersonByUsername($username, $password)
    {
    	$user = $this->db->findPersonByName($username);
    	
    	return $user->password == $password ? $user : null;
    }

    /**
     * Registers login of the user.
     */
	function registerLogin($pid)
	{
		$this->db->registerLogin($pid);
	}
	
	/**
	 * Returns permissions collection for a given account type.
	 */    
    function getPermissions($account_type_id)
    {
    	return $this->db->getPermissions($account_type_id);
    }
    
    /**
     * Returns the row from the folder table.
     */
    function getFolder($folderId)
    {
        return $this->db->findFolderByID($folderId);
    }
    
    /**
     * Looks up the array of folder by given opml url.
     */
    function getOPMLFolderIDsByURL($url)
    {
    	return $this->db->getOPMLFolderIDsByURL($url);
    }

	/**
	 * Returns the list of folder IDs of OPML folders to update. 
	 */
	function getOPMLFolderIDsToUpdate($maximum_update_time)
	{
		return $this->db->getOPMLFolderIDsToUpdate($maximum_update_time);
	}
    
    /**
     * Returns the array of parents linked through shortcuts.
     */
    function getFolderShortcutParents($folderId)
    {
        return $this->db->findFolderShortcutParents($folderId);
    }
    
    /**
     * Returns the list of all available view types.
     */
    function getViewTypes()
    {
        return $this->db->getViewTypes();
    }

    /**
     * Returns the list of all available item types.
     */
    function getItemTypes()
    {
        return $this->db->getItemTypes();
    }
 
 	/**
 	 * Returns the list of all available account types.
 	 */
 	function getAccountTypes()
 	{
 		return $this->db->getAccountTypes();
 	}
 	   
    // --- Data Blocks -----------------------------------------------------------

    /**
     * Returns folder with items and subfolders. No subfolders have items and subfolders,
     * but they have "hasChildren" boolean property indicating whether they have
     * children or not. This is useful for tree rendering.
     */
    function getFolderTreeInfo($folderId)
    {
        $folder = $this->db->findFolderByID($folderId);
        $folder->items = $this->db->findFolderItems($folder->id);
        $folder->tags = $this->db->findFolderTags($folder->id);
        $folder->subfolders = $this->db->findSubFolders($folder->id);
        
        // Detect whether subfolders have some items in them or no
        foreach ($folder->subfolders as $k => $v)
        {
            $folder->subfolders[$k]->hasChildren = $this->db->hasChildren($v->id);
        }
        
        return $folder;
    }
    
    /**
     * Returns folder view information block. It includes information about
     * this folder, its items and sub-folders with their items.
     */
    function getFolderViewInfo($folderId, $deep = false)
    {
        $folder = $this->db->findFolderByID($folderId);
        if ($folder)
        {
        	$folder = $this->_getFolderViewInfo($folder, $deep);
        }
        
        return $folder; 
    }
    
    /**
     * Loads information about the given folder (tags, items and subfolders). If
     * "deep" is set to true, loads all the subfolders recursively.
     */
    function _getFolderViewInfo($folder, $deep = false)
    {
        if ($folder)
        {
            $folder->items = $this->db->findFolderItems($folder->id);
            $folder->tags = $this->db->findFolderTags($folder->id);
            $subfolders = $this->db->findSubFolders($folder->id);
            
            foreach ($subfolders as $key => $val)
            {
                if ($deep)
                {
                    $subfolders[$key] = $this->db->_getFolderViewInfo($subfolders[$key], $deep);
                } else
                {
                	$sfid = $subfolders[$key]->id;
                	
                	$subfolders[$key]->subfolders = $this->db->findSubFolders($sfid);
                    $subfolders[$key]->items = $this->db->findFolderItems($sfid, 0, PAGE_ITEMS);
                }
            }
            $folder->subfolders = $subfolders;
        }
                
        return $folder;
    }
    
    /**
     * Returns the information about this folder for editing.
     */
    function getFolderEditInfo($folderId)
    {
        $info = $this->getFolderViewInfo($folderId);
        $info->hasChildren = $this->db->hasChildren($folderId);
        $info->directChildren = $this->db->findFolderDirectChildren($folderId);
        
        return $info;
    }
    
    /**
     * Returns item view information block. It includes information about
     * this item.
     */
    function getItemViewInfo($itemId)
    {
        $item = $this->db->findItemByID($itemId);
        if ($item)
        {
            $item->tags = $this->db->findItemTags($itemId);
        }
                
        return $item;
    }

    /**
     * Returns item edit information block. It includes information about
     * this item.
     */
    function getItemEditInfo($itemId)
    {
        $item = $this->getItemViewInfo($itemId);
        $item->folders = $this->db->findItemFolders($itemId);
                
        return $item;
    }

    /**
     * Returns the list of organizations registered in the system.
     */
    function getOrganizationsList()
    {
        return $this->db->getOrganizations();
    }

	/**
	 * Returns organizations dictionary (id-title).
	 */
	function getOrganizationsDict()
	{
		return $this->db->getOrganizationsDict();
	}
	    
    /**
     * Returns the information necessary for organization editing.
     */
    function getOrganizationEditInfo($oid)
    {
    	return $this->db->findOrganizationById($oid);
    }
    
    /**
     * Returns the list of folder_id-folder_title elements.
     */
    function getAllFolders()
    {
        return $this->db->getAllFolders();
    }
    
    /**
     * Returns the list of all authors and librarians.
     */
    function getAllAuthors()
    {
    	return $this->db->getAllAuthors();
    }
    
    /**
     * Returns the list of all users registered.
     */
    function getPersonsList()
    {
    	$people = $this->db->getAllUsers();
    	$types = $this->getAccountTypes();
    	
    	foreach ($people as $k => $person)
    	{
    		$people[$k]->accountType = $types[$person->type_id];
    	}
    	
    	return $people;
    }
    
    /**
     * Returns information necessary for editing the person.
     */
    function getPersonEditInfo($pid)
    {
    	$person = $this->db->findUserById($pid);
    	if ($person)
    	{
    		$person->tags = $this->db->findPersonTags($pid); 
    	}
    	
    	return $person;
    }
 
 	/**
 	 * Returns the list of all folders owned by the user.
 	 */
 	function getPersonFolders($pid)
 	{
 		return $this->db->findPersonFolders($pid);
 	}
 	   
    /**
     * Returns the list of all tags with items/folders/users counters.
     */
    function getAllTagsWithCounters()
    {
   		return $this->db->getAllTagsWithCounters();
    }
    
    // --- Side Blocks -----------------------------------------------------------
	
	/**
	 * Returns path object to display in path area for a given folder.
	 */
	function getPath($id, $is_folder = true)
	{
		$path = PathFinder::find_shortest_path($id, 1, $is_folder);
		if ($path == null)
		{
			$path = array($path);
		} else
		{
			$parents = $is_folder ? $this->getFolderShortcutParents($id) : $this->db->findParentFoldersOfItem($id);
			if (count($parents) > 1)
			{
				$pathlen = count($path);
				
				$path_parent = $path[$pathlen - 2];
				$path_parent_id = $path_parent->id;
				$target = &$path[$pathlen - 1];
				$target->parents = array();
				
				foreach ($parents as $parent)
				{
					$parent_id = $parent->id;
					if ($path_parent_id != $parent_id)
					{
						$target->parents[] = $parent;
					}
				}
			}
		}
				
		return $path;
	}
	
	/**
	 * Returns the list of top 10 items.
	 */
	function getTopItems($max = 10)
	{
		return $this->db->getTopItems($max);
	}
	
    /**
     * Returns navigation sideblock for the folder.
     */
    function getFolderNavigationSideblock($personId, $folderId)
    {
        $data = array();

		// Get navigation folders
		$nav = $this->db->findNavigationObjects();
		$nav_ids = $this->_collect_folder_ids($nav);

        // Put parents in the data array        
        $parents = array();
        $home = $this->db->findHomeFolder();
        $parents[] = &$home;
        $shortcuts = $this->db->findFolderShortcutParents($folderId);
        $shortcuts = $this->_filter_folders_out($shortcuts, $nav_ids);
        for ($i = 0; $i < count($shortcuts); $i++)
        {
        	if ($shortcuts[$i]->id != $home->id) $parents[] = &$shortcuts[$i];
        } 
        $data[] = $parents; 
        
        // Put sub-folders
        $subfolders = $this->db->findSubFolders($folderId);
        $subfolders = $this->_filter_folders_out($subfolders, $nav_ids);
        if (count($subfolders) > 0) $data[] = $subfolders;

		if ($personId && $personId != -1)
		{
			$bookmarks = $this->getBookmarks($personId);
		} else $bookmarks = array();
		
		// Put navigation folders
		if (count($nav) > 0) $data[] = $nav;

		return array('blocks' => &$data, 'bookmarks' => &$bookmarks);
    }
    
    /**
     * Returns navigation sideblock for the item.
     */
    function getItemNavigationSideblock($personId, $itemId)
    {
        $data = array();

		// Get navigation folders
		$nav = $this->db->findNavigationObjects();
		$nav_ids = $this->_collect_folder_ids($nav);
		
        // Put parents in the data array        
        $parents = $this->db->findParentFoldersOfItem($itemId, true);
        $parents = $this->_filter_folders_out($parents, $nav_ids);

        // Add the Home to the beginning
        $home = $this->db->findHomeFolder();
        array_unshift($parents, $home);
        $data[] = $parents; 
        
		if ($personId && $personId != -1)
		{
			$bookmarks = $this->getBookmarks($personId);
		} else $bookmarks = array();
		
		// Put navigation folders
		if (count($nav) > 0) $data[] = $nav;

		return array('blocks' => &$data, 'bookmarks' => &$bookmarks);
    }

	/** Returns the array of IDs of all folders in the source array. */
	function _collect_folder_ids($objects)
	{
		$ids = array();
		
		if ($objects != null)
		{
			foreach ($objects as $o)
			{
				// Check if $o is a folder
				if (is_a($o, 'Folder')) $ids[] = $o->id;
			}
		}
		
		return $ids;
	}

	/** Returns the array of folders with IDs not present in the ids array. */
	function _filter_folders_out($folders, $ids)
	{
		$fs = array();
		
		if ($folders != null && $ids != null &&
			count($folders) > 0 && count($ids) > 0)
		{
			foreach ($folders as $f)
			{
				if (!in_array($f->id, $ids)) $fs[] = $f;
			}
		}
		
		return $fs;
	}
		    
    /**
     * Returns the list of folders owned by person.
     */
    function getMyFoldersViewInfo($personId, $perm_edit_others_content)
    {
    	return $this->db->findAssignableFolders($personId, $perm_edit_others_content);
//        $folders = $this->db->findFoldersByOwnerId($personId, $perm_edit_others_content);
//
//        $my_folders = array();
//        foreach ($folders as $folder) $my_folders[$folder->id] = $folder->title;
//        
//        return $my_folders;
    }
    
    /**
     * Returns owner sideblock.
     */
    function getOwnerSideblock($personId)
    {
        return $this->getPersonInfo($personId);
    }
  
    /**
     * Returns information about the person.
     */
    function getPersonInfo($personId)
    {
        $person = $this->db->findPersonByID($personId);
        $person->tags = $this->db->findPersonTags($personId);
        return $person;
    }

    // --- OPML ------------------------------------------------------------------
    
    /**
     * Returns OPML corresponding to the top folder.
     */
    function getTopFolderOPML($count)
    {
    	$children = array();
    	
        // Items
        $items = $this->getTopItems($count);
        foreach ($items as $item)
        {
            $children[] = $this->_childItem($item);
        }

        return array('text' => 'Top ' . $count, 'children' => $children);
    }
    
    /**
     * Returns OPML structure for the folder.
     */
    function getFolderOPML($folder, $shallow, $item_descriptions, $tags, $foldersSeen = array(), $level = 0)
    {
    	if ($folder->dynamic == 0 && isset($folder->opml_url) && strlen(trim($folder->opml_url)) > 0)
    	{
    		// Return OPML
    		if ($level > 0)
    		{
    			$opml = $this->_folder2element($folder, $item_descriptions, false, $tags);
    		} else
    		{
    			$opml = $this->db->getFolderOPML($folder->id);
    		}
    	} else
    	{
	        // Add ID of this folder to don't let it appear again to avoid dead-loops
	        $foldersSeen[] = $folder->id;
	        
	        $children = array();

	        // Sub folders
	        $subfolders = $this->db->findSubFolders($folder->id);
	        foreach ($subfolders as $subfolder)
	        {
	            if (!in_array($subfolder->id, $foldersSeen))
	            {
		    	    if ($shallow)
		    	    {
		    	    	$children[] = $this->_folder2element($subfolder, $item_descriptions, true, $tags);
		    	    } else
		        	{
		                $children[] = $this->getFolderOPML($subfolder, false, $item_descriptions, $tags, $foldersSeen, $level + 1);
			        }
	            }
	        }
	    
	        // Items
	        $items = $this->db->findFolderItems($folder->id);
	        foreach ($items as $item)
	        {
	            $children[] = $this->_childItem($item, $item_descriptions, $tags);
	        }
	        
	        $opml = array('text' => $folder->title, 'children' => $children);
	        
	        if ($tags)
	        {
		        $folder_tags = $this->db->findFolderTags($folder->id);
	        	if (count($folder_tags) > 0) $opml['bb:tags'] = implode(',', $folder_tags);
	        }
    	}
        return $opml;
    }

	function _folder2element($subfolder, $item_descriptions, $shallow, $tags = false)
	{
		$options = '';
		if ($item_descriptions == 1) $options .= 'd';
		if ($shallow) $options .= 's';
		if ($tags) $options .= 't';
		
		$opml = array(
			'type' => 'link',
			'title' => $subfolder->title,
			'text' => $subfolder->description, 
			'xmlUrl' => $this->_folderURL($subfolder->id) . '.opml' .
				(strlen($options) > 0 ? '?o=' . $options : ''));
		
        if ($tags)
        {
	        $folder_tags = $this->db->findFolderTags($subfolder->id);
        	if (count($folder_tags) > 0) $opml['bb:tags'] = implode(',', $folder_tags);
        }

		return $opml;
	}
	
	/**
	 * Selects all folders and items created recently.
	 */
	function getNewAdditionsJS($limit)
	{
		$items = $this->db->selectNewAdditionsJS($limit);
    	$this->_addInfo($items);
    	
    	return $items;
	}
	
    /**
     * Returns structure for the folder.
     */
    function getFolderJS($fid, $sort, $order, $limit)
    {
    	$items = $this->db->selectFolderItemsJS($fid, $sort, $order, $limit);
    	$this->_addInfo($items, $this->_is_direct_feed_urls());
    	
    	return $items;
    }

	/**
	 * Singleton direct-feed-urls.
	 */
	function _is_direct_feed_urls()
	{
		static $direct_feed_urls = null;
		if (is_null($direct_feed_urls))
		{
			$p = $this->getApplicationProperties();
			$direct_feed_urls = isset($p['direct_feed_urls']) &&
				$p['direct_feed_urls'];
		}
		
		return $direct_feed_urls;
	}
	
	/**
	 * Singleton direct-feed-urls.
	 */
	function _is_generate_tags_and_descriptions()
	{
		static $generate_tags_and_descriptions = null;
		if (is_null($generate_tags_and_descriptions))
		{
			$p = $this->getApplicationProperties();
			$generate_tags_and_descriptions = isset($p['generate_tags_and_descriptions']) &&
				$p['generate_tags_and_descriptions'];
		}
		
		return $generate_tags_and_descriptions;
	}

	function _addInfo(&$items)
	{
    	if ($items)
    	{
    		foreach ($items as $k => $v)
    		{
    			$item = $v;
    			if ($item['is_folder'])
    			{
    				$url = $this->_folderURL($item['id']);
    				$xmlUrl = $url . '.opml';
    			} else
    			{
    				$url = $this->_itemURL($item['id']);
    				$xmlUrl = $this->_itemXMLURL($item);
    			}
    			
    			$items[$k]['url'] = $url;
    			$items[$k]['xmlUrl'] = $xmlUrl;
    		}
    	}
	}
	
	/**
	 * Returns child item array of attributes. If the item is outline,
	 * it makes sure that the XML URL ends with '.opml' applying a trick
	 * if necessary.
	 */    
    function _childItem($item, $item_descriptions = 0, $tags = 0)
    {
		$type = $item->type_id == 3 ? 'link' : 'rss';
		$xmlUrl = $item->type_id == 4 ? '' : $this->_itemXMLURL($item);
		    	
    	$child = array(
            'type' => $type,
            'text' => $item->title,
            'htmlUrl' => $item->siteURL);

        if ($xmlUrl != '') $child['xmlUrl'] = $xmlUrl;
                
        if ($item_descriptions == 1)
        {
        	$child['title'] = $item->title;
			$child['text'] = strlen($item->description) > 0 ? $item->description : $item->title;
        }

		if ($tags)
		{
			$item_tags = $this->db->findItemTags($item->id);
			if (count($item_tags) > 0) $child['bb:tags'] = implode(',', $item_tags);
		}
		        
        return $child;
    }
    
    /**
     * Returns XML URL of an item.
     */
    function _itemXMLURL($item)
    {
    	$dfu = $this->_is_direct_feed_urls();
    	$type_id = is_array($item) ? $item['type_id'] : $item->type_id;
    	$id = is_array($item) ? $item['id'] : $item->id;

    	if ($dfu && $type_id != 3)
    	{
    		$xmlUrl = is_array($item) ? $item['dataUrl'] : $item->dataURL;
    	} else
    	{
	    	$xmlUrl = $this->_itemURL($id) . ($type_id == 3 ? '.opml' : '.xml');
    	}
    	
    	return $xmlUrl;
    }
    
    /**
     * Imports outline.
     */
    function importOutline($parentId, $outline, $userId, $import_structure = true)
    {
        if (isset($outline['HTMLURL'])) $siteURL = $outline['HTMLURL'];
        if (isset($outline['XMLURL'])) $dataURL = $outline['XMLURL'];
        if (!isset($dataURL) && isset($outline['URL'])) $dataURL = $outline['URL'];
        if (isset($outline['TYPE'])) $type = strtolower($outline['TYPE']); else $type = 'link';

        if (isset($dataURL) && (isset($outline['TEXT']) || isset($outline['TITLE'])))
        {          
            $title = isset($outline['TEXT']) ? $outline['TEXT'] : $outline['TITLE'];
            if (isset($outline['BB:CUSTOMTITLE'])) $title = $outline['BB:CUSTOMTITLE'];
            
            if (!isset($siteURL)) $siteURL = $dataURL;
            $opml = ($type == 'link' && eregi('\.opml$', $dataURL)) || $type == 'include';
            
            // Feed
            $item = new Item();
            $item->title = $title;
            $item->siteURL = $siteURL;
            $item->dataURL = $dataURL;
            $item->owner_id = $userId;
            $item->type_id = $opml ? 3 : 1;
            
            $folders = array();
            $folders[] = $parentId;
            
            $this->addItem($item, $folders, DataManager::get_outline_tags($outline));
        } else if ($import_structure)
        {
            // Folder
            $folder = new Folder();
            if (isset($outline['TITLE'])) $folder->title = $outline['TITLE'];
            if (isset($outline['TEXT'])) $folder->title = $outline['TEXT'];
            if (isset($outline['DESCRIPTION'])) $folder->description = $outline['DESCRIPTION'];
            $folder->viewType_id = 1;
            $folder->owner_id = $userId;
            
            $folder = $this->addFolder($folder, array($parentId), DataManager::get_outline_tags($outline));
            
            foreach ($outline['children'] as $child)
            {
                $this->importOutline($folder->id, $child, $userId, $import_structure);
            }
        }        
    }

	/**
	 * Returns tags from the outline object if any present.
	 */
	function get_outline_tags(&$outline)
	{
		return isset($outline['BB:TAGS'])
			? $outline['BB:TAGS']
			: (isset($outline['TAGS'])
				? $outline['TAGS']
				: null);
	}
	
	/**
	 * Updates hierarchy of the OPML folder without OPML folder itself.
	 */
	function updateOPMLFolder($fid, $folder)
	{
		$this->deleteFolderContents($fid);
		$this->db->setFolderOPML($fid, $folder->opml);
		if ($folder->tags != null)
		{
			$this->db->setTagsIfNotSet($fid, $this->_str2tags($folder->tags));
		}
		$this->setFolderContents($folder, $fid);
	}

	/**
	 * Updates the last updated time of the folder.
	 */
	function updateOPMLFolderLastUpdated($fid)
	{
		$this->db->updateOPMLFolderLastUpdated($fid, mktime());
	}
		
	/**
	 * Deletes contents of the folder.
	 */
	function deleteFolderContents($fid)
	{
		$this->db->deleteFolderContents($fid);
	}
	
	/**
	 * Saves the contents of the folder and recurses for every sub-folder.
	 */
	function setFolderContents($folder, $pid)
	{
		if (isset($folder->children))
		{
			foreach($folder->children as $child)
			{
				if (is_a($child, 'Folder'))
				{
					$fld = $this->addFolder($child, array($pid), $child->tags);
					$this->setFolderContents($child, $fld->id);
				} else if (is_a($child, 'Item'))
				{
					$shortcuts = array();
					$shortcuts[] = $pid;
					
					$this->addItem($child, $shortcuts, $child->tags);
				}
			}
		}
	}

    // --- Actions ---------------------------------------------------------------
    
    /**
     * Adds the folder including shortcuts and tags associations.
     */
    function addFolder($folder, $shortcuts, $tags)
    {
        return $this->db->addFolder($folder, $shortcuts, $this->_folder_tags($folder, $tags)); 
    }
    
    /**
     * Updates the folder including shortcuts and tags associations.
     */
    function updateFolder($folder, $shortcuts, $tags)
    {        
        $this->db->updateFolder($folder, $shortcuts, $this->_folder_tags($folder, $tags));
    	PathFinder::invalidate_cache(true);

		if ($this->_is_generate_tags_and_descriptions())
		{
			$this->_regenerate_item_tags($folder);    	
		}
    }
    
    function _regenerate_item_tags($folder)
    {
    	// Update all autoTags items
    	$items = $this->db->findFolderItems($folder->id);
    	$ftags_map = array();
    	foreach ($items as $i)
    	{
    		if ($i->autoTags)
    		{
    			// Find shortcuts
    			$fs = $this->db->findItemFolders($i->id);
    			$iss = $fs['ids'];
    			
    			// Get all parents tags
    			$itags = array();
    			foreach ($iss as $is)
    			{
    				if (isset($ftags_map[$is]))
    				{
    					$ftags = $ftags_map[$is];
    				} else
    				{
    					$ftags = $this->db->findFolderTags($is);
    					$ftags_map[$is] = $ftags;
    				}
    				
    				$itags = array_merge($itags, $ftags);
    			}
    		
    			// Generate itags
    			$itags = Generator::tags($i->title, $itags);
				$itags = TagsManager::map($itags);
				
    			// Update item
    			$this->db->_setItemTags($i->id, $itags);	
    		}
    	}
    }
    
    function _folder_tags($folder, $tags)
    {
    	if ($folder->autoTags && $this->_is_generate_tags_and_descriptions())
    	{
			$t = Generator::tags($folder->title);
			$t = TagsManager::map($t);
    	} else
    	{
    		$t = $this->_str2tags($tags);
    	}
    	
		return $t;
    }
    
    /**
     * Adds the item including shortcuts and tags associations.
     */
    function addItem($item, $shortcuts, $tags)
    {
        return $this->db->addItem($item, $shortcuts, $this->_item_tags($item, $shortcuts, $tags));
    }
    
    function _item_tags($item, $shortcuts, $tags)
    {
    	if ($item->autoTags && $this->_is_generate_tags_and_descriptions())
    	{
    		// Get all parent tags
    		$parent_tags = array();
    		foreach ($shortcuts as $fid)
    		{
    			$parent_tags = array_merge($parent_tags, $this->db->findFolderTags($fid));
    		}
    		$parent_tags = array_unique($parent_tags);

    		$t = Generator::tags($item->title, $parent_tags);
			$t = TagsManager::map($t);
    	} else
    	{
    		$t = $this->_str2tags($tags);
    	}
    	
    	return $t;
    }
    
    /**
     * Updates the item including shortcuts and tags associations.
     */
    function updateItem($item, $shortcuts, $tags)
    {
        $this->db->updateItem($item, $shortcuts, $this->_item_tags($item, $shortcuts, $tags));
    	PathFinder::invalidate_cache(false);
    }

    /**
     * Deletes the item.
     */
    function deleteItem($itemId, $folderId = -1)
    {
        $this->db->deleteItem($itemId, $folderId);
    	PathFinder::invalidate_cache(false, $itemId);
    }
        
    /**
     * Deletes the folder and all associated stuff.
     */
    function deleteFolder($folderId, $parentId = -1)
    {
        $this->db->deleteFolder($folderId, $parentId);
    	PathFinder::invalidate_cache(true);
    }

    /**
     * Adds new organization to the database.
     */    
    function addOrganization($title, $recommendations_folder_id)
    {
        $this->db->addOrganization($title, $recommendations_folder_id == -1 ? null : $recommendations_folder_id);    
    }
    
    /**
     * Deletes organizations specified by ID's'.
     */
    function deleteOrganizations($orgs)
    {
        foreach($orgs as $orgId)
        {
            $this->db->deleteOrganization($orgId);
        }
    }
    
    /**
     * Saves changes to organization.
     */
    function updateOrganization($oid, $title, $recommendations_folder_id)
    {
    	$this->db->updateOrganization($oid, $title, $recommendations_folder_id == -1 ? null : $recommendations_folder_id);
    }
    
    /**
     * Adds person record.
     */
    function addPerson($person, $tags)
    {
    	return $this->db->addPerson($person, $this->_str2tags($tags));
    }
    
    /**
     * Deletes all people by their ID's.
     */
    function deletePeople($pids)
    {
    	foreach ($pids as $pid) $this->db->deletePerson($pid);
    }
    
    /**
     * Updates person record.
     */
    function updatePerson($person, $tags)
    {
    	$this->db->updatePerson($person, $this->_str2tags($tags));
    }

	/**
	 * Iterates through the list of people and adds the one by one if
	 * they don't exist yet. The duplicates are returned.
	 */
	function importPeople(&$people)
	{
		$dups = array();
		
		foreach ($people as $k => $person)
		{
			if (!$this->importPerson($person)) $dups[] = &$people[$k]; 
		}
		
		return $dups;	
	}
	
	/**
	 * Checks if the person exists and adds the record. Returns TRUE if added.
	 */
	function importPerson(&$person)
	{
		$added = false;
		if (!$this->db->findPersonByName($person->userName))
		{
			$this->addPerson($person, $person->tags);
			$added = true;
		}
		
		return $added;
	}
	
	/** Marks the user as accepted license. */
	function acceptLicense($pid, $license, $time)
	{
		$this->db->acceptLicense($pid, $license, $time);
	}
	
	/**
	 * Deletes all the tags from the list.
	 */
	function deleteTags($tags)
	{
		$this->db->deleteTags($tags);
	}
	
	/**
	 * Merges all the tags from the list so that they point to the last.
	 */
	function mergeTags($tags)
	{
		$this->db->mergeTags($tags);
	}
	
	/**
	 * Renames tag.
	 */
	function renameTag($tid, $name)
	{
		return $this->db->renameTag($tid, $name);
	}
	
    // --- Bookmarking -----------------------------------------------------------
    
    /**
     * Adds a bookmark to the list of the user.
     */
    function addBookmark($pid, $fid)
    {
    	$this->db->addBookmark($pid, $fid);
    }
    
    /**
     * Removes a bookmark from the list of the user.
     */
    function removeBookmark($pid, $fid)
    {
    	$this->db->removeBookmark($pid, $fid);
    }
    
    /**
     * Returns the list of folders, bookmarked by the given user.
     */
    function getBookmarks($pid)
    {
    	return $this->db->getBookmarks($pid);
    }
    
    // --- Recommendations -------------------------------------------------------
    
    /**
     * Returns the list of recommendataions or NULL.
     */
    function getRecommendations($orgId)
    {
    	$recs = null;
    	
    	if ($orgId)
    	{
    		$recs = $this->db->getRecommendations($orgId);
    	}
    	
    	return $recs;
    }

    // --- Data Access -----------------------------------------------------------
    
    /**
     * Registers item access and returns the data URL or NULL if item wasn't found.
     */
    function processItemAccess($iid)
    {
    	return $this->db->processItemAccess($iid);
    }

    // --- News ------------------------------------------------------------------
    
    /**
     * Returns a news item.
     */
    function getNewsItem($niid)
    {
    	return $this->db->getNewsItem($niid);
    }
    
    /**
     * Returns the list of all news items.
     */
    function getNewsItems($onlyPublic = true, $max = -1)
    {
    	return $this->db->getNewsItems($onlyPublic, $max);
    }
    
    /**
     * Adds new item and returns its ID.
     */
    function addNewsItem($title, $text, $public, $date, $uid)
    {
    	return $this->db->addNewsItem($title, $text, $public, $date, $uid);
    }
    
    /**
     * Updates the item with given information.
     */
    function updateNewsItem($niid, $title, $text, $public)
    {
    	$this->db->updateNewsItem($niid, $title, $text, $public);
    }
    
    /**
     * Deletes news item.
     */
    function deleteNewsItem($niid)
    {
    	$this->db->deleteNewsItem($niid);
    }
    
    // --- Properties ------------------------------------------------------------

    /**
     * Returns application properties.
     */
    function getApplicationProperties()
    {
    	return $this->db->getApplicationProperties();
    }
    
    /**
     * Updates application properties.
     */
    function updateApplicationProperties($props)
    {
    	$this->db->updateApplicationProperties($props);
    }
    
    /**
     * Updates single application property.
     */
    function updateApplicationProperty($name, $value)
	{
    	$this->db->updateApplicationProperty($name, $value);
	}
	
    // --- Private Functions -----------------------------------------------------

    /**
     * Converts the string into tags array.
     */
    function _str2tags($str)
    {
    	$tags = array();

    	if ($str != null)
    	{
	        // Detect the delimiter
	        $delimiter = ",";
	        if (!strpos($str, ",") && strpos($str, " ")) $delimiter = " ";
        	$tags = explode($delimiter, $str);
    	}
    	
        return $tags;
    }
      
    /**
     * Returns URL for the folder.
     */      
    function _folderURL($folderId)
    {
        return BASE_URL . "/folder/$folderId";
    }
  
    /**
     * Returns URL for the item.
     */     
    function _itemURL($itemId)
    {
        return BASE_URL . "/item/$itemId";
    }
}
?>