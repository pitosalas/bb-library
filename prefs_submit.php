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
// $Id: prefs_submit.php,v 1.2 2007/07/17 11:23:40 alg Exp $
//

require_once 'smarty_page.php';
require_once 'classes/TLA.class.php';

// Check necessary permissions to do actions or show pages
check_perm('manage_preferences');

$dm = new DataManager();
$dm->updateApplicationProperties($_POST);
$dm->close();

TLA::invalidate();

header('Location: ' . BASE_URL);

?>