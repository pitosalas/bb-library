{* Param: folder -- full folder structure,
          parent -- parent folder *}
<div class="folder_subfolder">
  <table class="layout header">
    <tr>
      <td class="folder_title"><a href="{url folder=$folder}">{$folder->title|upper}</a></td>
      <td class="pages">&nbsp;
      {strip}
        {include file="inclusions/page_icon.tpl" page=1 selected=true}
        {include file="inclusions/page_icon.tpl" page=2}
        {include file="inclusions/page_icon.tpl" page=3}
        {include file="inclusions/page_icon.tpl" page=4}
      {/strip}
      </td>
      <td class="see_all"><a href="{url folder=$folder}">See All</a></td>
      <td class="controls">{strip}
      	{if $folder->dynamic == 0}
	      	{if in_array('edit_content', $perm) && ($user_id == $folder->owner_id || in_array('edit_others_content', $perm))}
		      <a href="{url folder=$folder type='edt_folder'}"><img src="{image pic='edit.gif'}" border="0" title="Edit"></a>&nbsp;
		      <a href="{url folder=$folder type='del_folder' parent=$parent}" onClick="return onDeleteFolderLink()"><img src="{image pic='delete.gif'}" border="0" title="Delete"></a>&nbsp;
	        {/if}  
	    {/if}
      	{if $folder->dynamic == 0 or $folder->opml_url != ''}
	      	<a href="{url folder=$folder type='opml'}">{css_image pic='opml' title='Reading List'}</a>
	    {/if}
      {/strip}</td>
    </tr>
  </table>
  
  {if isset($folder->items)}
    <div class="folder_items">
      <div id="s_{$folder->id}" class="scroll">
      	{strip}{include file="folder_view_1_items.tpl" items=$folder->items subfolders=$folder->subfolders max=16}{/strip}
      </div>
    </div>
  {/if}
</div>
