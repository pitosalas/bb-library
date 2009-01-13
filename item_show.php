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
// $Id: item_show.php,v 1.3 2007/07/10 08:48:35 alg Exp $
//

require_once 'classes/DataManager.class.php';
require_once 'classes/MetaTags.class.php';
require_once 'smarty_page.php';

if (!isset($iid)) $iid = defGET('iid', 0);

$dm = new DataManager();
$item = $dm->getItemViewInfo($iid);

if ($item)
{
	require_once 'ajax.php';
    $pid = $item->owner_id;
    $owner = $dm->getOwnerSideblock($pid);

    MetaTags::set_item_tags($smarty, $item, $owner);
    
    $smarty->assign('item', $item);
    $smarty->assign('nav', $dm->getItemNavigationSideblock($uid, $iid));
    $smarty->assign('owner', $owner);
    $smarty->assign('path', $dm->getPath($iid, false));
    $smarty->assign('xajax_javascript', $xajax->getJavascript(BASE_URL.'/xajax'));
    
    $smarty->assign('title', $item->title);
    $smarty->assign('content', 'item_view');
    
    $smarty->assign('login_redirect', smarty_url_item($item, $smarty));
} else
{
    $smarty->assign('nav', $dm->getItemNavigationSideblock($uid, 1));
    $smarty->assign('title', 'Item Not Found');
    $smarty->assign('content', 'item_not_found');
}
$dm->close();

$smarty->assign("page", "main");
$smarty->display("layout.tpl");
?>
