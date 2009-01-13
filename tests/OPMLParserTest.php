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
// $Id: OPMLParserTest.php,v 1.1 2006/10/23 11:44:51 alg Exp $
//

require_once 'DefaultTestCase.php';
require_once $classes . 'OPMLParser.class.php';

class OPMLParserTest extends DefaultTestCase
{
	function test_create_outline()
	{
		$attrs = array('type' => 'rss', 'url' => 'http://test.com/');
		
		$outline = OPMLParser::_create_outline($attrs);
		$this->assertEqual('rss', $outline['type']);
		$this->assertEqual('http://test.com/', $outline['url']);
		$this->assertTrue(is_array($outline['children']));
		$this->assertEqual(0, count($outline['children']));
	}
	
	/**
	 * N1
	 */
	function test_get_children_1()
	{
		$n1 = array('children' => array());
		
		$ch = OPMLParser::pp_get_children($n1);
		$this->assertEqual(1, count($ch));
		$this->assertTrue($n1 == $ch[0]);
	}
	
	/**
	 * N1
	 *  \-- N2
	 */
	function test_get_children_2()
	{
		$n2 = array('children' => array());
		$n1 = array('children' => array(&$n2));
		
		$ch = OPMLParser::pp_get_children($n1);
		$this->assertEqual(1, count($ch));
		$this->assertTrue($n2 == $ch[0]);
	}
	
	/**
	 * N1
	 *  \-- N2
	 *       \-- N3
	 */
	function test_get_children_3()
	{
		$n3 = array('children' => array());
		$n2 = array('children' => array(&$n3));
		$n1 = array('children' => array(&$n2));
		
		$ch = OPMLParser::pp_get_children($n1);
		$this->assertEqual(1, count($ch));
		$this->assertTrue($n3 == $ch[0]);
	}

	/**
	 * N1
	 *  \-- N2
	 *  \-- N3
	 */
	function test_get_children_4()
	{
		$n2 = array('children' => array());
		$n3 = array('children' => array());
		$n1 = array('children' => array(&$n2, &$n3));
		
		$ch = OPMLParser::pp_get_children($n1);
		$this->assertEqual(2, count($ch));
		$this->assertTrue($n2 == $ch[0]);
		$this->assertTrue($n3 == $ch[1]);
	}

	/**
	 * N1
	 *  \-- N2
	 *      \-- N4
	 *      \-- N5
	 *  \-- N3
	 */
	function test_get_children_5()
	{
		$n4 = array('children' => array());
		$n5 = array('children' => array());
		$n2 = array('children' => array(&$n4, &$n5));
		$n3 = array('children' => array());
		$n1 = array('children' => array(&$n2, &$n3));
		
		$ch = OPMLParser::pp_get_children($n1);
		$this->assertEqual(3, count($ch));
		$this->assertTrue($n4 == $ch[0]);
		$this->assertTrue($n5 == $ch[1]);
		$this->assertTrue($n3 == $ch[2]);
	}

	/**
	 * N1
	 *  \-- N2
	 *      \-- N4
	 *      \-- N5
	 *  \-- N4
	 */
	function test_get_children_6()
	{
		$n4 = array('children' => array());
		$n5 = array('children' => array());
		$n2 = array('children' => array(&$n4, &$n5));
		$n1 = array('children' => array(&$n2, &$n4));
		
		$ch = OPMLParser::pp_get_children($n1);
		$this->assertEqual(3, count($ch));
		$this->assertTrue($n4 == $ch[0]);
		$this->assertTrue($n5 == $ch[1]);
		$this->assertTrue($n4 == $ch[2]);
	}
	
	/**
	 * N1
	 */
	function test_remove_empty_top_1()
	{
		$n1 = array('children' => array());
		
		$ch = OPMLParser::pp_remove_empty_top($n1);
		$this->assertTrue($n1 == $ch);
	}
	
	/**
	 * N1
	 *  \-- N2
	 */
	function test_remove_empty_top_2()
	{
		$n2 = array('children' => array());
		$n1 = array('children' => array(&$n2));
		
		$ch = OPMLParser::pp_remove_empty_top($n1);
		$this->assertTrue($n2 == $ch);
	}

	/**
	 * N1
	 *  \-- N2
	 *       \-- N3
	 */
	function test_remove_empty_top_3()
	{
		$n3 = array('children' => array());
		$n2 = array('children' => array(&$n3));
		$n1 = array('children' => array(&$n2));
		
		$ch = OPMLParser::pp_remove_empty_top($n1);
		$this->assertTrue($n3 == $ch);
	}

	/**
	 * N1
	 *  \-- N2
	 *       \-- N3
	 *       \-- N4
	 */
	function test_remove_empty_top_4()
	{
		$n4 = array('children' => array());
		$n3 = array('children' => array());
		$n2 = array('children' => array(&$n3, &$n4));
		$n1 = array('children' => array(&$n2));
		
		$ch = OPMLParser::pp_remove_empty_top($n1);
		$this->assertTrue($n2 == $ch);
	}

	/**
	 * N1
	 *  \-- N2
	 *       \-- N3
	 *       \-- N4
	 *  \-- N5
	 */
	function test_remove_empty_top_5()
	{
		$n5 = array('children' => array());
		$n4 = array('children' => array());
		$n3 = array('children' => array());
		$n2 = array('children' => array(&$n3, &$n4));
		$n1 = array('children' => array(&$n2, &$n5));
		
		$ch = OPMLParser::pp_remove_empty_top($n1);
		$this->assertTrue($n1 == $ch);
	}
}

?>