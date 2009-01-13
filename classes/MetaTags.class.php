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
// $Id: MetaTags.class.php,v 1.2 2007/07/10 09:50:04 alg Exp $
//

/**
 * Configures Smarty template engine for 'meta_keywords' and 'meta_description'
 * output.
 */
class MetaTags
{
	function set_folder_tags(&$smarty, $folder, $owner)
	{
		if ($folder->id == 1)
		{
			$k = 'best blog directory,best blogs,free blogs';
		} else
		{
			$t = array_values($folder->tags);
			$o = $owner == null ? array() : array_values($owner->tags);
			$t = MetaTags::update_expert_tags(false, $t, $o);
			$k = join(',', $t);
		}
		
		$d = $folder->description;
		
		MetaTags::set_tags($smarty, $k, $d);
	}
	
	function set_item_tags(&$smarty, $item, $owner)
	{
		$t = array_values($item->tags);
		$o = $owner == null ? array() : array_values($owner->tags);
		$t = MetaTags::update_expert_tags(true, $t, $o);
		$k = join(',', $t);
		$d = $item->description;
		MetaTags::set_tags($smarty, $k, $d);
	}
	
	function set_tags(&$smarty, $k, $d)
	{
		$smarty->assign('meta_keywords', $k);
		$smarty->assign('meta_description', $d);
	}
	
	/**
	 * Returns the modified version if the 'expert' tag is found among given.
	 * Depending on the type of the object ($is_item), different set of
	 * tags will be added.
	 */
	function update_expert_tags($is_item, $tags, $owner_tags)
	{
		if (!in_array('expert', $tags) && !in_array('expert', $owner_tags)) return $tags;

		$new_tags = array();
		foreach ($tags as $t)
		{
			$new_tags []= $t;
			 
			if ($t == 'expert') continue;
			
			if ($is_item)
			{
				$new_tags []= "$t blog";
				$new_tags []= "$t expert blog";
			} else
			{
				$new_tags []= "$t blogs";
				$new_tags []= "$t expert";
				$new_tags []= "$t expert blogs";
				$new_tags []= "$t advisor";
			}
		}
		
		return $new_tags;
	}
}
?>