{capture name=data}{strip}
<ul>
	{section name=i loop=$items}
		{assign var="item" value=$items[i]}
		<li><a href="{$item.xmlUrl}"><img src="{image pic=spacer.gif}" border="0" class="{if $item.is_folder}folder{else}item{/if}"></a><a href="{$item.url}">{$item.title|escape:"javascript"}</a></li>
	{/section}
</ul>
{/strip}{/capture}

document.write('{$smarty.capture.data}');