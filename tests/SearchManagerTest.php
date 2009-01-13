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
// $Id: SearchManagerTest.php,v 1.2 2007/01/03 13:53:26 alg Exp $
//

require_once 'DefaultTestCase.php';
require_once $classes . 'SearchManager.class.php';
require_once $classes . 'DataManager.class.php';

class SearchManagerTest extends DefaultTestCase
{
	var $item;
	var $folder;
	
	function setUp()
	{
		// Item
		$this->item = new Item();
		$this->item->title = 'abc';
		$this->item->description = 'cde';
		$this->item->siteURL = 'efg';
		$this->item->dataURL = 'ghi';
		$this->item->owner_id = ADMIN_USER_ID;
		$this->item->type_id = 1;
		$shortcuts = array('1');
	
		// Folder
		$this->folder = new Folder();
		$this->folder->owner_id = ADMIN_USER_ID;
		$this->folder->title = 'abc';
		$this->folder->description = 'cde';
		$this->folder->viewType_id = 1;
		$this->folder->opml_url = 'efg';
		
		$dm = new DataManager();
		$this->item = $dm->addItem($this->item, $shortcuts, 'tagi');
		$this->folder = $dm->addFolder($this->folder, array(ROOT_FOLDER_ID), 'tagf');
		$dm->close();
	}
	
	function tearDown()
	{
		$dm = new DataManager();
		$dm->deleteItem($this->item->id);
		$dm->deleteFolder($this->folder->id);
		$dm->close();
	}
	
	function test_search_feeds_title()
	{
		$res = SearchManager::search('abc', true, false, true, false, false, false, false);
		$this->assertNotNull($res);
		$this->assertEqual(true, is_array($res));
		$this->assertEqual(1, count($res));
		$this->assertIsA($res[0], 'Item');
		$this->assertEqual($this->item->id, $res[0]->id);
	}
	
	function test_search_feeds_description()
	{
		$res = SearchManager::search('cde', true, false, false, true, false, false, false);
		$this->assertNotNull($res);
		$this->assertEqual(true, is_array($res));
		$this->assertEqual(1, count($res));
		$this->assertIsA($res[0], 'Item');
		$this->assertEqual($this->item->id, $res[0]->id);
	}
	
	function test_search_feeds_urls()
	{
		$res = SearchManager::search('efg', true, false, false, false, false, true, false);
		$this->assertNotNull($res);
		$this->assertEqual(true, is_array($res));
		$this->assertEqual(1, count($res));
		$this->assertIsA($res[0], 'Item');
		$this->assertEqual($this->item->id, $res[0]->id);

		$res = SearchManager::search('ghi', true, false, false, false, false, false, true);
		$this->assertNotNull($res);
		$this->assertEqual(true, is_array($res));
		$this->assertEqual(1, count($res));
		$this->assertIsA($res[0], 'Item');
		$this->assertEqual($this->item->id, $res[0]->id);
	}
	
	function test_search_folder_title()
	{
		$res = SearchManager::search('abc', false, true, true, false, false, false, false);
		$this->assertNotNull($res);
		$this->assertEqual(true, is_array($res));
		$this->assertEqual(1, count($res));
		$this->assertIsA($res[0], 'Folder');
		$this->assertEqual($this->folder->id, $res[0]->id);
	}
	
	function test_search_folder_description()
	{
		$res = SearchManager::search('cde', false, true, false, true, false, false, false);
		$this->assertNotNull($res);
		$this->assertEqual(true, is_array($res));
		$this->assertEqual(1, count($res));
		$this->assertIsA($res[0], 'Folder');
		$this->assertEqual($this->folder->id, $res[0]->id);
	}

	function test_search_folder_url()
	{
		$res = SearchManager::search('efg', false, true, false, false, false, false, true);
		$this->assertNotNull($res);
		$this->assertEqual(true, is_array($res));
		$this->assertEqual(1, count($res));
		$this->assertIsA($res[0], 'Folder');
		$this->assertEqual($this->folder->id, $res[0]->id);
	}
	
	function test_search_folder_tags_only()
	{
		$res = SearchManager::search('tagf', false, true, false, false, true, false, false);
		$this->assertNotNull($res);
		$this->assertEqual(true, is_array($res));
		$this->assertEqual(1, count($res));
		$this->assertIsA($res[0], 'Folder');
		$this->assertEqual($this->folder->id, $res[0]->id);
	}
	
	function test_search_item_tags_only()
	{
		$res = SearchManager::search('tagi', true, false, false, false, true, false, false);
		$this->assertNotNull($res);
		$this->assertEqual(true, is_array($res));
		$this->assertEqual(1, count($res));
		$this->assertIsA($res[0], 'Item');
		$this->assertEqual($this->item->id, $res[0]->id);
	}
}

?>