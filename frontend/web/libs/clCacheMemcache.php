<?php

include_once dirname(__FILE__) . '/clCacheFake.php';

class clCacheMemcache extends clCacheFake {
	private $cache_object;
	//======================================================================
	// Конструктор
	public function __construct($host, $port, $persist = TRUE) {
		parent::__construct();
		// Создаём внутренний объект для работы с memcached
		$this->cache_object = new Memcache;
		// Если необходимо устойчивое соединение
		if ($persist) {
			// Создаём устойчивое
			$this->cache_object->pconnect($host, $port);
		}
		// В противном случае
		else {
			// Создаём обычное
			$this->cache_object->connect($host, $port);
		}
		
	}
	//======================================================================
	
	//======================================================================
	// Получение данных по ключу
	public function Get($key) {
		$rv = $this->cache_object->get($key);
		return $rv ? unserialize($rv) : FALSE;
	}
	//======================================================================
	
	//======================================================================
	// Запись данных в кэш
	public function Set($key, $data, $ttl = 86400) {
		$rv = $this->cache_object->set($key, serialize($data), MEMCACHE_COMPRESSED, $ttl);
		return $rv ? TRUE : FALSE;
	}
	//======================================================================
}
