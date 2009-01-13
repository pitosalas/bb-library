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
// $Id: top100.php,v 1.1 2006/10/23 11:44:51 alg Exp $
//

require_once 'smarty_page.php';

$dm = new DataManager();
$smarty->assign("nav", $dm->getFolderNavigationSideblock($uid, 1));
$smarty->assign('top100', $dm->getTopItems(100));
$dm->close();
        
$smarty->assign("title", 'Top 100');
$smarty->assign("content", "top100");

$smarty->assign("page", "main");
$smarty->display("layout.tpl");

?>
