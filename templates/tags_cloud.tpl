<div class="info_block">
  <h2>Tags</h2>
  <div class="description">The cloud of tags registered in the application.</div>
</div>

<div id="tagscloud">
	{foreach key=tag item=rating from=$cloud}
		<a class="tag_{$rating}" href="{url tag=$tag}">{$tag}</a>&nbsp; &nbsp;
	{/foreach}
</div>