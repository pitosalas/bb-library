{$xajax_javascript}
<div id="item_form" class="info_block">
  <h2>{$infoblock.title}{if isset($item)}: {$item->title}{/if}</h2>
  <div class="description">{$infoblock.description}</div>
</div>

<script type="text/javascript">
shortcuts = new Array();
validateOwner = {if in_array('edit_others_content', $perm)}false{else}true{/if};
owner_id = {if isset($item)}{$item->owner_id}{else}{$user->id}{/if};

{literal}
function validate(addMore) {
	var form = document.forms.f_item;
	var msg = '';

	var type = form.itemType_id.value;	

	if (trim(form.title.value).length == 0) {
		msg = 'Please enter the title.';
	} else if (shortcuts.length == 0) {
		msg = 'Please select at least one folder to place this item in.';
	} else if (!/^http:\/\/[^\.]+\.[^\.]+/i.test(trim(form.siteURL.value)))
	{
		msg = 'Please enter valid site URL.';
	} else if (type != 4 && !/^http:\/\/[^\.]+\.[^\.]+/i.test(trim(form.dataURL.value)))
	{
		msg = 'Please enter valid data URL.';
	} else if (!/^[0-9]{0,5}$/.test(form.order.value))
	{
		msg = 'Please enter positive number up to 99999 or leave empty.';
	}
	
	if (msg != '') alert(msg); else {
		if (form.addMore) form.addMore.value = addMore ? 1 : 0;
		submit();
	}
}

function submit() {
	var form = document.forms.f_item;
	if (!validateOwner || owner_id == form.owner_id.value ||
		confirm("You are asking to make someone else the owner of this item, " +
			"which means that you will lose the ability to modify it."))
	{
		form.shortcuts.value = shortcuts.join(',');
		form.submit();
	}
}

function initShortcuts(ids, titles) {
	for (var i = 0; i < ids.length; i++) {
		addShortcut(ids[i], titles[i]);
	}
}

function onAddShortcut() {
	var id = getSelectedFolderId();
	if (!isAlreadySelected(id)) {
		addShortcut(id);
	}
}

function addShortcut(id, title) {
	var shortcutsList = document.getElementById("shortcutsList");
	var shortcutBlock = createShortcutBlock(id, title);
	
	shortcutsList.appendChild(shortcutBlock);
	
	// Add this shortcut to the tail
	shortcuts.push(id);
}

function getSelectedFolderId() {
	return document.forms.f_item.shortcutSelector.value;
}

function createShortcutBlock(id, title) {
	div = document.createElement("div");
	div.setAttribute("id", "s-" + id);

{/literal}var delIconURL = "{image pic='pure_delete.gif'}";{literal}	

	delIcon = document.createElement("img");
	delIcon.setAttribute("border", "0");
	delIcon.setAttribute("src", delIconURL);

	delLink = document.createElement("a");
	delLink.setAttribute("href", "#");
	delLink.onclick = function() { onDeleteShortcut(this, id); return false; };
	delLink.appendChild(delIcon);
	
	labelTitle = document.createTextNode(title ? title : getShortcutTitle(id));
	
	div.appendChild(delLink);
	div.appendChild(document.createTextNode(" "));
	div.appendChild(labelTitle);
	
	return div;
}

function getShortcutTitle(id) {
	var title = null;
	var ss = document.getElementById("shortcutSelector");
	for (var el = ss.firstChild; title == null && el != null; el = el.nextSibling) {
		if (el.tagName == 'OPTGROUP') title = getShortcutTitleIn(el, id);
	}
	return title;
}

function getShortcutTitleIn(ssgrp, id) {
	var title = null;
	for (var option = ssgrp.firstChild; title == null && option != null; option = option.nextSibling) {
		if (option.value == id) {
			found = true;
			title = option.getAttribute("label");
		}
	}
	return title;
}

function isAlreadySelected(id) {
	var found = false;
	for (var i = 0; !found && i < shortcuts.length; i++) {
		found = shortcuts[i] == id;
	}
	return found;
}

function onDeleteShortcut(elem, id) {
	var shortcutBlock = elem.parentNode;
	var listShortcuts = shortcutBlock.parentNode;
	
	listShortcuts.removeChild(shortcutBlock);
	
	var index = -1;
	for (var i = 0; index == -1 && i < shortcuts.length; i++) {
		if (shortcuts[i] == id) index = i;
	}
	
	if (index > -1) shortcuts.splice(index, 1);
}

function onDiscover() {
	var address = document.forms.f_item.address.value;
	var status = document.getElementById('f_status');

	if (trim(address) != '') {
		status.innerHTML = 'Discoverying...';
		xajax_discoverBlog(address, 'f_status', 'f_title', 'f_description', 'f_siteURL', 'f_dataURL');
	} else {
		status.innerHTML = 'Enter the address to start discovery';
	}
}

function onKeyPress(e) {
	keynum = (window.event) ? e.keyCode : e.which;
	if (keynum == 13) onDiscover();
}

function onAutoTags() {
	var f = document.forms['f_item'];
	f['tags'].disabled = f['autoTags'].checked;
}
</script>
{/literal}

<div id="form">
	<form name="f_item" onSubmit="javascript:validate(); return false;" action="{$root_url}/item_submit.php" method="post">
		<input type="hidden" name="action" value="{$action}"/>
		{if isset($item)}<input type="hidden" name="iid" value="{$item->id}"/>{/if}
		<input type="hidden" name="fid" value="{$folder->id}"/>
		<input type="hidden" name="shortcuts" value=""/>
		<input type="hidden" name="addMore" value="0"/>
		<table class="layout">
{if isset($discovery_enabled) && $discovery_enabled}
			<tr><td colspan="2"><strong>Discover a Blog</strong></td></tr>
			<tr><td colspan="2">
				<div class="info">
					Discovery helps you find and fill in information about
					the blog you wish to register. Just enter any address
					which is relevant to this blog and our service will
					make its best to discover everything else. Or if you
					know the details, you are free to proceed to Manual
					Entry section.
				</div>
			</td></tr>
			<tr>
				<td>Address:</td>
				<td><input class="field" type="text" name="address" onkeypress="return onKeyPress(event);"> <input type="button" value="Discover" onClick="onDiscover(); return false;" /></td>
			</tr>
			<tr>
				<td></td>
				<td>Status: <span id="f_status" class="status">Idle</span></td>
			</tr>
			<tr><td>&nbsp;</td></tr>
			<tr><td colspan="2"><strong>Basic Information</strong></td></tr>
			<tr><td>&nbsp;</td></tr>
{/if}
			<tr>
				<td>Title:</td>
				<td><input class="field" type="text" id="f_title" name="title" maxchars="100" {if isset($item)}value="{$item->title}"{/if}></td>
			</tr>
			<tr valign="top">
				<td>Description:</td>
				<td><textarea class="field" id="f_description" name="description" cols="50" rows="5">{if isset($item)}{$item->description}{/if}</textarea></td>
			</tr>
			<tr>
			  <td>Type:</td>
			  <td>{html_options name="itemType_id" options=$itemTypes selected=$itemType_id}</td>
			</tr>
			<tr>
				<td>Site URL:</td>
				<td><input class="field" type="text" id="f_siteURL" name="siteURL" {if isset($item)}value="{$item->siteURL}"{/if}></td>
			</tr>
			<tr id="data_url">
				<td>Data URL:</td>
				<td><input class="field" type="text" id="f_dataURL" name="dataURL" {if isset($item)}value="{$item->dataURL}"{/if}></td>
			</tr>
			<tr valign="top">
				<td>Tags:</td>
				<td><input class="field" type="text" name="tags" {if isset($item)}value="{implode array=$item->tags separator=", "}"{/if}>{if $app_props.generate_tags_and_descriptions} <input type="checkbox" name="autoTags" {if isset($item) && $item->autoTags}checked{/if} onClick="onAutoTags()"/> Auto{else}<input type="hidden" name="autoTags" value="{if isset($item) && $item->autoTags}1{else}0{/if}"/>{/if}<br>&nbsp;&nbsp;example: tag1, tag2</td>
			</tr>
			<tr>
				<td>Owner:</td>
				<td>{html_options name="owner_id" options=$authors selected=$owner->id}</td>
			</tr>
			<tr>
				<td></td>
				<td><input type="checkbox" name="show_in_nav_bar" {if isset($item) && $item->show_in_nav_bar}checked{/if}/> Show in the navigation bar</td>
			</tr>
			<tr valign="top">
				<td>List in:</td>
				<td id="shortcutsList">
				</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td>
					<select name="_sc" id="shortcutSelector">
					    {html_options options=$my_folders}
					</select>
					<input type="button" value="Add" onClick="javascript:onAddShortcut()">
				</td>
			</tr>

			<tr><td>&nbsp;</td></tr>
			<tr><td colspan="2"><strong>Display Options</strong></td></tr>
			<tr><td>&nbsp;</td></tr>

			<tr id="show_preview">
				<td colspan="2"><input type="checkbox" name="showPreview" {if isset($item) && $item->showPreview}checked{/if}/> <span id="flash_player">Use Flash Player</span><span id="site_preview">Show site preview</span></td>
			</tr>
			<tr id="itunes_url">
				<td colspan="2"><input type="checkbox" name="useITunesURL" {if isset($item) && $item->useITunesURL}checked{/if}/> iTunes Music Store URL: <input class="field" type="text" name="itunesURL" {if isset($item)}value="{$item->itunesURL}"{/if}></td>
			</tr>
			<tr id="play_buttons">
				<td colspan="2"><input type="checkbox" name="usePlayButtons" {if isset($item) && $item->usePlayButtons}checked{/if}/> Add "Play Mp3" button for enclosures and .mp3 links</td>
			</tr>
			<tr>
				<td>Position:</td>
				<td><input class="short-field" type="text" id="f_order" name="order" {if isset($item)}value="{$item->order}"{/if}></td>
			</tr>
			
			<tr><td>&nbsp;</td></tr>
			
			<tr>
				<td>&nbsp;</td>
				<td><input type="button" value="Go Back" onclick="history.back()">&nbsp;<input type="button" value="OK" onclick="javascript:validate(false);">{if $action == 'add'}&nbsp;<input type="button" value="OK, and Add More" onclick="javascript:validate(true);">{/if}</td>
			</tr>
		</table>
	</form>
</div>

<script type="text/javascript">
	initShortcuts(new Array({implode array=$shortcut_ids separator=", " quotes=true}), new Array({implode array=$shortcut_nam separator=", " quotes=true}));

{literal}
	function en(b) { return b ? "" : "none"; }
	function sh(id, b) { return document.getElementById(id).style.display = en(b); }
	function updateFieldsVisibility(s) {
		var type = s.value;

		// type: feed=1, podcast, outline, website
		var feed = s.value == 1;
		var podcast = s.value == 2;
		var outline = s.value == 3;
		var website = s.value == 4;

		sh("data_url", !website);
		sh("itunes_url", podcast);
		sh("play_buttons", feed || podcast);
		sh("show_preview", podcast || website);
		sh("flash_player", podcast);
		sh("site_preview", website);
	}

	var s = document.forms.f_item.itemType_id;
	s.onchange = function() { updateFieldsVisibility(this); }
	updateFieldsVisibility(s);
{/literal}
{if $app_props.generate_tags_and_descriptions}
	onAutoTags();
{/if}
</script>