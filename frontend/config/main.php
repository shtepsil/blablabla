<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);
return [
    'id' => 'app-frontend',
    'language' => 'ru',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log', 'seo', 'debug_detect'],
    'controllerNamespace' => 'frontend\controllers',
    'aliases' => [
        '@shadow' => '@app/../shadow',
    ],
    'components' => [
        'debug_detect' => [
            'class' => 'common\components\Debugger',
        ],
        'opengraph' => [
            'class' => 'frontend\components\OpenGraph',
        ],
		'seo' => [
			'class' => 'shadow\plugins\seo\SSeo',
			'enableRule' => true,
		],
        'session' => [
            'timeout' => 604800,
            'cookieParams' => [
                'httponly' => true,
                'lifetime' => 604800
            ]
        ],
        'request' => [
            'class' => 'common\components\Request',
            'web' => '/frontend/web'
        ],
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_kingfisher', 'httpOnly' => true],
            'idParam' => '__id_kingfisher',
            'loginUrl' => ['site/index'],
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
//                [
//                    'class' => 'yii\log\EmailTarget',
//                    'levels' => ['error', 'warning'],
//                    'message' => [
//                        'from' => ['developer@chikita.kz'],
//                        'to' => ['developer@instinct.kz'],
//                        'subject' => 'Errors kingfisher.kz',
//                    ],
//                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'assetManager' => [
            'linkAssets' => true,
        ],
		'urlManager' => [
			'enablePrettyUrl' => true,
			'showScriptName' => false,
			'suffix' => '/',
		],
    ],
    'params' => $params,
];
