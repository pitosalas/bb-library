<div class="info_block">
  <h2>Tasks</h2>
  <div class="description">
  	Periodically executed tasks. To make the tasks execute periodically you need to
  	ping the following link at least once a minute: <i>{$pulse_link}</i><br><br>
  	It is convenient to use '<i>cron</i>' together with '<i>wget -q --spider {$pulse_link}</i>'
  	command.
  </div>
</div>

<form name="tasks" method="post" action="{url page=tasks}">
<table id="tasks" border="0" cellspacing="0">
	{foreach from=$tasks item=task}
	<tr>
		<td class="title">{$task->title}:&nbsp;</td>
		<td class="period">{html_options name=$task->name options=$periods selected=$task->period_id}&nbsp;</td>
		<td class="last_exec">Last: {if $task->last_exec}{$task->last_exec|date_format:"%B %e, %Y %H:%M:%S"}{else}Never{/if}&nbsp;</td>
		<td class="runnow"><a href="{url page=tasks}?run={$task->name}">Run Now</a></td>
	</tr>
	{/foreach}
	<tr><td colspan="4">&nbsp;</td></tr>
	<tr>
		<td colspan="4"><input type="button" value="Go Back" onclick="history.back()">&nbsp;<input type="submit" value="Update"></td>
	</tr>
</table>
</form>