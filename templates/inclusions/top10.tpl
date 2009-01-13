{capture name="data"}
<table class="layout">
{section name=i loop=$top10}
  {strip}
  <tr valign="top">
  	<td class="icon">{css_image_link item=$top10[i] small=true}</td>
  	<td><a href="{url item=$top10[i]}">{$top10[i]->title}</a></td>
  </tr>
  {/strip}
{/section}
</table>
{/capture}
{capture name="footer"}
<table class="layout">
	<tr><td class="icon">&nbsp;</td>
	<td>See also <a href="{url page=top100}">TOP 100</a></td>
	</tr>
</table>{/capture}
{capture name="title"}
<div class="controls"><a href="{url page=top10 type='opml'}">{css_image pic='opml' title='Reading List'}</a></div>Top 10
{/capture}
{include file="components/sidebar_block.tpl" title=$smarty.capture.title data=$smarty.capture.data footer=$smarty.capture.footer}
