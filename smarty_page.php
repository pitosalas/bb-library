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
// $Id: smarty_page.php,v 1.1 2006/10/23 11:44:51 alg Exp $
//

require_once 'classes/DataManager.class.php';
require_once 'smarty.php';

$uid = $_SESSION['user_id'];

// Set all-pages info: top 10, user block
$dm = new DataManager();
$smarty->assign('top10', $dm->getTopItems(10));
if ($uid)
{
	$user = $dm->getPersonInfo($uid);
	$smarty->assign('user_id', $uid);
	$smarty->assign('user', $user);
}  
$dm->close();
?>
