<nav class="navbar" role="navigation">
	<div class="container">
		<ul class="nav navbar-nav">
			<li><a href="/"><?php print $Lang->GetString('Main page'); ?></a></li>
			<li class="dropdown">
				<a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php print $Lang->GetString('Categories'); ?><span class="caret"></span></a>
				<ul class="dropdown-menu" role="menu">
<?php
	foreach ($categories_list as $category) {
		if (isset($category['current']) && $category['current']) {
			print "<li class=\"active\"><a href=\"category?id=" . htmlspecialchars($category['id']) . "\">" . htmlspecialchars($category['title']) . "</a></li>\n";
		}
		else {
			print "<li><a href=\"category?id=" . htmlspecialchars($category['id']) . "\">" . htmlspecialchars($category['title']) . "</a></li>\n";
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
			<div class="panel-heading panel-title"><?php print $Lang->GetString('Categories'); ?></div>
				<div class="">
					<div class="list-group">
					<?php
					foreach ($categories_list as $category) {
					if (isset($category['current']) && $category['current']) {
					print "<a class=\"list-group-item link-cat-search active\" href=\"category?id=" . htmlspecialchars($category['id']) . "\">" . htmlspecialchars($category['title']) . "</a>\n";
					}
					else {
					print "<a class=\"list-group-item link-cat-search\" href=\"category?id=" . htmlspecialchars($category['id']) . "\">" . htmlspecialchars($category['title']) . "</a>\n";
					}
					}
					?>
					</div>
				</div>
			</div>
		</div>
		<div class="col-md-9">

<?php
// Если нет информации о товаре
if (!$offer_info) {
?>
	<div class="alert alert-danger" role="alert"><strong><?php print $Lang->GetString('Problem!'); ?></strong> <?php print $Lang->GetString('Good not found.'); ?></div>
<?php
}
// Если информация о товаре  получена
else {
?>
		<div class="panel panel-default">
			<div class="panel-heading"><?php print htmlspecialchars($offer_info['name']); ?></div>
			<div class="panel-body">
			<?php if (isset($offer_info['all_images']) && is_array($offer_info['all_images'])) { ?>
				<div class="row">
					<div class="col-md-2 hidden-xs">
						<ul class="list-group">
						<?php
							foreach ($offer_info['all_images'] as $image_num => $image_url) {
								print "<li class=\"list-group-item\"><a href=\"#\" onclick=\"javascript:document.getElementById('good_img_main').src = document.getElementById('image_preview_$image_num').src; return false;\">";
								print "<img id=\"image_preview_$image_num\" src=\"" . htmlspecialchars($image_url) . "\" style=\"max-width:100%;\">";
								print "</a></li>\n";
							}
						?>
						</ul>
					</div>
					<div class="col-md-10">
						<p><img id="good_img_main" src="<?php print htmlspecialchars($offer_info['all_images'][0]) ;?>" style="max-width:50%;"></p>
					</div>
				</div>
			<?php } elseif ($offer_info['picture'] != '') { ?>
				<p><img src="<?php print htmlspecialchars($offer_info['picture']) ;?>" style="max-width:50%;"></p>
			<?php } ?>
				<p><strong><?php print $Lang->GetString('Price'); ?></strong>: <?php print htmlspecialchars($offer_info['price'] . ' ' . $offer_info['currency']); ?>
				<?php if ($offer_info['category'] != '') { ?>
					<br />
					<strong><?php print $Lang->GetString('Category'); ?></strong>: <a href="category?id=<?php print htmlspecialchars($offer_info['id_category']); ?>"><?php print htmlspecialchars($offer_info['category']) ?></a>
				<?php } ?>
				</p>
				<div class="btn-toolbar" role="toolbar">
					<div class="btn-group">
						<a class="btn btn-success" href="go?id=<?php print htmlspecialchars($offer_info['id']); ?>" rel="nofollow"><?php print $Lang->GetString('Buy now!'); ?></a>
					</div>
				</div>
			</div>
		</div>
<?php
	if (sizeof($offers)) {
		print "<h3>" . $Lang->GetString('See also:') . "</h3>\n";
		print "<div>\n";
		print "<div class=\"row\">\n";
		for ($i = 0; $i < 3; $i++) {
			print "<div class=\"col-md-4 col-sm-6 col-xs-12 wr-good\">\n";
			if (isset($offers[$i])) {
	?>
				<div class="panel panel-default">
					<div class="panel-heading name-good"><?php print htmlspecialchars($offers[$i]['name']); ?></div>
					<div class="panel-body text-center">
						<?php if ($offers[$i]['picture'] != '') { ?>
							<p class="wr-img"><a href="<?php print htmlspecialchars($offers[$i]['link']); ?>"><img class="good-img" src="<?php print htmlspecialchars($offers[$i]['picture']) ;?>"></a></p>
						<?php } ?>
						<p class="text-left text-cat-price"><strong><?php print $Lang->GetString('Price'); ?></strong>: <span class="price-style"><?php print htmlspecialchars($offers[$i]['price'] . ' ' . $offers[$i]['currency']); ?></span></p>
						<div class="btn-toolbar" role="toolbar">
							<div class="btn-group">
								<a class="btn btn-b" href="<?php print htmlspecialchars($offers[$i]['url']) ?>" rel="nofollow"><?php print $Lang->GetString('Buy now!'); ?></a>
							</div>
							<div class="wr-btn-more-info">
								<a class="btn btn-more-info" href="<?php print htmlspecialchars($offers[$i]['link']) ?>"><?php print $Lang->GetString('More info'); ?></a>
							</div>
						</div>
					</div>
				</div>
	<?php
			}
			print "</div>\n";
		}
		print "</div>\n";
		print "</div>\n";
	}
}
?>
		</div>
	</div>
	</div>
