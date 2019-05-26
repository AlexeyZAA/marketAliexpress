<?php
// Функции для работы с языковыми константами

class clLang {
	private $lang = array();
	// Конструктор
	public function __construct($lang) {
		// Путь к файлу с переводами
		$lng_file = dirname(__FILE__) . '/../lang/' . $lang . '.php';
		// Если файл с переводами существует
		if (file_exists($lng_file)) {
			// Пытаемся считать
			$lang = $this->lang;
			include $lng_file;
			// Если получилось
			if (is_array($lang)) {
				// Запоминаем язык
				$this->lang = $lang;
			}
		}
	}
	// Получение переведённой строки
	public function GetString($line) {
		return isset($this->lang[$line]) ? $this->lang[$line] : $line;
	}
}
