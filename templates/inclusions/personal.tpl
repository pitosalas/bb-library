{* Param:  *}
{capture name="data"}
<ul>
	{if $is_logged_in}
		<li><a href="{url page=profile}">My Profile</a></li>

		{if in_array('edit_content', $perm)}
			<li><a href="{url page=upload_opml}{if isset($folder)}?fid={$folder->id}{/if}">Upload OPML</a></li>
		{/if}
	{/if}
	
    <li class="separator">&nbsp;</li>

	<li><a href="{url page=users}">{if in_array('manage_users', $perm)}Manage {/if}Users</a></li>

	{if in_array('manage_organizations', $perm) ||
	    in_array('manage_tags', $perm)}
		{if in_array('manage_organizations', $perm)}
			<li><a href="{url page=organizations}">Manage Organizations</a></li>
		{/if}
		{if in_array('manage_tags', $perm)}
			<li><a href="{url page=tags}">Manage Tags</a></li>
		{/if}
	{/if}

	{if in_array('do_backups_restores', $perm) || 
	    in_array('manage_preferences', $perm) ||
	    in_array('manage_tasks', $perm)}
	    <li class="separator">&nbsp;</li>
		{if in_array('manage_tasks', $perm)}
			<li><a href="{url page=tasks}">Tasks</a></li>
		{/if}
		{if in_array('do_backups_restores', $perm)}
			<li><a href="{url page=backup_restore}">Backup &amp; Restore</a></li>
		{/if}
		{if in_array('manage_preferences', $perm)}
			<li><a href="{url page=preferences}">Preferences</a></li>
		{/if}
	{/if}

	{if $is_logged_in}
		<li class="separator">&nbsp;</li>
		<li><a href="{url page=home action=logout}">Log Out</a></li>
	{/if}
</ul>

{/capture}
{assign var="personal_title" value="Personal"}
{if isset($user)}{assign var="personal_title" value=$user->fullName}{/if}
{include file="components/sidebar_block.tpl" title=$personal_title data=$smarty.capture.data}
