<div id="folder_add" class="info_block">
  <h2>{$infoblock.title}{if isset($folder)}: {$folder->title}{/if}</h2>
  <div class="description">{$infoblock.description}</div>
</div>

<script type="text/javascript">
shortcuts = new Array();
validateOwner = {if in_array('edit_others_content', $perm)}false{else}true{/if};
validateOPML = {if isset($folder) && $folder->opml_url == '' && $folder->hasChildren}true{else}false{/if};
owner_id = {if isset($folder)}{$folder->owner_id}{else}{$user->id}{/if};
is_root = {if isset($folder) && $folder->id == 1}true{else}false{/if};

{literal}
function validate(addMore) {
	var form = document.forms.folder;
	var msg = '';
	
	if (trim(form.title.value).length == 0) {
		msg = 'Please enter the title.';
	} else if (!is_root && shortcuts.length == 0) {
		msg = 'Please select at least one folder to place this new folder in.';
	} else if (trim(form.opmlURL.value).length != 0 && !isLink(form.opmlURL.value))
	{
		msg = 'Please enter valid OPML Link.';
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
	var form = document.forms.folder;
	
	var opmlURL = trim(form.opmlURL.value);

	if ((!validateOPML || opmlURL == '' ||
			confirm("The folder becomes dynamic, which means " +
				"that all manually added feeds and folders will be removed. Continue?")) &&
		(!validateOwner || owner_id == form.owner_id.value ||
			confirm("You are asking to make someone else the owner of this folder, " +
				"which means that you will lose the ability to modify it.")))
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
	return document.forms.folder.shortcutSelector.value;
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

function onAutoTags() {
	var f = document.forms['folder'];
	f['tags'].disabled = f['autoTags'].checked;
}
{/literal}
</script>

<div id="form">
	<form name="folder" onsubmit="validate(); return false;" action="{$root_url}/folder_submit.php" method="post">
		<input type="hidden" name="action" value="{$action}"/>
		{if isset($folder)}<input type="hidden" name="fid" value="{$folder->id}"/>{/if}
		<input type="hidden" name="shortcuts" value=""/>
		<input type="hidden" name="addMore" value="0"/>
		<table class="layout">
			<tr>
				<td>Title:</td>
				<td><input class="field" type="text" name="title" maxchars="100" {if isset($folder)}value="{$folder->title}"{/if}></td>
			</tr>
			<tr valign="top">
				<td>Description:</td>
				<td><textarea class="field" name="description" cols="50" rows="5">{if isset($folder)}{$folder->description}{/if}</textarea></td>
			</tr>
			<tr valign="top">
				<td>Tags:</td>
				<td><input class="field" type="text" name="tags" {if isset($folder)}value="{implode array=$folder->tags separator=", "}"{/if}>{if $app_props.generate_tags_and_descriptions} <input type="checkbox" name="autoTags" {if isset($folder) && $folder->autoTags}checked{/if} onClick="onAutoTags()"/> Auto{else}<input type="hidden" name="autoTags" value="{if isset($folder) && $folder->autoTags}1{else}0{/if}"/>{/if}<br>&nbsp;&nbsp;example: tag1, tag2</td>
			</tr>
			<tr>
				<td>Owner:</td>
				<td>{html_options name="owner_id" options=$authors selected=$owner->id}</td>
			</tr>
			<tr>
			    <td>View As:</td>
			    <td>
					<select name="viewType_id">
					    {html_options options=$viewTypes selected=$viewType_id}
					</select>
			    </td>
			</tr>
			{if !isset($folder) || $folder->id != 1}
				<tr>
					<td></td>
					<td><input type="checkbox" name="show_in_nav_bar" {if isset($folder) && $folder->show_in_nav_bar}checked{/if}/> Show in the navigation bar</td>
				</tr>
				<tr valign="top">
					<td>List in:</td>
					<td>
						<div id="shortcutsList"></div>
					</td>
				</tr>
				{if isset($my_folders) && count($my_folders) gt 0}
					<tr>
						<td>&nbsp;</td>
						<td>
							<select name="_sc" id="shortcutSelector">
							    {html_options options=$my_folders}
							</select>
							<input type="button" value="Add" onClick="javascript:onAddShortcut()">
						</td>
					</tr>
				{/if}
			{/if}
			<tr>
				<td>Order:</td>
				<td><input class="field" type="text" id="f_order" name="order" {if isset($folder)}value="{$folder->order}"{/if}></td>
			</tr>
			<tr><td>&nbsp;</td></tr>
			<tr><td colspan="2">Dynamic OPML Folder Info:</td></tr>
			<tr>
				<td>OPML Link:</td>
				<td><input class="field" type="text" name="opmlURL" {if isset($folder)}value="{$folder->opml_url}"{/if}></td>
			</tr>
			<tr>
				<td>User name:</td>
				<td><input class="field" type="text" name="opmlUser" {if isset($folder)}value="{$folder->opml_user}"{/if}> (optional)</td>
			</tr>
			<tr>
				<td>Password:</td>
				<td><input class="field" type="password" name="opmlPassword" {if isset($folder)}value="{$folder->opml_password}"{/if}> (optional)</td>
			</tr>
			<tr>
				<td>Last updated:</td>
				<td>{if isset($folder) and $folder->opml_last_updated gt 0}{$folder->opml_last_updated|date_format:"%A, %B %e, %Y"}{else}Never{/if}</td>
			</tr>
			<tr><td>&nbsp;</td></tr>
			<tr>
				<td>&nbsp;</td>
				<td><input type="button" value="Go Back" onclick="history.back()">&nbsp;<input type="button" value="OK" onclick="javascript:validate(false);">{if $action eq 'add'}&nbsp;<input type="button" value="OK, and Add More" onclick="javascript:validate(true);">{/if}</td>
			</tr>
		</table>
	</form>
</div>

{if !isset($folder) || $folder->id != 1}
<script type="text/javascript">
initShortcuts(new Array({implode array=$shortcut_ids separator=", " quotes=true}), new Array({implode array=$shortcut_nam separator=", " quotes=true}));
</script>
{/if}
<script type="text/javascript">
{if $app_props.generate_tags_and_descriptions}onAutoTags();{/if}
</script>