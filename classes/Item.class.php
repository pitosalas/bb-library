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
// $Id: Item.class.php,v 1.7 2007/09/26 12:48:45 alg Exp $
//

class Item
{
    var $id;
    var $owner_id;
    var $title;
    var $description;
    var $created;
    var $siteURL;
    var $dataURL;
    var $itunesURL;
    var $type_id;
    var $dynamic;
    
    // Technorati fields
    var $technoInlinks;
    var $technoRank;
    
    var $order;
    
    var $useITunesURL;
    var $usePlayButtons;
    var $showPreview;
    
    var $autoTags;
    var $show_in_nav_bar;
    
    function Item($owner_id = 0, $title = null, $description = null, $siteURL = null,
    	$dataURL = null, $type_id = 0, $dynamic = 0, $technoInlinks = null, $technoRank = null, $order = null,
    	$itunesURL = null, $autoTags = true, $show_in_nav_bar = false)
    {
        $this->created = mktime();
        
        $this->owner_id = $owner_id;
        $this->title = $title;
        $this->description = $description;
        $this->siteURL = $siteURL;
        $this->dataURL = $dataURL;
        $this->type_id = $type_id;
        $this->dynamic = $dynamic;
        $this->technoInlinks = $technoInlinks;
        $this->technoRank = $technoRank;
        $this->order = $order;
        $this->itunesURL = $itunesURL;
        
        $this->useITunesURL = false;
        $this->usePlayButtons = false;
        $this->showPreview = false;
        
        $this->autoTags = $autoTags;
        $this->show_in_nav_bar = $show_in_nav_bar;
    }
}

?>