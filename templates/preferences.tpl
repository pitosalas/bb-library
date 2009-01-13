<div class="info_block">
  <h2>Application Preferences</h2>
  <div class="description">Application preferences define how application looks and feels.</div>
</div>

{literal}
<script type="text/javascript">
function validate() {
	var form = document.forms.f_prefs;
	var msg = '';
	
	if (msg != '') alert(msg); else form.submit();
}
</script>
{/literal}

<div id="form">
	<form name="f_prefs" onsubmit="validate(); return false;" action="{$root_url}/prefs_submit.php" method="post">
		<table class="layout">
			<tr>
				<td>Title:</td>
				<td><input class="field" type="text" name="title" maxchars="100" value="{$app_props.title}"></td>
			</tr>
			<tr>
				<td>Contacts:</td>
				<td><input class="field" type="text" name="contacts" maxchars="250" value="{$app_props.contacts|escape:"htmlall"}"></td>
			</tr>
			<tr>
				<td>Login Prompt: Log in&nbsp;</td>
				<td><input class="field" type="text" name="login_prompt" maxchars="100" value="{$app_props.login_prompt}"></td>
			</tr>
			<tr>
			    <td>Theme:</td>
			    <td>{html_options name="theme" options=$themes selected=$app_props.theme}</td>
			</tr>
			<tr>
			    <td>Feed Preview Mode:</td>
			    <td>{html_options name="feed_preview_mode" options=$feed_preview_modes selected=$app_props.feed_preview_mode}</td>
			</tr>

			<tr><td colspan="2">&nbsp;</td></tr>

			<tr>
			    <td>Direct Feed URLs (no Top 10/100):</td>
			    <td><select name="direct_feed_urls">
			    	<option value="0">No</option>
			    	<option value="1" {if $app_props.direct_feed_urls}selected{/if}>Yes</option>
			    </select>
			</tr>
			<tr>
			    <td>Generate tags and descriptions:</td>
			    <td><select name="generate_tags_and_descriptions">
			    	<option value="0">No</option>
			    	<option value="1" {if $app_props.generate_tags_and_descriptions}selected{/if}>Yes</option>
			    </select>
			</tr>

			<tr><td colspan="2">&nbsp;</td></tr>

			<tr>
			    <td>News Box Title:</td>
			    <td><input class="field" type="text" name="news_box_title" maxchars="100" value="{$app_props.news_box_title}"></td>
			</tr>
			<tr>
			    <td>News Box Items:</td>
			    <td><input class="field" type="text" name="news_box_items" maxchars="100" value="{$app_props.news_box_items}"></td>
			</tr>

			<tr><td colspan="2">&nbsp;</td></tr>

			<tr>
				<td>Partner Site Title:</td>
				<td><input class="field" type="text" name="partner_site_title" maxchars="100" value="{$app_props.partner_site_title}"></td>
			</tr>
			<tr>
				<td>Partner Site Link:</td>
				<td><input class="field" type="text" name="partner_site_link" maxchars="100" value="{$app_props.partner_site_link}"></td>
			</tr>

			<tr><td colspan="2">&nbsp;</td></tr>

			<tr>
				<td>Google Analytics Account:</td>
				<td><input class="field" type="text" name="google_analytics_acct" maxchars="100" value="{$app_props.google_analytics_acct}"> (ex. <strong>AB-12345-1</strong>)</td>
			</tr>

			<tr><td colspan="2">&nbsp;</td></tr>

			<tr>
				<td>Text Link Ads Box Title:</td>
				<td><input class="field" type="text" name="tla_box_title" maxchars="100" value="{$app_props.tla_box_title}"></td>
			</tr>
			<tr>
				<td>Text Link Ads API Key:</td>
				<td><input class="field" type="text" name="tla_apikey" maxchars="100" value="{$app_props.tla_apikey}"></td>
			</tr>

			<tr><td colspan="2">&nbsp;</td></tr>

			<tr>
				<td>Send link checker reports to:<br/>
				(leave empty for no reports)</td>
				<td><input class="field" type="text" name="lc_recepients" maxchars="100" value="{$app_props.lc_recepients}"></td>
			</tr>

			<tr><td colspan="2">&nbsp;</td></tr>

			<tr>
				<td>&nbsp;</td>
				<td><input type="button" value="Go Back" onclick="history.back()">&nbsp;<input type="button" value="OK" onclick="javascript:validate();"></td>
			</tr>
		</table>
	</form>
</div>
