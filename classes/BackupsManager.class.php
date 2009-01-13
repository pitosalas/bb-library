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
// $Id: BackupsManager.class.php,v 1.1 2006/10/23 11:44:51 alg Exp $
//

require_once 'FileUtils.class.php';

/**
 * Backups manager.
 */
class BackupsManager
{
	/** Creates backup and returns the messages array with two possible keys: message and error. */
	function backup()
	{
		$messages = array();
		
		$cmd = MYSQL_DUMP;
		if (defined('DB_SOCKET')) $cmd .= ' --socket=' . DB_SOCKET;
		$cmd .= ' --add-drop-table -u ' . DB_USER . (strlen(DB_PASSWORD) > 0 ? ' -p' . DB_PASSWORD : '') . ' ' . DB_NAME;
		
		$output = array('SET AUTOCOMMIT=0;', 'SET FOREIGN_KEY_CHECKS=0;', '');
		$result = -1;
		exec($cmd, $output, $result);
		
		if ($result == 0)
		{
			$output[] = 'SET FOREIGN_KEY_CHECKS=1;';
			$output[] = 'COMMIT;';
			$output[] = 'SET AUTOCOMMIT=1;';
	
			$date = date('Y_m_d_H_i_s', mktime());
			$filename = BACKUPS_DIR . '/' . $date . '.sql';
			if (FileUtils::file_put_contents($filename, join("\n", $output)) < 0)
			{
				$messages['error'] = 'Failed to write data to file.';
			} else
			{
				$messages['message'] = 'The database backup was successfully created.';
			}
		} else
		{
			$messages['error'] = 'Failed to backup database. Error code is ' . $result;
		}
		
		return $messages;
	}

	/** Deletes backups and returns status messages array with possible keys: message. */	
	function delete(&$files)
	{
		foreach ($files as $filename) @unlink(BACKUPS_DIR . '/' . $filename);
		
		return array('message' => 'Selected files deleted.');
	}

	/** Restores database from the given filename. */	
	function restore(&$filename)
	{
		$cmd = MYSQL . ' ' . DB_NAME . ' -u ' . DB_USER . (strlen(DB_PASSWORD) > 0 ? ' -p' . DB_PASSWORD : '') . ' <' . BACKUPS_DIR . '/' . $filename;
		
		$output = array();
		$result = -1;
		exec($cmd, $output, $result);

		$messages = array();		
		if ($result == 0)
		{
			$messages['message'] = 'Database was successfully restored.';
		} else
		{
			$messages['error'] = "There was an error restoring database. (Code $result) <br>" . escape4HTML(join('<br>', $output));
		}
		
		return $messages;
	}
}
?>
