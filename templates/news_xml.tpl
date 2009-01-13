<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<feed xmlns="http://purl.org/atom/ns#" version="0.3" xml:lang="en-US">
	<title mode="escaped" type="text/html">Latest News</title>
	<link href="{url page=news}" rel="alternate" title="Latest News" type="text/html"/>

	{section name=i loop=$news_items}
		{assign var="item" value=$news_items[i]}
		<entry>
			<link href="{url news=$item}" rel="alternate" title="{$item.title|escape:"html"}" type="text/html"/>
			<id>{url news=$item}</id>
			<published>{$item.date|date_format:"%Y-%m-%dT%H:%M:%S-00:00"}</published>
			<title mode="escaped" type="text/html">{$item.title|escape:"html"}</title>
			<content type="application/xhtml+xml">
				<div xmlns="http://www.w3.org/1999/xhtml">{$item.text}</div>
			</content>
		</entry>
	{/section}
</feed>
