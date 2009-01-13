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
// $Id: TagsManagerTest.php,v 1.2 2007/01/03 13:53:26 alg Exp $
//

require_once 'DefaultTestCase.php';
require_once $classes . 'Database.class.php';
require_once $classes . 'TagsManager.class.php';

class TagsManagerTest extends DefaultTestCase
{
	var $db;
	var $folder, $folder2;
	var $item, $item2;
	var $person, $person2;
	
	function setUp()
	{
		$this->db = new Database();
	}
	
	function setUpFolders()
	{
		$folder = new Folder(ADMIN_USER_ID, 'tags_manager_test 1', 'tmt', 2);
		$shortcuts = array(ROOT_FOLDER_ID);
		$tags = array('tag1', 'tag2');
		$this->folder = $this->db->addFolder($folder, $shortcuts, $tags);
		$this->folder->tags = $tags; 

		// Add another folder
		$folder = new Folder(ADMIN_USER_ID, 'tags_manager_test 2', 'tmt', 2);
		$shortcuts = array(ROOT_FOLDER_ID);
		$tags = array('tag2', 'tag3');
		$this->folder2 = $this->db->addFolder($folder, $shortcuts, $tags);
		$this->folder2->tags = $tags; 
	}

	function setUpItems()
	{
		$this->setUpFolders();

		// Add item
		$item = new Item(ADMIN_USER_ID, 'tags_manager_test 1', 'tmt', 'tmt', 'tmt', 1, 0);
		$shortcuts = array(ROOT_FOLDER_ID);
		$tags = array('tag1', 'tag2');
		$this->item = $this->db->addItem($item, $shortcuts, $tags);
		$this->item->tags = $tags; 

		// Add another item
		$item = new Item(ADMIN_USER_ID, 'tags_manager_test 2', 'tmt', 'tmt', 'tmt', 1, 0);
		$shortcuts = array(ROOT_FOLDER_ID);
		$tags = array('tag2', 'tag3');
		$this->item2 = $this->db->addItem($item, $shortcuts, $tags);
		$this->item2->tags = $tags; 
	}

	function setUpPeople()
	{
		$this->person = new Person('tags_manager_test_1', 'tmt', 'tmt');
		$tags = array('tag1', 'tag2');
		$this->person->id = $this->db->addPerson($this->person, $tags);
		
		$this->person2 = new Person('tags_manager_test_2', 'tmt', 'tmt');
		$tags = array('tag2', 'tag3');
		$this->person2->id = $this->db->addPerson($this->person2, $tags);
	}

	function tearDown()
	{
		if (isset($this->folder)) $this->db->deleteFolder($this->folder->id);
		if (isset($this->folder2)) $this->db->deleteFolder($this->folder2->id);
		
		if (isset($this->item)) $this->db->deleteItem($this->item->id);
		if (isset($this->item2)) $this->db->deleteItem($this->item2->id);

		if (isset($this->person)) $this->db->deletePerson($this->person->id);
		if (isset($this->person2)) $this->db->deletePerson($this->person2->id);

		$this->db->disconnect();
	}

	/** Tests getting of the tags cloud. */
	function test_tags_cloud_raw()
	{
		$this->setUpFolders();
		
		$cloud = TagsManager::_getTagsCloudRaw();
		
		// Tags are defined
		$this->assertTrue(isset($cloud['tag1']));
		$this->assertTrue(isset($cloud['tag2']));
		$this->assertTrue(isset($cloud['tag3']));
		
		if (!isset($cloud['tag1']) || 
			!isset($cloud['tag2']) || 
			!isset($cloud['tag3'])) return;
		
		// The counters are correct
		$this->assertTrue($cloud['tag1'] >= 1);
		$this->assertTrue($cloud['tag2'] >= 2);
		$this->assertTrue($cloud['tag3'] >= 1);
		
		// There are no zero-tags (I don't know how they could get there)
		foreach ($cloud as $tag => $count)
		{
			$this->assertNotEqual(0, $count, 'Zero count for tag: ' . $tag);
		}	
	}	
	
	/**	Tests finding folders by tags. */
	function test_find_folders()
	{
		$this->setUpFolders();
		
		$folders = TagsManager::_findFoldersByTag('tag1', $this->db);
		$this->assertObjectExists($folders, $this->folder);
		$this->assertObjectNotExists($folders, $this->folder2);
		
		$folders = TagsManager::_findFoldersByTag('tag2', $this->db);
		$this->assertObjectExists($folders, $this->folder);
		$this->assertObjectExists($folders, $this->folder2);

		$folders = TagsManager::_findFoldersByTag('tag3', $this->db);
		$this->assertObjectNotExists($folders, $this->folder);
		$this->assertObjectExists($folders, $this->folder2);

		$folders = TagsManager::_findFoldersByTag('unknown tag', $this->db);
		$this->assertObjectNotExists($folders, $this->folder);
		$this->assertObjectNotExists($folders, $this->folder2);
	}
	
	/**	Tests finding items by tags. */
	function test_find_items()
	{
		$this->setUpItems();
		
		$items = TagsManager::_findItemsByTag('tag1', $this->db);
		$this->assertObjectExists($items, $this->item);
		$this->assertObjectNotExists($items, $this->item2);
		
		$items = TagsManager::_findItemsByTag('tag2', $this->db);
		$this->assertObjectExists($items, $this->item);
		$this->assertObjectExists($items, $this->item2);

		$items = TagsManager::_findItemsByTag('tag3', $this->db);
		$this->assertObjectNotExists($items, $this->item);
		$this->assertObjectExists($items, $this->item2);

		$items = TagsManager::_findItemsByTag('unknown tag', $this->db);
		$this->assertObjectNotExists($items, $this->item);
		$this->assertObjectNotExists($items, $this->item2);
	}

	/**	Tests finding people by tags. */
	function test_find_people()
	{
		$this->setUpPeople();
		
		$people = TagsManager::_findPeopleByTag('tag1', $this->db);
		$this->assertObjectExists($people, $this->person);
		$this->assertObjectNotExists($people, $this->person2);
		
		$people = TagsManager::_findPeopleByTag('tag2', $this->db);
		$this->assertObjectExists($people, $this->person);
		$this->assertObjectExists($people, $this->person2);

		$people = TagsManager::_findPeopleByTag('tag3', $this->db);
		$this->assertObjectNotExists($people, $this->person);
		$this->assertObjectExists($people, $this->person2);

		$people = TagsManager::_findPeopleByTag('unknown tag', $this->db);
		$this->assertObjectNotExists($people, $this->person);
		$this->assertObjectNotExists($people, $this->person2);
	}

	/** Tests normalizig the cloud. */
	function test_normalize_cloud()
	{
		$cloud = array('a' => 1);
		$cloud = TagsManager::_normalizeCloud($cloud);
		$this->assertEqual(5, $cloud['a']);
		
		$cloud = array('a' => 2, 'b' => 1);
		$cloud = TagsManager::_normalizeCloud($cloud);
		$this->assertEqual(5, $cloud['a']);
		$this->assertEqual(3, $cloud['b']);

		$cloud = array('a' => 50, 'b' => 40, 'c' => 30, 'd' => 20, 'e' => 10, 'f' => 4);
		$cloud = TagsManager::_normalizeCloud($cloud);
		$this->assertEqual(5, $cloud['a']);
		$this->assertEqual(4, $cloud['b']);
		$this->assertEqual(3, $cloud['c']);
		$this->assertEqual(2, $cloud['d']);
		$this->assertEqual(1, $cloud['e']);
		$this->assertEqual(0, $cloud['f']);

		$cloud = array('a' => 100, 'b' => 99);
		$cloud = TagsManager::_normalizeCloud($cloud);
		$this->assertEqual(5, $cloud['a']);
		$this->assertEqual(5, $cloud['b']);
	}
	
	// ------------------------------------------------------------------------
	
	function assertObjectNotExists($objects, $object)
	{
		$this->assertFalse($this->objectExists($objects, $object), 'Object is on the list');
	}
	
	function assertObjectExists($objects, $object)
	{
		$this->assertTrue($this->objectExists($objects, $object), 'Object is missing');
	}
	
	function objectExists(&$objects, &$object)
	{
		$ex = false;
		
		$this->assertNotNull($objects);
		$this->assertNotNull($object);
		
		$is_ar = ($objects != null) && is_array($objects);
		$this->assertTrue(is_array($objects), 'Not an array');
		
		if ($is_ar)
		{
			foreach ($objects as $obj)
			{
				if ($obj->id == $object->id)
				{
					$ex = true;
					break;
				}
			}
		}
		
		return $ex;
	}
}

?>
