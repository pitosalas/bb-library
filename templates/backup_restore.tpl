<div class="info_block">
  <h2>Backup and Restore</h2>
  <div class="description">This page helps you to perform backups and restorations of your database.</div>
</div>

<script type="text/javascript">
{literal}
function onDelete() {
	if (confirm('Are you sure to delete the selected backups?')) {
		document.forms.del.submit();
	}
}

function onRestore() {
	if (confirm('Are you sure to restore database from the selected backup?')) {
		document.forms.restore.submit();
	}
}

function onBackup() {
	if (confirm('Are you sure to create new backup?')) {
		document.forms.backup.submit();
	}
}
{/literal}
</script>

{if isset($message)}<p class="message">{$message}</p>{/if}
{if isset($error)}<p class="error">{$error}</p>{/if}

{if count($backups) gt 0}
<h2>Manage Backups:</h2>
<p class="descr">Below is the list of all existing backups. You can delete these you don't need any more.</p>

<form id="backups" name="del" method='post' action='{url page=backup_restore}'>
  <input type="hidden" name="action" value="delete">
  <table border="0" cellspacing="0" cellpadding="2">
    <thead>
      <tr>
        <td>&nbsp;</td>
        <td class='date'>Date</td>
        <td class='size'>Size (Kb)</td>
      </tr>
    </thead>

    {section name=backup loop=$backups}
    {assign var='i' value=$smarty.section.backup.index%2}
    <tr {if $i eq 0}class='altrow'{/if}>
      <td><input type="checkbox" name="backup[]" value="{$backups[backup].name}"></td>
      <td>{$backups[backup].date}</td>
      <td class='size'>{$backups[backup].size|string_format:"%.1f"}</td>
    </tr>
    {/section}
  
    <tr><td colspan="5">&nbsp;</td></tr>
    <tr><td colspan="5">
      <input type="button" onClick="selectAll('backups', true); return false;" value="All">&nbsp;
      <input type="button" onClick="selectAll('backups', false); return false;" value="None">&nbsp;&nbsp;
      <input type="button" onClick="onDelete();" value="Delete"/>
    </td></tr>
  </table>
</form>

<br>
<h2>Restore:</h2>
<p class="descr">Please select a backup to revert your database to.</p>

<form name="restore" method="post" action='{url page=backup_restore}'>
  <input type="hidden" name="action" value="restore">
  {html_options name="backup" options=$backups_dict}
  <input type="button" onClick="onRestore(); return false;" value="Restore">
</form>
{/if}

<br>
<h2>Create Backup:</h2>
<p class="descr">You can create the backup of your database in its current state. Depending on
the size of the database it can take some time to prepare the complete image. Also note that
during this operation your users won't be able to work with the application.</p>

{if !$can_backup}<p class="error">Backups directory is missing, not writable or path to mysqldump isn't specified</p>{/if}

<form name="backup" method="post" action='{url page=backup_restore}'>
  <input type="hidden" name="action" value="backup">
  <input type="button" onClick="onBackup(); return false;" value="Create Backup" {if !$can_backup}disabled{/if}>
</form>