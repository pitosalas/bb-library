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
// $Id: item_image.php,v 1.2 2007/01/03 13:53:26 alg Exp $
//

require_once 'sites/config.php';
require_once 'classes/DataManager.class.php';
require_once 'classes/AmazonThumbshooter.class.php';
require_once 'functions.php';

if (!isset($iid)) $iid = defGET('iid', 0);

$dm = new DataManager();
$item = $dm->getItemViewInfo($iid);
$dm->close();

$url = $item == null || trim($item->siteURL) == '' ? null : $item->siteURL;

AmazonThumbshooter::output_image_for_site($url);
?>
