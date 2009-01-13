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
// $Id: item_xml.php,v 1.1 2006/10/23 11:44:51 alg Exp $
//

require_once 'classes/DataManager.class.php';
require_once 'functions.php';

$found = false;

$iid = defGET('iid', null);
if ($iid)
{
	$dm = new DataManager();
	$xmlURL = $dm->processItemAccess($iid);
	$dm->close();
	
	if ($xmlURL)
	{
		header('Location: ' . $xmlURL);
		$found = true;
	}  
}

if (!$found) header('HTTP/1.0 404 Not Found');
?>
