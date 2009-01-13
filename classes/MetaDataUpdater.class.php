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
// $Id: MetaDataUpdater.class.php,v 1.1 2006/10/23 11:44:51 alg Exp $
//

require_once 'Database.class.php';
require_once 'BBService.class.php';

// A period of an item meta-data updates
define('UPDATE_PERIOD_SEC', 60*60*24);
define('MDU_DEBUG', false);

/**
 * Updates stale feeds meta-data.
 */
class MetaDataUpdater
{
	function run()
	{
		if (!BBS_ENABLED) return;
	
		set_time_limit(600);

        $db = new Database();

		$links = MetaDataUpdater::get_links_to_update($db);
		if (MDU_DEBUG) echo 'To Update: ' . count($links) . ' links<br>';
		foreach ($links as $link)
		{
			$meta = MetaDataUpdater::get_meta_for_link($link);
			if ($meta != null)
			{
				MetaDataUpdater::update_link_meta_data($link, $meta, $db);
			}
		}
		
		$db->disconnect();
	}
	
	function get_links_to_update($db)
	{
		$t = mktime() - UPDATE_PERIOD_SEC;
		$q = 'SELECT siteUrl FROM Item WHERE trim(siteUrl) != \'\' AND lastMetadataUpdate < ' . $t;
		$r = $db->_query($q);
		$links = array();
		if ($r)
		{
			while ($row = mysql_fetch_row($r)) $links[] = $row[0];
			mysql_free_result($r);
		}
		
		return $links;
	}
	
	/**
	 * Contacts the service to get meta-information from it.
	 */
	function get_meta_for_link($link)
	{
		// Discovery is in progress
		$meta = null;
		
		$data = BBService::discover($link);
		if ($data != null)
		{
			$inlinks = null;
			$rank = null;
			
			if ($data['code'] == 0)
			{
				$inlinks = (int) $data['inboundLinks'];
				if ($inlinks < 0) $inlinks = null;
				
				$rank = (int) $data['rank'];
				if ($rank < 0) $rank = null;
				
			}

			$meta = array('inlinks' => $inlinks, 'rank' => $rank);
			
			if (MDU_DEBUG) echo $link . ' (' . $inlinks . ', ' . $rank . ')<br>';
		} else
		{
			if (MDU_DEBUG) echo $link . ' (Unknown)<br>';
		}

		if (MDU_DEBUG)
		{
			ob_flush();
			flush();
		}

		return $meta;
	}
	
	function update_link_meta_data($link, $meta, $db)
	{
		$inlinks = $meta['inlinks'];
		$rank = $meta['rank'];
		
		$u = array();
		if ($inlinks != null) $u[] = 'technoInlinks = ' . $inlinks;
		if ($rank != null) $u[] = 'technoRank = ' . $rank;
		
		$u[] = 'lastMetadataUpdate = ' . mktime();
		$q = 'UPDATE Item SET ' . implode(', ', $u) . ' WHERE siteUrl = \'' . $db->_escapeSQL($link) . '\'';
		$db->_query($q);
	}
}
?>