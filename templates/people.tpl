<div class="info_block">
  <h2>Users</h2>
  <div class="description">The list of all users registered in the system.</div>
</div>

<script type="text/javascript" src="{$root_url}/script/user_validate.js"></script>
{literal}
<script type="text/javascript">
function onAdd() {
	var form = document.forms.add;
	var msg = validate(form, true);
	if (msg != '') alert(msg); else form.submit();
}

function onDelete() {
	if (!confirm('Are you sure to delete selected records?')) return;

	document.forms.del.submit();
}

function go(url) {
	window.location.href = url;
}
</script>
{/literal}

<table border="0" id="people" cellspacing="0" cellpadding="2">
  <thead>
  	<tr>
  		<td>&nbsp;</td>
        <td class='name'>Name</td>
        <td class='email'>E-Mail</td>
        <td class='type'>Type</td>
        <td class='last_login'>Last&nbsp;Login</td>
	</tr>
  </thead>

  <form id='del' name="del" method='post' action='{url page=users}'>
    <input type="hidden" name="action" value="delete">
    {section name=person loop=$people}
    {assign var='i' value=$smarty.section.person.index%2}
    <tr {if $i eq 0}class='altrow'{/if}>
      <td>{if $can_edit}<input type="checkbox" name="people[]" value="{$people[person]->id}" {if $people[person]->id eq 1}disabled{/if}>{/if}</td>
      <td><a href="{url person=$people[person]}">{$people[person]->fullName}</a></td>
      <td>{$people[person]->email}</td>
      <td>{$people[person]->accountType}</td>
      <td>{$people[person]->last_login|date_format:"%D %H:%M"}</td>
    </tr>
    {/section}
  </form>
  
  <tr><td colspan="5">&nbsp;</td></tr>
{if $can_edit}
  <tr><td colspan="5">
  	<input type="button" onClick="selectAll('del', true); return false;" value="All">&nbsp;
  	<input type="button" onClick="selectAll('del', false); return false;" value="None">&nbsp;&nbsp;
  	<input type="button" onClick="onDelete();" value="Delete">&nbsp;
  	<input type="button" onClick="go('{url page=csv_import}')" value="CSV Import">
  </td></tr>
  <tr><td colspan="5">&nbsp;</td></tr>
  
  <tr class='altrow'><td colspan="5">Add User:</td></tr>
  <tr class='altrow'>
  	<form name='add' onsubmit="onAdd(); return false;" method='post' action='{url page=users}' enctype="multipart/form-data">
    <input type="hidden" name="action" value="add">
    <td colspan="5">
      <table class="layout">
        <tr><td>Full Name:</td><td><input class="field" type="text" name="fullName"></td></tr>
        <tr><td>E-Mail:</td><td><input class="field" type="text" name="email"></td></tr>
        <tr><td>My Blog or Site:</td><td><input class="field" type="text" name="home_page"></td></tr>
        <tr><td>User Name:</td><td><input class="field" type="text" name="userName"></td></tr>
        <tr><td>Password:</td><td><input class="field" type="password" name="pwd"></td></tr>
        <tr><td>Again:</td><td><input class="field" type="password" name="again"></td></tr>
        <tr><td>Type:</td><td>{html_options name="type_id" options=$types selected="2"}</td></tr>
        <tr><td>Organization:</td><td>{html_options name="organization_id" options=$organizations}</td></tr>
        <tr><td>Bio:</td><td><textarea class="field" name="description" rows="10"></textarea></td></tr>
        <tr><td>Tags:</td><td><input class="field" type="text" name="tags"></td></tr>
        <tr><td>Picture (50x75):</td><td><input class="field" type="file" name="picture"></td></tr>
        <tr><td></td><td><input type="checkbox" name="no_ads" /> Don't show ads in his folders and items</td></td>
        <tr><td colspan='2'>&nbsp;</td></tr>
        <tr><td colspan='2'><input type="button" onClick="onAdd(); return false;" value="Add"></td></tr>
      </table>
	</td>
    </form>
  </tr>
{/if} {* can_edit *}
</table>
