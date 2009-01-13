{if in_array('edit_content', $perm)}
{literal}
<script type="text/javascript">
function onDeleteFolder() {
	return confirm("Are you sure to delete this folder with subfolders and items?");
}
function onDeleteFolderLink() {
	return confirm("Are you sure to delete the link between this folder and its parent?");
}
function onDeleteItem() {
	return confirm("Are you sure to delete this item?");
}
</script>
{/literal}
{/if}

<table class="fullwidth layout" cellspacing=0 cellpadding=0>
  <tr id="path_to_home"> {* Path to home *}
    <td colspan="3">{strip}
      <div id="holder">
        <div id="login_info">
          {if !$is_logged_in}
            <div id="loginPrompt">
            	<a href="about:blank" onClick="showLoginForm(); return false;">Log in</a> {$app_props.login_prompt}
            </div>
            <form id="loginForm" method="post" action="{if isset($login_redirect)}{$login_redirect}?action=login{else}{url page=home action=login}{/if}">
              Username:&nbsp;<input name="username" type="text">&nbsp;
	          Password:&nbsp;<input name="password" type="password">&nbsp;
              <input id="btn" type="submit" value="Log In">
            </form>
          {else}
            Welcome {$user->fullName} | <a href="{url page=home action=logout}">Log Out</a>
          {/if}
        </div>
        <div id="path">{if isset($login_error)}<span class='error'>{$login_error}</span>{else}{include file="inclusions/path.tpl"}{/if}</div>
	  </div>
	  {/strip}
    </td>
  </tr>
  
  {if isset($user_type_id) and $user_type_id == 0 and 
      isset($app_props.available_version) and $app_props.available_version != FL_VERSION}
	<tr id="new_version">
		<td colspan="3">New version is available <strong>{$app_props.available_version}</strong>!</td>
	</tr>
  {/if}
  
  {if isset($eval_days_left)}
  <tr id="eval_warning">
  	<td colspan="3">
  		<h1>Your trial of Feed Library On Demand has {$eval_days_left} days to go.</h1>
  		Please contact us at <a href="mailto:support@blogbridge.com">support@blogbridge.com</a> to discuss continued use.
  	</td>
  </tr>
  {/if}
  
  <tr id="main_area"> {* Main area *}
    <td class="sidebar" id="left">
      {if isset($nav)}{include file="inclusions/navigation.tpl" blocks=$nav.blocks bookmarks=$nav.bookmarks}{/if}
      {if $is_logged_in}{include file="inclusions/personal.tpl"}{/if}
    </td>
    
    <td><div id="content">
      {include file="$content.tpl"}
    </div></td>
    
    <td class="sidebar" id="right">
      {if isset($owner)}{include file="inclusions/owner.tpl" person=$owner}{/if}
      {if isset($tla_links)}{include file="inclusions/tla.tpl"}{/if}
      {if isset($news_items)}{include file="inclusions/news.tpl"}{/if}
      {if isset($recommendations)}{include file="inclusions/recommendations.tpl" rec_items=$recommendations}{/if}
      {if isset($top10) && !$app_props.direct_feed_urls}{include file="inclusions/top10.tpl"}{/if}
    </td>
  </tr>
</table>

{if !$is_logged_in}
<script type="text/javascript">
{literal}
function showLoginForm() {
	document.getElementById('loginForm').style.display = 'inline';
	document.getElementById('loginPrompt').style.display = 'none';
}
{/literal}
</script>
{/if}
