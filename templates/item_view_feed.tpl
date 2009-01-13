<div id="feed_preview">
	<div class="header">
		<h3>Preview</h3>
		<div class="controls">
			<a href="#" onclick="on_refresh(); return false;"><span></span>Refresh</a> /
			<a href="#" onclick="on_collapse_all(); return false;"><span></span>Collapse</a> /
			<a href="#" onclick="on_expand_all(); return false;"><span></span>Expand</a>
			{if $item->type_id == 2}
			<a class="itunes" href="{if isset($item->itunesURL) and $item->itunesURL neq "" and $item->useITunesURL}{$item->itunesURL}{else}{itunes_url url=$item->dataURL}{/if}" title="Click to subscribe via iTunes">
				<img class="itunes" src="{image pic='spacer.gif'}" border="0" />
			</a>
			{/if}
		</div>
	</div>
	<div id="feed_preview_items"></div>
</div>

<script>
var initially_collapsed = {if $app_props.feed_preview_mode == 'collapsed'}1{else}0{/if};
{literal}
var feed_preview_id = 'feed_preview_items';

function loadFeedPreview(reload) {
	// Set 'loading...'
	{/literal}imageurl = "{image pic='spacer.gif'}";{literal}
	el = document.getElementById(feed_preview_id);
	el.innerHTML = '<img src="' + imageurl + '" id="spinner" class="root">&nbsp;Loading...';

	// Start loading
	xajax_getFeedPreview({/literal}{$item->id}{literal}, feed_preview_id, reload, initially_collapsed);
}

function on_handle_click(a) {
	var collapsed = a.className == 'collapsed';
	collapse(a, !collapsed);
}

function collapse(a, collapse) {
	a.className = collapse ? 'collapsed' : 'expanded';
	var body = find_sibling(a.parentNode, 'body');
	body.style.display = collapse ? 'none' : 'block';
}

function find_sibling(el, clazz) {
	var found = false;
	var el = el.nextSibling;
	while (!found && el != null) {
		found = el.className == clazz;
		if (!found) el = el.nextSibling;
	}

	return el;
}

function on_refresh () { loadFeedPreview(1); }
function on_collapse_all() { on_collapse_expand(true); }
function on_expand_all() { on_collapse_expand(false); }

function on_collapse_expand(col) {
	var fpb = document.getElementById(feed_preview_id);
	var handles = getElementsByClass(fpb, 'handle', 'span');
	var hLen = handles.length;
	for (i = 0; i < hLen; i++) {
		var h = handles[i];
		var a = h.getElementsByTagName('a').item(0);
		collapse(a, col);
	}
}

loadFeedPreview(0);
{/literal}
</script>