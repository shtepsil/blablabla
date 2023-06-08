<?php
return [
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
	'language' => 'ru',
	'timeZone' => 'Asia/Almaty',
    'bootstrap' => ['devicedetect'],
    'aliases' => [
//        '@template' => __DIR__ . '/../shadow/helpers/template',
        '@template' => '@app/../shadow/helpers/template',
        '@shadow' => '@app/../shadow',
        '@web_main' => '/frontend/web',
        '@web_frontend' => '@frontend/web',
		 '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'components' => [
        'one_signal' => [
            'class' => 'common\components\api\onesignal\OneSignalNotifications',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'i18n' => [
            'translations' => [
                'main*' => [
                    'class' => 'shadow\SDbMessageSource',
                ],
            ],
        ],
        'settings'=>[
            'class' => 'shadow\SSettings',
        ],
        'function_system'=>[
            'class' => 'frontend\components\FunctionComponent',
        ],
        'devicedetect'=>[
            'class'=> 'shadow\SDeviceDetect',
        ],
		'seo' => [
            'class' => 'shadow\plugins\seo\SSeo',
            'enable' => false,
            'enableRule' => false,
        ],
        'db'=>[
            'schemaMap'=>[
                'mysqli' => 'shadow\db\mysql\Schema', // MySQL
                'mysql' => 'shadow\db\mysql\Schema', // MySQL
            ]
        ],
        'yml'=>[
            'class'=>'shadow\plugins\yml\Yml',
            'shopOptions'=>[
                'name' => 'Kingfisher',
                'company' => 'Kingfisher',
                'url' => 'http://kingfisher.kz',
                'platform' => '',
                'version' => '',
                'agency' => '',
                'email' => ''
            ],
            'typeLaunch'=>'web'
        ],
    ],
];
