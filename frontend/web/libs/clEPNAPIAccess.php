<?php

// Класс для работы с API EPN
class clEPNAPIAccess {
	const EPN_API_URL = 'http://api.epn.bz/json';
	const EPN_CLIENT_API_VERSION = 2;
	
	// Параметры
	private $user_api_key = 0;
	private $user_hash = '';
	private $prepared_requests = array();
	private $request_results = array();
	private $last_error = '';
	private $last_error_type = 'none';
	//======================================================================
	// Конструктор
	public function __construct($user_api_key, $user_hash) {
		$this->user_api_key = $user_api_key;
		$this->user_hash = $user_hash;
        }
        //======================================================================
        
        //======================================================================
        // Добавление запроса в список
        private function AddRequest($name, $action, $params = array()) {
		// Нормализуем входные данные
		if (!is_array($params)) {
			$params = array();
		}
		$params['action'] = $action;
		$this->prepared_requests[$name] = $params;
		return TRUE;
        }
        //======================================================================
        
        //======================================================================
        // Добавление запроса на получение списка категорий
        public function AddRequestCategoriesList($name, $lang = 'en') {
		$this->AddRequest(
				$name,
				'list_categories',
				array(
					'lang' => $lang
				)
			);
		return TRUE;
        }
        //======================================================================
        
        //======================================================================
        // Добавление запроса на получение списка категорий
        public function AddRequestCurrenciesList($name) {
		$this->AddRequest(
				$name,
				'list_currencies',
				array()
			);
		return TRUE;
        }
        //======================================================================
        
        //======================================================================
        // Запрос на поиск
        public function AddRequestSearch($name, $options = array()) {
		// Здесь надо бы написать валидацию опций. Кто сделает тому шоколадка.
		/*
		...
		*/
		// Добавляем запрос в список
		$this->AddRequest($name, 'search', $options);
		return TRUE;
        }
        //======================================================================
        
        //======================================================================
        // Запрос на получение количества товаров в категориях
        public function AddRequestCountForSearch($name, $options = array()) {
		// Здесь надо бы написать валидацию опций. Кто сделает тому шоколадка.
		/*
		...
		*/
		// Добавляем запрос в список
		$this->AddRequest($name, 'count_for_categories', $options);
		return TRUE;
        }
        //======================================================================
        
        //======================================================================
        // Запрос на получение информации о товаре
        public function AddRequestGetOfferInfo($name, $id, $lang = 'en', $currency = 'USD') {
		// На всякий случай Нормализуем то что на входе
		// intval не используем в связи с проблемами на 32-битных системах
		$id = preg_replace('{[^\d]+}ui', '', $id);
		if ($id == '') $id = 0;
		// Добавим запрос в список
		$this->AddRequest(
				$name,
				'offer_info',
				array(
					'id' => $id,
					'lang' => $lang,
					'currency' => $currency,
				)
			);
		return TRUE;
        }
        //======================================================================
        
        //======================================================================
        // Получение топовых товаров
        public function AddRequestGetTopMonthly($name, $lang = 'en', $currency = 'USD', $orderby = 'sales', $category = '') {
		$this->AddRequest(
			$name,
			'top_monthly',
			array(
				'lang' => $lang,
				'currency' => $currency,
				'orderby' => $orderby,
				'category' => $category,
			)
		);
        }
        //======================================================================
        
        //======================================================================
        // Выполнение всех запросов
        public function RunRequests() {
		// Сбрасываем переменные
		$this->request_results = array();
		$this->last_error = '';
		$this->last_error_type = 'none';
		
		// Если список запросов пуст
		if (!sizeof($this->prepared_requests)) {
			return TRUE;
		}
        
		// Структура для отправки запросса
		$data = array(
			'user_api_key' => $this->user_api_key,
			'user_hash' => $this->user_hash,
			'api_version' => self::EPN_CLIENT_API_VERSION,
			'requests' => $this->prepared_requests,
		);
		//print_r($this->prepared_requests);
		// Строка запроса
		$post_data = json_encode($data);
		// Будем использовать cURL
		$ch = curl_init();
		// Выполняем запрос
		curl_setopt($ch, CURLOPT_URL,            self::EPN_API_URL);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST,           1);
		curl_setopt($ch, CURLOPT_POSTFIELDS,     $post_data); 
		curl_setopt($ch, CURLOPT_HTTPHEADER,     array('Content-Type: text/plain'));
		$result = curl_exec($ch);
		$curl_error_msg = curl_error($ch);
		//print "<!-- $curl_error_msg\n\n$result -->\n";
		// Если http-запрос обработан с ошибкой
		if ($curl_error_msg != '') {
			$this->last_error = $curl_error_msg;
			$this->last_error_type = 'network';
		}
		else {
			// Парсим данные
			$result_data = json_decode($result, TRUE);
			$this->last_error = isset($result_data['error']) ? $result_data['error'] : '';
			if ($this->last_error != '') {
				$this->last_error_type = 'data';
			}
			$this->request_results = isset($result_data['results']) && is_array($result_data['results']) ? $result_data['results'] : array();
		}
		// Независимо от результата сбрасываем список запросов
		$this->prepared_requests = array();
		// Если не было ошибок то всё хорошо
		return $this->last_error == '' ? TRUE : FALSE;
		
	}
        //======================================================================
        
        //======================================================================
        // Получение отклика
        public function GetRequestResult($name) {
		return isset($this->request_results[$name]) ? $this->request_results[$name] : FALSE;
        }
        //======================================================================
        
	//======================================================================
	// Информация о последней ошибке
	public function LastError() {
		return $this->last_error;
	}
	//======================================================================
	//======================================================================
	// Информация о типе последней ошибки
	public function LastErrorType() {
		return $this->last_error_type;
	}
	//======================================================================
}
