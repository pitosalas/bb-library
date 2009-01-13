{$xajax_javascript}
<div class="info_block">
  <h2>Tags</h2>
  <div class="description">This page allows you to manage registered tags.</div>
</div>

<script type="text/javascript">
{literal}
function onMerge() {
	if (confirm('Are you sure to merge selected tags?')) {
		document.forms.tags.action.value = 'merge';
		document.forms.tags.submit();
	}
}

function onDelete() {
	if (confirm('Are you sure to delete selected tags?')) {
		document.forms.tags.action.value = 'delete';
		document.forms.tags.submit();
	}
}

var curEditId = -1;
var curEditName = '';

function onEdit(id) {
	if (curEditId != -1) onEditCancel();

	var el = document.getElementById('t' + id);
	var name = el.firstChild.innerHTML;
	curEditId = id;
	curEditName = name;
	
	setEditBox(id, name);
}

function setLink(id, name) {
	var ael = document.createElement('a');
	ael['href'] = '#';
	ael.onclick = function() { onEdit(id); return false; };
	ael.appendChild(document.createTextNode(name));
	
	var el = document.getElementById('t' + id);
	el.replaceChild(ael, el.firstChild);
}

function setEditBox(id, name) {
	var editBox = document.createElement('input');
	editBox['type'] = 'text';
	editBox['id'] = 'nameInput';
	editBox.onkeydown = function(event) { onKeyPress(event ? event : window.event); }
	editBox.onblur = function() { onEditCancel(); return false; };
	editBox.value = name;
	
	var el = document.getElementById('t' + id);
	el.replaceChild(editBox, el.firstChild);
	
	editBox.focus();
}

function onKeyPress(event) {
	if (event.keyCode == 13) onEditAccept();
	else if (event.keyCode == 27) onEditCancel();
}

function onEditAccept() {
	name = document.getElementById('nameInput').value;
	if (trim(name) == '')
	{
		alert("The name can't be empty.");
		return false;
	}

	curEditName = name;
	xajax_renameTag(curEditId, curEditName);
	
	// Clear the input box
	onEditCancel();
}

function onEditCancel() {
	setLink(curEditId, curEditName);
	
	curEditId = -1;
	curEditName = null;
}
{/literal}
</script>

<form id="tags" name="tags" method='post' action='{url page=tags}'>
  <input type="hidden" name="action" value="">
  <table border="0" cellspacing="0" cellpadding="2">
    <thead>
      <tr>
        <td>&nbsp;</td>
        <td class='name'>Tag</td>
        <td class='folders'>Folders</td>
        <td class='items'>Items</td>
        <td class='users'>Users</td>
      </tr>
    </thead>

    {foreach name=tags key=id item=tag from=$tags}
    {assign var='i' value=$smarty.foreach.tags.iteration%2}
    <tr {if $i eq 0}class='altrow'{/if}>
      <td><input type="checkbox" name="tag[]" value="{$tag.id}"{if $tag.folders eq 0 and $tag.items eq 0 and $tag.users eq 0} class="unused"{/if}></td>
      <td id="t{$tag.id}"><a href="#" onClick="onEdit({$tag.id}); return false;">{$tag.name|escape}</a></td>
      <td class='folders'>{$tag.folders}</td>
      <td class='items'>{$tag.items}</td>
      <td class='users'>{$tag.users}</td>
    </tr>
    {/foreach}
  
    <tr><td colspan="5">&nbsp;</td></tr>
    <tr><td colspan="5">
      <input type="button" onClick="selectAll('tags', true); return false;" value="All">&nbsp;
      <input type="button" onClick="selectAll('tags', true, 'unused'); return false;" value="Unused">&nbsp;
      <input type="button" onClick="selectAll('tags', false); return false;" value="None">&nbsp;&nbsp;
      <input type="button" onClick="onDelete(); return false;" value="Delete"/>&nbsp;
      <input type="button" onClick="onMerge(); return false;" value="Merge"/>
    </td></tr>
  </table>
</form>
