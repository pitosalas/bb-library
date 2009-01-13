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
// $Id: function.itunes_url.php,v 1.1 2007/03/16 14:56:13 alg Exp $
//

/**
 * Converts a URL into iTunes subscription URL.
 */ 
function smarty_function_itunes_url($params, &$smarty)
{
	return preg_replace('/http:/', 'itpc:', $params['url']);
}
?>
