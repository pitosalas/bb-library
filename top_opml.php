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
// $Id: top_opml.php,v 1.1 2006/10/23 11:44:51 alg Exp $
//

require_once 'smarty_page.php';
require_once 'opml.php';

$count = $_GET['count'];

$dm = new DataManager();
$opmlFolder = $dm->getTopFolderOPML($count);
$dm->close();
        
outputFolderOPML($opmlFolder);
?>
