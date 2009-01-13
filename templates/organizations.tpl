<div class="info_block">
  <h2>Organizations Management</h2>
  <div class="description">Each user can be assigned to some organization.</div>
</div>

{literal}
<script type="text/javascript">
function onAdd() {
	var form = document.forms.add;
	
	var msg = '';
	if (trim(form.title.value).length == 0) {
		msg = 'Please enter organization title.';
	}
	
	if (msg != '') alert(msg); else form.submit();
}

function onDelete() {
	if (!confirm('Are you sure to delete selected records?')) return;
	var form = document.forms.orgs;
	form.action.value = 'delete';
	form.submit();
}
</script>
{/literal}

<table border="0" id="orgs" cellspacing="0" cellpadding="2">
  <thead>
  	<tr>
  		<td>&nbsp;</td>
        <td class='title'>Organization</td>
        <td class='rec'>Recommendations Folder</td>
        <td class='users'>Users</td>
        <td>&nbsp;</td>
	</tr>
  </thead>

  <form id="orgsf" name="orgs" method='post' action='{url page=organizations}'>
    <input type="hidden" name="action" value="">
    {section name=org loop=$organizations}
    {assign var='i' value=$smarty.section.org.index%2}
    <tr {if $i eq 0}class='altrow'{/if}>
      {if isset($organizations[org].id)}
        <td><input type="checkbox" name="org[]" value="{$organizations[org].id}"></td>
        <td><a href="{url org=$organizations[org].id}">{$organizations[org].title}</a></td>
      {else}
        <td><input type="checkbox" name="_" disabled></td>
        <td>{$organizations[org].title}</td>
      {/if}
      <td>{$organizations[org].recommendations_folder_title}</td>
      <td class='users'>{$organizations[org].users}</td>
      <td>&nbsp;</td>
    </tr>
    {/section}
  </form>
  
  <tr><td colspan="5">&nbsp;</td></tr>
  <tr><td colspan="5">
  	<input type="button" onClick="selectAll('orgsf', true); return false;" value="All">&nbsp;
  	<input type="button" onClick="selectAll('orgsf', false); return false;" value="None">&nbsp;&nbsp;
  	<input type="button" onClick="onDelete();" value="Delete"/>&nbsp;<input type="button" disabled="true" onclick="onMerge();" value="Merge"/>
  </td></tr>
  <tr><td colspan="5">&nbsp;</td></tr>
  
  <tr class='altrow'><td colspan="5">Add Organization:</td></tr>
  <tr class='altrow'>
  	<form name='add' onsubmit="onAdd(); return false;" method='post' action='{url page=organizations}'>
      <input type="hidden" name="action" value="add">
      <td>&nbsp;</td>
      <td><input class='title' type='text' name='title'></td>
      <td><select name='recommendations_folder_id'>{html_options options=$folders}</select></td>
      <td colspan='2'><input type="button" onClick="onAdd(); return false;" value="Add"/></td>
    </form>
  </tr>
</table>
