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
// $Id: folder_opml.php,v 1.1 2006/10/23 11:44:51 alg Exp $
//

require_once 'classes/DataManager.class.php';
require_once 'functions.php';
require_once 'opml.php';

if (isset($_GET['fid']))
{
	// Shallow means there will be no sub-folders, but links to embedded lists only
	$shallow = false;
	// Item descriptions flag controls whether the texts will be added to items as descriptions
	$item_descriptions = 0;
	// Tags flag controls the adding of tags to outlines in bb:tags attribute
	$tags = 0;
	
	if (isset($_GET['o']))
	{
		$options = $_GET['o'];
		
		$shallow = stristr($options, 's');
		$item_descriptions = stristr($options, 'd') ? 1 : 0;
		$tags = stristr($options, 't') ? 1 : 0;
	}

	// We exclude the folders from this list from the outline	
	$exclude_ids = isset($_GET['ex']) ? explode(',', $_GET['ex']) : array();
	
    $dm = new DataManager();
    $folder = $dm->getFolderViewInfo($_GET['fid']);
    $opmlFolder = $folder == null 
    	? array('children' => array(), 'text' => '') 
    	: $dm->getFolderOPML($folder, $shallow, $item_descriptions, $tags, $exclude_ids);
    $dm->close();

	outputFolderOPML($opmlFolder);    
}
?>
