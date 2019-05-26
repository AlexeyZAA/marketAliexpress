<?php print "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\n"; ?>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
	<channel>
		<atom:link href="<?php print htmlspecialchars($Path->Rss()); ?>" rel="self" type="application/rss+xml" />
		<title><?php print htmlspecialchars($settings['site_name']); ?></title>
		<description></description>
		<link>http://<?php print htmlspecialchars($_SERVER['HTTP_HOST']); ?></link>
<?php foreach ($offers as $offer_info) { ?>
		<item>
			<title><?php print htmlspecialchars($offer_info['name']); ?></title>
			<description><?php print htmlspecialchars($offer_info['description']); ?></description>
			<link><?php print htmlspecialchars($offer_info['link']); ?></link>
			<guid isPermaLink="false"><?php print htmlspecialchars($offer_info['guid']); ?></guid>
		</item>
<?php } ?>
	</channel>
</rss>