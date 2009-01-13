{* News Box, params: $news_items - the array of news items. *}
{capture name="data"}
<ul class="bulleted" id="news">
  {section name=i loop=$news_items max=$app_props.news_box_items}
  	<li><a href="{url news=$news_items[i]}">{$news_items[i].title}</a><br />({$news_items[i].date|date_format:"%B %e, %Y"})</li>
  {/section}
</ul>
{/capture}
{capture name="title"}
<div class="controls"><a href="{url page=news type=rss}">{css_image pic='rss' title='News Feed'}</a></div><a href="{url page=news}">{$app_props.news_box_title}</a>
{/capture}

{include file="components/sidebar_block.tpl" title=$smarty.capture.title data=$smarty.capture.data}
