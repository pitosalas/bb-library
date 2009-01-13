{* List view type *}
<script type="text/javascript">
var SELECTED = 1;
var UNSELECTED = 0;
var PAGE_WIDTH = 520;

var pageicons = new Array(4);
pageicons[0] = new Array('{image pic='1.gif'}', '{image pic='1s.gif'}');
pageicons[1] = new Array('{image pic='2.gif'}', '{image pic='2s.gif'}');
pageicons[2] = new Array('{image pic='3.gif'}', '{image pic='3s.gif'}');
pageicons[3] = new Array('{image pic='4.gif'}', '{image pic='4s.gif'}');

var selected = new Array();
var timer = null;

var scrlCurPos = 0;
var scrlTarPos = 0;
var scrlArea = null;

{literal}
function onOpenPage(fid, page) {
	if (scrlArea) return;
	
	var curPage = currentPage(fid);
		
	scrollToPage(fid, page);

	setStatus(fid, curPage, UNSELECTED);
	setStatus(fid, page, SELECTED);
	selected[fid] = page;
}

function setStatus(fid, page, status) {
	var iconURL = pageicons[page][status];
	var img = document.getElementById('p_' + fid + '_' + page);
	
	img.src = iconURL;
}

function currentPage(fid) {
	var curPage = selected[fid];
	return !curPage ? 0 : curPage;
}

function scrollToPage(fid, page) {
	scrlArea = document.getElementById('s_' + fid);

	scrlCurPos = currentPage(fid) * PAGE_WIDTH;
	scrlTarPos = page * PAGE_WIDTH;
	scroll();
}

function scroll() {
	if (!scrlArea) return;

	var dist = scrlTarPos - scrlCurPos;
	var offs = Math.ceil(dist / 4);
	var newPos = scrlCurPos + offs;
	if (offs == 0) newPos = scrlTarPos;
	
	scrlCurPos = newPos;
	scrlArea.style.marginLeft = -newPos + "px";
	
	if (newPos != scrlTarPos) setTimeout('scroll()', 20); else scrlArea = null;
}
{/literal}
</script>

<div class="folder_items">
	{include file="folder_view_1_items.tpl" items=$folder->items}
</div>

{section name=subfolder loop=$folder->subfolders}
  {include file="folder_view_1_subfolder.tpl" folder=$folder->subfolders[subfolder] parent=$folder}
{/section}
