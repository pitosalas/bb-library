{$xajax_javascript}
<div id="item" class="info_block">
  <div class="controls">{strip}
	{if $item->dynamic == 0}
		{css_image_link item=$item real=$app_props.direct_feed_urls}
		{if in_array('edit_content', $perm) && ($user_id == $item->owner_id || in_array('edit_others_content', $perm))}
		  <br/><a href="{url item=$item type='edt_item'}">Edit Item</a>
		  <br/><a href="{url item=$item type='del_item'}" onClick="return onDeleteItem()">Delete Item</a>
		{/if}
	{/if}
  {/strip}</div>
  <a class="image" href="{url item=$item type='site'}"><img class="image" src="{url item=$item type='img'}" width="111" height="86" border="0"></a>
  <h2>{$item->title|default:"Untitled"}</h2>
  <span class="date">{$item->created|date_format:"%A, %B %e, %Y"}</span>
  <div class="tags">{strip}
	  {foreach name=t item=tag from=$item->tags}
		<a href="{url tag=$tag}">{$tag}</a>{if !$smarty.foreach.t.last}, {/if}
	  {/foreach}
  {/strip}&nbsp;</div>
  <div class="meta">
  	<ul>
  		<li>Rank: {if isset($item->technoRank)}{$item->technoRank}{else}Unknown{/if}</li>
  		<li>In-Links: {if isset($item->technoInlinks)}{$item->technoInlinks}{else}Unknown{/if}</li>
  	</ul>
  </div>
  <div class="description">{$item->description}</div>
</div>

{if isset($app_props.feed_preview_mode) && $app_props.feed_preview_mode != 'hidden'}
	{if $item->type_id == 1 or ($item->type_id == 2 and !$item->showPreview)}
		{include file="item_view_feed.tpl"}
	{elseif $item->type_id == 2}
		{include file="item_view_podcast.tpl"}
	{elseif $item->type_id == 4 and $item->showPreview}
		{include file="item_view_website.tpl"}
	{/if}
{/if}