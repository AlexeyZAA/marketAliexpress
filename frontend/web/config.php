<?php
// Настройки
$settings = array(
	// Ключ для доступа к API
	'user_api_key' => '0386336437dba8b58ab3da7bb693d29f',
	// Хэш для построения диплинков
	'user_deep_link_hash' => 'obg2127ij9fbj5l0xv870uefhth7dsd9',
	
	// Количество товаров на странице
	'items_per_page' => 21,
	
	// Количество товаров в RSS-ленте
	'rss_items_count' => 21,
	
	// Минимальная цена товара
	'price_min' => 0.0,
	// Максимальная цена товара
	'price_max' => 100000.0,
	
	// Название нашего сайта
	'site_name' => 'Магазин Ната',
	
	// Язык описаний товаров (может быть en или ru)
	'lang' => 'ru',
	
	// Желаемая валюта
	// Поддерживаются как минимум USD, EUR, RUR, UAH, KZT. Подробнее - в документации
	'currency' => 'RUR',
	
	// Используемая библиотека кэширования
	// Если есть поддержка на хостинге то крайне рекоммендуется включить
	// Возможные значения: none, apc, mysql, xcache, memcache, memcached, wincache
	'cache_library' => 'none',
	
	// Только если в качестве кэша выбран memcache или memcached
	'memcached_host' => '127.0.0.1',
	'memcached_port' => 11211,
	'memcached_pconnect' => TRUE,
	
	// Настройки MySQL. Используются если в качестве кэша указан mysql
	'mysql_host' => '127.0.0.1',
	'mysql_user' => 'root',
	'mysql_pass' => '',
	'mysql_base' => 'test',
	'mysql_persist' => FALSE,
);

