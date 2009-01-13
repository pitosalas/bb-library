{* Folder preview item in the subfolder box.
 Param: subfolder -- folder to display,
        folder -- folder to jump to when deleted item *}
{config_load file="folders.conf"}
<div class="folder_item">
{strip}
  <a class="image" href="{url folder=$subfolder}">
    {strip}
      {if #images#}<img border="0" src="{image pic=spacer.gif}" class="folder">
      {else}&nbsp;{/if}
    {/strip}
  </a>
  <a class="title" href="{url folder=$subfolder}" title="{$subfolder->title}">{$subfolder->title|default:"Untitled"|truncate:32:"...":true}</a>
  <div class="controls">
	{if $subfolder->dynamic == 0 or $subfolder->opml_url != ''}
	  <a class="opml" href="{url folder=$subfolder type='opml'}">{css_image pic='opml' title='Reading List'}</a>
	{/if}
    {if in_array('edit_content', $perm) && $subfolder->dynamic == 0 && ($user_id == $subfolder->owner_id || in_array('edit_others_content', $perm))}
      &nbsp;<a href="{url folder=$subfolder type='edt_folder'}"><img src="{image pic='edit.gif'}" border="0" title="Edit"></a>
      &nbsp;<a href="{url folder=$subfolder parent=$folder type='del_folder'}" onClick="return onDeleteFolderLink()"><img src="{image pic='delete.gif'}" border="0" title="Delete"></a>
    {/if}
  </div>
{/strip}
</div>
