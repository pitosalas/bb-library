{* Param: blocks - array of arrays of items (title, url) *}
{capture name="data"}
<ul>
{if isset($app_props.partner_site_title) && $app_props.partner_site_title &&
    isset($app_props.partner_site_link) && $app_props.partner_site_link}
  <li><a href="{$app_props.partner_site_link}">{$app_props.partner_site_title}</a></li>
{/if}
  {section name=block loop=$blocks}
    {section name=item loop=$blocks[block]}
      <li><a href="{if is_a($blocks[block][item], 'Folder')}{url folder=$blocks[block][item]}{else}{url item=$blocks[block][item]}{/if}">{$blocks[block][item]->title}</a></li>
    {/section}
    {if $smarty.section.block.index lt count($blocks) - 1}<li class="separator">&nbsp;</li>{/if}
  {/section}
  <li class="separator">&nbsp;</li>
  <li><a href="{url page=news}">Latest News</a></li>
  <li class="separator">&nbsp;</li>
  <li><a href="{url page=tags_cloud}">Tags</a></li>
  <li><form id="search" name="search" action="{url page=search}" method="post"><a href="{url page=search}">Search</a><br><input type="text" name="search"></form></li>
</ul>
<div id="bookmarks">{include file="inclusions/bookmarks.tpl"}</div>
{/capture}

{include file="components/sidebar_block.tpl" title="Navigation" data=$smarty.capture.data}
