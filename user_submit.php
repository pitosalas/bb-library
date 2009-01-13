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
// $Id: user_submit.php,v 1.4 2007/07/17 11:17:58 alg Exp $
//

require_once 'smarty.php';

$action = defPOST('action', '');
if ($action == 'add')
{
	// Check necessary permissions to do actions or show pages
	check_perm('manage_users');

    // Adding new user
    $person = post2Person();
    $tags = $_POST['tags'];
    
    $dm = new DataManager();
    $pid = $dm->addPerson($person, $tags);
    $dm->close();
    
    // If there's a picture uploaded, scale it to 50x75
    if (isset($_FILES['picture'])) move_uploaded_file($_FILES['picture']['tmp_name'], PHOTOS_DIR . '/' . $pid . '.img');
} else if ($action == 'delete' && isset($_POST['people']) && count($_POST['people']) > 0)
{
	// Check necessary permissions to do actions or show pages
	check_perm('manage_users');

    // Delete users
    $people = $_POST['people'];

    $dm = new DataManager();
    $dm->deletePeople($people);
    $dm->close();
} else if ($action == 'edit')
{
	// Check necessary permissions to do actions or show pages
	if (!isset($_POST['pid']) || $_POST['pid'] != $uid) check_perm('manage_users');

	// Edit person -- saving...
	$person = post2Person();
	$person->id = $_POST['pid'];
	$tags = $_POST['tags'];
	
    $dm = new DataManager();
    $dm->updatePerson($person, $tags);
    $dm->close();

    // If there's a picture uploaded, scale it to 50x75
    if (isset($_FILES['picture'])) move_uploaded_file($_FILES['picture']['tmp_name'], PHOTOS_DIR . '/' . $person->id . '.img');
}

/**
 * Converts POST information into person record.
 */
function post2Person()
{
    $person = new Person();
    if (isset($_POST['userName'])) $person->userName = $_POST['userName'];
    $person->password = $_POST['pwd'];
    $person->fullName = $_POST['fullName'];
    $person->email = defPOST('email', null);
    if (isset($_POST['organization_id'])) $person->organization_id = $_POST['organization_id'];
    $person->description = defPOST('description', null);
    $person->home_page = defPOST('home_page', null);
	$person->no_ads = isset($_POST['no_ads']);
	
	// Update type only if you have permissions to do it
    $person->type_id = -1;
    if (perm('manage_users') && isset($_POST['type_id'])) $person->type_id = $_POST['type_id'];

    return $person;
}

?>
