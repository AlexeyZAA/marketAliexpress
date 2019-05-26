<?php
// Здесь описываем все те функции, которые больше негде описать

class clCommon {
	// Конструктор
	public function __construct() {
	}
	// Получение пути к шаблону
	function GetTemplatePath($tpl) {
		$rv = dirname(__FILE__) . '/../templates/' . $tpl . '.tpl';
		return $rv;
	}
	
	// Проверка наличия необходимых модулей
	function CheckCMSRequirements () {
		// Необходимые модули и проверочные функции для них
		$check_php_modules = array(
			'curl' => 'curl_init',
			'json' => 'json_decode',
		);
		
		// Проверяем модули
		$not_found = array();
		foreach ($check_php_modules as $module => $func) {
			if (!function_exists($func)) {
				$not_found[] = $module;
			}
		}
		if (sizeof($not_found)) {
			print "Ваша инсталяция PHP не содержит в себе всех необходимых модулей. Как минимум необходимо добавить следующие модули:<br />\n";
			print "<ul>\n";
			foreach ($not_found as $module) {
				print "\t<li>$module</li>\n";
			}
			print "</ul>\n";
			exit();
		}
	}
}
