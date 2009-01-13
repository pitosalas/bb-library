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
// $Id: FoldersTest.php,v 1.2 2007/01/03 13:53:26 alg Exp $
//

require_once 'DefaultTestCase.php';
require_once $classes . 'Database.class.php';

class FoldersTest extends DefaultTestCase
{
	var $db;
	var $folder;
	var $folder2;
	
	function setUp()
	{
		$this->db = new Database();

		$folder = new Folder(ADMIN_USER_ID, 'folders_test title',
			'description', 2, 'param', 'opml', 'opml_user', 'opml_user',
			'opml_password', 3, 4, 'opml_last_error', 0);
		
		$shortcuts = array(ROOT_FOLDER_ID);
		$tags = array('tag1', 'tag2');
		$this->folder = $this->db->addFolder($folder, $shortcuts, $tags);
		$this->folder->tags = $tags; 

		// Add another folder
		$folder2 = new Folder(ADMIN_USER_ID, 'folders_test title2',
			'description', 2, 'param', 'opml', 'opml_user', 'opml_user',
			'opml_password', 3, 4, 'opml_last_error', 0);

		$shortcuts = array(ROOT_FOLDER_ID);
		$tags = null;
		$this->folder2 = $this->db->addFolder($folder2, $shortcuts, $tags);
		$this->folder2->tags = $tags; 
	}
	
	function tearDown()
	{
		$this->db->deleteFolder($this->folder->id);
		$this->db->deleteFolder($this->folder2->id);

		$this->db->disconnect();
	}
	
	function test_create_folder()
	{
		$fid = $this->folder->id;

		// Load folder by ID
		$folderLoaded = $this->db->findFolderById($fid);
		$folderLoaded->tags = $this->db->findFolderTags($fid);
		$shortcuts = $this->_load_shortcuts_ids($fid);
		
		// Check
		$this->assertEqual($this->folder->owner_id, $folderLoaded->owner_id);
		$this->assertEqual($this->folder->title, $folderLoaded->title);
		$this->assertEqual($this->folder->description, $folderLoaded->description);
		$this->assertEqual($this->folder->viewType_id, $folderLoaded->viewType_id);
		$this->assertEqual($this->folder->viewTypeParam, $folderLoaded->viewTypeParam);
		$this->assertEqual($this->folder->opml, $folderLoaded->opml);
		$this->assertEqual($this->folder->opml_url, $folderLoaded->opml_url);
		$this->assertEqual($this->folder->opml_user, $folderLoaded->opml_user);
		$this->assertEqual($this->folder->opml_password, $folderLoaded->opml_password);
		$this->assertEqual($this->folder->opml_updates_period, $folderLoaded->opml_updates_period);
		$this->assertEqual($this->folder->opml_last_updated, $folderLoaded->opml_last_updated);
		$this->assertEqual($this->folder->opml_last_error, $folderLoaded->opml_last_error);
		$this->assertEqual($this->folder->dynamic, $folderLoaded->dynamic);
		
		$this->assertArray($this->folder->tags, $folderLoaded->tags);
		
		$this->assertArray(array(ROOT_FOLDER_ID), $shortcuts);
	}
	
	function test_move_folder()
	{
		// Move first folder to the second		
		$new_shortcuts = array($this->folder2->id);
		$this->db->updateFolder($this->folder, $new_shortcuts, $this->folder->tags);

		// Load folder by ID
		$shortcuts = $this->_load_shortcuts_ids($this->folder->id);
		
		// Check
		$this->assertArray($new_shortcuts, $shortcuts);
	}

	function test_copy_folder()
	{
		// Copy first folder to the second		
		$new_shortcuts = array(ROOT_FOLDER_ID, $this->folder2->id);
		$this->db->updateFolder($this->folder, $new_shortcuts, $this->folder->tags);

		// Load folder by ID
		$shortcuts = $this->_load_shortcuts_ids($this->folder->id);
		
		// Check
		$this->assertArray($new_shortcuts, $shortcuts);
	}
	
	/**
	 * We move the folder to the other folder and delete the second.
	 * The former should be deleted as well as it has no links to hierarchy.
	 */
	function test_cascade_delete()
	{
		// Move first folder to the second		
		$new_shortcuts = array($this->folder2->id);
		$this->db->updateFolder($this->folder, $new_shortcuts, $this->folder->tags);

		// Delete the second folder
		$this->db->deleteFolder($this->folder2->id);
		
		// Try to find the former
		$this->assertNull($this->db->findFolderByID($this->folder->id), 'Should be deleted');
	}
	
	/**
	 * Tests removing only link between a folder and another folder.
	 * If then the folder becomes unlinked (it was the only link), then
	 * it's removed.
	 */
	function test_delete_link()
	{
		// Add another link
		$shortcuts = array(ROOT_FOLDER_ID, $this->folder->id);
		$this->db->updateFolder($this->folder2, $shortcuts, $this->folder2->tags);
		
		// Delete link between f2 and f1 (that we just added)
		$this->db->deleteFolder($this->folder2->id, $this->folder->id);
		
		// The folder should stay linked to the root
		$this->assertNotNull($this->db->findFolderByID($this->folder2->id));
		$shortcuts = $this->_load_shortcuts_ids($this->folder2->id);
		$this->assertArray(array(ROOT_FOLDER_ID), $shortcuts);
		
		// Delete the last link between f2 and root
		$this->db->deleteFolder($this->folder2->id, ROOT_FOLDER_ID);

		// The folder should leave as unlinked
		$this->assertNull($this->db->findFolderByID($this->folder2->id));
	}
	
	/**
	 * Tests what folders are returned as available for shortcuts.
	 * The dynamic and opml folders mustn't be returned.
	 */
	function test_my_folders_for_shortcuts()
	{
		// Create opml folder
		$opml_folder = new Folder(ADMIN_USER_ID, 'folders_test opml', '', 1, null, null,
			'someurl', null, null, mktime() + 3600);
		$opml_folder = $this->db->addFolder($opml_folder, array(ROOT_FOLDER_ID), null);
		
		$dyna_folder = new Folder(ADMIN_USER_ID, 'folders_test dyna', '', 1);
		$dyna_folder->dynamic = 1;
		$dyna_folder = $this->db->addFolder($dyna_folder, array(ROOT_FOLDER_ID), null);
		
		// Check what folders are available
		$folders = $this->db->findFoldersByOwnerId(ADMIN_USER_ID, true);
		$home_seen = false;
		foreach ($folders as $folder)
		{
			$this->assertNotEqual($opml_folder->id, $folder->id, 'OPML folders mustn\'t be reported');
			$this->assertNotEqual($dyna_folder->id, $folder->id, 'Dynamic folders mustn\'t be reported');
			$home_seen |= ($folder->id == 1);
		}
		
		$this->assertTrue($home_seen, 'Home folder wasn\'t on the list.');
		
		// Cleanup
		$this->db->deleteFolder($opml_folder->id);
		$this->db->deleteFolder($dyna_folder->id);
	}
	
	function _load_shortcuts_ids($fid)
	{
		$sh = $this->db->findFolderShortcutParents($fid);
		
		$shortcuts = array();
		for ($i = 0; $i < count($sh); $i++)
		{
			$shortcuts[] = $sh[$i]->id;
		}
		
		return $shortcuts;
	}
}

?>
