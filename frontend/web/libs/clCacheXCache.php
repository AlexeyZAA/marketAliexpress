<?php

include_once dirname(__FILE__) . '/clCacheFake.php';

class clCacheXCache extends clCacheFake {
	//======================================================================
	// Конструктор
	public function __construct() {
		parent::__construct();
	}
	//======================================================================
	
	//======================================================================
	// Получение данных по ключу
	public function Get($key) {
		$rv = xcache_get($key);
		return $rv ? unserialize($rv) : FALSE;
	}
	//======================================================================
	
	//======================================================================
	// Запись данных в кэш
	public function Set($key, $data, $ttl = 86400) {
		$rv = xcache_set($key, serialize($data), $ttl);
		return $rv ? TRUE : FALSE;
	}
	//======================================================================
}
