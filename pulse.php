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
// $Id: pulse.php,v 1.2 2007/07/31 04:35:32 alg Exp $
//

require_once 'session.php';
require_once 'classes/TasksManager.class.php';

set_time_limit(600);

TasksManager::runTasksIfTimeHasCome();

?>
