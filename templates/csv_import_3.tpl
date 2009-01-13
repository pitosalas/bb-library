<div class="info_block">
  <h2>CSV Users Import</h2>
  <div class="description">This feature allows you to import user accounts from a CSV file.</div>
</div>

<p id="step_descr">{$added} of {$total} user accounts were added.</p>

{literal}
<script type="text/javascript">
function showRest() {
	document.getElementById('show_rest').style.display = 'none';
	document.getElementById('rest').style.display = 'block';
}
</script>
{/literal}

{if $added lt $total}
<div id="duplicates">
	<h2>Accounts with duplicate user names:</h2>
	{section loop=$duplicates name=dup max=5}
		{$duplicates[dup]->userName} ({$duplicates[dup]->fullName})<br>
	{/section}
	{if $rest gt 0}
	<div id="show_rest"><a href="#" onClick="showRest(); return false;">Show the rest ({$rest})</a></div>
	<div id="rest">
		{section loop=$duplicates name=dup start=5}
			{$duplicates[dup]->userName} ({$duplicates[dup]->fullName})<br>
		{/section}
	</div> 
	{/if}
</div>
{/if}

{literal}
<script type="text/javascript">
var el = document.getElementById('rest');
if (el) el.style.display = 'none';
</script>
{/literal}