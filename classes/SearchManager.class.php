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
// $Id: SearchManager.class.php,v 1.2 2006/11/13 11:09:21 alg Exp $
//

require_once 'Database.class.php';

class SearchManager
{
    function search($search, $typeFeeds, $typeFolders, $typePeople, $zoneTitle, $zoneDescription, $zoneTags,
        $zoneSiteURL, $zoneDataURL)
    {
        $results = array();
        
        $db = new Database();
        if ($typeFolders) $results = $db->findFolders($search, $zoneTitle, $zoneDescription, $zoneTags, $zoneDataURL);
        if ($typeFeeds) $results = array_merge($results, $db->findItems($search, $zoneTitle, $zoneDescription, $zoneTags, $zoneSiteURL, $zoneDataURL));
        if ($typePeople) $results = array_merge($results, $db->findPeople($search, $zoneTitle, $zoneDescription, $zoneTags));
        $db->disconnect();

        return $results;
    }
}

?>