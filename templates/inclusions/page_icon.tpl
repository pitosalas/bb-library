{* Renders single page number. *}{strip}
{if count($folder->items)+count($folder->subfolders) gt ($page-1)*4}
	<img id="p_{$folder->id}_{$page-1}" 
	{if isset($selected) && $selected}src="{image pic=$page suffix="s.gif"}"{else}src="{image pic="$page.gif"}"{/if}
	border="0" onclick="onOpenPage({$folder->id}, {$page-1}); return false;">
{else}
	<img src="{image pic=$page suffix="d.gif"}" class="disabled" border="0">
{/if}{/strip}