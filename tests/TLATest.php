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
// $Id: TLATest.php,v 1.1 2007/07/16 11:30:56 alg Exp $
//

require_once 'DefaultTestCase.php';
require_once $classes . 'TLA.class.php';

/**
 * Tests parts of TLA.
 */
class TLATest extends DefaultTestCase
{
	/** Empty links list. */
	function testXmlToLinks_empty()
	{
		$this->assertEqual(count(TLA::xml_to_links('')), 0);
		$this->assertEqual(count(TLA::xml_to_links(
			"<?xml version=\"1.0\" ?>\n" .
			"<Links>\n" .
			"</Links>")), 0);
	}
	
	/** Single link is present. */
	function testXmlToLinks_single()
	{
		$links = TLA::xml_to_links(
			"<?xml version=\"1.0\" ?>\n" .
			"<Links>\n" .
			"	<Link>\n" .
			"		<PostID>0</PostID>\n" .
			"		<URL>http://test.url</URL>\n" .
			"		<Text>Test Text</Text>\n" .
			"		<BeforeText> </BeforeText>\n" .
			"		<AfterText> </AfterText>\n" .
			"\n" .
			"		<RssText></RssText>\n" .
			"		<RssBeforeText></RssBeforeText>\n" .
			"		<RssAfterText></RssAfterText>\n" .
			"		<RssPrefix></RssPrefix>\n" .
			"		<RssMaxAds>6</RssMaxAds>\n" .
			"	</Link>\n" .
			"</Links>");
			
		$this->assertEqual(count($links), 1);
		
		$l = $links[0];
		$this->assertEqual($l['url'], "http://test.url");
		$this->assertEqual($l['text'], "Test Text");
	}

	/** Three links: 2 normal, 1 per-post; 2 should be used. */
	function testXmlToLinks_two()
	{
		$links = TLA::xml_to_links(
			"<?xml version=\"1.0\" ?>\n" .
			"<Links>\n" .
			"	<Link>\n" .
			"		<PostID>0</PostID>\n" .
			"		<URL>tu1</URL>\n" .
			"		<Text>tt1</Text>\n" .
			"		<BeforeText> </BeforeText>\n" .
			"		<AfterText> </AfterText>\n" .
			"\n" .
			"		<RssText></RssText>\n" .
			"		<RssBeforeText></RssBeforeText>\n" .
			"		<RssAfterText></RssAfterText>\n" .
			"		<RssPrefix></RssPrefix>\n" .
			"		<RssMaxAds>6</RssMaxAds>\n" .
			"	</Link>\n" .
			"	<Link>\n" .
			"		<PostID>0</PostID>\n" .
			"		<URL>tu2</URL>\n" .
			"		<Text>tt2</Text>\n" .
			"		<BeforeText> </BeforeText>\n" .
			"		<AfterText> </AfterText>\n" .
			"\n" .
			"		<RssText></RssText>\n" .
			"		<RssBeforeText></RssBeforeText>\n" .
			"		<RssAfterText></RssAfterText>\n" .
			"		<RssPrefix></RssPrefix>\n" .
			"		<RssMaxAds>6</RssMaxAds>\n" .
			"	</Link>\n" .
			"	<Link>\n" .
			"		<PostID>1</PostID>\n" .
			"		<URL>per-post -- don't use</URL>\n" .
			"		<Text>tt3</Text>\n" .
			"		<BeforeText> </BeforeText>\n" .
			"		<AfterText> </AfterText>\n" .
			"\n" .
			"		<RssText></RssText>\n" .
			"		<RssBeforeText></RssBeforeText>\n" .
			"		<RssAfterText></RssAfterText>\n" .
			"		<RssPrefix></RssPrefix>\n" .
			"		<RssMaxAds>6</RssMaxAds>\n" .
			"	</Link>\n" .
			"</Links>");
			
		$this->assertEqual(count($links), 2);
		
		$l = $links[0];
		$this->assertEqual($l['url'], "tu1");
		$this->assertEqual($l['text'], "tt1");

		$l = $links[1];
		$this->assertEqual($l['url'], "tu2");
		$this->assertEqual($l['text'], "tt2");
	}

	/** Empty links list. */	
	function testLinks_to_html_empty()
	{
		$this->assertEqual(TLA::links_to_html(array()), '');
	}
	
	/** Single link. */
	function testLinks_to_html_single()
	{
		$this->assertEqual(TLA::links_to_html(array(
			array('url' => 'a', 'text' => 'b'))),
			'<ul class="tla"><li><a href="a">b</a></li></ul>');
	}
	
	/** Two links. */
	function testLinks_to_html_two()
	{
		$this->assertEqual(TLA::links_to_html(array(
			array('url' => 'a', 'text' => 'b'), array('url' => 'c', 'text' => 'd'))),
			'<ul class="tla"><li><a href="a">b</a></li><li><a href="c">d</a></li></ul>');
	}
}
 
?>
