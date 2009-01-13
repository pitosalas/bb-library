<script type="text/javascript">
{literal}
function onAccept() {
	document.forms.license.submit();
}

function onDoNotAccept() {
	alert('You must ACCEPT this license to start using the application.');
}
{/literal}
</script>

<div id="license">
{include file=$license_path}

<form name="license" action="{if isset($login_redirect)}{$login_redirect}{else}{url page=home}{/if}" method="post">
	<input type="hidden" name="action" value="accept_license"/>
	<div id="accept_buttons">
		<input type="button" value="ACCEPT" onclick="onAccept(); return false;"/>
		<input type="button" value="DO NOT ACCEPT" onclick="onDoNotAccept(); return false;"/>
	</div>
</form>
</div>