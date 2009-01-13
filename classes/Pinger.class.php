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
// $Id: Pinger.class.php,v 1.1 2006/10/23 11:44:51 alg Exp $
//

/**
 * Asynchronous HTTP pinger.
 */
class Pinger
{
	function ping_link($link)
	{
		$regs = array();
		if (ereg('^http://([^/]+)(/([^/]*/)*[^/]+)$', $link, $regs))
		{
			$host = $regs[1];
			$resource = $regs[2];

			Pinger::ping($host, 80, $resource);
		}
	}
	
	function ping($host, $port, $link, $timeout = 20)
	{
		$errno = 0;
		$errstr = null;
		
		if (!$fp = @fsockopen($host, $port, $errno, $errstr, $timeout))
		{
            switch($errno)
            {
				case -3: $errormsg = 'Socket creation failed (-3)';
				case -4: $errormsg = 'DNS lookup failure (-4)';
				case -5: $errormsg = 'Connection refused or timed out (-5)';
				default: $errormsg = 'Connection failed (' . $errno . ')';
			}

			return $errormsg;
		}
		
		$request = Pinger::build_request($host, $link);
        fwrite($fp, $request);
        fclose($fp);
        
        return null;
	}
	
	function build_request($host, $link, $user_agent = 'BlogBridge Library Pinger', $accept = 'text/xml,application/xml,application/xhtml+xml,text/html,text/plain,image/png,image/jpeg,image/gif,*/*')
	{
        $headers = array();
        $headers[] = "GET {$link} HTTP/1.0"; // Using 1.1 leads to all manner of problems, such as "chunked" encoding
        $headers[] = "Host: {$host}";
        $headers[] = "User-Agent: {$user_agent}";
        $headers[] = "Accept: {$accept}";

    	return implode("\r\n", $headers)."\r\n\r\n";
	}
}
?>