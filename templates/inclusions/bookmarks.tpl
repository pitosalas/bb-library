{* Param: bookmarks - the list of folders. *}
{if count($bookmarks) gt 0}
<ul><li class="separator">&nbsp;</li></ul>
<table class="layout">
	{section name=b loop=$bookmarks}{strip}
		<tr valign="top"><td class="icon">
			<span class="folder_icon">&nbsp;</span>
		</td>
		<td>
			<a href="{url folder=$bookmarks[b]}">{$bookmarks[b]->title}</a>&nbsp;
			<a href="#" onclick="xajax_removeBookmark({$bookmarks[b]->id}); return false;">
				<img src="{image pic='pure_delete.gif'}" border="0" title="Delete">
			</a>
		</td></tr>
		</li>
	{/strip}{/section}
</table>
{/if}