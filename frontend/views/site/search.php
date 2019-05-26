<nav class="navbar navbar-default new-navbar" role="navigation">
	<div class="container">
		<ul class="nav navbar-nav">
			<li><a href="/"><?php print $Lang->GetString('Main page'); ?></a></li>
			<li class="dropdown">
				<a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php print $Lang->GetString('Categories'); ?><span class="caret"></span></a>
				<ul class="dropdown-menu" role="menu">
<?php
	foreach ($categories_list as $category) {
		if (isset($category['current']) && $category['current']) {
			print "<li class=\"active\"><a href=\"" . htmlspecialchars($category['link']) . "\">" . htmlspecialchars($category['title']) . "</a></li>\n";
		}
		else {
			print "<li><a href=\"" . htmlspecialchars($category['link']) . "\">" . htmlspecialchars($category['title']) . "</a></li>\n";
		}
	}
?>
				</ul>
			</li>
		</ul>
		<form class="navbar-form navbar-right" role="search" method="get" action="<?php print htmlspecialchars($Path->Search()); ?>">
			<div class="form-group">
				<input type="text" name="q" class="form-control form-control-new-style" placeholder="<?php print $Lang->GetString('Search'); ?>" value="<?php if (isset($search_query)) print htmlspecialchars($search_query); ?>">
			</div>
			<button type="submit" class="btn btn-default btn-search"><?php print $Lang->GetString('Search'); ?></button>
		</form>
      </div>
</nav>
<div class="page-header">
	<div class="container">
		<div class="row">
			<div class="col-md-3 col-sm-3 col-xs-3">
				<h1 class="h1-head-logo"><a href="/"><img src="/img/korzina.jpg" class="logo-img"><div class="clearfix"></div></a></h1>
			</div>
			<div class="col-md-9 col-sm-9 col-xs-9">
				<h1 class="header-text">
					<?php print $Lang->GetString('Best goods from universe!'); ?><br />
					<small><?php print $Lang->GetString('Free shipping, fast check out'); ?></small>
				</h1>
			</div>
		</div>
	</div>
</div>
<div class="container">
	<div class="row">
		<div class="col-md-3">
			<div class="panel panel-default hidden-xs hidden-sm">
				<div class="panel-heading panel-heading-new-style"><?php print $Lang->GetString('Categories'); ?></div>
				<div class="">
					<div class="list-group">
<?php
	foreach ($categories_list as $category) {
		if (isset($category['current']) && $category['current']) {
			print "<a class=\"list-group-item link-cat-search active\" href=\"" . htmlspecialchars($category['link']) . "\">" . htmlspecialchars($category['title']) . "</a>\n";
		}
		else {
			print "<a class=\"list-group-item link-cat-search\" href=\"" . htmlspecialchars($category['link']) . "\">" . htmlspecialchars($category['title']) . "</a>\n";
		}
	}
?>
					</div>
				</div>
			</div>
		</div>
<div class="col-md-9">


<?php

if ($search_query == '') {
?>
	<div class="alert alert-danger" role="alert"><strong>Problem!</strong> Search query can't be empty!</div>
<?php
}
else {
	print "<div class=\"col-md-12\"><h1 class=\"name-category\" align=\"center\">Результаты поиска - &laquo;" . htmlspecialchars($search_query) . "&raquo;";
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
	} ?>
        
        <div class="clearfix"></div> 

 <?php
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
?>
        </div>
    </div>
</div>
