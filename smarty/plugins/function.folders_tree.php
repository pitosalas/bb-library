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
// $Id: function.folders_tree.php,v 1.3 2007/08/23 17:04:11 alg Exp $
//

require_once('function.image.php');

/**
 * Displays nice tree of folders for the folder view.
 * @params folder            - folder to unroll
 *         perm_edit_content - TRUE to allow editing content
 *         userId            - current user ID (to decide whether to show edit controls or no).
 *         smarty            - template engine object
 *         outstripes        - TRUE to output alternating rows
 *         perm_edit_others_content - TRUE to allow editing everyone's content
 *         direct_feed_urls  - TRUE to show direct URLs of feeds (not our masquerade)
 */
function _folders_tree($folder, $perm_edit_content, $userId, &$smarty, $outstripes, $perm_edit_others_content, $direct_feed_urls = false)
{
    $txt = "";
    $cnt = 0;
    list($mks, $sec) = explode(' ', microtime());
    $guid = (int)(($sec / 1000 + $mks) * 1000);
    
    if (isset($folder->subfolders))
    {
        foreach ($folder->subfolders as $subfolder)
        {
            if (isset($subfolder->hasChildren))
            {
                $hasSubelements = $subfolder->hasChildren;
            } else
            {                
                $hasSubelements = (isset($subfolder->subfolders) && count($subfolder->subfolders) > 0) ||
                    (isset($subfolder->items) && count($subfolder->items) > 0);
            }
            
            if ($hasSubelements) $guid++;
                  
            $txt .= '<div class="subnode subfolder' . ($cnt % 2 == 1 ? ' alt' : '') . '">';
            $txt .= '<div class="header">';
            $txt .= '<div class="controls">';

			if ($subfolder->dynamic == 0)
			{
	            if ($perm_edit_content && ($userId == $subfolder->owner_id || $perm_edit_others_content))
	            {
	                $txt .= '<a href="' . smarty_function_url(array('folder' => $subfolder, 'type' => 'edt_folder'), $smarty) . '"><img src="' . _image('edit.gif') . '" border="0" title="Edit"></a>&nbsp;';
	                $txt .= '<a href="' . smarty_function_url(array('folder' => $subfolder, 'parent' => $folder, 'type' => 'del_folder'), $smarty) . '" onClick="return onDeleteFolderLink()"><img src="' . _image('delete.gif') . '" border="0" title="Delete"></a>&nbsp;';
	            }  
			}
			
			if ($subfolder->dynamic == 0 || $subfolder->opml_url != '')
			{
	            $txt .= '<a href="' . smarty_function_url(array('folder' => $subfolder, 'type' => 'opml'), $smarty) . '">' . _css_image('opml', 'Reading List') . '</a>';
			}
			
            $txt .= '</div>';
            if ($hasSubelements)
            {
                $txt .= '<img class="handle" src="' . _image('spacer.gif') . '" width="9" height="9" onclick="javascript:toggleFolder(this,'.$subfolder->id.','.$guid.')">';
            } else
            {
                $txt .= '<img class="spacer" src="' . _image('spacer.gif') . '" width="9" height="9">';
            }
     
            $txt .= '<a href="' . smarty_function_url(array('folder' => $subfolder), $smarty) . '"><img class="icon" src="' . _image('folder.gif') . '" border="0"><h2>' . $subfolder->title . '</h2></a>';
            $txt .= '<div class="description">' . $subfolder->description . '</div>';
            $txt .= '</div>';
            
            if ($hasSubelements)
            {
                $txt .= '<div class="subelements" id="f'.$guid.'"></div>';
            }
          
            $txt .= '</div>';
            
            $cnt++;
        }
    }
    
    if (isset($folder->items))
    {
        foreach ($folder->items as $item)
        {
            $txt .= '<div class="subnode item' . ($cnt % 2 == 1 ? ' alt' : '') . '">';
            $txt .= '<div class="controls">';
      
            if ($perm_edit_content && $item->dynamic == 0 && ($userId == $item->owner_id || $perm_edit_others_content))
            {
                $txt .= '<a href="' . smarty_function_url(array('item' => $item, 'type' => 'edt_item'), $smarty) . '"><img src="' . _image('edit.gif') . '" border="0" title="Edit"></a>&nbsp;';
                $txt .= '<a href="' . smarty_function_url(array('folder' => $folder, 'item' => $item, 'type' => 'del_item'), $smarty) . '" onClick="return onDeleteItem()"><img src="' . _image('delete.gif') . '" border="0" title="Delete"></a>&nbsp;';
            }  
    
            $txt .= '<img src="' . _image('spacer.gif') . '" width="15" height="1">';
            $txt .= '</div>';
            $txt .= '<img class="spacer" src="' . _image('spacer.gif') . '" width="9" height="9">';
     
            $txt .= smarty_function_css_image_link(array('item' => $item, 'real' => $direct_feed_urls), $smarty);
            $txt .= '<a href="' . smarty_function_url(array('item' => $item), $smarty) . '"><h2>' . ($item->title == '' ? 'Untitled' : $item->title) . '</h2></a>';
            $txt .= '<div class="description">' . $item->description . '</div>';
    
            $txt .= '</div>';
            
            $cnt++;
        }
    }
    return $txt;
}?>
