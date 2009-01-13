<?php

require_once 'classes/DataManager.class.php';
require_once 'classes/Database.class.php';

$dm = new DataManager();

if ($dm->_is_generate_tags_and_descriptions())
{
	// Take all folders requiring updates
	$db = new Database();
	$rows = $db->_query_rows("SELECT f.* FROM Folder f WHERE autoTags = 1 and dynamic = 0");

	echo count($rows) . ' folders found<br>';
	flush();
		
	// Walk through all of them and update the folder
	foreach ($rows as $row)
	{
		// Create a folder instance
		$folder = $db->_row2Folder($row);

		echo 'Folder: ' . $folder->title . ' ... ';
		flush();
		
		// Generate tags and save
		$ftags = $dm->_folder_tags($folder, false);
		$db->_setFolderTags($folder->id, $ftags);
		
		// Regenerate all items
		$dm->_regenerate_item_tags($folder);
		
		echo 'done<br/>';
		flush();
	}
} else
{
	// No conversion is required
	echo 'The tag generation is turned off for this instance.';
}	

// Close the manager and the database
$dm->close();

?>
