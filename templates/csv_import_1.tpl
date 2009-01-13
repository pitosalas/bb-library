<div class="info_block">
  <h2>CSV Users Import</h2>
  <div class="description">This feature allows you to import user accounts from a CSV file.</div>
</div>

{literal}
<script type="text/javascript">
function validate() {
	var form = document.forms.csvimport;
	var msg = '';

	if (form.csv.value == '') msg = 'Please choose file to upload.';
	
	if (msg != '') alert(msg); else form.submit();
}
</script>
{/literal}

{if isset($error)}<div class='error'>{$error}</div>{/if}

<p id="step_descr">Step 1: Select a file to load data from</p>

<p class="descr">The file should be valid CSV (comma-separated) file with record-row format.
The fields can go in arbitrary order. During the next step, if the file can be
understood, you will map the columns from the file to the fields of a user
record. Don't worry if you don't have some fields, like user name, password, description
or tags. The application offers various ways to evaluate or generate them.</p>

<form id="csvimport" onsubmit="validate(); return false;" method="post" action="{url page=csv_import}" enctype="multipart/form-data">
<input type="hidden" name="step" value="1">
<p>
	<table class="layout">
		<tr><td class="label">CSV File:</td><td><input name="csv" type="file"></td></tr>
		<tr><td colspan="2">&nbsp;</td></tr>
		<tr><td colspan="2"><input type="button" onclick="validate(); return false;" value="Step 2"></td></tr>
	</table>
</p>
</form>
