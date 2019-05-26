<?php
return [
    'language' => 'ru',
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
//    'components' => [
//        'cache' => [
//            'class' => 'yii\caching\FileCache',
//        ],
//    ],
    'components' => [
//    'user' => [
//        'identityClass' => 'budyaga\users\models\User',
//        'enableAutoLogin' => true,
//        'loginUrl' => ['/login'],
//    ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
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
    'urlManager' => [
        'enablePrettyUrl' => true,
        'showScriptName' => false,
        'rules' => [
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
            '/oauth/<authclient:[\w\-]+>' => '/user/auth/index'
        ],
    ],
    'authManager' => [
        'class' => 'yii\rbac\DbManager',
    ],
],
'modules' => [
    'user' => [
        'class' => 'budyaga\users\Module',
        'userPhotoUrl' => 'http://isp/uploads/img/imgavatar',
        'userPhotoPath' => '@frontend/web/uploads/img/imgavatar'
    ],
],
    
];
