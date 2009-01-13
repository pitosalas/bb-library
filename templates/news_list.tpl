<div class="info_block">
  <div class="controls">
    <a href="{url page=news type=rss}">{css_image pic='rss' title='News Feed'}</a>
  {if in_array('manage_news', $perm)}
    <br/><a href="{url page=news type='add_nitem'}">New Post</a>
  {/if}
  </div>
  <h2>Latest News</h2>
  <div class="description">Here are the latest news!</div>
</div>

{if !isset($news_items)}
  There is no news at the moment!
{else}
{if in_array('manage_news', $perm)}
<script type="text/javascript">
{literal}
function onDeleteNewsItem() {
	return confirm("Are you sure to delete this news item?");
}
{/literal}
</script>
{/if}
<div id="news_items">
  {section name=i loop=$news_items}
    {include file="news_list_item.tpl" news_item=$news_items[i]}
  {/section}
</div>
{/if}