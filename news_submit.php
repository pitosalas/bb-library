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
// $Id: news_submit.php,v 1.4 2006/10/27 11:30:38 alg Exp $
//

require_once 'smarty_page.php';

// Check necessary permissions to do actions or show pages
check_perm('manage_news');

// News item ID
$action = defPOST('action', null);
$niid = defPOST('niid', null);

$dm = new DataManager();

if ($action == 'update' && $niid)
{
	$dm->updateNewsItem($niid, $_POST['title'], $_POST['text'], isset($_POST['public']));
} else if ($action == 'add')
{
	$niid = $dm->addNewsItem($_POST['title'], $_POST['text'], isset($_POST['public']), mktime(), $uid);
}

$dm->close();

//unset $_POST['niid'];
//unset $_POST['action'];

include ('news_show.php');
?>
