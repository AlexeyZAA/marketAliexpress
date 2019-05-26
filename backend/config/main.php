<?php

$params = array_merge(
        require(__DIR__ . '/../../common/config/params.php'), require(__DIR__ . '/../../common/config/params-local.php'), require(__DIR__ . '/params.php'), require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-backend',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'backend\controllers',
    'bootstrap' => ['log'],
        'components' => [
        'user' => [
        'identityClass' => 'budyaga\users\models\User',
        'enableAutoLogin' => true,
        'loginUrl' => ['/login'],
        ],
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
                    '' => 'site/',
                    '/signup' => '/user/user/signup',
                    '/login' => '/user/user/login',
                    '/logout' => '/user/user/logout',
                    '/requestPasswordReset' => '/user/user/request-password-reset',
                    '/resetPassword' => '/user/user/reset-password',
                    '/profile' => '/user/user/profile',
                    '/profileadm' => '/user/user/profileadm',
                    '/profileuser' => '/user/user/profileuser',
                    '/retryConfirmEmail' => '/user/user/retry-confirm-email',
                    '/confirmEmail' => '/user/user/confirm-email',
                    '/unbind/<id:[\w\-]+>' => '/user/auth/unbind',
                    '/oauth/<authclient:[\w\-]+>' => '/user/auth/index',
                ],
            ],
            'authManager' => [
                'class' => 'yii\rbac\DbManager',
            ],
            'assetManager' => [
                'basePath' => '@webroot/assets',
                'baseUrl' => '@web/assets'
            ],
            'request' => [
                'baseUrl' => '/admin'
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
