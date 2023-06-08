<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-backend',
    'name'=>'Admin Panel',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'backend\controllers',
    'bootstrap' => ['log', 'seo', 'urlManagerFrontEnd', 'debug_detect'],
    'defaultRoute' => 'orders/index',
    'modules' => [
		'seo' => [
            'class' => 'backend\modules\seo\SeoModule',
        ],
	],
//    'controllerMap'=>[
//        'seo'=>'backend\controllers\main\SeoController'
//    ],
    'components' => [
        'debug_detect' => [
            'class' => 'common\components\Debugger',
        ],
		'seo' => [
			'class' => 'shadow\plugins\seo\SSeo',
			/**
			 * Эта строка выключается в common в компоненте seo
			 * По этому включим её для backend
			 */
			'enable' => true,
		],
        'authManager' => [
            'class' => 'yii\rbac\PhpManager',
        ],
        'frontend_cache' => [
            'class' => 'yii\caching\FileCache',
            'cachePath'=>'@frontend/runtime/cache'
        ],
		'assetManager' => [
//				'class'=>'yii\web\AssetManage',
			'linkAssets' => true,
		],
        'request' => [
            'class' => 'common\components\Request',
            'web'=> '/backend/web',
            'adminUrl' => '/admin',
            'csrfParam' => '_backendCSRF',
        ],
        'user' => [
            'class'=>'shadow\SWebUser',
            'identityClass' => 'backend\models\SUser',
			'loginUrl' => ['login/index'],
            'enableAutoLogin' => true,
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
        'i18n' => [
            'translations' => [
                'yii*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    //'basePath' => '@app/messages',
                    'sourceLanguage' => 'ru-RU',
//                    'fileMap' => [
//                        'app' => 'app.php',
//                        'app/error' => 'error.php',
//                    ],
                ],
            ],
        ],
		'urlManager' => [
			 'class' => 'yii\web\UrlManager',
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'enableStrictParsing' => true,
            'rules' => [],
        ],
		 'urlManagerFrontEnd' => [
            'class' => 'yii\web\UrlManager',
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'enableStrictParsing' => true,
            'rules' => [],
        ],
    ],
    'params' => $params,
];
