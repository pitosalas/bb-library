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
// $Id: Folder.class.php,v 1.5 2007/09/21 14:42:33 alg Exp $
//

class Folder
{
    var $id;
    var $owner_id;
    var $title;
    var $description;
    var $created;
    var $viewType_id;
    var $viewTypeParam;
  	
  	var $opml;
  	var $opml_url;
  	var $opml_user;
  	var $opml_password;
  	var $opml_updates_period;
  	var $opml_last_updated;
  	var $opml_last_error;
  	var $dynamic;
    
    var $order;
    var $autoTags;
    var $show_in_nav_bar;
    
    function Folder($owner_id = 0, $title = null, $description = null,
    	$viewType_id = 1, $viewTypeParam = null, $opml = null, $opml_url = null,
    	$opml_user = null, $opml_password = null, $opml_updates_period = 0,
    	$opml_last_updated = 0, $opml_last_error = null, $dynamic = 0, $order = null,
    	$autoTags = true, $show_in_nav_bar = false)
    {
        $this->created = mktime();

		$this->owner_id = $owner_id;
		$this->title = $title;
		$this->description = $description;
		$this->viewType_id = $viewType_id;
		$this->viewTypeParam = $viewTypeParam;
		$this->opml = $opml;
		$this->opml_url = $opml_url;
		$this->opml_user = $opml_user;
		$this->opml_password = $opml_password;
        $this->opml_updates_period = $opml_updates_period;
        $this->opml_last_updated = $opml_last_updated;
        $this->opml_last_error = $opml_last_error;
        $this->dynamic = $dynamic;
        $this->order = $order;
        $this->autoTags = $autoTags;
        $this->show_in_nav_bar = $show_in_nav_bar;
    }
    
    /** Returns TRUE if the folder is OPML folder. */
    function is_opml_folder()
    {
    	return isset($this->opml_url) && trim($this->opml_url) != '';
    }
}

?>