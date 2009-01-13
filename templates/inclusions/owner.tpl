{* Shows person info. Param: person -- person info (image_url, full_name, tags, description). *}
{capture name="data"}
<img id="owner_photo" src="{image person=$person}" width="50" height="76">
<div id="owner_info">
  <h2><a href="{url person=$person}">{$person->fullName}</a></h2>
  <p id="tags">{strip}
	  {foreach name=t item=tag from=$person->tags}
		<a href="{url tag=$tag}">{$tag}</a>{if !$smarty.foreach.t.last}, {/if}
	  {/foreach}
  {/strip}</p>
  {if isset($person->home_page) && $person->home_page != ''}
  	<p id="home_page"><a href="{$person->home_page}">Home Page</a></p>
  {/if}
  <p id="description">{$person->description}</p>
</div>
<div class="clear"></div>
{/capture}

{include file="components/sidebar_block.tpl" title="Librarian" data=$smarty.capture.data}
