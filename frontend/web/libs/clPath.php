<?php
// Объект для работы с путями
class clPath {
	private $deep_link_hash = '';
	
	// Пути 
	const PATH_OFFER    = '/good';
	const PATH_CATEGORY = '/category';
	const PATH_GO       = '/redirect';
	const PATH_SEARCH   = '/search';
	const PATH_RSS      = '/rss';
	
	// Конструктор
	public function __construct($deep_link_hash) {
		$this->deep_link_hash = $deep_link_hash;
	}
	
	// Функция для нормализации чисел
	// Написана в связи с проблемами у intval на 32-битных системах
	private function IntValue($val) {
		$val = preg_replace('{[^\d]+}ui', '', $val);
		if ($val == '') $val = 0;
		return $val;

	}
	
	// Создание строки для использования в урлах
	private function GetSafeString($s) {
		$s = mb_strtolower($s, 'UTF-8');
		$s = mb_substr($s, 0, 255, 'UTF-8');
		$s = preg_replace('/[^\pL\pN]+/u', '-', $s);
		$s = preg_replace('/^[^\pL\pN]+/u', '', $s);
		$s = preg_replace('/[^\pL\pN]+$/u', '', $s);
		return $s;
	}
	
	// Путь к товару
	public function Offer($id, $title = '') {
		$rv = self::PATH_OFFER . '/' . $this->IntValue($id);
		if ($title != '') $rv .= '-' . rawurlencode($this->GetSafeString($title));
		return $rv;
	}
	
	// Путь на редирект-скрипт
	public function Go($id) {
		return self::PATH_GO . '/' . $this->IntValue($id);
	}

	// Ссылка на RSS-ленту
	public function Rss() {
		$rv = '';
		if (isset($_SERVER['HTTP_HOST'])) {
			$rv = "http://{$_SERVER['HTTP_HOST']}";
		}
		$rv .= self::PATH_RSS;
		
		return $rv;
	}

	// Путь к категории
	public function Category($id, $title = '', $page = 1, $orderby = '') {
		$rv = self::PATH_CATEGORY . '/' . $this->IntValue($id);
		if ($title != '') $rv .= '-' . rawurlencode($this->GetSafeString($title));
		if ($page > 1) $rv .= '/' . $this->IntValue($page);
		if ($orderby != '') $rv .= '?order=' . rawurlencode($orderby);
		return $rv;
	}
	
	// Путь на поисковую выдачу
	public function Search($query = '', $page = 1, $category = 0) {
		$rv = self::PATH_SEARCH;
		if ($query != '') {
			$rv .= '?q=' . rawurlencode($query);
			if ($page > 1) {
				$rv .= '&page=' . $this->IntValue($page);
			}
			if ($category > 0) {
				$rv .= '&category=' . $this->IntValue($category);
			}
		}
		return $rv;
	}
	
	// Подготовка реферальной ссылки
	public function PrepareRefUrl($url) {
		return str_replace('__DEEPLINK-HASH__', $this->deep_link_hash, $url);
	}
}
