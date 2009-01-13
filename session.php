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
// $Id: session.php,v 1.4 2007/07/02 15:24:43 alg Exp $
//

require_once 'sites/config.php';
require_once 'functions.php';
require_once 'classes/DataManager.class.php';

prepare_directories();
session_save_path(SESSIONS_DIR);
session_start();

// Defaults
if (!isset($_SESSION['is_logged_in'])) $_SESSION['is_logged_in'] = false;
if (!isset($_SESSION['user_id'])) $_SESSION['user_id'] = null;

// Check if we need to log in or log out
$action = defGET('action', '');
if ($action == 'login' && isset($_POST['username']) && isset($_POST['password']))
{
	$dm = new DataManager();
	$person = $dm->getPersonByUsername($_POST['username'], $_POST['password']);
	if ($person)
	{
		$permissions = $dm->getPermissions($person->type_id);
		$dm->registerLogin($person->id);
	}
	$dm->close();
	
	if ($person != null)
	{
		$_SESSION['is_logged_in'] = true;
		$_SESSION['user_id'] = $person->id;
		$_SESSION['license_accepted'] = $person->license_accepted;
		$_SESSION['permissions'] = $permissions;
		$_SESSION['type_id'] = $person->type_id;
	} else
	{
		$login_error = 'Wrong name or password.';
	}
} else if ($action == 'logout')
{
	// Log out
	$_SESSION['is_logged_in'] = false;
	$_SESSION['user_id'] = null;
	$_SESSION['license_accepted'] = null;
	$_SESSION['type_id'] = null;
	unset($_SESSION['permissions']);
}

/** Returns TRUE only if the user is logged in and has the given permission. */ 
function perm($name)
{
	return isset($_SESSION['permissions']) && in_array($name, $_SESSION['permissions']);
}

/** Checks for necessary permission and redirects to the main page if the aren't granted. */
function check_perm($perm)
{
	if (!perm($perm))
	{
		header("Location: " . BASE_URL);
		exit;
	}
}

/** Checks directories and prepares them. */
function prepare_directories()
{
	ensure_writable(SESSIONS_DIR, 'sessions');
}

/** Ensures that the directory exists and writable. Reports and dies otherwise. */
function ensure_writable($path, $type)
{
	if (!is_dir($path))
	{
		// Try to create the directory
		if (!mkdir($path))
		{
			die ('Please create ' . $type . ' directory and give write permissions to your web server: ' . $path);
		}
	}
	
	if (!is_writable($path))
	{
		die ('The ' . $type . ' directory exists, but isn\'t writable: ' . $path); 
	}
}
?>
