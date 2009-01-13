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
// $Id: ajax.php,v 1.3 2007/07/26 11:36:40 alg Exp $
//

require_once 'sites/config.php';
require_once 'xajax/xajax.inc.php';

$xajax = new xajax(BASE_URL.'/ajax.server.php');
$xajax->registerFunction('loadFolderTreeItems');
$xajax->registerFunction('addBookmark');
$xajax->registerFunction('removeBookmark');
$xajax->registerFunction('renameTag');
$xajax->registerFunction('getFeedPreview');
$xajax->registerFunction('discoverBlog');
$xajax->registerFunction('postAnnouncement');
?>