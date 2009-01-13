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
// $Id: index.php,v 1.1 2006/10/23 11:44:51 alg Exp $
//

require_once 'functions.php';
require_once 'session.php';

$action = defGET('action', '');
$type = defGET('type', 'folder');

if ($type == 'folder')
{
    // Folders
    if ($action == 'add_folder') include('folder_add.php'); else
    if ($action == 'edt_folder') include('folder_edit.php'); else
    if ($action == 'del_folder') include('folder_delete.php'); else
    if ($action == 'add_item') include('item_add.php'); else
    if ($action == 'opml_update')
    {
		include('opml_ping.php');
    	include('folder_show.php');
    } else include('folder_show.php');
} else if ($type == 'item')
{
    // Items
    if ($action == 'edt_item') include('item_edit.php'); else
    if ($action == 'del_item') include('item_delete.php'); else
    include('item_show.php');
} else if ($type == 'org')
{
	// Organizations
	include('organization_show.php');
} else if ($type == 'user')
{
	// Users
	include('user_show.php');
} else if ($type == 'news')
{
	// News item
	include('news_show.php');
}

?>
