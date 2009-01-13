{* Tree view type *}
{literal}
<script type="text/javascript">
function toggleFolder(handle, fid, guid) {
	var sub = document.getElementById('f' + guid);
	var open = handle.className.indexOf("open") != -1;
	var newClass = 'handle';
	var subVisible = false;
	
	if (!open) {
		newClass += ' open';
		subVisible = true;

		if (sub.innerHTML == '') loadFolder(fid, guid, false);
	}
	
	handle.className = newClass;
	sub.style.display = subVisible ? 'block' : 'none';
}

function loadFolder(fid, guid, toplevel) {
{/literal}imageurl = "{image pic='spacer.gif'}";{literal}
	el = document.getElementById('f' + guid);
	el.innerHTML = '<img src="' + imageurl + '" id="spinner"' + (toplevel ? ' class="root"' : '') + '>&nbsp;Loading...';
	xajax_loadFolderTreeItems(fid, guid);
}
</script>
{/literal}

<div class="viewtype2" id="froot"></div>

<script>loadFolder({$folder->id}, 'root', true);</script>
