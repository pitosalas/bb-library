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
// $Id: TagsGenerationTest.php,v 1.2 2007/08/17 15:17:11 alg Exp $
//

require_once 'DefaultTestCase.php';
require_once $classes . 'DataManager.class.php';

class TagsGenerationTest extends DefaultTestCase
{
	function setUp()
	{
		$this->dm = new DataManager();
		$this->db = $this->dm->db;
		
		// Home is the shortcut
		$this->s = array();
		$this->s []= 1;
		
		// Sample tags
		$this->t = array('a', 'b', 'c'); 
		$this->ts = 'a, b, c';
	}
	
	function tearDown()
	{
		$this->dm->close();
	}
	
	// ------------------------------------------------------------------------
	// Folders
	// ------------------------------------------------------------------------
	
	function test_folder_add()
	{
		// Non-autotags folder
		$f = new Folder(1);
		$f->title = 'mytest';
		$f->autoTags = false;

		// Adding...
		$f = $this->dm->addFolder($f, $this->s, $this->ts);
		
		// Checking
		$tags = $this->db->findFolderTags($f->id);
		$this->assertHasAll($tags, $this->t);
		
		// Cleanup
		$this->db->deleteFolder($f->id);
	}
	
	function test_folder_add_autotags()
	{
		// Autotags folder
		$f = new Folder(1);
		$f->title = 'mytest boogie';
		$f->autoTags = true;
		
		// Adding...
		$f = $this->dm->addFolder($f, $this->s, $this->ts);
		
		// Checking
		$tags = $this->db->findFolderTags($f->id);
		$this->assertHasAll($tags, array('mytest', 'boogie'));
		
		// Cleanup
		$this->db->deleteFolder($f->id);
	}
	
	function test_folder_update()
	{
		// Autotags folder
		$f = new Folder(1);
		$f->title = 'mytest boogie';
		$f->autoTags = true;
		
		// Adding and updating
		$f = $this->dm->addFolder($f, $this->s, $this->ts);
		$f->autoTags = false;
		$this->dm->updateFolder($f, $this->s, $this->ts);
		
		// Checking
		$tags = $this->db->findFolderTags($f->id);
		$this->assertHasAll($tags, $this->t);
		
		// Cleanup
		$this->db->deleteFolder($f->id);
	}
	
	function test_folder_update_autotags()
	{
		// Non-autotags folder
		$f = new Folder(1);
		$f->title = 'mytest';
		$f->autoTags = false;
		
		// Adding...
		$f = $this->dm->addFolder($f, $this->s, $this->ts);
		$f->autoTags = true;
		$f->title = 'mytest boogie';
		$this->dm->updateFolder($f, $this->s, $this->ts);
		
		// Checking
		$tags = $this->db->findFolderTags($f->id);
		$this->assertHasAll($tags, array('mytest', 'boogie'));
		
		// Cleanup
		$this->db->deleteFolder($f->id);
	}
	
	// ------------------------------------------------------------------------
	// Items
	// ------------------------------------------------------------------------

	function test_item_add()
	{
		// Non-autotags item
		$i = new Item(1);
		$i->type_id = 1;
		$i->title = 'mytest';
		$i->autoTags = false;
		
		// Adding an item
		$i = $this->dm->addItem($i, $this->s, $this->ts);
		
		// Checking
		$tags = $this->db->findItemTags($i->id);
		$this->assertHasAll($tags, $this->t);
		
		// Cleanup
		$this->db->deleteItem($i->id); 
	}
	
	function test_item_add_autotags()
	{
		// Sample folder
		$f = new Folder(1);
		$f->title = 'f1';
		$f->autoTags = false;
		$f = $this->dm->addFolder($f, $this->s, '');

		// Autotags item
		$i = new Item(1);
		$i->type_id = 1;
		$i->title = 'mytest boogie';
		$i->autoTags = true;
		
		// Adding an item
		$i = $this->dm->addItem($i, array($f->id), $this->ts);
		
		// Checking
		$tags = $this->db->findItemTags($i->id);
		$this->assertHasAll($tags, array('mytest', 'boogie'));
		
		// Cleanup
		$this->db->deleteFolder($f->id); 
	}
	
	function test_item_update()
	{
		// Autotags item
		$i = new Item(1);
		$i->type_id = 1;
		$i->title = 'mytest boogie';
		$i->autoTags = true;
		
		// Adding an item
		$i = $this->dm->addItem($i, $this->s, $this->ts);
		$i->autoTags = false;
		$this->dm->updateItem($i, $this->s, $this->ts);
		
		// Checking
		$tags = $this->db->findItemTags($i->id);
		$this->assertHasAll($tags, $this->t);
		
		// Cleanup
		$this->db->deleteItem($i->id); 
	}
	
	function test_item_update_autotags()
	{
		// Sample folder
		$f = new Folder(1);
		$f->title = 'f1';
		$f->autoTags = false;
		$f = $this->dm->addFolder($f, $this->s, '');

		// Non-autotags item
		$i = new Item(1);
		$i->type_id = 1;
		$i->title = 'mytest';
		$i->autoTags = false;
		
		// Adding an item
		$i = $this->dm->addItem($i, array($f->id), $this->ts);
		$i->title = 'mytest boogie';
		$i->autoTags = true;
		$this->dm->updateItem($i, array($f->id), $this->ts);
		
		// Checking
		$tags = $this->db->findItemTags($i->id);
		$this->assertHasAll($tags, array('mytest', 'boogie'));
		
		// Cleanup
		$this->db->deleteFolder($f->id); 
	}
	
	// ------------------------------------------------------------------------
	// Combination
	// ------------------------------------------------------------------------
	
	function test_comb_folder_update()
	{
		// Create two folders with sample tags
		$f1 = new Folder(1);
		$f1->title = 'f1';
		$f1->autoTags = false;
		$f1 = $this->dm->addFolder($f1, $this->s, 'a, b');
				
		$f2 = new Folder(1);
		$f2->title = 'f2';
		$f2->autoTags = false;
		$f2 = $this->dm->addFolder($f2, $this->s, 'c, d');

		// Create an item with autoTags
		$i = new Item(1);
		$i->type_id = 1;
		$i->title = 'mytest';
		$i->autoTags = true;
		$s = array($f1->id, $f2->id);
		$i = $this->dm->addItem($i, $s, $this->ts);
		
		// Check that item has all tags
		$tags = $this->db->findItemTags($i->id);
		$this->assertHasAll($tags, array('a', 'b', 'c', 'd', 'mytest'));
		
		// Now update the first folder with new tags
		$this->dm->updateFolder($f1, $this->s, 'e, f');
		
		// Check that the item was also updated
		$tags = $this->db->findItemTags($i->id);
		$this->assertHasAll($tags, array('e', 'f', 'c', 'd', 'mytest'));
		
		// Cleanup
		$this->db->deleteFolder($f1->id);
		$this->db->deleteFolder($f2->id);
	}

	// ------------------------------------------------------------------------
	// Helpers
	// ------------------------------------------------------------------------

	function assertHasAll($target, $tags)
	{
		$this->assertEqual(count($target), count($tags));
		foreach ($tags as $t) $this->assertTrue(in_array($t, $target));
	}
}

?>