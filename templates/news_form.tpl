<div class="info_block">
  <h2>{$infoblock.title}</h2>
  <div class="description">{$infoblock.description}</div>
</div>

{literal}
<script type="text/javascript">
function validate() {
	var form = document.forms.f_nitem;
	var msg = '';
	
	if (trim(form.title.value).length == 0) {
		msg = 'Please enter the title.';
	} else if (trim(form.text.value).length == 0) {
		msg = 'Please enter the text.';
	}
	
	if (msg != '') alert(msg); else form.submit();
}
</script>
{/literal}

<div id="form">
	<form name="f_nitem" onsubmit="validate(); return false;" action="{$root_url}/news_submit.php" method="post">
		<input type="hidden" name="action" value="{$action}"/>
		{if isset($news_item)}<input type="hidden" name="niid" value="{$news_item.id}"/>{/if}
		<table class="layout">
			<tr>
				<td>Title:</td>
				<td><input class="field" type="text" name="title" maxchars="100" {if isset($news_item)}value="{$news_item.title}"{/if}></td>
			</tr>
			<tr valign="top">
				<td>Text:</td>
				<td><textarea class="field" name="text" cols="50" rows="5">{if isset($news_item)}{$news_item.text}{/if}</textarea></td>
			</tr>
			<tr>
			    <td>Public:</td>
			    <td><input type="checkbox" name="public" {if !isset($news_item) || $news_item.public}checked{/if}></td>
			</tr>
			<tr><td>&nbsp;</td></tr>
			<tr>
				<td>&nbsp;</td>
				<td><input type="button" value="Go Back" onclick="history.back()">&nbsp;<input type="button" value="OK" onclick="javascript:validate();"></td>
			</tr>
		</table>
	</form>
</div>
