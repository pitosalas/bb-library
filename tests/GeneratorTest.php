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
// $Id: GeneratorTest.php,v 1.1 2007/08/17 10:41:50 alg Exp $
//

require_once 'DefaultTestCase.php';
require_once $classes . 'Generator.class.php';

class GeneratorTest extends DefaultTestCase
{
	// ------------------------------------------------------------------------
	// Title to Tags conversion test
	// ------------------------------------------------------------------------
	
	function test_tags_empty_title()
	{
		$tags = Generator::tags('');
		$this->assertEqual(count($tags), 0);
	}
	
	function test_tags_one_word()
	{
		$tags = Generator::tags('halo');
		$this->assertEqual(count($tags), 1);
		$this->assertEqual('halo', $tags[0]);
	}
	
	function test_tags_many_words()
	{
		$tags = Generator::tags('halo WORLD');
		$this->assertEqual(count($tags), 2);
		$this->assertEqual('halo', $tags[0]);
		$this->assertEqual('world', $tags[1]);
	}

	function test_tags_punctuation()
	{
		$tags = Generator::tags(' Halo,  WORLD !!!');
		$this->assertEqual(count($tags), 2);
		$this->assertEqual('halo', $tags[0]);
		$this->assertEqual('world', $tags[1]);
	}
	
	function test_tags_stop_words()
	{
		$tags = Generator::tags('Science and Technology');
		$this->assertEqual(count($tags), 2);
		$this->assertEqual('science', $tags[0]);
		$this->assertEqual('technology', $tags[1]);
	}
	
	function test_tags_merge()
	{
		$tags = Generator::tags('Science and Technology', array('halo', 'science'));
		print_r($tags);
		$this->assertEqual(count($tags), 3);
		$this->assertEqual('science', $tags[0]);
		$this->assertEqual('technology', $tags[1]);
		$this->assertEqual('halo', $tags[2]);
	}
	
	// ------------------------------------------------------------------------
	// Folder Description generation test
	// ------------------------------------------------------------------------
	
	function test_folder_description_no_title_expert()
	{
		$desc = Generator::folder_description('', 'Aleksey', array('expert'));
		$this->assertNull($desc);
	}
	
	function test_folder_description_no_title_publisher()
	{
		$desc = Generator::folder_description('', 'Aleksey', array('publisher'));
		$this->assertEqual($desc, 'A collection of feeds and blogs from Aleksey');
	}
	
	function test_folder_description_normal()
	{
		$desc = Generator::folder_description('Test Title', 'Aleksey', array());
		$this->assertNull($desc);
	}
	
	function test_folder_description_expert()
	{
		$desc = Generator::folder_description('Test Title', 'Aleksey', array('expert'));
		$this->assertEqual($desc, 'Aleksey\'s recommendations for the best feeds and blogs about: Test Title');
	}
	
	function test_folder_description_publisher()
	{
		$desc = Generator::folder_description('Test Title', 'Aleksey', array('publisher'));
		$this->assertEqual($desc, 'A collection of feeds and blogs from Aleksey');
	}
}

?>