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
// $Id: organization_submit.php,v 1.1 2006/10/23 11:44:51 alg Exp $
//

require_once 'smarty.php';

// Check necessary permissions to do actions or show pages
check_perm('manage_organizations');

$action = defPOST('action', '');
if ($action == 'add')
{
    // Adding new organization
    $title = $_POST['title'];
    $folder = defPOST('recommendations_folder_id', null);
    
    $dm = new DataManager();
    $dm->addOrganization($title, $folder);
    $dm->close();
} else if ($action == 'delete' && isset($_POST['org']) && count($_POST['org']) > 0)
{
    // Delete organizations
    $orgs = $_POST['org'];

    $dm = new DataManager();
    $dm->deleteOrganizations($orgs);
    $dm->close();
} else if ($action == 'edit')
{
	// Edit organization -- saving...
    $title = $_POST['title'];
    $folder = defPOST('folder', null);

    $dm = new DataManager();
    $dm->updateOrganization($_POST['oid'], $title, $folder);
    $dm->close();
}
?>