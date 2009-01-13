<div class="info_block">
  <div class="controls">
    <a href="{url page=top100 type='opml'}">{css_image pic='opml' title='Reading List'}</a>
  </div>
  <h2>Top 100</h2>
  <div class="description">This page shows the list of the most accessed items.</div>
</div>

<table class="tblCntIconTitle" border="0" cellspacing="0">
{section name=top loop=$top100}{strip}
	{assign var='i' value=$smarty.section.top.index+1}
    {assign var='i2' value=$smarty.section.top.index%2}
    {assign var='i10' value=$smarty.section.top.index%10}
	<tr valign="top" class="{if $i2 eq 0}altrow{/if} {if $i10 eq 9}row10{/if}">
		<td class="counter">{$i}</td>
		<td class="icon">
		{if $top100[top]->type_id == 3}
		<a href="{url item=$top100[top] type='opml'}">{css_image pic='opml' title='Reading List'}</a>
		{else}
		<a href="{url item=$top100[top] type='rss'}">{css_image pic='rss' title='Data Feed'}</a>
		{/if}
		</td>
		<td><a href="{url item=$top100[top]}">{$top100[top]->title}</a></td>
	</tr>
{/strip}{/section}
</table>