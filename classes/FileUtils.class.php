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
// $Id: FileUtils.class.php,v 1.1 2006/10/23 11:44:51 alg Exp $
//

/**
 * File utilities.
 */
class FileUtils
{
	/** Writes contents of the string to the file. */
	function file_put_contents($filename, $string)
	{
		$written = 0;
		
		if ($h = fopen($filename, 'w'))
		{
			$written = fwrite($h, $string);
			fclose($h);
		}
		
		return $written;
	}
}
?>