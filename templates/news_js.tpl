{capture name=data}{strip}
<ul>
	{section name=i loop=$news_items}
		{assign var="item" value=$news_items[i]}
		<li><a href="{if isset($item.folder_id)}{url folder_id=$item.folder_id}{else}{url news=$item}{/if}">{$item.title|escape:"javascript"} ({$item.date|date_format:"%B %e, %Y"})</a></li>
	{/section}
</ul>
{/strip}{/capture}

document.write('{$smarty.capture.data}');