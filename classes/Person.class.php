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
// $Id: Person.class.php,v 1.3 2007/07/17 11:17:58 alg Exp $
//

class Person
{
    var $id;
    var $userName;
    var $fullName;
    var $password;
    var $email;
    var $description;
    var $organization_id;
    var $type_id;
    var $last_login;
    var $home_page;
    var $license_accepted;
    var $no_ads;
    
    function Person($userName = null, $fullName = null, $password = null, $email = null,
    	$description = null, $organization_id = null, $type_id = 0, $last_login = null,
    	$home_page = null, $license_accepted = null, $no_ads = false)
    {
	    $this->userName = $userName;
	    $this->fullName = $fullName;
	    $this->password = $password;
	    $this->email = $email;
	    $this->description = $description;
	    $this->organization_id = $organization_id;
	    $this->type_id = $type_id;
	    $this->last_login = $last_login;
	    $this->home_page = $home_page;
	    $this->license_accepted = $license_accepted;
	    $this->no_ads = $no_ads;
    }
}

?>