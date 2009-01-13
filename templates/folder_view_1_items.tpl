{* Param: items      -- array of folder items
          subfolders -- array of sub folders
          max        -- maximum number of items to display *}
{strip}
{if isset($max)}
<table class="layout">
<tr>
  {section name=item loop=$items max=$max}
    <td class="item">{include file="folder_view_1_item.tpl" item=$items[item]}</td>
  {/section}
  {math assign=foldermax equation="max-items" max=$max items=$items|@count}
  {section name=subf loop=$subfolders max=$foldermax}
  	<td class="folder">{include file="folder_view_1_folder.tpl" subfolder=$subfolders[subf]}</td>
  {/section}
</tr>
</table>
{else}
  {section name=item loop=$items}
    {include file="folder_view_1_item.tpl" item=$items[item]}
  {/section}
{/if}
{/strip}