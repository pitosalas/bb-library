<div class="info_block">
  <h2>Organization: {$organization.title}</h2>
  <div class="description">Please enter information about the organization.</div>
</div>

{literal}
<script type="text/javascript">
function onSave() {
	var form = document.forms.org;
	
	var msg = '';
	if (trim(form.title.value).length == 0) {
		msg = 'Please enter organization title.';
	}
	
	if (msg != '') alert(msg); else form.submit();
}
</script>
{/literal}

<form name="org" onsubmit="onSave(); return false;" method='post' action='{url page=organizations}'>
  <input type="hidden" name="action" value="edit">
  <input type="hidden" name="oid" value="{$organization.id}">
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

    <tr>
      <td>&nbsp;</td>
      <td><input type="text" name="title" value="{$organization.title}"></td>
      <td>{html_options name="folder" options=$folders selected=$organization.recommendations_folder_id}</td>
      <td class='users'>{$organization.users}</td>
      <td>&nbsp;</td>
    </tr>
  
    <tr><td colspan="5">&nbsp;</td></tr>

    <tr>
      <td>&nbsp;</td>
      <td colspan="4"><input type="button" value="Go Back" onclick="history.back()">&nbsp;<input type="button" value="OK" onClick="onSave(); return false;"></td>
    </tr>
  </table>
</form>