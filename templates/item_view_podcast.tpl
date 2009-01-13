<script type="text/javascript" src="{$root_url}/flashplayer/ufo.js"></script>
<div id="feed_preview">
	<div class="header">
		<h3>Preview</h3>
		<div class="controls"><a class="itunes" href="{if isset($item->itunesURL) and $item->itunesURL neq "" and $item->useITunesURL}{$item->itunesURL}{else}{itunes_url url=$item->dataURL}{/if}" title="Click to subscribe via iTunes">
		<img class="itunes" src="{image pic='spacer.gif'}" border="0" /></a></div>
	</div>

	<div id="feed_preview_items">
		<p id="player"><a href="http://www.macromedia.com/go/getflashplayer">Get the Flash Player</a> to see the preview.</p>
		<script type="text/javascript">
			var FU = {literal}{{/literal} movie:"{$root_url}/flashplayer/mp3player.swf",
				width:"520",height:"200",majorversion:"7",build:"0",
				flashvars:"file={pencode url=$item->dataURL}&{$fp_options}"
			{literal}}{/literal};
			UFO.create(FU,"player");
		</script>
	</div>
</div>