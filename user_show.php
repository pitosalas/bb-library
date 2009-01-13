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
// $Id: user_show.php,v 1.2 2006/12/12 11:51:09 alg Exp $
//

require_once 'smarty_page.php';

if (isset($_POST['action'])) include('user_submit.php');

$can_edit = (isset($_GET['pid']) && $_GET['pid'] == $uid) || perm('manage_users');
$smarty->assign('can_edit', $can_edit);

$dm = new DataManager();
$organizations = array(-1 => '<Default>') + $dm->getOrganizationsDict();
$types = $dm->getAccountTypes();

if (isset($_GET['pid']))
{
	$pid = $_GET['pid'];
	$person = $dm->getPersonEditInfo($pid);
	$person->type_name = $types[$person->type_id];
	$person->organization_title = ($person->organization_id ? $organizations[$person->organization_id] : 'Default' );
	$person->folders = $dm->getPersonFolders($pid); 
	
	$smarty->assign('person', $person);
	$smarty->assign('title', 'User: ' . $person->fullName);
	$smarty->assign('content', $can_edit ? 'person' : 'person_view');
} else
{
	$smarty->assign('people', $dm->getPersonsList());
	$smarty->assign('title', 'Users');
	$smarty->assign('content', 'people');
}

$smarty->assign('organizations', $organizations);
$smarty->assign('types', $types);
$smarty->assign('nav', $dm->getFolderNavigationSideblock($uid, 1));

$dm->close();

$smarty->assign('page', 'main');
$smarty->display('layout.tpl');
?>