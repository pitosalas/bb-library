<div class="news_item{if !$news_item.public} private{/if}">
  {if in_array('manage_news', $perm)}
    <div class="controls">
      <a href="{url news=$news_item type='edt_nitem'}">Edit</a> - 
      <a href="{url news=$news_item type='del_nitem'}" onClick="return onDeleteNewsItem()">Delete</a>
    </div>
  {/if}
  <h2>{$news_item.title}{if isset($news_item.folder_id)} <a href="{url folder_id=$news_item.folder_id}">{css_image pic='folder-link'}</a>{/if}</h2>
  <div class="text"><img class="author" src="{if isset($news_item.folder_owner_id)}{image person_id=$news_item.folder_owner_id}{else}{image person_id=$news_item.author_id}{/if}" width="50" height="76">{$news_item.text}</div>
  <div class="posted_by">Posted by {$news_item.author} on {$news_item.date|date_format:"%A, %B %e, %Y"}</div>
</div>
