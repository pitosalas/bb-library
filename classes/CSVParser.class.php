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
// $Id: CSVParser.class.php,v 1.1 2006/10/23 11:44:51 alg Exp $
//

class CSVParser
{
	var $file;
	var $seq;
	
	/**
	 * Creates parser and initializes it with the file.
	 */
    function CSVParser($file)
    {
    	$this->file = $file;
    	$this->seq = mktime();
    }
    
    /**
     * Parses the file and returns statistical information about it:
     *  - preview row
     *  - number of rows
     * ... or simple error message if something is wrong.
     */
    function getStats()
    {
    	$row = 1;
    	$cols = -1;
		$sample = null;
		$error = null;
		
		$handle = fopen($this->file, 'r');
		while (($data = fgetcsv($handle, 1000, ',')) !== FALSE)
		{
			if ($row == 1) $sample = $data;
			
   			if ($cols == -1) $cols = count($data); else
   			if ($cols != count($data)) $error = 'Number of columns changes from ' . $cols . ' to ' . count($data) . ' in the row ' . $row;
   			 
			$row++;
		}
		fclose($handle);
    	
    	if ($row == 1) $error = 'Empty file.';
    	
    	return $error ? $error : array(
    		'samples' => $sample,
    		'rows' => $row - 1
    	);
    }
    
    /**
     * Returns the list of people read from the file and converted using
     * the given set of rules.
     */
    function getPeople(&$rules)
    {
    	$people = array();
    	
    	$row = 0;
		$handle = fopen($this->file, 'r');
		while (($data = fgetcsv($handle, 1000, ',')) !== FALSE)
		{
			$row++;

			if ($row == 1 && isset($rules['skiprow'])) continue;

			$person = $this->_row2person($data, $rules);
			if ($person) $people[] = $person; 			
		}
		fclose($handle);
    	
    	return $people;
    }
    
    /**
     * Converts single row to the person object or returns NULL if not possible.
     */
    function _row2person(&$row, &$rules)
    {
    	$person = new Person();
    	$person->organization_id = $rules['organization_id'];
    	$person->type_id = $rules['type_id'];
    	$person->fullName = $this->_row2fullName($row, $rules);
    	$person->userName = $this->_row2userName($row, $rules);
    	$person->password = $this->_row2password($row, $rules);
    	$person->email = $this->_row2email($row, $rules);
    	$person->description = $this->_row2description($row, $rules);
    	$person->tags = $this->_row2tags($row, $rules);
    	
    	return $person;
    }
    
    /**
     * Convert row and rules to full name. 
     */
    function _row2fullName(&$row, &$rules)
    {
    	return $row[(int) $rules['fullName']];
    }    
    
    /**
     * Convert row and rules to user name. 
     */
    function _row2userName(&$row, &$rules)
    {
    	$rule = $rules['userName'];
    	
    	if ($rule == 'generate')
    	{
    		$userName = 'u' . $this->seq;
    		$this->seq++;
    	} else if ($rule == 'fullName')
    	{
    		$fullName = $this->_row2fullName($row, $rules);
    		
    		// Leave only alpha-numerics
    		$userName = ereg_replace('[^a-zA-Z0-9]', '_', $fullName);
    		
    		// Compact
    		$userName = ereg_replace('_+', '_', $userName);
    	} else
    	{
    		// Column
    		$userName = $row[(int)$rule];
    	}
    	
    	
    	return $userName;
    }    
    
    /**
     * Convert row and rules to e-mail. 
     */
    function _row2email(&$row, &$rules)
    {
    	$rule = $rules['email'];
    	
    	if ($rule == 'blank')
    	{
    		$email = null;
    	} else $email = $row[(int)$rule];
    	
    	return $email;
    }    
    
    /**
     * Convert row and rules to password. 
     */
    function _row2password(&$row, &$rules)
    {
    	$rule = $rules['password'];
    	
    	if ($rule == 'const')
    	{
    		$password = $rules['passwordConst'];
    	} else if ($rule == 'userName')
    	{
    		$password = $this->_row2userName($row, $rules);
    	} else $password = $row[(int)$rule];
    	
    	return $password;
    }    

    /**
     * Convert row and rules to description. 
     */
    function _row2description(&$row, &$rules)
    {
    	$rule = $rules['description'];
    	
    	if ($rule == 'blank')
    	{
    		$description = null;
    	} else $description = $row[(int)$rule];
    	
    	return $description;
    }    
    
    /**
     * Convert row and rules to tags. 
     */
    function _row2tags(&$row, &$rules)
    {
    	$rule = $rules['tags'];
    	
    	if ($rule == 'blank')
    	{
    		$tags = null;
    	} else if ($rule == 'const')
    	{
    		$tags = $rules['tagsConst'];
    	} else $tags = $row[(int)$rule];
    	
    	return $tags;
    }    
}
?>