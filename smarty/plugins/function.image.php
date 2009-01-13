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
// $Id: function.image.php,v 1.7 2007/08/23 17:04:11 alg Exp $
//

include_once 'function.url.php';

/**
 * Returns image URL.
 * @param person - person object, or
 *        pic    - picture file name.
 */
function smarty_function_image($params, &$smarty)
{
    $url = IMAGES_URL;
    
    if (isset($params['person']))
    {
        $person = $params['person'];
        $url = IMAGES_PEOPLE_URL . '/' . $person->id . '.img';
    } else if (isset($params['person_id']))
    {
        $url = IMAGES_PEOPLE_URL . '/' . $params['person_id'] . '.img';
    } else if (isset($params['pic']))
    {
        $url = _image($params['pic']);
    }
    
    if (isset($params['suffix'])) $url .= $params['suffix'];
    
    return $url;
}

function _image($pic)
{
    return IMAGES_URL . '/' . $pic;
}

function smarty_function_css_image($params, &$smarty)
{
	return _css_image($params['pic'], isset($params['title']) ? $params['title'] : '');
}

function _css_image($pic, $title = '')
{
	return '<img border="0" class="' . $pic . '" src="' . _image('spacer.gif') . '" title="' . $title . '">';
}

function smarty_function_css_image_link($params, &$smarty)
{
	$item = $params['item'];
	$small = isset($params['small']);

	if (is_a($item, 'Folder'))
	{
		$url = smarty_function_url(array('folder' => $item, 'type' => 'opml'), $smarty);
		$img = _css_image('opml' . ($small ? '_small' : ''), 'Outline');
	} else
	{
		$type = $item->type_id;
		$real = isset($params['real']) && $params['real'];
	
		switch ($type)
		{
			case 4: // Web Site
				$url = $item->siteURL;
				$img = _css_image('web' . ($small ? '_small' : ''), 'Web Site');
				break;
			case 3: // Outline
				$url = smarty_function_url(array('item' => $item, 'type' => 'opml'), $smarty);
				$img = _css_image('opml' . ($small ? '_small' : ''), 'Outline');
				break;
			default: // RSS and Podcast
				$url = smarty_function_url(array('item' => $item, 'type' => 'rss', 'real' => $real), $smarty);
				$img = _css_image('rss' . ($small ? '_small' : ''), 'Data Feed');
				break;
		}
	}
		
	return "<a href=\"$url\">$img</a>";
}
?>
