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
// $Id: function.pencode.php,v 1.1 2007/03/16 14:56:13 alg Exp $
//

/**
 * Encodes the URL for the podcasts player.
 */ 
function smarty_function_pencode($params, &$smarty)
{
	$s = array('@\?@', '@=@', '@&@');
	$r = array('%3F',  '%3D', '%26');
	
	return preg_replace($s, $r, $params['url']);
}
?>
