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
// $Id: function.implode.php,v 1.2 2007/03/09 20:45:43 alg Exp $
//

/**
 * Joins elements of array ('array') together with separator ('separator').
 */ 
function smarty_function_implode($params, &$smarty)
{
  $sep = ', ';
  if (isset($params['separator'])) $sep = $params['separator']; 
  
  $array = array();
  if (isset($params['array'])) $array = $params['array'];
  
  if (isset($params['quotes']) && $params['quotes'] == 'true')
  {
    for ($i = 0; $i < count($array); $i++) $array[$i] = '"' . $array[$i] . '"';
  }
  
  return implode($sep, $array);
}
?>
