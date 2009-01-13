{if isset($feed)}
<div id="feeditems">
{foreach name=item item=item from=$feed.items}
	<div class="news_item">
	  <span class="handle"><a href="#" class="{if $collapsed}collapsed{else}expanded{/if}" onClick="on_handle_click(this); return false;"><img src="{image pic='spacer.gif'}" border="0" /></a></span>
	  <h2>{$item.title}</h2>
	  <div class="body" {if !$collapsed}style="display: block;"{/if}>
		  <div class="text">{$item.text}</div>
{if $usePlayButtons}
	<div class="enclosues">
	{foreach name=enc item=enc from=$item.enclosures}
		{if stristr($enc->type, 'audio/')}
		<a href="{$enc->link}"><img class="play-button" src="{image pic='spacer.gif'}" border="0" title="{$enc->basename}"/></a> 
		{/if}
	{/foreach}
	</div>
{/if}
		  <div class="posted_by">Posted {if isset($item.author) && $item.author != ''} by {$item.author}{/if} on {$item.date}</div>
	  </div>
	</div>
{/foreach}
</div>
{else}
<div class="error">Feed wasn't found or format is invalid.</div>
{/if}