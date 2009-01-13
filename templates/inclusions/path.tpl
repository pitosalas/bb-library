{*
	Displays the path between one folder (home) and another (target).
	Param: path - the list of Folder objects
*}
{strip}
{if isset($path) && count($path) gt 0}
	<a class="first" href="{url folder=$path[0]}">{$path[0]->title}</a>
	{if count($path) lt 2}
		<span class="endfirst">&nbsp;</span>
	{else}
		<span class="nextfirst">&nbsp;</span>
		{section name=el loop=$path start=1}
			<a href="{if is_a($path[el], 'Item')}{url item=$path[el]}{else}{url folder=$path[el]}{/if}"{if $smarty.section.el.last} class="current"{/if}>{$path[el]->title}</a>
			{if !$smarty.section.el.last}
				<span class="next">&nbsp;</span>
			{elseif isset($path[el]->parents) && count($path[el]->parents) gt 0}
				<span class="also-listed">
					(also listed in&nbsp;
						{foreach name=p item=parent from=$path[el]->parents}
							<a class="simple" href="{url folder=$parent}">{$parent->title}</a>
							{if !$smarty.foreach.p.last}, {/if}
						{/foreach}
					)
				</span>
			{/if}
		{/section}
		<span class="end">&nbsp;</span>
	{/if}
{/if}
{/strip}