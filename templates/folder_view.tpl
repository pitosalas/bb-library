{$xajax_javascript}
<script type="text/javascript">
{literal}
function bookmarkThis() { xajax_addBookmark({/literal}{$folder->id}{literal}); }
function showAnnouncement() {
	document.getElementById('pa_title').value = '{/literal}{$folder->title|escape:javascript}{literal}';
	document.getElementById('pa_text').value = '{/literal}{$folder->title|escape:javascript} was updated!{literal}';
	document.getElementById('pa_form').style.display = 'block';
}
function postAnnouncement() {
{/literal}{if isset($user_id)}{literal}
	var title = document.getElementById('pa_title').value;
	var text = document.getElementById('pa_text').value;
	if (trim(title) == '' || trim(text) == '') {
		alert('Title and Text must be specified.');
	} else {
		xajax_postAnnouncement({/literal}{$folder->id}, {$user_id}{literal}, title, text);
		document.getElementById('pa_form').style.display = 'none';
	}
{/literal}{/if}{literal}
}
{/literal}
</script>

<div id="folder" class="info_block">
  <div class="controls">{strip}
    {if $folder->dynamic == 0 or $folder->opml_url != ''}
	    <a href="{url folder=$folder type='opml'}">{css_image pic='opml' title='Reading List'}</a>
  	{/if}
    {if $is_logged_in}
      <br/><a href="#" onclick="bookmarkThis(); return false;">Add Bookmark</a>
    {/if}
    {if in_array('edit_content', $perm) && $folder->dynamic == 0 && ($user_id == $folder->owner_id || in_array('edit_others_content', $perm))}
      {if $folder->opml_url == ''}
	      <br/><a href="{url folder=$folder type='add_folder'}">Add Folder</a>
	      <br/><a href="{url folder=$folder type='add_item'}">Add Item</a>
      {/if}
      <br/><a href="{url folder=$folder type='edt_folder'}">Edit Folder</a>
      {if $folder->id != 1}<br/><a href="{url folder=$folder type='del_folder'}" onClick="return onDeleteFolder()">Delete Folder</a>{/if}
    {/if}

	  {if in_array('edit_content', $perm) && $folder->dynamic == 0 && ($user_id == $folder->owner_id || in_array('edit_others_content', $perm))}
  		<br/><a href="#" onclick="showAnnouncement();">Post Announcement</a>
  	{/if}
  {/strip}</div>
  <h2>{$folder->title}</h2>
  <span class="date">{$folder->created|date_format:"%A, %B %e, %Y"}</span>
  <div class="tags">{strip}
	  {foreach name=t item=tag from=$folder->tags}
		<a href="{url tag=$tag}">{$tag}</a>{if !$smarty.foreach.t.last}, {/if}
	  {/foreach}
  {/strip}&nbsp;</div>
  <div class="description">{$folder->description}</div>

  {if $folder->opml_url != ''}
  	<div class="opml_folder_info">
  		<div id="opml_last_updated">Last Updated: {if $folder->opml_last_updated == 0}Never{else}{$folder->opml_last_updated|date_format:"%A, %B %e, %Y"}{/if}</div>
		{if in_array('edit_content', $perm) && $folder->dynamic == 0 && ($user_id == $folder->owner_id || in_array('edit_others_content', $perm))}
	  		<div id="opml_update_now"><a href="{url folder=$folder type=opml_update}">Update Now</a></div>
	  	{/if}
  	</div>
  {/if}
  
  {if in_array('edit_content', $perm) && $folder->dynamic == 0 && ($user_id == $folder->owner_id || in_array('edit_others_content', $perm))}
    <div id="post_announcement">
  		<div id="pa_form" style="display: none;">
  			<table>
  				<tr>
  					<td>
			  			<label for="pa_title">Title:</label><br />
			  			<input id="pa_title" class="field" type="text" />
  					</td>
  				</tr>
  				<tr>
  					<td>
			  			<label for="pa_text">Text:</label><br />
			  			<textarea id="pa_text" class="field"></textarea>
  					</td>
  				</tr>
  				<tr>
  					<td>
			  			<button onclick="postAnnouncement()">Save and Publish</button>
  					</td>
  				</tr>
  			</table>
	  	</div>
	  </div>
	{/if}
</div>

{include file="folder_view_$viewType.tpl"}