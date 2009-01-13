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
// $Id: robots.php,v 1.1 2007/07/10 09:50:04 alg Exp $
//

require_once 'sites/config.php';
require_once 'classes/RobotsTxt.class.php';

header('Content-type: text/plain');
echo RobotsTxt::get();
?>
