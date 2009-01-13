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
// $Id: opml_upload.php,v 1.2 2007/08/17 15:16:33 alg Exp $
//

require_once 'classes/OPMLParser.class.php';
require_once 'smarty.php';

// Check necessary permissions to do actions or show pages
check_perm('edit_content');

// This is from session
$userId = $_SESSION['user_id'];

$import_structure = isset($_POST['import_structure']);
$suppress_empty_top = isset($_POST['suppress_empty_top']);

$error = null;

// Aquire OPML
$parser = new OPMLParser();
if ($_POST['source'] == 'f')
{
	if (!isset($_FILES['opml']) || $_FILES['opml']['tmp_name'] == '')
	{
		$error = 'File wasn\'t specified';
	} else
	{
		$file = $_FILES['opml']['tmp_name'];
		$opml = $parser->parse($file, $import_structure, $suppress_empty_top);
	}
} else
{
	require_once 'classes/httpclient/HttpClient.class.php';

	$url = defPOST('url', '');
	if ($url == '')
	{
		$error = 'URL wasn\'t specified.';
	} else
	{
		$opmlStr = HttpClient::quickGet($url);
		if ($opmlStr == '')
		{
			$error = 'Failed to read the resource.';
		} else
		{
			$opml = $parser->parseString($opmlStr, $import_structure, $suppress_empty_top);			
		}
	} 
}

if (!isset($error))
{
	if (!$opml || count($opml['children']) == 0)
	{
	    // Invalid OPML
	    $error = 'Invalid OPML';
	} else
	{
	    set_time_limit(600);
	    
	    $parent_id = $_POST['folder'];
	    
	    $dm = new DataManager();
	    
	    // If creating new folder
	    $title = defPOST('title', null);
	    if ($title != null)
	    {
		    $descr = defPOST('description', null);
		    $tags = defPOST('tags', null);

	    	$fldr = new Folder($userId, $title, $descr);
	    	$fldr->autoTags = defPost('autoTags', false);
	    	$fldr = $dm->addFolder($fldr, array($_POST['folder']), $tags);
	    	$parent_id = $fldr->id;
	    }
	    
	    // Create sub-elements
    	foreach ($opml['children'] as $child)
    	{
    		$dm->importOutline($parent_id, $child, $userId, $import_structure);
    	}
	    $dm->close();
	
	    $fid = $parent_id;
	    include 'folder_show.php';
	}
}

if (isset($error)) include 'opml_upload_form.php';
?>
