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
// $Id: functions.php,v 1.5 2007/08/23 17:04:11 alg Exp $
//

require_once 'sites/config.php';

/**
 * Returns value if it's set. Otherwise, returns default.
 */
function defGET($name, $def)
{
    return isset($_GET[$name]) ? $_GET[$name] : $def;
}

/**
 * Returns value if it's set. Otherwise, returns default.
 */
function defPOST($name, $def)
{
    return isset($_POST[$name]) ? $_POST[$name] : $def;
}

/** Escapes string for HTML.*/
function escape4HTML($str)
{
    return htmlspecialchars($str, ENT_QUOTES, "UTF-8");
}

/** Escapes string for JS.*/
function escape4JS($str)
{
	return preg_replace("/(\r\n|\n\r|\n)/", '\n', escape4HTML($str));
}

/**
 * Registers uploaded file and returns the name.
 */
function register_upload($name, $module, $user)
{
	$newname = UPLOADS_DIR . '/' . $module . '_' . $user . '_' . mktime();
	move_uploaded_file($name, $newname);
	
	return $newname;
}

/** Returns the text of the license. */
function license_text()
{
	return file_get_contents(LICENSE);
}

/** Unsets folder from the array. */
function unset_folder(&$folders, $fid)
{
	unset($folders[MY_FOLDERS][$fid]);
	if (isset($folders[OTHERS_FOLDERS])) unset($folders[OTHERS_FOLDERS][$fid]);
}

/**
 * Converts all non-alphanumerics to dashes.
 */
function title_encode($title)
{
	$title = strtolower($title);
	$title = trim(ereg_replace("[^a-z0-9]", ' ', $title));
	$title = ereg_replace(' +', '-', $title);
	return $title;
}

/** Returns the folder URL basing on the id and optional title which is encoded into the URL. */
function folder_url($fid, $folder_title = false)
{
	return FOLDER_URL . $fid . ($folder_title ? '-' . title_encode($folder_title) : '');
}

/** Returns the item URL basing on the id and optional title which is encoded into the URL. */
function item_url($iid, $item_title = false)
{
	return ITEM_URL . $iid . ($item_title ? '-' . title_encode($item_title) : '');
}

?>
