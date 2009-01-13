<?php
// BlogBridge Directory  
// Copyright (C) 2006 by R. Pito Salas
//
// This program is free software; you can redistribute it and/or modify it under
// the terms of the GNU General Public License as published by the Free Software Foundation;
// either version 2 of the License, or (at your option) any later version.
//
// This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
// without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
// See the GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License along with this program;
// if not, write to the Free Software Foundation, Inc., 59 Temple Place,
// Suite 330, Boston, MA 02111-1307 USA
//
// Contact: R. Pito Salas
// Mail To: pitosalas@gmail.com
//
// $Id $
//

if (file_exists('sites/config.conf')) require_once('sites/config.conf');

require_once 'FileUtils.class.php';
require_once 'Database.class.php';
require_once 'httpclient/HttpClient.class.php';

/** The name of the links file holding the cached version of TLA HTML. */
define('LINKS_FILE', SITE_PATH . 'tla.inc');
/** The number of seconds the file can live before it's considered outdated. */
define('EXPIRATION_AGE', 60 * 60 * 24);
/** Main inventory URL. */
define('TLA_URL', 'http://www.text-link-ads.com/xml.php?inventory_key=');
/** TLA apikey property name. */
define('TLA_APIKEY', 'tla_apikey');

/**
 * This is a text-link-ads support class. When TLA support is enabled
 * this class returns the ul-li-a list of links for a given site, or
 * FALSE when TLA is disabled. If there are no links yet, but TLA is
 * enabled, the result will be empty.
 */
class TLA
{
	/**
	 * Reads links from file when TLA is enabled.
	 * Returns:
	 * 	- FALSE if disabled
	 *  - empty string when no links
	 *  - UL-LI-A HTML string with links when present
	 */
	function get_links()
	{
		$apikey = TLA::db_get_apikey();
		
		if (!$apikey) return false;
		
		if (TLA::is_expired())
		{
			touch(LINKS_FILE);
			TLA::update_links($apikey);
		}
		
		return TLA::read_from_file(); 
	}
	
	/**
	 * Returns TRUE if the links file doesn't exist or outdated.
	 */
	function is_expired()
	{
		return !file_exists(LINKS_FILE) ||
			(mktime() - filemtime(LINKS_FILE) > EXPIRATION_AGE);
	}

	/**
	 * Reads contents of the links file.
	 */
	function read_from_file()
	{
		return file_get_contents(LINKS_FILE);
	}
		
	/**
	 * Updates links, but fetching them from the TLA site,
	 * converting them into the HTML and saving to the file.
	 */
	function update_links($apikey)
	{
		$links = TLA::net_get_links($apikey);
		$html = TLA::links_to_html($links);
		TLA::save_to_file($html);
	}

	/** Converts the list of links into HTML format. */	
	function links_to_html($links)
	{
		$html = '';
		
		if (count($links) > 0)
		{
			$html .= '<ul class="tla">';

			foreach ($links as $l)
			{
				$html .= '<li><a href="' . $l['url'] . '">' . $l['text'] . '</a></li>';
			}

			$html .= '</ul>';
		}
		
		return $html;
	}

	/**
	 * Saves HTML to the file.
	 */	
	function save_to_file($html)
	{
		FileUtils::file_put_contents(LINKS_FILE, $html);
	}
	
	/**
	 * Invalidates the cached links. Useful for cache reset on
	 * preferences change.
	 */
	function invalidate()
	{
		@unlink(LINKS_FILE);
	}
	
	// ------------------------------------------------------------------------
	// Network
	// ------------------------------------------------------------------------
	
	/**
	 * Returns the array of 'text', 'url' pairs representing links,
	 * or empty array if no links present.
	 */
	function net_get_links($apikey)
	{
		$xml = HttpClient::quickGet(TLA_URL . $apikey);
		return TLA::xml_to_links($xml);
	}

	/**
	 * Parses XML received and returns the array of 'text', 'url' pairs.
	 */
	function xml_to_links($xml)
	{
		$links = array();

		$linkss = spliti("</link>", $xml);
		foreach ($linkss as $l)
		{
			if (eregi("<url>(.*)</url>.*<text>(.*)</text>", $l, $r))
			{
				$url = $r[1];
				$txt = $r[2];
				
				$links []= array('url' => $url, 'text' => $txt);
			}
		}
		
		return $links;
	}
	
	// ------------------------------------------------------------------------
	// Database
	// ------------------------------------------------------------------------
	
	/**
	 * Returns the TLA API key or FALSE if not present. 
	 */
	function db_get_apikey()
	{
		$db = new Database();
		$apikey = trim($db->getApplicationProperty(TLA_APIKEY));
		$db->disconnect();
		
		return strlen($apikey) == 0 ? false : $apikey;
	}
}
?>
