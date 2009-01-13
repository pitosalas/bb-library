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
// $Id: CSVParserTest.php,v 1.1 2006/10/23 11:44:51 alg Exp $
//

require_once 'DefaultTestCase.php';
require_once $classes . 'CSVParser.class.php';

class CSVParserTest extends DefaultTestCase
{
	function test_row2tags_blank()
	{
		$rules = array('tags' => 'blank');
		$row = array('a', 'b');
		
		$tags = CSVParser::_row2tags($row, $rules);
		$this->assertEqual('', $tags);
	}
	
	function test_row2tags_const()
	{
		$rules = array('tags' => 'const', 'tagsConst' => 'a');
		$row = array('a', 'b');
		
		$tags = CSVParser::_row2tags($row, $rules);
		$this->assertEqual('a', $tags);
	}
	
	function test_row2tags_field()
	{
		$row = array('a', 'b');

		$rules = array('tags' => '0');
		$tags = CSVParser::_row2tags($row, $rules);
		$this->assertEqual('a', $tags);

		$rules = array('tags' => '1');
		$tags = CSVParser::_row2tags($row, $rules);
		$this->assertEqual('b', $tags);
	}
	
	function test_row2description_blank()
	{
		$rules = array('description' => 'blank');
		$row = array('a', 'b');
		
		$descr = CSVParser::_row2description($row, $rules);
		$this->assertEqual('', $descr);
	}

	function test_row2description_field()
	{
		$row = array('a', 'b');

		$rules = array('description' => '0');
		$descr = CSVParser::_row2description($row, $rules);
		$this->assertEqual('a', $descr);

		$rules = array('description' => '1');
		$descr = CSVParser::_row2description($row, $rules);
		$this->assertEqual('b', $descr);
	}

	function test_row2password_const()
	{
		$rules = array('password' => 'const', 'passwordConst' => 'a');
		$row = array('a', 'b');
		
		$val = CSVParser::_row2password($row, $rules);
		$this->assertEqual('a', $val);
	}
	
	function test_row2password_username()
	{
		$rules = array('password' => 'userName', 'userName' => '1');
		$row = array('a', 'b');
		
		$parser = new CSVParser('');
		$val = $parser->_row2password($row, $rules);
		$this->assertEqual('b', $val);
	}
	
	function test_row2password_field()
	{
		$row = array('a', 'b');

		$rules = array('password' => '0');
		$val = CSVParser::_row2password($row, $rules);
		$this->assertEqual('a', $val);

		$rules = array('password' => '1');
		$val = CSVParser::_row2password($row, $rules);
		$this->assertEqual('b', $val);
	}
	
	function test_row2email_blank()
	{
		$rules = array('email' => 'blank');
		$row = array('a', 'b');
		
		$val = CSVParser::_row2email($row, $rules);
		$this->assertEqual('', $val);
	}
	
	function test_row2email_field()
	{
		$row = array('a', 'b');

		$rules = array('email' => '0');
		$val = CSVParser::_row2email($row, $rules);
		$this->assertEqual('a', $val);

		$rules = array('email' => '1');
		$val = CSVParser::_row2email($row, $rules);
		$this->assertEqual('b', $val);
	}
		
	function test_row2username_generate()
	{
		$parser = new CSVParser('');
		
		$rules = array('userName' => 'generate');
		$row = array('a', 'b');
		
		$val = $parser->_row2userName($row, $rules);
		$this->assertEqual('u' . ($parser->seq - 1), $val);
		$val2 = $parser->_row2userName($row, $rules);
		$this->assertEqual('u' . ($parser->seq - 1), $val2);
		
		$this->assertNotEqual($val, $val2);
	}

	function test_row2username_fullname()
	{
		$rules = array('userName' => 'fullName', 'fullName' => '0');
		$row = array('Test,Name()');
		
		$parser = new CSVParser(''); 
		$val = $parser->_row2userName($row, $rules);
		$this->assertEqual('Test_Name_', $val);
	}

	function test_row2username_field()
	{
		$row = array('a', 'b');

		$rules = array('userName' => '0');
		$val = CSVParser::_row2userName($row, $rules);
		$this->assertEqual('a', $val);

		$rules = array('userName' => '1');
		$val = CSVParser::_row2userName($row, $rules);
		$this->assertEqual('b', $val);
	}
	
	function test_row2fullname_field()
	{
		$row = array('a', 'b');

		$rules = array('fullName' => '0');
		$val = CSVParser::_row2fullName($row, $rules);
		$this->assertEqual('a', $val);

		$rules = array('fullName' => '1');
		$val = CSVParser::_row2fullName($row, $rules);
		$this->assertEqual('b', $val);
	}
}

?>