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
// $Id: FeedParser.class.php,v 1.3 2007/05/11 11:12:45 alg Exp $
//

require_once 'simplepie/simplepie.inc';

/**
 * Calls everything you need to parse a feed.
 */
class FeedParser
{
    function parse($url, $reload = false)
    {
    	$feed = null;
    	
    	if ($url != null && trim($url) != '')
    	{
    		if ($reload)
    		{
				$cache_filename = SIMPLEPIE_CACHE_DIR . '/' . urlencode($url) . '.spc';
				@unlink($cache_filename);
    		}
    		
    		$sp = new SimplePie();
    		$sp->feed_url($url);
    		$sp->cache_location(SIMPLEPIE_CACHE_DIR);
    		$sp->bypass_image_hotlink(false);
    		$sp->strip_ads(true);
    		$sp->strip_attributes(false);
    		$sp->init();
    		
    		$sp->handle_content_type();
    		
    		$feed = FeedParser::convert_to_object($sp);
    	}
    	
    	return $feed;
    }
    
    function convert_to_object(&$sp)
    {
    	$feed = array('title', $sp->get_feed_title(), 'items' => array());
    	$items = $sp->get_items();

		if (isset($items) && is_array($items))
		{    	
	    	foreach ($items as $item)
	    	{
	    		$author = $item->get_author(0);
	    		if ($author != null) $author = $author->get_name();
	    		
	    		// Join enclosures and links to mp3 files
	    		$sp->local = array();
	    		$sp->elsewhere = array();
	    		$links = $sp->get_links($item->get_description());
	    		
	    		$mp3_links = FeedParser::get_mp3_links($sp->local + $sp->elsewhere);

	    		$enclosures = $item->get_enclosures();
	    		if (count($mp3_links) > 0)
	    		{
	    			foreach ($mp3_links as $link)
	    			{
	    				$enclosures[] = new SimplePie_Enclosure($link, 'audio/mp3', -1);
	    			}
	    		}
	    		
	    		$feed['items'][] = array(
	    			'title' => $item->get_title() . $links[0],
	    			'date' => $item->get_date(),
	    			'text' => $item->get_description(),
	    			'author' => $author,
	    			'enclosures' => $enclosures);
	    	}
		}
    	
    	return $feed;
    }
    
    /** Returns the list of links for mp3 files. */
    function get_mp3_links($links)
    {
    	$mp3_links = array();
    	
    	foreach ($links as $link)
    	{
    		if (FeedParser::is_mp3_link($link)) $mp3_links[] = $link;
    	}
    	
    	return $mp3_links;
    }
    
    /** Returns not FALSE if the file has mp3 extension. */
    function is_mp3_link($link)
    {
		$ext = empty($link) ? false : pathinfo($link, PATHINFO_EXTENSION);
		return stristr($ext, 'mp3');
    }
}
?>