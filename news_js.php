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
// $Id: news_js.php,v 1.3 2007/07/26 11:36:40 alg Exp $
//

require_once 'classes/DataManager.class.php';
require_once 'smarty_page.php';

$max = isset($_GET['max']) ? (int)$_GET['max'] : -1;

$dm = new DataManager();
$smarty->assign('news_items', $dm->getNewsItems(true, $max));
$dm->close();

$smarty->display('news_js.tpl');
?>
