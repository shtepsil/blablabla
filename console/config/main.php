<?php
use yii\web\UrlManager;

$urlRulesBackEnd = require(__DIR__ . '/../../backend/config/urlRules.php');
$urlRules = require(__DIR__ . '/../../frontend/config/urlRules.php');
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log', 'gii'],
    'controllerNamespace' => 'console\controllers',
    'modules' => [
        'gii' => 'yii\gii\Module',
    ],
    'components' => [
        'urlManager' => [
            'baseUrl' => '/',
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'enableStrictParsing' => true,
            'hostInfo'=>'http://kingfisher.kz/',
//			'suffix' => '.html',
            'rules' => $urlRules,
        ],
        'urlManagerBackEnd' => [
            'class' => UrlManager::className(),
            'baseUrl' => '/admin',
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'enableStrictParsing' => true,
            'hostInfo'=>'http://kingfisher.kz/',
//			'suffix' => '.html',
            'rules' => $urlRulesBackEnd,
        ],
        'log' => [
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
    ],
    'params' => $params,
];
