<div class="info_block">
  <h2>Search</h2>
  <div class="description">The results of your search and options.</div>
</div>

{if isset($results)}
	{include file="components/search_results_table.tpl"}
{/if}

<form class="form" id="searchAdv" name="search" method="post" action="{url page=search}">
	<input type="hidden" name="advanced" value="1">
	<div class="section">
		<h2>Search</h2>
		<ul>
			<li><input type="text" name="search" value="{$search}"></li>
		</ul>
	</div>
	<div class="section">
		<h2>Look for</h2>
		<ul>
			<li><input type="checkbox" name="typeFeeds" value="1" {if $typeFeeds}checked{/if}>Feeds</input></li>
			<li><input type="checkbox" name="typeFolders" value="1" {if $typeFolders}checked{/if}>Folders</input></li>
{if in_array('manage_users', $perm)}
			<li><input type="checkbox" name="typePeople" value="1" {if $typePeople}checked{/if}>People</input></li>
{/if}
		</ul>
	</div>
	<div class="section">
		<h2>Look at</h2>
		<ul>
			<li><input type="checkbox" name="zoneTitle" value="1" {if $zoneTitle}checked{/if}>Title</input></li>
			<li><input type="checkbox" name="zoneDescription" value="1" {if $zoneDescription}checked{/if}>Description</input></li>
			<li><input type="checkbox" name="zoneTags" value="1" {if $zoneTags}checked{/if}>Tags</input></li>
			<li><input type="checkbox" name="zoneSiteURL" value="1" {if $zoneSiteURL}checked{/if}>Site URL</input></li>
			<li><input type="checkbox" name="zoneDataURL" value="1" {if $zoneDataURL}checked{/if}>Data URL</input></li>
		</ul>
	</div>
	<div class="section">
		<input type="submit" value="Search">
	</div>
</form>