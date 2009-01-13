{capture name="data"}
<table class="layout">
{section name=sub loop=$rec_items->subfolders}
  <tr valign="top"><td class="icon"><span class="folder_icon">&nbsp;</span></td><td><a href="{url folder=$rec_items->subfolders[sub]}">{$rec_items->subfolders[sub]->title}</a></td></tr>
{/section}
{section name=it loop=$rec_items->items}
  <tr valign="top"><td class="icon"><span class="item_icon">&nbsp;</span></td><td><a href="{url item=$rec_items->items[it]}">{$rec_items->items[it]->title}</a></td></tr>
{/section}
</table>
{/capture}

{include file="components/sidebar_block.tpl" title="Recommendations" data=$smarty.capture.data}
