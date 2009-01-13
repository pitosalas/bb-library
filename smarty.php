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
// $Id: smarty.php,v 1.16 2007/11/22 07:25:43 alg Exp $
//

require_once 'version.php';
require_once 'sites/config.php';
require_once 'functions.php';
require_once 'session.php';

define('SMARTY_DIR', APP_DIR.'/smarty/libs/');

require(SMARTY_DIR.'Smarty.class.php');
require_once('classes/Folder.class.php');
require_once('classes/Item.class.php');
require_once('classes/TLA.class.php');

$smarty = new Smarty;

$smarty->template_dir = APP_DIR.'/templates';
$smarty->compile_dir = SITE_DIR.'/templates_c';
$smarty->config_dir = SITE_DIR.'/smarty';

$smarty->compile_check = true;
$smarty->debugging = false;

$smarty->assign('version', FL_VERSION);
$smarty->assign('styles_url', STYLES_URL);
$smarty->assign('themes_url', STYLES_URL.'/themes');
$smarty->assign('root_url', BASE_URL);

$smarty->assign('perm', isset($_SESSION['permissions']) ? $_SESSION['permissions'] : array());

// -----------------------------------------------------------------------------
// Load application properties
// -----------------------------------------------------------------------------

require_once('classes/DataManager.class.php');

global $app_props;

$dm = new DataManager();
$app_props = $dm->getApplicationProperties();

if (!isset($app_props['feed_preview_mode'])) $app_props['feed_preview_mode'] = 'expanded';
if (!isset($app_props['news_box_title'])) $app_props['news_box_title'] = 'Latest News';
if (!isset($app_props['news_box_items'])) $app_props['news_box_items'] = '5';
if (!is_numeric($app_props['news_box_items'])) $app_props['news_box_items'] = 5;

$smarty->assign('app_props', $app_props);

if ($_SESSION['is_logged_in'])
{
	$smarty->assign('user_type_id', $_SESSION['type_id']);
	
	// Licensed application w/ or w/o trial period
	define('APP_TYPE_LICENSED', 0);
	// The application with a trial period (as we set or default to evaluation period)
	define('APP_TYPE_TRIAL', 1);
	// The number of days we grant for evaluation purposes since accepting the license
	define('APP_EVALUATION_DAYS', 60);
	// The number of days to warn the end of the evaluation period ahead
	define('APP_WARNING_DAYS', 7);
	
	$app_type = isset($app_props['app_type']) ? $app_props['app_type'] : APP_TYPE_LICENSED;
	$app_expiration_date = isset($app_props['app_expiration_date']) ? (integer) $app_props['app_expiration_date'] : 0;
	$license_accepted = $_SESSION['license_accepted'];
	
	// If license isn't accepted yet and there's accept request, do it
	if (!$license_accepted && defPOST('action', null) == 'accept_license')
	{
		$license_accepted = true;
		$time = mktime();
		$dm->acceptLicense($_SESSION['user_id'], license_text(), $time);
		$_SESSION['license_accepted'] = $time; 
	}
	
	if (!$license_accepted)
	{
		$smarty->assign('show_license', true);
		$smarty->assign('license_path', LICENSE);
	} else
	{
		if ($app_expiration_date == 0 && $app_type == APP_TYPE_TRIAL)
		{
			// expiration date isn't set -- set it now
			$app_expiration_date = mktime() + 60 * 60 * 24 * APP_EVALUATION_DAYS;
			$dm->updateApplicationProperty('app_expiration_date', $app_expiration_date);
		} else if ($app_expiration_date != 0)
		{
			$time_left = $app_expiration_date - mktime();
			
			if ($time_left <= 0)
			{
				// trial period has expired
				$smarty->assign('license_expired', true);
			} else if ($time_left < 24 * 3600 * APP_WARNING_DAYS)
			{
				// start warning number of evaulations days left
				$smarty->assign('eval_days_left', (integer) ($time_left / 24 / 3600) + 1);
			}
		}
	}
}
$dm->close();

// -----------------------------------------------------------------------------
// Flash Player configuration
// -----------------------------------------------------------------------------

$themeconf = APP_DIR . '/styles/themes/' . $app_props['theme'] . '/flashplayer.php';
$defaultconf = APP_DIR . '/styles/flashplayer.php';
if (file_exists($themeconf)) include($themeconf); else include($defaultconf);

$smarty->assign('fp_options',
	'lightcolor=' . FP_COLOR_LGHT . 
	'&backcolor=' . FP_COLOR_BACK .
	'&frontcolor=' . FP_COLOR_FORE .
	'&displayheight=' . FP_DISPLAY_HEIGHT . 
	'&thumbsinplaylist=' . FP_THUMBS .
	'&shownotesWidth=' . FP_SHOWNOTES_WIDTH);

// -----------------------------------------------------------------------------
// TLA
// -----------------------------------------------------------------------------

$links = TLA::get_links();
if ($links) $smarty->assign('tla_links', $links);

// -----------------------------------------------------------------------------
// Custom functions
// -----------------------------------------------------------------------------

require_once('smarty/plugins/function.implode.php');
require_once('smarty/plugins/function.url.php');
require_once('smarty/plugins/function.image.php');
require_once('smarty/plugins/function.folders_tree.php');
require_once('smarty/plugins/function.pencode.php');
require_once('smarty/plugins/function.itunes_url.php');

$smarty->register_function('implode', 'smarty_function_implode');
$smarty->register_function('url', 'smarty_function_url');
$smarty->register_function('image', 'smarty_function_image');
$smarty->register_function('css_image', 'smarty_function_css_image');
$smarty->register_function('css_image_link', 'smarty_function_css_image_link');
$smarty->register_function('folders_tree', 'smarty_function_folders_tree');
$smarty->register_function('pencode', 'smarty_function_pencode');
$smarty->register_function('itunes_url', 'smarty_function_itunes_url');

// -----------------------------------------------------------------------------
// Load some session variables
// -----------------------------------------------------------------------------

$smarty->assign('is_logged_in', isSessionSet('is_logged_in'));

if (isset($login_error)) $smarty->assign('login_error', $login_error);

function isSessionSet($name) { return isset($_SESSION[$name]) && $_SESSION[$name]; }

// -----------------------------------------------------------------------------
// Local smarty settings overload
// -----------------------------------------------------------------------------

if (file_exists('configs/smarty.conf')) { include_once 'configs/smarty.conf'; }

?>
