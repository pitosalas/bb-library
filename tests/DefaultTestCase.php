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
// $Id: DefaultTestCase.php,v 1.3 2007/07/16 11:30:56 alg Exp $
//

$root = dirname(__FILE__) . '/../';
$classes = $root . 'classes/';

$_SERVER['SERVER_NAME'] = 'default';
$_SERVER['DOCUMENT_ROOT'] = $root;

require_once $root . 'sites/config.php';

define('ROOT_FOLDER_ID', 1);
define('ADMIN_USER_ID', 1);

class DefaultTestCase extends UnitTestCase
{
	function assertArray($ar1, $ar2)
	{
		$this->assertTrue(is_array($ar1), 'First object is not array');
		$this->assertTrue(is_array($ar2), 'Second object is not array');
		
		$lar1 = count($ar1);
		$lar2 = count($ar2);
		$this->assertEqual($lar1, $lar2, 'Arrays have different lengths');
		if ($lar1 != $lar2) return;
		
		$valid = true;
		$i = 0;
		for ($i = 0; $valid && $i < $lar1; $i++)
		{
			$valid = $ar1[$i] == $ar2[$i];
		}
		
		$i--;
		$this->assertTrue($valid, 'Arrays are different: i=' . $i . ' of ' . $lar1 . ', first=' . $ar1[$i] . ', second=' . $ar2[$i]);	
	}
}

?>