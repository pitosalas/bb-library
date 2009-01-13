{* Displays three-column (cnt, rss/opml icon, title) table with alternating rows. 
   Param: results - the list of elements.
          start   - index to start from.
*}
{if !isset($start)}{assign var='start' value='0'}{/if}
<table class="tblCntIconTitle" border="0" cellspacing="0">
{section name=res loop=$results}{strip}
	{assign var='in' value=$smarty.section.res.index+$start}
	{assign var='i' value=$in+1}
    {assign var='i2' value=$in%2}
    {assign var='i10' value=$in%10}
	<tr valign="top" class="{if $i2 eq 0}altrow{/if} {if $i10 eq 9}row10{/if}">
		<td class="counter">{$i}</td>
		{if is_a($results[res], 'Folder')}
            <td class="icon">{if $results[res]->dynamic == 0 or $results[res]->opml_url <> ''}{css_image_link item=$results[res]}{/if}</td>
            <td><a href="{url folder=$results[res]}">{$results[res]->title}</a>{if isset($results[res]->item_count)} <span class="item_count">({$results[res]->folder_count} folders, {$results[res]->item_count} items)</span>{/if}</td>
		{elseif is_a($results[res], 'Item')}
            <td class="icon">{css_image_link item=$results[res] real=$app_props.direct_feed_urls}</td>
            <td><a href="{url item=$results[res]}">{$results[res]->title}</a></td>
		{else}
            <td class="icon">{css_image pic='person' title='Person'}</td>
            <td><a href="{url person=$results[res]}">{$results[res]->fullName} ({$results[res]->userName})</a></td>
        {/if}
	</tr>
{/strip}{/section}
</table>
