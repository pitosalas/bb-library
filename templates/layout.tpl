<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<HTML>
<HEAD>
  <TITLE>{$title}</TITLE>
  <META http-equiv="Content-Type" content="text/html; charset=utf-8"/>
  <META http-equiv="Content-Language" content="en-us"/>
{if isset($app_props.theme) && $app_props.theme}
  <link rel="stylesheet" href="{$themes_url}/{$app_props.theme}/page.css"/>
  <link rel="stylesheet" href="{$themes_url}/{$app_props.theme}/{$page}.css"/>
{else}
  <link rel="stylesheet" href="{$styles_url}/page.css"/> 
  <link rel="stylesheet" href="{$styles_url}/{$page}.css"/>
{/if}
{if isset($meta_keywords)}
  <meta name="keywords" content="{$meta_keywords|escape:"html"}" />
{/if}
{if isset($meta_description)}
  <meta name="description" content="{$meta_description|escape:"html"}" />
{/if}
  <script type="text/javascript" src="{$root_url}/script/general.js"></script>
</HEAD>

<BODY bgcolor="#ffffff">

<table id="page" class="fullwidth layout">
  <tr> {* Header *}
    <td id="header" align="center" valign="top">
      <h1><a href="{$root_url}">{$app_props.title}</a></h1>
      <a id="help1" href="http://www.blogbridge.com/products-services/feed-library/visual-tour/"></a>
    </td>
  </tr>
  
  <tr> {* Main Part*}
    <td id="main_part">
      {if isset($show_license)}
        {include file="license.tpl"}
      {elseif isset($license_expired)}
        {include file="license_expired.tpl"}
      {else}
        {include file="$page.tpl"}
      {/if}
    </td>
  </tr>
  
  <tr> {* Footer *}
    <td id="footer" valign="top">
      <table class="fullwidth layout">
        <tr valign="top">
          <td width="250">&copy; 2000-2007 <a href="http://www.salas.com/">Salas Associates, Inc.</a><br>version {$version}</td>
          <td align="center">Contact: {$app_props.contacts}</td>
          <td width="250" align="right">Designed by <a href="http://www.noizeramp.com/">NCG</a></td> 
        </tr>
      </table>
    </td>
  </tr>
</table>

{if isset($app_props.google_analytics_acct) && $app_props.google_analytics_acct != ''}
<script src="http://www.google-analytics.com/urchin.js" type="text/javascript">
</script>
<script type="text/javascript">
_uacct = "{$app_props.google_analytics_acct}";
urchinTracker();
</script>
{/if}
</BODY>
</HTML>
