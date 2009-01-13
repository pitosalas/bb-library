<?php
header('Content-type: text/xml');
echo '<?xml version="1.0" encoding="UTF-8"?>';

require_once 'sites/config.php';
$site = 'http://' . strtolower($_SERVER['SERVER_NAME']) . '/';
?>
<urlset xmlns="http://www.google.com/schemas/sitemap/0.84">
	<url>
		<loc><?= $site ?></loc>
		<changefreq>daily</changefreq>
		<priority>0.2</priority>
	</url>
	<url>
		<loc><?= $site . 'news' ?></loc>
		<changefreq>weekly</changefreq>
		<priority>0.4</priority>
	</url>
	<url>
		<loc><?= $site . 'top100' ?></loc>
		<changefreq>daily</changefreq>
		<priority>0.8</priority>
	</url>
	<url>
		<loc><?= $site . 'tags_cloud' ?></loc>
		<changefreq>daily</changefreq>
		<priority>1</priority>
	</url>
</urlset>