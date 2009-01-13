{assign var=tid value=$person->type_id}
{assign var=oid value=$person->organization_id}
<div class="info_block">
  <img id="owner_photo" src="{image person=$person}" width="50" height="76">
  <h2>{$person->fullName}</h2>
  <p>{$types.$tid} {if isset($person->organization_id)}at {$organizations.$oid}{/if}</p>
  <p id="tags">Tags: {strip}
   {if count($person->tags) gt 0}
   {foreach name=t item=tag from=$person->tags}
    <a href="{url tag=$tag}">{$tag}</a>{if !$smarty.foreach.t.last}, {/if}
   {/foreach}
   {else}
    Not Set
   {/if}
  {/strip}</p>
  
 {if isset($person->home_page) && $person->home_page != ''}
  <p id="home_page" class="clear"><a href="{$person->home_page}">Home Page</a></p>
 {/if}

  <p id="description" class="clear">{$person->description}</p>
</div>

{if isset($person->folders) && count($person->folders) gt 0}
<div class="clear" id="user_inventory">
  <h2>Folders</h2>
  
  {include file="components/search_results_table.tpl" results=$person->folders}
</div>
{/if}
