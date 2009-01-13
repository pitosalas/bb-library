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
// $Id: item_delete.php,v 1.2 2007/07/10 09:50:04 alg Exp $
//

require_once 'smarty.php';
require_once 'classes/RobotsTxt.class.php';

// Check necessary permissions to do actions or show pages
check_perm('edit_content');

$iid = defGET('iid', -1);
$fid = defGET('fid', -1);

if ($iid != -1)
{
    $dm = new DataManager();
    $dm->deleteItem($iid, $fid);
    $dm->close();
    
    RobotsTxt::flush();
}

include('folder_show.php');
?>