{if isset($owner) and not $owner->no_ads}
{include file="components/sidebar_block.tpl" title=$app_props.tla_box_title data=$tla_links}
{/if}