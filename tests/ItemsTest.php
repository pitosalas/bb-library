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
// $Id: ItemsTest.php,v 1.3 2007/03/16 11:56:21 alg Exp $
//

require_once 'DefaultTestCase.php';
require_once $classes . 'Database.class.php';

class ItemsTest extends DefaultTestCase
{
	var $db;
	var $folder;
	var $item;
	
	function setUp()
	{
		$this->db = new Database();

		$folder = new Folder(ADMIN_USER_ID, 'items_test title',
			'description', 2, 'param', 'opml', 'opml_user', 'opml_user',
			'opml_password', 3, 4, 'opml_last_error', 0);
		
		$shortcuts = array(1);
		$tags = null;
		$this->folder = $this->db->addFolder($folder, $shortcuts, $tags);
		$this->folder->tags = $tags; 

		// Add item
		$item = new Item(ADMIN_USER_ID, 'items_test title', 'description',
			'site_url', 'data_url', 1, 0);

		$shortcuts = array(ROOT_FOLDER_ID);
		$tags = array('tag1', 'tag2');
		$this->item = $this->db->addItem($item, $shortcuts, $tags);
		$this->item->tags = $tags; 
	}
	
	function tearDown()
	{
		$this->db->deleteItem($this->item->id);
		$this->db->deleteFolder($this->folder->id);

		$this->db->disconnect();
	}
	
	function test_create_item()
	{
		$iid = $this->item->id;

		// Load folder by ID
		$itemLoaded = $this->db->findItemById($iid);
		$itemLoaded->tags = $this->db->findItemTags($iid);
		$shortcuts = $this->db->findItemFolders($iid);
		
		// Check
		$this->assertEqual($this->item->owner_id, $itemLoaded->owner_id);
		$this->assertEqual($this->item->title, $itemLoaded->title);
		$this->assertEqual($this->item->description, $itemLoaded->description);
		$this->assertEqual($this->item->dynamic, $itemLoaded->dynamic);
		$this->assertEqual($this->item->siteURL, $itemLoaded->siteURL);
		$this->assertEqual($this->item->dataURL, $itemLoaded->dataURL);
		$this->assertEqual($this->item->type_id, $itemLoaded->type_id);
		$this->assertArray($this->item->tags, $itemLoaded->tags);
		
		$this->assertArray(array(ROOT_FOLDER_ID), $shortcuts['ids']);
	}
	
	function test_move_item()
	{
		// Move item to the folder		
		$new_shortcuts = array($this->folder->id);
		$this->db->updateItem($this->item, $new_shortcuts, $this->item->tags);

		// Load item shortcuts by ID
		$shortcuts = $this->db->findItemFolders($this->item->id);
		
		// Check
		$this->assertArray($new_shortcuts, $shortcuts['ids']);
	}

	function test_copy_item()
	{
		// Link item to the folder		
		$new_shortcuts = array(ROOT_FOLDER_ID, $this->folder->id);
		$this->db->updateItem($this->item, $new_shortcuts, $this->item->tags);

		// Load item shortcuts by ID
		$shortcuts = $this->db->findItemFolders($this->item->id);
		
		// Check
		$this->assertArray($new_shortcuts, $shortcuts['ids']);
	}
	
	/**
	 * We move the item to the folder and delete this folder. The item should be deleted
	 * as well as it has no links to hierarchy.
	 */
	function test_cascade_delete()
	{
		// Move item to the folder		
		$new_shortcuts = array($this->folder->id);
		$this->db->updateItem($this->item, $new_shortcuts, $this->item->tags);
		
		// Delete the folder
		$this->db->deleteFolder($this->folder->id);
		
		// Try to find item
		$item = $this->db->findItemByID($this->item->id);
		$this->assertNull($item, 'Should be deleted');
	}
}

?>
