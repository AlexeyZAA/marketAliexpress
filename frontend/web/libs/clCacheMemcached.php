<?php

include_once dirname(__FILE__) . '/clCacheFake.php';

class clCacheMemcached extends clCacheFake {
	private $cache_object;
	//======================================================================
	// Конструктор
	public function __construct($host, $port, $persist = TRUE) {
		parent::__construct();
		// Создаём внутренний объект для работы с memcached
		// Если необходимо устойчивое соединение
		if ($persist) {
			$this->cache_object = new Memcached($this->name_space);
		}
		// В противном случае
		else {
			$this->cache_object = new Memcached();
		}
		// Если ещё недобавлено ни одного сервера
		if (!sizeof($this->cache_object->getServerList())) {
			// Добавляем сервер
			$this->cache_object->addServer($host, $port);
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
		$rv = $this->cache_object->set($key, serialize($data), $ttl);
		return $rv ? TRUE : FALSE;
	}
	//======================================================================
}
