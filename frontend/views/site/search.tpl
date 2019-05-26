<?php
// Метаданные страницы
if ($search_query == '') {
	$template_title = "{$settings['site_name']}";
	$template_description = "{$settings['site_name']}";
}
else {
	$template_title = "{$settings['site_name']} - " . sprintf($Lang->GetString('Search results for query "%s".'), htmlspecialchars($search_query));
	$template_description = "{$settings['site_name']} - " . sprintf($Lang->GetString('Found %d goods for query "%s".'), sizeof($offers), $search_query);
}

$template_keywords = "china goods, best goods, phones, food, tablets, shirts";

include_once $Common->GetTemplatePath('_up');
 
if ($search_query == '') {
?>
	<div class="alert alert-danger" role="alert"><strong>Problem!</strong> Search query can't be empty!</div>
<?php
}
else {
	print "<div class=\"col-md-12\"><h1 class=\"name-category\" align=\"center\">Search results for query &laquo;" . htmlspecialchars($search_query) . "&raquo;";
	if (!sizeof($offers)) {
		print "<br /><small>Sorry, but no results found.</small>";
	}
	print "</h1></div>\n";
	
	if (sizeof($search_count_hash) > 1) {
		print "<div class=\"col-md-12\"><ul class=\"nav nav-pills nav-pills-search\">\n";
		foreach ($search_categories_count as $search_cat_info) {
			if ($search_cat_info['current']) {
				print "<li class=\"active\"><a class=\"link-cat-search\" href=\""  . htmlspecialchars($search_cat_info['link']) .
						"\">" . htmlspecialchars($search_cat_info['title']) .
						"&nbsp;(" . htmlspecialchars($search_cat_info['count']) . ")</a></li>\n";
			}
			else {
				print "<li><a class=\"link-cat-search\" href=\"" . htmlspecialchars($search_cat_info['link']) .
						"\">" . htmlspecialchars($search_cat_info['title']) .
						"&nbsp;(" . htmlspecialchars($search_cat_info['count']) . ")</a></li>\n";
			}
		}
		print "</ul></div>\n";
	}
	
	
	foreach ($offers as $offer_info) {
?>
	<div class="col-md-4 col-sm-6 col-xs-12 wr-good">
		<div class="panel panel-default">
			<div class="panel-heading name-good"><?php print htmlspecialchars($offer_info['name']); ?></div>
			<div class="panel-body text-center">
			<?php if ($offer_info['picture'] != '') { ?>
				<p class="wr-img"><a href="<?php print htmlspecialchars($offer_info['link']); ?>"><img class="good-img" src="<?php print htmlspecialchars($offer_info['picture']); ?>"></a></p>
			<?php } ?>
				<p class="text-left text-cat-price"><strong><?php print $Lang->GetString('Price'); ?></strong>: <?php print htmlspecialchars($offer_info['price'] . ' ' . $offer_info['currency']); ?>
				<?php if ($offer_info['category'] != '') { ?>
					<br />
					<strong><?php print $Lang->GetString('Category'); ?></strong>: <a href="<?php print htmlspecialchars($offer_info['category_link']); ?>"><?php print htmlspecialchars($offer_info['category']) ?></a>
				<?php } ?>
				</p>
				<div class="btn-toolbar" role="toolbar">
					<div class="btn-group">
						<a class="btn btn-b" href="<?php print htmlspecialchars($offer_info['url']); ?>" rel="nofollow"><?php print $Lang->GetString('Buy now!'); ?></a>
					</div>
					<div class="wr-btn-more-info">
						<a class="btn btn-more-info" href="<?php print htmlspecialchars($offer_info['link']); ?>"><?php print $Lang->GetString('More info'); ?></a>
					</div>
				</div>
			</div>
		</div>
	</div>

<?php
	} ?><div class="clearfix"></div> <?php
	if (sizeof($pages)) {
		print "<div class=\"col-xs-12\"><ul class=\"pagination\">\n";
		foreach ($pages as $page_data) {
			if ($page_data['link'] != '') {
				print "<li><a href=\"" . htmlspecialchars($page_data['link']) . "\">" . htmlspecialchars($page_data['page']) . "</a></li>\n";
			}
			else {
				print "<li class=\"active\"><span>" . htmlspecialchars($page_data['page']) . " <span class=\"sr-only\">(current)</span></a></li>\n";
			}
		}
		print "</ul></div>\n";
	}

}

include_once $Common->GetTemplatePath('_down');
