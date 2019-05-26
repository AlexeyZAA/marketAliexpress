<?php
/* @var $this yii\web\View */

use yii\widgets\ListView;
use yii\helpers\Html;
use yii\bootstrap\Tabs;
use yii\bootstrap\Alert;
use yii\bootstrap\Carousel;
use yii\widgets\LinkPager;
use yii\bootstrap\NavBar;
use frontend\assets\AppAsset;
use yii\bootstrap\Nav;

//AppAsset::register($this);
$this->title = 'Интернет магазин НатА';
?>
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
		print "<h1 class=\"text-center\">Лучшие цены</h1>";
		foreach ($offers as $offer_info) {
		?>
		<div class="col-md-4 col-sm-6 col-xs-12 wr-good">
			<div class="panel panel-default">
			<div class="panel-heading name-good"><?php print htmlspecialchars($offer_info['name']); ?></div>
				<div class="panel-body text-center">
				<?php if ($offer_info['picture'] != '') { ?>
				<p class="wr-img"><a href="site/good?id=<?php print htmlspecialchars($offer_info['id']); ?>"><img class="good-img" src="<?php print htmlspecialchars($offer_info['picture']) ;?>" ></a></p>
				<?php } ?>
				<p class="text-left text-cat-price"><strong><?php print $Lang->GetString('Price'); ?></strong>: <span class="price-style"><?php print htmlspecialchars($offer_info['price'] . ' ' . $offer_info['currency']); ?></span>
				<?php if ($offer_info['category'] != '') { ?>
				<br />
				<strong><?php print $Lang->GetString('Category'); ?></strong>: <a href="category?id=<?php print htmlspecialchars($offer_info['id_category']); ?>"><?php print htmlspecialchars($offer_info['category']) ?></a>
				<?php } ?>
				</p>
					<div class="btn-toolbar" role="toolbar">
						<div class="btn-group">
						<a class="btn btn-success" href="site/go?id=<?php print htmlspecialchars($offer_info['id']); ?>" rel="nofollow"><?php print $Lang->GetString('Buy now!'); ?></a>
						</div>
							<div class="wr-btn-more-info">
							<a class="btn-more-info" href="good?id=<?php print htmlspecialchars($offer_info['id']); ?>"><?php print $Lang->GetString('More info'); ?></a>
							</div>
					</div>
				</div>
			</div>
		</div>
		<?php
		}
		?>
		</div>
	</div>
</div>
