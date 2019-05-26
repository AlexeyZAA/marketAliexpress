<?php
namespace frontend\controllers;

use Yii;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\LoginForm;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResetPasswordForm;
use frontend\models\SignupForm;
use frontend\models\ContactForm;
use frontend\models\Page;
use yii\data\Pagination;

/*
 * Для списка объявлений
 */
use yii\data\ActiveDataProvider;

/**
 * Site controller
 */
class SiteController extends Controller
{
    
        /**
     * Logs in a user.
     *
     * @return mixed
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        } else {
            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }
        
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['kabinet', 'logout', 'signup'],
                'rules' => [
                    [
                        'actions' => ['signup'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['kabinet'],
                        'allow' => true,
                        'roles' => ['administrator'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return mixed
     */
public function actionIndex()
    {
/*
 * Вставка из ЦМС АПИ
 */
include_once 'common.php';

$categories_list = array();
$offers = array();


// Готовим обращение к API
//------------------------------------------------------------------------------
$cache_key_categories = $Cache->PrepareCacheKey(array(
	'for' => 'categories',
	'lang' => Yii::$app->params['lang'],
));
if (!$categories_list = $Cache->Get($cache_key_categories)) {
	$APIAccess->AddRequestCategoriesList('categories', Yii::$app->params['lang']);
}
//------------------------------------------------------------------------------

//------------------------------------------------------------------------------
$search_params = array(
		'query' => '',
		'offset' => 0,
		'limit' => Yii::$app->params['items_per_page'],
		'orderby' => 'rand',
		'price_min' => Yii::$app->params['price_min'],
		'price_max' => Yii::$app->params['price_max'],
		'lang' => Yii::$app->params['lang'],
		'currency' => Yii::$app->params['currency'],
	);

$cache_key_search = $Cache->PrepareCacheKey($search_params);

$offers_tmp = FALSE;
if (!$offers_tmp = $Cache->Get($cache_key_search)) {
	$APIAccess->AddRequestSearch('search', $search_params);
}
else {
	$offers = $offers_tmp['offers'];
	$total_offers = $offers_tmp['total_found'];
}
//------------------------------------------------------------------------------

// Выполняем запрос к API
if ($APIAccess->RunRequests()) {
	// Достаём данные
	if (($categories_list_tmp = $APIAccess->GetRequestResult('categories')) && isset($categories_list_tmp['categories'])) {
		$categories_list = $categories_list_tmp['categories'];
		$Cache->Set($cache_key_categories, $categories_list, 86400);
	}
	// Достаём данные
	if (($offers_tmp = $APIAccess->GetRequestResult('search')) && isset($offers_tmp['offers'])) {
		$offers = $offers_tmp['offers'];
		$Cache->Set($cache_key_search, $offers_tmp, 120);
	}
}
// Если что-то пошло не так и ошибка в данных, а не сетевая
elseif ($APIAccess->LastErrorType() == 'data') {
	print "Проблема при обращении к API ePN: <br />\n";
	print $APIAccess->LastError() . "\n";
	exit();
}
// Если что-то пошло не так и ошибка сетевая
elseif ($APIAccess->LastErrorType() == 'network') {
	print "Сетевая проблема при обращении к API ePN: <br />\n";
	print $APIAccess->LastError() . "\n";
	exit();
}

// Здесь будет хэш id => info
$categories_hash = array();
// Дополняем данные
foreach ($categories_list as $key => $value) {
	$categories_list[$key]['link'] = $Path->Category($value['id'], $value['title']);
	$categories_list[$key]['current'] = FALSE;
	$categories_hash[$value['id']] = $categories_list[$key];
}



// Дополняем информацию о товарах
foreach ($offers as $key => $value) {
	// Информация о категории
	$offers[$key]['category'] = isset($categories_hash[$value['id_category']]) ? $categories_hash[$value['id_category']]['title'] : '';
	$offers[$key]['category_link'] = isset($categories_hash[$value['id_category']]) ? $categories_hash[$value['id_category']]['link'] : '';
	// "Прямая" ссылка
	$offers[$key]['url'] = $Path->Go($value['id']);
	// Ссылка на более подробную информацию
	$offers[$key]['link'] = $Path->Offer($value['id'], $value['name']);
}


/*
 *  Конец АПИ
 */        
        return $this->render('index', [
            'actionurl' => $actionurl,
            'Common' => $Common,
            'offers' => $offers,
            'Lang' => $Lang,
            'categories_list' => $categories_list,
            'Path' => $Path,

        ]);
    }
    
public function actionCategory($id,$page=null, $order=null){

include_once 'common.php';

// Получаем идентификатор категории и номер страницы
$id = isset($_GET['id']) ? $_GET['id'] : 0;
$page = isset($_GET['page']) ? $_GET['page'] : 1;
if ($page < 1) $page = 1;
// Разбираемся с сортировкой
$order_line = isset($_GET['order']) ? $_GET['order'] : '';
$orderby = 'added_at';
$order_direction = 'DESC';
if ($order_line == 'price-desc') {
	$orderby = 'price';
	$order_direction = 'DESC';
}
elseif ($order_line == 'price-asc') {
	$orderby = 'price';
	$order_direction = 'ASC';
}
elseif ($order_line == 'orders') {
	$orderby = 'orders_count';
	$order_direction = 'DESC';
}
else {
	// На всякий случай. Чтобы не плодить урлы с фигнёй
	$order_line = '';
}

$categories_list = array();
$offers = array();
$total_offers = 0;


// Готовим обращение к API
//------------------------------------------------------------------------------
$cache_key_categories = $Cache->PrepareCacheKey(array(
	'for' => 'categories',
	'lang' => $settings['lang'],
));
if (!$categories_list = $Cache->Get($cache_key_categories)) {
	$APIAccess->AddRequestCategoriesList('categories', $settings['lang']);
}
//------------------------------------------------------------------------------

//------------------------------------------------------------------------------
$search_params = array(
		'query' => '',
		'offset' => 0,
		'limit' => $settings['items_per_page'],
		'offset' => ($page-1)*$settings['items_per_page'],
		'category' => $id,
		'price_min' => $settings['price_min'],
		'price_max' => $settings['price_max'],
		'orderby' => $orderby,
		'order_direction' => $order_direction,
		'lang' => $settings['lang'],
		'currency' => $settings['currency'],
	);
$cache_key_search = $Cache->PrepareCacheKey($search_params);

$offers_tmp = FALSE;
if (!$offers_tmp = $Cache->Get($cache_key_search)) {
	$APIAccess->AddRequestSearch('search', $search_params);
}
else {
	$offers = $offers_tmp['offers'];
	$total_offers = $offers_tmp['total_found'];
}
//------------------------------------------------------------------------------


// Выполняем запрос к API
if ($APIAccess->RunRequests()) {
	// Достаём данные
	if (($categories_list_tmp = $APIAccess->GetRequestResult('categories')) && isset($categories_list_tmp['categories'])) {
		$categories_list = $categories_list_tmp['categories'];
		$Cache->Set($cache_key_categories, $categories_list, 86400);
	}
	// Достаём данные
	if (($offers_tmp = $APIAccess->GetRequestResult('search')) && isset($offers_tmp['offers'])) {
		$offers = $offers_tmp['offers'];
		$total_offers = $offers_tmp['total_found'];
		$Cache->Set($cache_key_search, $offers_tmp, 14400);
	}
}
// Если что-то пошло не так и ошибка в данных, а не сетевая
elseif ($APIAccess->LastErrorType() == 'data') {
	print "Проблема при обращении к API ePN: <br />\n";
	print $APIAccess->LastError() . "\n";
	exit();
}
// Если что-то пошло не так и ошибка сетевая
elseif ($APIAccess->LastErrorType() == 'network') {
	print "Сетевая проблема при обращении к API ePN: <br />\n";
	print $APIAccess->LastError() . "\n";
	exit();
}



// Здесь будет хэш id => info
$categories_hash = array();
// Здесь будет информация о текущей категории
$current_category = FALSE;
// Дополняем данные
foreach ($categories_list as $key => $value) {
	$categories_list[$key]['link'] = $Path->Category($value['id'], $value['title']);
	$categories_list[$key]['current'] = ($value['id'] == $id);
	$categories_hash[$value['id']] = $categories_list[$key];
	if ($value['id'] == $id) {
		$current_category = $categories_list[$key];
		$current_category['count'] = $total_offers;
	}
}

// Дополняем информацию о товарах
foreach ($offers as $key => $value) {
	// Информация о категории
	$offers[$key]['category'] = isset($categories_hash[$value['id_category']]) ? $categories_hash[$value['id_category']]['title'] : '';
	$offers[$key]['category_link'] = isset($categories_hash[$value['id_category']]) ? $categories_hash[$value['id_category']]['link'] : '';
	// "Прямая" ссылка
	$offers[$key]['url'] = $Path->Go($value['id']);
	// Ссылка на более подробную информацию
	$offers[$key]['link'] = $Path->Offer($value['id'], $value['name']);
}

// Строим пейджер
$pages = array();
$orders = array();
// Общее число страниц
$page_count = ceil($total_offers / $settings['items_per_page']);
// А нужен ли вообще пейджер и сортировоки?
if ($current_category && $page_count > 1) {
	$page_min = $page - 4;
	if ($page_min < 1) $page_min = 1;
	
	$page_max = $page + 4;
	if ($page_max > $page_count) $page_max = $page_count;
	
	if ($page_min > 1) {
		$pages[] = array(
				'page' => '<<',
				'link' => $Path->Category($id, 'category', 1, $order_line),
				//'link' => $Path->Category($id, $current_category['title'], 1, $order_line),
			);
	}
	
	if ($page > 1) {
		$pages[] = array(
				'page' => '<',
				'link' => $Path->Category($id, 'category', $page - 1, $order_line),
				//'link' => $Path->Category($id, $current_category['title'], $page - 1, $order_line),
			);
	}
	
	for ($i = $page_min; $i <= $page_max; $i++) {
		$pages[] = array(
				'page' => $i,
				'link' => $i == $page ? '' : $Path->Category($id, 'category', $i, $order_line),
				//'link' => $i == $page ? '' : $Path->Category($id, $current_category['title'], $i, $order_line),
			);
	}

	if ($page < $page_count) {
		$pages[] = array(
				'page' => '>',
				'link' => $Path->Category($id, 'category', $page + 1, $order_line),
				//'link' => $Path->Category($id, $current_category['title'], $page + 1, $order_line),
			);
	}
	
	if ($page_max < $page_count) {
		$pages[] = array(
				'page' => '>>',
				'link' => $Path->Category($id, 'category', $page_count, $order_line),
				//'link' => $Path->Category($id, $current_category['title'], $page_count, $order_line),
			);
	}
	
	// Сортировки
	$orders = array(
		array(
			'title' => $Lang->GetString('default'),
			'order' => '',
			'nofollow' => FALSE,
		),
		array(
			'title' => $Lang->GetString('price lower'),
			'order' => 'price-asc',
			'nofollow' => TRUE,
		),
		array(
			'title' => $Lang->GetString('price higher'),
			'order' => 'price-desc',
			'nofollow' => TRUE,
		),
		array(
			'title' => $Lang->GetString('popular'),
			'order' => 'orders',
			'nofollow' => TRUE,
		),
	);
	foreach ($orders as $key => $value) {
		$orders[$key]['url'] = $Path->Category($id, $current_category['title'], 1, $value['order']);
		$orders[$key]['current'] = ($value['order'] == $order_line);
	}
}

$pagesp = new Pagination(['totalCount' => $page_count]);
   
        return $this->render('category', [
                'Lang' => $Lang,
                'categories_list' => $categories_list,
                'Path' => $Path,
                'current_category' => $current_category,
                'id' => $id,
                'orders' => $orders,
                'offers' => $offers,
                'pages' => $pages,
		'order_line' => $order_line,
                'pagesp' => $pagesp,
        ]);
        }
     
public function actionGo($id)
    {
		
include_once 'common.php';

// Получаем идентификатор
$id = isset($_GET['id']) ? $_GET['id'] : 0;

// Сюда мы будем редиректить пользователя
$redirect_url = '/';

$offer_info = FALSE;

// Готовим обращение к API
//------------------------------------------------------------------------------
$cache_key_offer_info = $Cache->PrepareCacheKey(array(
	'for' => 'offer',
	'lang' => $settings['lang'],
	'currency' => $settings['currency'],
	'id' => $id
));
if (!$offer_info = $Cache->Get($cache_key_offer_info)) {
	$APIAccess->AddRequestGetOfferInfo('info', $id, $settings['lang'], $settings['currency']);
}
//------------------------------------------------------------------------------

// Выполняем запрос к API
if ($APIAccess->RunRequests()) {
	// Достаём данные
	if (($offer_info_tmp = $APIAccess->GetRequestResult('info')) && isset($offer_info_tmp['offer'])) {
		$offer_info = $offer_info_tmp['offer'];
		$Cache->Set($cache_key_offer_info, $offer_info, 86400);
	}
}
// Если что-то пошло не так и ошибка в данных, а не сетевая
elseif ($APIAccess->LastErrorType() == 'data') {
	print "Проблема при обращении к API ePN: <br />\n";
	print $APIAccess->LastError() . "\n";
	exit();
}
// Если что-то пошло не так и ошибка сетевая
elseif ($APIAccess->LastErrorType() == 'network') {
	print "Сетевая проблема при обращении к API ePN: <br />\n";
	print $APIAccess->LastError() . "\n";
	exit();
}



// Если информация о товаре была получена
if ($offer_info) {
	// Используем реферальный урл
	$redirect_url = $Path->PrepareRefUrl($offer_info['url']);
}

// Выполняем редирект
Header("Location: $redirect_url", TRUE, 302);

// Для особо тупых браузеров отдадим ещё и немного данных
print "<html>\n<head>\n";
// Некоторые поймут так
print "<meta http-equiv=\"refresh\" content=\"3; url=" . htmlspecialchars($redirect_url) . "\">\n";
print "</head>\n<body>\n";
// А некоторым может понадобиться и вот такой велосипед
print "Page moved <a id=\"mainlink\" href=\"" . htmlspecialchars($redirect_url) . "\">here</a>.";
print "<script type=\"text/javascript\">\n";
print "window.location.href = document.getElementById(\"mainlink\").href;\n";
print "</script>\n";
print "</body>\n</html>\n";
		
        return;
    }	 
	
public function actionGood($id){
	
include_once 'common.php';

// Получаем идентификатор
$id = isset($_GET['id']) ? $_GET['id'] : 0;

$categories_list = array();
$offer_info = FALSE;
$offers = array();

// Готовим обращение к API
//------------------------------------------------------------------------------
$cache_key_categories = $Cache->PrepareCacheKey(array(
	'for' => 'categories',
	'lang' => $settings['lang'],
));
if (!$categories_list = $Cache->Get($cache_key_categories)) {
	$APIAccess->AddRequestCategoriesList('categories', $settings['lang']);
}
//------------------------------------------------------------------------------


//------------------------------------------------------------------------------
$cache_key_offer_info = $Cache->PrepareCacheKey(array(
	'for' => 'offer',
	'lang' => $settings['lang'],
	'currency' => $settings['currency'],
	'id' => $id
));
if (!$offer_info = $Cache->Get($cache_key_offer_info)) {
	$APIAccess->AddRequestGetOfferInfo('info', $id, $settings['lang'], $settings['currency']);
}
//------------------------------------------------------------------------------

//------------------------------------------------------------------------------
$search_params = array(
		'query' => '',
		'offset' => 0,
		'limit' => 3,
		'orderby' => 'rand',
		'lang' => $settings['lang'],
		'currency' => $settings['currency'],
	);
$cache_key_search = $Cache->PrepareCacheKey($search_params);

$offers_tmp = FALSE;
if (!$offers_tmp = $Cache->Get($cache_key_search)) {
	$APIAccess->AddRequestSearch('search', $search_params);
}
else {
	$offers = $offers_tmp['offers'];
	$total_offers = $offers_tmp['total_found'];
}

//------------------------------------------------------------------------------

// Выполняем запрос к API
if ($APIAccess->RunRequests()) {
	// Достаём данные
	if (($categories_list_tmp = $APIAccess->GetRequestResult('categories')) && isset($categories_list_tmp['categories'])) {
		$categories_list = $categories_list_tmp['categories'];
		$Cache->Set($cache_key_categories, $categories_list, 86400);
	}
	// Достаём данные
	if (($offer_info_tmp = $APIAccess->GetRequestResult('info')) && isset($offer_info_tmp['offer'])) {
		$offer_info = $offer_info_tmp['offer'];
		$Cache->Set($cache_key_offer_info, $offer_info, 86400);
	}
	// Достаём данные
	if (($offers_tmp = $APIAccess->GetRequestResult('search')) && isset($offers_tmp['offers'])) {
		$offers = $offers_tmp['offers'];
		$Cache->Set($cache_key_search, $offers_tmp, 120);
	}
}
// Если что-то пошло не так и ошибка в данных, а не сетевая
elseif ($APIAccess->LastErrorType() == 'data') {
	print "Проблема при обращении к API ePN: <br />\n";
	print $APIAccess->LastError() . "\n";
	exit();
}
// Если что-то пошло не так и ошибка сетевая
elseif ($APIAccess->LastErrorType() == 'network') {
	print "Сетевая проблема при обращении к API ePN: <br />\n";
	print $APIAccess->LastError() . "\n";
	exit();
}



// Здесь будет хэш id => info
$categories_hash = array();
// Дополняем данные
foreach ($categories_list as $key => $value) {
	$categories_list[$key]['link'] = $Path->Category($value['id'], $value['title']);
	$categories_list[$key]['current'] = FALSE;
	$categories_hash[$value['id']] = $categories_list[$key];
}



// Если информация о товаре была получена
if ($offer_info) {
	// Дополняем данные
	// "Прямая" ссылка
	$offer_info['url'] = $Path->Go($offer_info['id']);
	// Информация о категории
	$offer_info['category'] = isset($categories_hash[$offer_info['id_category']]) ? $categories_hash[$offer_info['id_category']]['title'] : '';
	$offer_info['category_link'] = isset($categories_hash[$offer_info['id_category']]) ? $categories_hash[$offer_info['id_category']]['link'] : '';
	
	// Дополняем информацию о товарах
	foreach ($offers as $key => $value) {
		// Информация о категории
		$offers[$key]['category'] = isset($categories_hash[$value['id_category']]) ? $categories_hash[$value['id_category']]['title'] : '';
		$offers[$key]['category_link'] = isset($categories_hash[$value['id_category']]) ? $categories_hash[$value['id_category']]['link'] : '';
		// "Прямая" ссылка
		$offers[$key]['url'] = $Path->Go($value['id']);
		// Ссылка на более подробную информацию
		$offers[$key]['link'] = $Path->Offer($value['id'], $value['name']);
	}

}

return $this->render('good',[
	'id' => $id,
	'Lang' => $Lang,
	'categories_list' => $categories_list,
	'Path' => $Path,
        'offer_info' => $offer_info,
	
]);

}	
    
public function actionSearch(){
	
include_once 'common.php';

// Получаем поисковый запрос
$search_query = isset($_GET['q']) ? $_GET['q'] : '';
$category = isset($_GET['category']) ? $_GET['category'] : 0;
$page = isset($_GET['page']) ? $_GET['page'] : 1;
if ($page < 1) $page = 1;

$categories_list = array();
$offers = array();
$total_offers = 0;
$search_categories_count = array();
$search_count_hash = array();


// Готовим обращение к API
//------------------------------------------------------------------------------
$cache_key_categories = $Cache->PrepareCacheKey(array(
	'for' => 'categories',
	'lang' => $settings['lang'],
));
if (!$categories_list = $Cache->Get($cache_key_categories)) {
	$APIAccess->AddRequestCategoriesList('categories', $settings['lang']);
}
//------------------------------------------------------------------------------

//------------------------------------------------------------------------------
$search_params = array(
		'query' => $search_query,
		'limit' => $settings['items_per_page'],
		'offset' => ($page-1)*$settings['items_per_page'],
		'price_min' => $settings['price_min'],
		'price_max' => $settings['price_max'],
		'category' => ($category > 0) ? $category : '',
		'lang' => $settings['lang'],
		'currency' => $settings['currency'],
	);
$cache_key_search = $Cache->PrepareCacheKey($search_params);

$offers_tmp = FALSE;
if (!$offers_tmp = $Cache->Get($cache_key_search)) {
	$APIAccess->AddRequestSearch('search', $search_params);
}
else {
	$offers = $offers_tmp['offers'];
	$total_offers = $offers_tmp['total_found'];
}
//------------------------------------------------------------------------------

//------------------------------------------------------------------------------
$search_count_params = array(
		'query' => $search_query,
		'price_min' => $settings['price_min'],
		'price_max' => $settings['price_max'],
		'lang' => $settings['lang'],
	);
	
$cache_key_search_count = $Cache->PrepareCacheKey($search_count_params);
if (!$search_count_hash = $Cache->Get($cache_key_search_count)) {
	$APIAccess->AddRequestCountForSearch('search_count', $search_count_params);
}
//------------------------------------------------------------------------------


// Выполняем запрос к API
if ($APIAccess->RunRequests()) {
	// Достаём данные
	if (($categories_list_tmp = $APIAccess->GetRequestResult('categories')) && isset($categories_list_tmp['categories'])) {
		$categories_list = $categories_list_tmp['categories'];
		$Cache->Set($cache_key_categories, $categories_list, 86400);
	}
	if (($offers_tmp = $APIAccess->GetRequestResult('search')) && isset($offers_tmp['offers'])) {
		$offers = $offers_tmp['offers'];
		$total_offers = $offers_tmp['total_found'];
		$Cache->Set($cache_key_search, $offers_tmp, 14400);
	}
	if (($search_count_hash_tmp = $APIAccess->GetRequestResult('search_count')) && isset($search_count_hash_tmp['count'])) {
		$search_count_hash = $search_count_hash_tmp['count'];
		$Cache->Set($cache_key_search_count, $search_count_hash, 14400);
	}
}
// Если что-то пошло не так и ошибка в данных, а не сетевая
elseif ($APIAccess->LastErrorType() == 'data') {
	print "Проблема при обращении к API ePN: <br />\n";
	print $APIAccess->LastError() . "\n";
	exit();
}
// Если что-то пошло не так и ошибка сетевая
elseif ($APIAccess->LastErrorType() == 'network') {
	print "Сетевая проблема при обращении к API ePN: <br />\n";
	print $APIAccess->LastError() . "\n";
	exit();
}


// Здесь будет хэш id => info
$categories_hash = array();
// Дополняем данные
foreach ($categories_list as $key => $value) {
	$categories_list[$key]['link'] = $Path->Category($value['id'], $value['title']);
	$categories_list[$key]['current'] = FALSE;
	$categories_hash[$value['id']] = $categories_list[$key];
}
//==============================================================================
// Строим структуру с описанием количества товаров в категориях
$categories_total = 0;
foreach ($search_count_hash as $key => $value) {
	if (isset($categories_hash[$key])) {
		$item = $categories_hash[$key];
		$item['count'] = $value;
		$item['link'] = $Path->Search($search_query, 1, $key);
		$item['current'] = $key == $category ? TRUE : FALSE;
		$search_categories_count[] = $item;
		$categories_total += $item['count'];
	}
}
$item = array(
	'title' => $Lang->GetString('All'),
	'count' => $categories_total,
	'current' => $category == 0 ? TRUE : FALSE,
	'link' => $Path->Search($search_query)
);
array_unshift($search_categories_count, $item);
//==============================================================================

/*
print "<!--\n";
print_r($search_categories_count);
print "\n-->\n";
*/

// Дополняем информацию о товарах
foreach ($offers as $key => $value) {
	// Информация о категории
	$offers[$key]['category'] = isset($categories_hash[$value['id_category']]) ? $categories_hash[$value['id_category']]['title'] : '';
	$offers[$key]['category_link'] = isset($categories_hash[$value['id_category']]) ? $categories_hash[$value['id_category']]['link'] : '';
	// "Прямая" ссылка
	$offers[$key]['url'] = $Path->Go($value['id']);
	// Ссылка на более подробную информацию
	$offers[$key]['link'] = $Path->Offer($value['id'], $value['name']);
}

// Строим пейджер
$pages = array();
// Общее число страниц
$page_count = ceil($total_offers / $settings['items_per_page']);
// А нужен ли вообще пейджер
if ($page_count > 1) {
	$page_min = $page - 4;
	if ($page_min < 1) $page_min = 1;
	
	$page_max = $page + 4;
	if ($page_max > $page_count) $page_max = $page_count;
	
	if ($page_min > 1) {
		$pages[] = array(
				'page' => '<<',
				'link' => $Path->Search($search_query, 1, $category),
			);
	}
	
	if ($page > 1) {
		$pages[] = array(
				'page' => '<',
				'link' => $Path->Search($search_query, $page - 1, $category),
			);
	}
	
	for ($i = $page_min; $i <= $page_max; $i++) {
		$pages[] = array(
				'page' => $i,
				'link' => $i == $page ? '' : $Path->Search($search_query, $i, $category),
			);
	}

	if ($page < $page_count) {
		$pages[] = array(
				'page' => '>',
				'link' => $Path->Search($search_query, $page + 1, $category),
			);
	}
	
	if ($page_max < $page_count) {
		$pages[] = array(
				'page' => '>>',
				'link' => $Path->Search($search_query, $page_count, $category),
			);
	}
}

	
	return $this->render('search', [
            'Lang' => $Lang,
            'categories_list' => $categories_list,
            'Path' => $Path,
            'search_query' => $search_query,
            'offers' => $offers,
            'pages' => $pages,
            'search_count_hash' => $search_count_hash,
            'search_categories_count' => $search_categories_count,
        ]);
	
	
}	
	
	/*Экшн для личного кабинета*/
     public function actionKabinet(){
        return $this->render('kabinet');
    }
      
    /**
     * Logs out the current user.
     *
     * @return mixed
     */
    public function actionLogout(){
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return mixed
     */
    public function actionContact(){
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail(Yii::$app->params['adminEmail'])) {
                Yii::$app->session->setFlash('success', 'Благодарим Вас за обращение к нам. Мы ответим как можно скорее.');
            } else {
                Yii::$app->session->setFlash('error', 'Произошла ошибка при отправке email.');
            }

            return $this->refresh();
        } else {
            return $this->render('contact', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Displays about page.
     *
     * @return mixed
     */
    public function actionAbout(){
        return $this->render('about');
    }

    /**
     * Signs user up.
     *
     * @return mixed
     */
    public function actionSignup()
    {
        $model = new SignupForm();
        if ($model->load(Yii::$app->request->post())) {
            if ($user = $model->signup()) {
                if (Yii::$app->getUser()->login($user)) {
                    return $this->goHome();
                }
            }
        }

        return $this->render('signup', [
            'model' => $model,
        ]);
    }

    /**
     * Requests password reset.
     *
     * @return mixed
     */
    public function actionRequestPasswordReset()
    {
        $model = new PasswordResetRequestForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'Проверьте свою электронную почту для получения дальнейших инструкций.');

                return $this->goHome();
            } else {
                Yii::$app->session->setFlash('error', 'К сожалению, мы не можем сбросить пароль.');
            }
        }

        return $this->render('requestPasswordResetToken', [
            'model' => $model,
        ]);
    }

    /**
     * Resets password.
     *
     * @param string $token
     * @return mixed
     * @throws BadRequestHttpException
     */
    public function actionResetPassword($token)
    {
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidParamException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->session->setFlash('success', 'Новый пароль сохранен.');

            return $this->goHome();
        }

        return $this->render('resetPassword', [
            'model' => $model,
        ]);
    }
    
}
