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
// $Id: new_additions_js.php,v 1.1 2006/10/23 11:44:51 alg Exp $
//

require_once 'classes/DataManager.class.php';
require_once 'smarty_page.php';

$dm = new DataManager();
$smarty->assign('items', $dm->getNewAdditionsJS(10));
$dm->close();

$smarty->display('new_additions_js.tpl');
?>