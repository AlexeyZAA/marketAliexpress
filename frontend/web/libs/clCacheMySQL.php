<?php

include_once dirname(__FILE__) . '/clCacheFake.php';

class clCacheMySQL extends clCacheFake {
	// Объект для работы с БД
	private $db = FALSE;
	// Имя таблицы кэша
	const CACHE_TABLE_NAME = 'ali_data_cache';

	//======================================================================
	// Конструктор
	public function __construct($host, $user, $pass, $base, $persist) {
		parent::__construct();
		// Пытаемся соединиться с БД
		$this->db = new mysqli(
				$persist ? "p:$host" : $host,
				$user,
				$pass,
				$base
			);
		// Если что-то не так
		if ($this->db->connect_error) {
			die("Problem with MySQL. Code: " . $this->db->connect_errno . ". Description: " . $this->db->connect_error);
		}
		// Создаём таблицу для кэша (если она ещё не существует)
		$sql = 'CREATE TABLE IF NOT EXISTS `' . self::CACHE_TABLE_NAME . '` (
				`id` int(11) NOT NULL AUTO_INCREMENT,
				`key` varchar(32) NOT NULL DEFAULT \'\',
				`value` varchar(16384) NOT NULL DEFAULT \'\',
				`ttl` TIMESTAMP,
				PRIMARY KEY (`id`),
				UNIQUE KEY `key`  (`key`)
			) ENGINE=InnoDB CHARSET=utf8';
		$this->db->query($sql);
	}
	//======================================================================
	
	//======================================================================
	// Получение данных по ключу
	public function Get($key) {
		// Возвращаемый результат
		$rv = FALSE;
		
		// Экранируем данные
		$key = $this->db->real_escape_string($key);
		
		// Строим запрос
		$sql = "SELECT * FROM `" . self::CACHE_TABLE_NAME . "`
				WHERE `key` = MD5('$key') AND `ttl` >= NOW()";
		// Выполняем запрос
		if ($result = $this->db->query($sql)) {
			// Если есть данные
			if ($result->num_rows) {
				// Используем их
				$rv_tmp = $result->fetch_assoc();
				$rv = json_decode($rv_tmp['value'], TRUE);
			}
			// Освобождаем ресурсы
			$result->free_result();
		}
		// Возвращаем результат
		return $rv;
	}
	//======================================================================
	
	//======================================================================
	// Запись данных в кэш
	public function Set($key, $data, $ttl = 86400) {
		// Сериализуем данные
		$data = json_encode($data);
		// Слишком длинные строки тупо не поместятся в соответствующее поле!
		if (mb_strlen($data) > 16384) return FALSE;
		// Экранируем данные
		$data = $this->db->real_escape_string($data);
		$key = $this->db->real_escape_string($key);
		$ttl = intval($ttl);
		// Формируем запрос
		$sql = "INSERT INTO `" . self::CACHE_TABLE_NAME . "` SET
					`key` = MD5('$key'),
					`value` = '$data',
					`ttl` = NOW() + INTERVAL $ttl SECOND
				ON DUPLICATE KEY UPDATE
					`value` = '$data',
					`ttl` = NOW() + INTERVAL $ttl SECOND";
		// Выполняем запрос
		$rv = $this->db->query($sql);
		// Возвращаем результат
		return $rv ? TRUE : FALSE;
	}
	//======================================================================
}
