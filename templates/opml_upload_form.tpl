<div class="info_block">
  <h2>{$infoblock.title}</h2>
  <div class="description">{$infoblock.description}</div>
</div>

{if $my_folders_count eq 0}
<h2 class="error">You don't own any folders to upload data to.</h2>
	{if !in_array('edit_others_content', $perm)}
	<p>Please a librarian or administrator to create a working folder for you.</p>
	{/if}
{else}

{literal}
<script type="text/javascript">
function validate() {
	var form = document.forms.opml;
	var msg = '';
	
	if (trim(form.opml.value).length == 0 && trim(form.url.value).length == 0) {
		msg = 'Please either select a file or enter an URL.';
	}
	
	if (msg != '') alert(msg); else form.submit();
}

function onURLChange() {
	document.getElementById("urls").checked = true;
}
function onFileChange() {
	document.getElementById("files").checked = true;
}

function on_import_structure_check() {
	var is = document.getElementById("import_structure");
	var st = document.getElementById("suppress_empty_top");
	st.disabled = is.checked ? false : 'disabled';
}

function onAutoTags() {
	var f = document.forms['folder'];
	f['tags'].disabled = f['autoTags'].checked;
}
</script>
{/literal}

{if isset($error)}<p class="error">{$error}</p>{/if}

<div id="form">
	<form name="opml" onsubmit="validate(); return false;" action="{$root_url}/opml_upload.php" method="post" enctype="multipart/form-data">
		<table class="layout">
			<tr valign="top">
				<td>OPML Resource:</td>
				<td>
					<input type='radio' name='source' value="f" id="files" checked>File:</input><br>
					<input class="field" type="file" name="opml" onchange="onFileChange()"><br>
					<input type='radio' name='source' value="u" id="urls">URL:</input><br>
					<input class="field" type="text" name="url" onchange="onURLChange()">
				</td>
			</tr>
			<tr>
				<td>Upload To:</td>
				<td>
				    {html_options name=folder options=$my_folders selected=$folder_id}
				</td>
			</tr>

			<tr><td colspan="2">&nbsp;</td></tr>
			<tr>
				<td colspan="2">To create a sub-folder for new items enter information below:</td>				
			</tr>
			<tr><td colspan="2">&nbsp;</td></tr>

			<tr>
				<td>Title:</td>
				<td><input class="field" type="text" name="title" maxchars="100"></td>
			</tr>
			<tr valign="top">
				<td>Description:</td>
				<td><textarea class="field" name="description" cols="50" rows="5"></textarea></td>
			</tr>
			<tr>
				<td>Tags:</td>
				<td><input class="field" type="text" name="tags" disabled> <input type="checkbox" name="autoTags" checked onClick="onAutoTags()"/> Auto</td>
			</tr>
			<tr>
				<td valign="top">Options:</td>
				<td>
					<input type="checkbox" id="import_structure" name="import_structure" checked="true"/> Import folder structure<br />
					&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" id="suppress_empty_top" name="suppress_empty_top" /> Don't import empty top level folders
				</td>
			</tr>
			
			<tr><td>&nbsp;</td></tr>
			
			<tr>
				<td>&nbsp;</td>
				<td><input type="button" value="Go Back" onclick="history.back()">&nbsp;<input type="button" value="OK" onclick="javascript:validate();"></td>
			</tr>
		</table>
	</form>
</div>

<script type="text/javascript">
var is = document.getElementById("import_structure");
is.onclick = on_import_structure_check;
on_import_structure_check();
</script>
{/if}
