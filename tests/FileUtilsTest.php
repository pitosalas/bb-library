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
// $Id: FileUtilsTest.php,v 1.1 2006/10/23 11:44:51 alg Exp $
//

require_once 'DefaultTestCase.php';
require_once $classes . 'FileUtils.class.php';

/**
 * File utils test suite.
 */
class FileUtilsTest extends DefaultTestCase
{
	/**
	 * Tests saving file contents.
	 */
	function test_file_put_contents()
	{
		$filename = 'test_file_put_contents.data';
		$string = " test\nstring ";
		
		// Cleanup
		@unlink($filename);
		
		FileUtils::file_put_contents($filename, $string);
		
		// Read file
		$handle = fopen($filename, "rb");
		$string_read = fread($handle, filesize($filename));
		fclose($handle);
		
		$this->assertEqual($string, $string_read);
		
		// Cleanup
		@unlink($filename);
	}
}
?>