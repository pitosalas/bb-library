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
// $Id: organization_show.php,v 1.1 2006/10/23 11:44:51 alg Exp $
//

require_once 'smarty_page.php';

// Check necessary permissions to do actions or show pages
check_perm('manage_organizations');

if (isset($_POST['action'])) include('organization_submit.php');

$dm = new DataManager();
if (isset($_GET['oid']))
{
	$oid = $_GET['oid'];
	$org = $dm->getOrganizationEditInfo($oid);
	
	$smarty->assign('organization', $org);
	$smarty->assign('title', 'Organization: ' . $org['title']);
	$smarty->assign('content', 'organization');
} else
{
	$smarty->assign('organizations', $dm->getOrganizationsList());
	$smarty->assign('title', 'Organizations');
	$smarty->assign('content', 'organizations');
}

$smarty->assign('nav', $dm->getFolderNavigationSideblock($uid, 1));
$smarty->assign('folders', array(-1 => '<No Folders>') + $dm->getAllFolders());

$dm->close();

$smarty->assign('page', 'main');
$smarty->display('layout.tpl');
?>