<div class="info_block">
  <h2>Tag: {$tag}</h2>
  <div class="description">The list of objects tagged as {$tag}.</div>
</div>

<div id="taggedObjects">
	{math assign=itemsStart equation="cnt" cnt=$folders|@count}
	{math assign=peopleStart equation="cnt+itemsStart" cnt=$items|@count itemsStart=$itemsStart}
	{include file="components/search_results_table.tpl" results=$folders}
	{include file="components/search_results_table.tpl" results=$items start=$itemsStart}
	{include file="components/search_results_table.tpl" results=$people start=$peopleStart}	
</div>