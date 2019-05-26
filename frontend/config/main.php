<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);


return [
    'id' => 'app-frontend',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'frontend\controllers',
    'components' => [
    'user' => [
        'identityClass' => 'budyaga\users\models\User',
        'enableAutoLogin' => true,
        'loginUrl' => ['/login'],
    ],
        'authClientCollection' => [
        'class' => 'yii\authclient\Collection',
        'clients' => [
            'vkontakte' => [
                'class' => 'budyaga\users\components\oauth\VKontakte',
                'clientId' => 'XXX',
                'clientSecret' => 'XXX',
                'scope' => 'email'
            ],
            'google' => [
                'class' => 'budyaga\users\components\oauth\Google',
                'clientId' => 'XXX',
                'clientSecret' => 'XXX',
            ],
            'facebook' => [
                'class' => 'budyaga\users\components\oauth\Facebook',
                'clientId' => 'XXX',
                'clientSecret' => 'XXX',
            ],
            'github' => [
                'class' => 'budyaga\users\components\oauth\GitHub',
                'clientId' => 'XXX',
                'clientSecret' => 'XXX',
                'scope' => 'user:email, user'
            ],
            'linkedin' => [
                'class' => 'budyaga\users\components\oauth\LinkedIn',
                'clientId' => 'XXX',
                'clientSecret' => 'XXX',
            ],
            'live' => [
                'class' => 'budyaga\users\components\oauth\Live',
                'clientId' => 'XXX',
                'clientSecret' => 'XXX',
            ],
            'yandex' => [
                'class' => 'budyaga\users\components\oauth\Yandex',
                'clientId' => 'XXX',
                'clientSecret' => 'XXX',
            ],
            'twitter' => [
                'class' => 'budyaga\users\components\oauth\Twitter',
                'consumerKey' => 'XXX',
                'consumerSecret' => 'XXX',
            ],
        ],
            ],
//        'user' => [
//            'identityClass' => 'common\models\User',
//            'enableAutoLogin' => true,
//        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                '' => 'site/index',
                ''=>'site/',
            '/signup' => '/user/user/signup',
            '/login' => '/user/user/login',
            '/logout' => '/user/user/logout',
            '/requestPasswordReset' => '/user/user/request-password-reset',
            '/resetPassword' => '/user/user/reset-password',
            '/profile' => '/user/user/profile',
            '/profileuser' => '/user/user/profileuser',
            '/retryConfirmEmail' => '/user/user/retry-confirm-email',
            '/confirmEmail' => '/user/user/confirm-email',
            '/unbind/<id:[\w\-]+>' => '/user/auth/unbind',
            '/oauth/<authclient:[\w\-]+>' => '/user/auth/index',
            '<action:\w+>' => 'site/<action>',
            ]
        ],
        
        'authManager' => [
        'class' => 'yii\rbac\DbManager',
    ],
        'assetManager' => [
            'basePath' => '@webroot/assets',
            'baseUrl' => '@web/assets'
        ],
        'request' => [
            'baseUrl' => ''
        ]
    ],
  
'modules' => [
    'user' => [
        'class' => 'budyaga\users\Module',
        'userPhotoUrl' => '/uploads/img/imgavatar',
        'userPhotoPath' => '@frontend/web/uploads/img/imgavatar'
    ],
    ],
    'params' => $params,
];
