<div class="info_block">
  <h2>CSV Users Import</h2>
  <div class="description">This feature allows you to import user accounts from a CSV file.</div>
</div>

{literal}
<script type="text/javascript">
function validate() {
	var form = document.forms.csvimport;
	var msg = '';

	if (form.password.value == 'const' && trim(form.passwordConst.value) == '')
	{
		msg = 'Please enter password to assign.';
	} else if (form.tags.value == 'const' && trim(form.tagsConst.value) == '')
	{
		msg = 'Please enter tags to assign.';
	}
	
	if (msg != '') alert(msg); else form.submit();
}
</script>
{/literal}

{if isset($error)}<div class='error'>{$error}</div>{/if}

<p id="step_descr">Step 2: Review the columns and specify the rules of processing</p>

<p class="descr">The file is verified to be in readable format. Now review the sample
of columns taken from it and specify which column corresponds to which record
field. Some fields have additional options to help you import the information even
if you don't have values for all of them. During the next step the application
will import data. All existing user records will NOT be overwritten, so it's safe to
make several imports of the same file.</p>

<div id="col_sample">
	<h2>Columns sample:</h2>
	<table class="layout">
		{foreach from=$stats.samples key=c item=s}
			<tr><td class="label">Column {$c}:</td><td>{$s}</td></tr>
		{/foreach}
	</table>
</div>

<h2>The sources for user record fields:</h2>

<form id="csvimport" onsubmit="validate(); return false;" name="csvimport" method="post" action="{url page=csv_import}" enctype="multipart/form-data">
<input type="hidden" name="step" value="2">
<p>
	<table class="layout">
		<tr><td class="label">Organization:</td><td>{html_options class="field" name="organization_id" options=$organizations}</td><td></td></tr>
		<tr><td class="label">Account Type:</td><td>{html_options class="field" name="type_id" options=$types}</td><td></td></tr>
		<tr><td class="label">Full Name:</td><td>{html_options class="field" name="fullName" options=$fullName}</td><td></td></tr>
		<tr><td class="label">User Name:</td><td>{html_options class="field" name="userName" options=$userName}</td><td></td></tr>
		<tr><td class="label">E-mail:</td><td>{html_options class="field" name="email" options=$email}</td><td></td></tr>
		<tr><td class="label">Password:</td><td>{html_options class="field" name="password" options=$password selected=$password_def}</td><td><input type="text" name="passwordConst"></td></tr>
		<tr><td class="label">Description:</td><td>{html_options class="field" name="description" options=$description selected=$description_def}</td><td></td></tr>
		<tr><td class="label">Tags:</td><td>{html_options class="field" name="tags" options=$tags selected=$tags_def}</td><td><input type="text" name="tagsConst"></td></tr>
	</table>
</p>

<h2>Additional Options:</h2>

<input type="checkbox" name="skiprow">Skip the first row</input>

<p><input type="button" value="Go Back" onclick="history.back()">&nbsp;<input type="button" onClick="validate(); return false;" value="Import"></p>
</form>

{literal}
<script type="text/javascript">
function onChange(list, field) { field.disabled = list.value != 'const'; }

var form = document.forms.csvimport;

// Init fields
onChange(form.password, form.passwordConst);
onChange(form.tags, form.tagsConst);

// Register listeners
form.password.onchange = function() { onChange(form.password, form.passwordConst); }
form.tags.onchange = function() { onChange(form.tags, form.tagsConst); }
</script>
{/literal}