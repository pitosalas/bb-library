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
// $Id: csv_import.php,v 1.1 2006/10/23 11:44:51 alg Exp $
//

require_once 'smarty_page.php';

// Check necessary permissions to do actions or show pages
check_perm('manage_users');

require_once 'classes/CSVParser.class.php';

$dm = new DataManager();
$smarty->assign('nav', $dm->getFolderNavigationSideblock($uid, 1));
$smarty->assign('title', 'CSV Import');

$step = defPost('step', null);
if ($step == '1')
{
	// Step 1: Uploading file
	$file = register_upload($_FILES['csv']['tmp_name'], 'csv', $uid);
	$parser = new CSVParser($file);
	
	$stats = $parser->getStats();
	if (is_array($stats))
	{
		// Good file, read the stats and to the Step 2
		$_SESSION['csv_file'] = $file;
		
		$columns = array();
		foreach ($stats['samples'] as $key => $col) $columns[$key] = 'Column ' . $key . ' (' . $col . ')';
		$columns_or_blank = $columns + array('blank' => 'Leave blank');
		
		// Prepare options for the form fields
		$smarty->assign('organizations', array(-1 => '<Default>') + $dm->getOrganizationsDict());
		$smarty->assign('types', $dm->getAccountTypes());
		$smarty->assign('fullName', $columns);
		$smarty->assign('userName', $columns + array('fullName' => 'Convert from Full Name', 'generate' => 'Generate'));
		$smarty->assign('email', $columns_or_blank);
		$smarty->assign('password', $columns + array('const' => 'Constant', 'userName' => 'Set to User Name'));
		$smarty->assign('password_def', 'username');
		$smarty->assign('description', $columns_or_blank);
		$smarty->assign('description_def', 'blank');
		$smarty->assign('tags', $columns_or_blank + array('const' => 'Constant'));
		$smarty->assign('tags_def', 'blank');
		
		$smarty->assign('stats', $stats);
		$smarty->assign('content', 'csv_import_2');
	} else
	{
		// Bad file, display error and back to Step 1
		$smarty->assign('error', $stats);
		$step = null;
	}
} else if ($step == '2')
{
	// Step 2: Processing
	$file = $_SESSION['csv_file'];
	$parser = new CSVParser($file);
	$people = $parser->getPeople($_POST);
	unlink($file);

	$total = count($people);
	$duplicates = $dm->importPeople($people);
	$dups = count($duplicates);
	
	$smarty->assign('duplicates', $duplicates);
	$smarty->assign('total', $total);
	$smarty->assign('added', $total - $dups);
	$smarty->assign('rest', $dups - 5);
	$smarty->assign('content', 'csv_import_3');
}

// We intentionally check it again because upon Step 1 error we resort to the
// first step again
if (!$step) $smarty->assign('content', 'csv_import_1');

$dm->close();

$smarty->assign('page', 'main');
$smarty->display('layout.tpl');
?>
