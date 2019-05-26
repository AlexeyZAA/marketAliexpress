<?php
namespace frontend\components\cmsapi\libs;

use frontend\components\cmsapi\libs\clCacheFake;

class clCacheAPC extends clCacheFake {
	//======================================================================
	// Конструктор
	public function __construct() {
		parent::__construct();
	}
	//======================================================================
	
	//======================================================================
	// Получение данных по ключу
	public function Get($key) {
		$rv = apc_fetch($key);
		return $rv ? unserialize($rv) : FALSE;
	}
	//======================================================================
	
	//======================================================================
	// Запись данных в кэш
	public function Set($key, $data, $ttl = 86400) {
		$rv = apc_store($key, serialize($data), $ttl);
		return $rv ? TRUE : FALSE;
	}
	//======================================================================
}
