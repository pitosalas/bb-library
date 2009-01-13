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
// $Id: AmazonThumbshooter.class.php,v 1.2 2007/01/03 13:53:26 alg Exp $
//

if (file_exists('sites/config.conf')) require_once('sites/config.conf');
require_once 'httpclient/HttpClient.class.php';

/**
 * The thumbshooter is responsible for contacting Amazon AST for thumbshots.
 * It also maintains local cache of thumbnails: records new items, updates
 * and removes them.
 */
class AmazonThumbshooter
{
	/**
	 * Queries and writes image to output stream.
	 */
	function output_image_for_site($url)
	{		
		$filename = AmazonThumbshooter::get_thumbshot_file($url);

		header('Content-Type: image/jpeg');
		readfile($filename);
	}
	
	/**
	 * Returns filename in the cache to use for reading of image data.
	 */
	function get_thumbshot_file($url)
	{
		$filename = AmazonThumbshooter::cache_url_to_filename($url);
		$image_exists = file_exists($filename);

		// If image isn't there or it's time to update it,
		// continue
		if (!$image_exists || AmazonThumbshooter::cache_image_update_required($filename))
		{
			$amazon_url = AmazonThumbshooter::amazon_get_image_url($url);

			// Update timestamp of the file
			if ($image_exists) touch($filename);
			
			if ($amazon_url != null || !$image_exists)
			{
				// We set our own URL if Amazon URL wasn't found and image
				// still doesn't exist (we aren't updating it)
				if ($amazon_url == null) $amazon_url = AMAZON_NO_IMAGE_URL;
				
				// Writing to cache
				if (!AmazonThumbshooter::cache_write_image($filename, $amazon_url))
				{
					if (!AmazonThumbshooter::cache_write_image($filename, AMAZON_NO_IMAGE_URL))
					{
						// If writing wasn't successful, return the filename of fixed
						// no-image file as a last resort
						$filename = AMAZON_NO_IMAGE_FILENAME;
					}
				}
			}
		}
		
		return $filename;
	}
	
	// ----------------------------------------------------------------------------------
	// Cache
	// ----------------------------------------------------------------------------------

	/**
	 * Converts URL to a filename ready for caching.
	 */
	function cache_url_to_filename($url)
	{
		if (AMAZON_FRONT_PAGE_OPTIMIZATION) $url = AmazonThumbshooter::optimizeURL($url);
		
		return AMAZON_CACHE_DIR . '/' . sprintf('%u', crc32($url));
	}

	/**
	 * Checks if cache cleaning is required and performs it.
	 */
	function cache_clean()
	{
		$entries = AmazonThumbshooter::cache_get_entries();
		$to_remove = count($entries) - AMAZON_CACHE_MAX_ENTRIES; 
		if ($to_remove > 0)
		{
			asort($entries);
			foreach ($entries as $fn => $time)
			{
				unlink(AMAZON_CACHE_DIR . '/' . $fn);
				
				$to_remove--;
				if ($to_remove == 0) break;
			}
		}
	}
	
	/**
	 * Returns all entries except for '.', '..', and 'readme.txt'.
	 */
	function cache_get_entries()
	{
		$entries = array();
		
		$dir = AMAZON_CACHE_DIR;
		$dh  = opendir($dir);
		
		while (false !== ($filename = readdir($dh)))
		{
			if ($filename != '.' && $filename != '..' && $filename != 'readme.txt')
			{
   				$entries[$filename] = filemtime($dir . '/' . $filename);
			}
		}
		
		return $entries;
	}
	
	/**
	 * Checks if it's time to update the image.
	 */
	function cache_image_update_required($filename)
	{
		return time() - filemtime($filename) > AMAZON_UPDATE_PERIOD_SEC;
	}
	
	/**
	 * Saves the contents of image from a given Amazon URL to cache file with a given name.
	 * Returns TRUE if writing was successful, FALSE if:
	 *  - there's no a resource specified by URL
	 *  - unable to open file
	 *  - unable to write file
	 */
	function cache_write_image($filename, $amazon_url)
	{
		$data = HttpClient::quickGet($amazon_url);

		return AmazonThumbshooter::cache_write_image_data($filename, $data);
	}

	/**
	 * Writes block of data to file.
	 */	
	function cache_write_image_data($filename, &$data)
	{
		$fn = false;

		if ($data)
		{
			if ($f = fopen($filename, 'w'))
			{
				$fn = fwrite($f, $data) !== FALSE;
			} 
		}

		return $fn;
	}
	
	// ----------------------------------------------------------------------------------
	// Amazon
	// ----------------------------------------------------------------------------------

	/**
	 * Contacts Amazon AST for image URL for a given site.
	 * If Amazon AST reports no image, the result is NULL.
	 */
	function amazon_get_image_url($url)
	{
		//$image_url = 'http://pthumbnails.alexa.com/image_server.cgi?size=small&amp;url=' . urlencode($url);

		$image_url = null;
		
		$response = AmazonThumbshooter::amazon_get_response($url);
		if ($response != null)
		{
			$image_url = AmazonThumbshooter::amazon_parse_response($response);
		}
		
		return $image_url;
	}
	
	/**
	 * Calls Amazon AST and returns response.
	 */
	function amazon_get_response($url)
	{
        $timestamp = AmazonThumbshooter::amazon_generate_timestamp();
        $url_enc = urlencode($url);
        $timestamp_enc = urlencode($timestamp);
        $signature_enc = urlencode(AmazonThumbshooter::amazon_calculate_RFC2104HMAC(
			'AlexaSiteThumbnail' . 'Thumbnail' . $timestamp, AMAZON_SECRET_ACCESSKEY
		));

        $request_url = 'http://ast.amazonaws.com/xino/?' .
        	'Service=AlexaSiteThumbnail' .
            '&Action=Thumbnail' .
            '&AWSAccessKeyId=' . AMAZON_AWSACCESSKEYID .
            '&Timestamp=' . $timestamp_enc .
            '&Signature=' . $signature_enc .
            '&Size=Small' .
            '&Url=' . urlencode($url);

		return HttpClient::quickGet($request_url);
	}
	
	/**
	 * Parses response of the service and return image URL.
	 */
	function amazon_parse_response($response)
	{
		$image_url = null;
		
		$expr = '/<aws:thumbnail\s+([^>]+)>([^<]+)/i';
		$matches = array();
		
		if (preg_match($expr, $response, $matches))
		{
			$attrs = $matches[1];
			if (preg_match('/exists\s*=\s*[\'"]true[\'"]/i', $attrs))
			{
				$image_url = html_entity_decode(trim($matches[2]));
			}
		}
		
		return $image_url;
	}
	
	/**
	 * Calculates hash.
	 */
	function amazon_calculate_RFC2104HMAC($data, $key)
	{
	    return base64_encode(
	        pack("H*", sha1((str_pad($key, 64, chr(0x00)) ^(str_repeat(chr(0x5c), 64))) .
	        pack("H*", sha1((str_pad($key, 64, chr(0x00)) ^(str_repeat(chr(0x36), 64))) .
	        $data))))
	     );
	}

	/**
	 * Generates timestamp in format: yyyy-MM-dd'T'HH:mm:ss.SSS'Z'
	 */
	function amazon_generate_timestamp()
	{
	    return gmdate("Y-m-d\TH:i:s.\\0\\0\\0\\Z", time());
	}
	
	/**
	 * Takes only the first (site) part of the URL.
	 */
	function optimizeURL($url)
	{
		$matches = array();
		if (preg_match('/^(\w+:\/+[^\/]+(\/|$))/', $url, $matches))
		{
			$url = strtolower(trim($matches[1]));
		}
		
		return $url;
	}
}

?>