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
// $Id: folder_delete.php,v 1.2 2007/07/10 09:50:04 alg Exp $
//

require_once 'smarty.php';
require_once 'classes/RobotsTxt.class.php';

// Check necessary permissions to do actions or show pages
check_perm('edit_content');

if (isset($_GET['fid']))
{
    $fid = $_GET['fid'];
	$pfid = defGET('pfid', -1);
	
    $dm = new DataManager();
    $folder = $dm->getFolder($fid);
    $dm->deleteFolder($fid, $pfid);
    $dm->close();
    
    // Prepare for displaying parent folder
    $fid = ($pfid == -1) ? 1 : $pfid; 
	
	RobotsTxt::flush();
}

include('folder_show.php');
?>