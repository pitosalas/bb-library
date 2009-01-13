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
// $Id: OrganizationsTest.php,v 1.2 2007/01/03 13:53:26 alg Exp $
//

require_once 'DefaultTestCase.php';
require_once $classes . 'Database.class.php';

class FoldersTest extends DefaultTestCase
{
	var $db;
	var $org_id;
	var $folder;
	
	function setUp()
	{
		$this->db = new Database();

		$folder = new Folder(ADMIN_USER_ID, 'folders_test title',
			'description', 2, 'param', 'opml', 'opml_user', 'opml_user',
			'opml_password', 3, 4, 'opml_last_error', 0);
		
		$shortcuts = array(ROOT_FOLDER_ID);
		$tags = null;
		$this->folder = $this->db->addFolder($folder, $shortcuts, $tags);
		$this->folder->tags = $tags;
		
		// Add organization
		$this->org_id = $this->db->addOrganization('organizations_test', $this->folder->id); 
	}
	
	function tearDown()
	{
		$this->db->deleteFolder($this->folder->id);
		$this->db->deleteOrganization($this->org_id);
		
		$this->db->disconnect();
	}
	
	/**
	 * When deleting recommendations folder, the recommendations folder id should be
	 * cleared.
	 */
	function test_delete_recommendation_folder()
	{
		$org = $this->db->findOrganizationByID($this->org_id);
		$this->assertEqual($this->folder->id, $org['recommendations_folder_id']);

		// Deleting folder
		$this->db->deleteFolder($this->folder->id);

		// Checking
		$org = $this->db->findOrganizationByID($this->org_id);
		$this->assertNull($org['recommendations_folder_id']);
	}
}

?>
