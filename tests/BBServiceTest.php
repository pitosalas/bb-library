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
// $Id: BBServiceTest.php,v 1.1 2006/10/23 11:44:51 alg Exp $
//

require_once 'DefaultTestCase.php';
require_once $classes . 'BBService.class.php';

class BBServiceTest extends DefaultTestCase
{
	function test_fix_url()
	{
        $this->assertNULL(BBService::fix_url(null));
        $this->assertNULL(BBService::fix_url(""));
        $this->assertNULL(BBService::fix_url(" "));

        $this->assertEqual("http://a", BBService::fix_url(" a "));
        $this->assertEqual("https://a", BBService::fix_url(" https://a "));
        $this->assertEqual("http://a", BBService::fix_url("feed://a"));
        $this->assertEqual("http://a", BBService::fix_url("feed:http://a"));
        $this->assertEqual("http://a", BBService::fix_url("feed://http://a"));
	}
}

?>