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
// $Id: config.php,v 1.8 2007/08/01 15:01:27 alg Exp $
//

/** Define constant only if it wasn't defined before. */
function def($name, $value)
{
	if (!defined($name)) define($name, $value);
}

/* --- Site-specific Definitions ------------------------------------------- */

$site = $_SERVER['SERVER_NAME'];

if ($site && strlen($site) > 0)
{
	$site_path = dirname(__FILE__) . '/' . strtolower($site);
	if (!is_dir($site_path))
	{
		$site = 'default';
		$site_path = dirname(__FILE__) . '/' . $site;
	}
}

$cfg = $site_path . '/config.php';
if (file_exists($cfg)) include_once $cfg;

define('SITE_PATH', $site_path . '/');

/* --- BB Service ---------------------------------------------------------- */

// Enables / disables BBS integration
def('BBS_ENABLED', true);

// Main service servlet URL
def('BBS_URL', 'http://www.blogbridge.com/bbservice/servlet/Service');

/* --- Database ------------------------------------------------------------ */

/* Database connection information. */
def('DB_HOST', 'localhost');
def('DB_NAME', 'feedlibrary');
def('DB_USER', 'root');
def('DB_PASSWORD', '');

/* Optional socket definition when database has non-standard socket.
 * The socket is necessary for normal backup functionality operation.
 */
//def('DB_SOCKET', '/tmp/mysql.sock');

/* --- Paths --------------------------------------------------------------- */

// Mysql and mysql dump
def('MYSQL', '/usr/bin/mysql');
def('MYSQL_DUMP', '/usr/bin/mysqldump');

// The URL of the application root
def('BASE_URL', 'http://' . $_SERVER['SERVER_NAME']);
// Absolute path to the application root
def('APP_DIR', $_SERVER['DOCUMENT_ROOT']);
// Absolute path to the site directory
def('SITE_DIR', APP_DIR . '/sites/' . $site);

// Absolute path to the license
def('LICENSE', APP_DIR . '/license.html');

// Derived paths

def('IMAGES_PEOPLE_URL', BASE_URL . '/images/people');
def('IMAGES_URL', BASE_URL . '/images');
def('STYLES_URL', BASE_URL . '/styles');
def('FOLDER_URL', BASE_URL . '/folder/');
def('ITEM_URL', BASE_URL . '/item/');
def('ORGANIZATION_URL', BASE_URL . '/organization/');
def('USER_URL', BASE_URL . '/user/');
def('NEWS_URL', BASE_URL . '/news');

// Absolute path to the people photos
def('PHOTOS_DIR', SITE_DIR . '/images/people');
// The directory where sub-directories with themes are created
def('THEMES_DIR', APP_DIR . '/styles/themes');

// The place where we store the uploads
def('UPLOADS_DIR', SITE_DIR . '/uploads');
// The place where we store the backups
def('BACKUPS_DIR', SITE_DIR . '/backups');
// The place where the sessions are stored
def('SESSIONS_DIR', SITE_DIR . '/sessions');
// The place where simplepie stores its cached files
def('SIMPLEPIE_CACHE_DIR', SITE_DIR . '/simplepie_cache');

// The place where we store Amazon AST thumbshots
def('AMAZON_CACHE_DIR', APP_DIR . '/amazon_ast_cache');

/* --- AMAZON AST section -------------------------------------------------- */

// AWS access key ID
def('AMAZON_AWSACCESSKEYID', '0AGQ2A6H3EH19XV0B582');

// Secret access key
def('AMAZON_SECRET_ACCESSKEY', 'ldnUHuIy9eztTBhCIxB1poZUv4QV3choGwd38pUf');

// No Image Filename
def('AMAZON_NO_IMAGE_FILENAME', APP_DIR . '/images/amazon-no-image.jpg');
def('AMAZON_NO_IMAGE_URL', BASE_URL . '/images/amazon-no-image.jpg');

// Thumbshots update period in seconds
def('AMAZON_UPDATE_PERIOD_SEC', 60 * 60 * 24);

// Maximum number of entries in the image cache.
def('AMAZON_CACHE_MAX_ENTRIES', 10000);

// When turned on only the site-part of the link is used to
// lookup the thumbnail. It looks like Amazon AST doesn't use
// the path and the name of the file to snapshot the site.
// It's a huge optimization.
def('AMAZON_FRONT_PAGE_OPTIMIZATION', true);

/* --- AMAZON AST section -------------------------------------------------- */

def('MY_FOLDERS', 'My Folders');
def('OTHERS_FOLDERS', 'Other\'s Folders');

/** The link to the latest version number. */
def('UPDATES_CHECK_URL', 'http://www.blogbridge.com/tools/fl_check_updates.php');

/* --- Links Checker ------------------------------------------------------- */

/** The subject line of invalid links summary letter. */
def('LC_MAIL_SUBJECT', 'Report: Items with invalid links in ' . BASE_URL);
?>
