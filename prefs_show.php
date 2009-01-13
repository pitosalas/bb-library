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
// $Id: prefs_show.php,v 1.1 2006/10/23 11:44:51 alg Exp $
//

require_once 'smarty_page.php';

// Check necessary permissions to do actions or show pages
check_perm('manage_preferences');

// Find themes
$themes = array(null => 'Default');
$themesDir = opendir(THEMES_DIR);
if ($themesDir)
{
	while (($theme = readdir($themesDir)) !== false)
	{
		if ($theme != '.' && $theme != '..' && $theme != 'CVS')
		{
			$themes[$theme] = $theme;
		}
	}
	closedir($themesDir);
}
$smarty->assign('themes', $themes);

// Init feed preview modes
$feed_preview_modes = array(
	'collapsed' => 'Initially Collapsed',
	'expanded' => 'Initially Expanded',
	'hidden' => 'Hidden'
);
$smarty->assign('feed_preview_modes', $feed_preview_modes);

$dm = new DataManager();
$smarty->assign('nav', $dm->getFolderNavigationSideblock($uid, 1));
$dm->close();

$smarty->assign('title', 'Preferences');
$smarty->assign('content', 'preferences');
$smarty->assign('page', 'main');
$smarty->display('layout.tpl');
?>