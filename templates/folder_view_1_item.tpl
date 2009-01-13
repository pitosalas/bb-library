{* Param: item -- item to display,
          folder -- folder to jump to when deleted item *}
{config_load file="folders.conf"}
<div class="folder_item">
{strip}
  <a class="image" href="{url item=$item type='site'}">
    {if #images#}<img border="0" src="{url item=$item type='img'}">
    {else}&nbsp;{/if}
  </a>
  <a class="title" href="{url item=$item}" title="{$item->title}">{$item->title|default:"Untitled"|truncate:32:"...":true}</a>
  <div class="controls">
    {css_image_link item=$item real=$app_props.direct_feed_urls}
  	{if $item->dynamic == 0}
	  {if in_array('edit_content', $perm) && ($user_id == $item->owner_id || in_array('edit_others_content', $perm))}
	    &nbsp;<a href="{url item=$item type='edt_item'}"><img src="{image pic='edit.gif'}" border="0" title="Edit"></a>
	    &nbsp;<a href="{url folder=$folder item=$item type='del_item'}" onClick="return onDeleteItem()"><img src="{image pic='delete.gif'}" border="0" title="Delete"></a>
	  {/if}
	{/if}
  </div>
{/strip}
</div>
