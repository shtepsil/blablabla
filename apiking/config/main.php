<?php

use yii\web\JsonParser;
use yii\web\Response;

$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);
return [
    'id' => 'app-api',
    'language' => 'ru',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log', 'debug_detect'],
    'controllerNamespace' => 'apiking\controllers',
    'aliases' => [
        '@shadow' => '@app/../shadow',
    ],
    'components' => [
        'debug_detect' => [
            'class' => 'common\components\Debugger',
        ],
        'session' => [
		'name' => 'front_s',
            'timeout' => 604800,
            'cookieParams' => [
                'httponly' => true,
                'lifetime' => 604800
            ]
        ],
		 'request' => [
            'baseUrl'   => '/apiking',
            'parsers' => [
                'application/json' => JsonParser::class,
            ],
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
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'base-api/error',
        ],
        'assetManager' => [
            'linkAssets' => true,
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'enableStrictParsing' => true,
            'showScriptName' => false,


            'rules' => [
                '' => 'site/index',
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'us',
                    'pluralize'=>false
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'siteapi',
                    'pluralize'=>false
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'userapi',
                    'pluralize'=>false
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'userlkapi',
                    'pluralize'=>false
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'basketapi',
                    'pluralize'=>false
                ],

            ],
        ],
    ],
    'params' => $params,

];

