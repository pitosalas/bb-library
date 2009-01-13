<div class="info_block">
  <h2>User: {$person->fullName}</h2>
  <div class="description">Please enter information.</div>
</div>

<script type="text/javascript" src="{$root_url}/script/user_validate.js"></script>
{literal}
<script type="text/javascript">
function onSave() {
	var form = document.forms.edit;
	var msg = validate(form, false);
	if (msg != '') alert(msg); else form.submit();
}
</script>
{/literal}

<form name='edit' onsubmit="onSave(); return false;" method='post' action='{url page=users}' enctype="multipart/form-data">
  <input type="hidden" name="action" value="edit">
  <input type="hidden" name="pid" value="{$person->id}">
  <table class="layout">
    <tr><td>Full Name:</td><td><input class="field" type="text" name="fullName" value="{$person->fullName}"></td></tr>
    <tr><td>E-Mail:</td><td><input class="field" type="text" name="email" value="{$person->email}"></td></tr>
    <tr><td>My Blog or Site:</td><td><input class="field" type="text" name="home_page" value="{$person->home_page}"></td></tr>
    <tr><td>User Name:</td><td><input class="field" type="text" name="userName" value="{$person->userName}" {if !in_array('manage_users', $perm)}disabled{/if}></td></tr>
    <tr><td>Password:</td><td><input class="field" type="password" name="pwd"></td></tr>
    <tr><td>Again:</td><td><input class="field" type="password" name="again"></td></tr>
    <tr><td>Type:</td><td>
    {if in_array('manage_users', $perm)}{html_options name="type_id" options=$types selected=$person->type_id}
    {else}<input class="field" value="{$person->type_name}" disabled>{/if}
    </td></tr>
    <tr><td>Organization:</td><td>
    {if in_array('manage_users', $perm)}{html_options name="organization_id" options=$organizations selected=$person->organization_id}
    {else}<input class="field" value="{$person->organization_title}" disabled>{/if}</td></tr>
    <tr><td>Bio:</td><td><textarea class="field" name="description" rows="10">{$person->description}</textarea></td></tr>
    <tr valign="top"><td>Tags:</td><td><input class="field" type="text" name="tags" value="{implode array=$person->tags separator=", "}"><br>&nbsp;&nbsp;example: tag1, tag2</td></tr>
    <tr><td>Picture (50x75):</td><td><input class="field" type="file" name="picture"></td></tr>
    <tr><td></td><td><input type="checkbox" name="no_ads" {if $person->no_ads}checked{/if}/> Don't show ads in my folders and items</td></td>
    <tr><td>Last Logged In On:</td><td>{if $person->last_login}{$person->last_login|date_format:"%A, %B %e, %Y"}{else}Never{/if}</td></tr>
    <tr><td colspan='2'>&nbsp;</td></tr>
    <tr><td colspan='2'><input type="button" value="Go Back" onclick="history.back()">&nbsp;<input type="button" onClick="onSave(); return false;" value="OK"></td></tr>
  </table>
</form>

{if $person->id eq 1}
{literal}
<script>
form = document.forms.edit;
form.type_id.disabled = true;
form.organization_id.disabled = true;
</script>
{/literal}
{/if}

{if isset($person->folders) && count($person->folders) gt 0}
<div class="clear" id="user_inventory">
  <h2>Folders</h2>
  
  {include file="components/search_results_table.tpl" results=$person->folders}
</div>
{/if}
