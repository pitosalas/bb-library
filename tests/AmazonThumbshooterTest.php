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
// $Id: AmazonThumbshooterTest.php,v 1.1 2006/10/23 11:44:51 alg Exp $
//

require_once 'DefaultTestCase.php';
require_once $classes . 'AmazonThumbshooter.class.php';

class AmazonThumbshooterTest extends DefaultTestCase
{
	/**
	 * Returning nothing on empty input.
	 */
	function test_amazon_parse_response_empty()
	{
		$this->assertNull(AmazonThumbshooter::amazon_parse_response(''));
		$this->assertNull(AmazonThumbshooter::amazon_parse_response(null));
	}

	/**
	 * No link reported.
	 */
	function test_amazon_parse_response_no_thumbnail()
	{
		$r = '<some></some>';
		$this->assertNull(AmazonThumbshooter::amazon_parse_response($r));

		$r = "<aws:thumbnail>link</aws:thumbnail>";
		$this->assertNull(AmazonThumbshooter::amazon_parse_response($r));

		$r = "<aws:thumbnail exists ='false'>link</aws:thumbnail>";
		$this->assertNull(AmazonThumbshooter::amazon_parse_response($r));
	}

	/**
	 * Link reported.
	 */
	function test_amazon_parse_response_present()
	{
		$r = "a <aws:thumbnail exists='true'>link</aws:thumbnail> b";
		$this->assertEqual('link', AmazonThumbshooter::amazon_parse_response($r));

		$r = '<?xml version="1.0"?><aws:ThumbnailResponse xmlns:aws="http://ast.amazonaws.com/doc/2005-10-05/"><aws:Response><aws:OperationRequest><aws:RequestId>1bc51637-d6ca-4c5a-b8d8-f7daed0e4b57</aws:RequestId></aws:OperationRequest><aws:ThumbnailResult><aws:Thumbnail Exists="true">http://s3-external-1.amazonaws.com/alexa-thumbnails/A0280E61CDD0105FBFCBBB259EEF63E2264F5ACBs?Signature=lbjsz%2FK%2Fu4S6pa6Y2WOcpvtXchc%3D&amp;Expires=1155829366&amp;AWSAccessKeyId=1FVZ0JNEJDA5TK457CR2</aws:Thumbnail><aws:RequestUrl>http://bbc.com/</aws:RequestUrl></aws:ThumbnailResult><aws:ResponseStatus><aws:StatusCode>Success</aws:StatusCode></aws:ResponseStatus></aws:Response></aws:ThumbnailResponse>';
		$this->assertEqual('http://s3-external-1.amazonaws.com/alexa-thumbnails/A0280E61CDD0105FBFCBBB259EEF63E2264F5ACBs?Signature=lbjsz%2FK%2Fu4S6pa6Y2WOcpvtXchc%3D&Expires=1155829366&AWSAccessKeyId=1FVZ0JNEJDA5TK457CR2', AmazonThumbshooter::amazon_parse_response($r));
	}
	
	function test_optimizeURL_empty()
	{
		$u = AmazonThumbshooter::optimizeURL(null);
		$this->assertEqual(null, $u);
		
		$u = AmazonThumbshooter::optimizeURL('');
		$this->assertEqual('', $u);
	}

	function test_optimizeURL_valid()
	{
		$this->assertEqual('http://www.some.com', AmazonThumbshooter::optimizeURL('http://www.Some.com'));
		$this->assertEqual('http://www.some.co/', AmazonThumbshooter::optimizeURL('http://www.Some.co/'));
		$this->assertEqual('http://www.some.co/', AmazonThumbshooter::optimizeURL('http://www.Some.co/A'));
		$this->assertEqual('www.Some.co/', AmazonThumbshooter::optimizeURL('www.Some.co/'));
	}
}

?>