<?php
$urlRulesBackEnd = require(__DIR__ . '/../../backend/config/urlRules.php');
$urlRules = require(__DIR__ . '/urlRules.php');
$config = [
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'bhYIuAOP2-xOw0DWbNTfPt1jDOKRiF0K',
        ],
        'urlManager' => [
            'baseUrl' => '/',
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'enableStrictParsing' => true,
//			'suffix' => '.html',
            'rules' => $urlRules,
        ],
        'urlManagerBackEnd' => [
            'class' => 'yii\web\urlManager',
            'baseUrl' => '/admin',
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'enableStrictParsing' => true,
//			'suffix' => '.html',
            'rules' => $urlRulesBackEnd,
        ],
        'authClientCollection' => [
            'class' => 'yii\authclient\Collection',
            'clients' => [
//                'google' => [
//                    'class' => 'shadow\authclient\GoogleOAuth',
//                    'clientId' => '494663494259-q8n5qs7fiep86rlnb86ruto71essth8l.apps.googleusercontent.com',
//                    'clientSecret' => 'FA2f8t5Wk1TRTXdNEUxro_bP',
//                ],
                //redirect  /auth.html?authclient=facebook
                // /auth.html?authclient=facebook
                // /auth.html?authclient=facebook
                'facebook' => [
                    'class' => 'yii\authclient\clients\Facebook',
                    'clientId' => '147612095611819',
                    'clientSecret' => 'a98eecce3f51ecf1b2350987ebd7f9cf',
				//	'clientSecret' => '1b91113e4be7d96fbfa5bbcd3f86a143',
					// 'clientId' => '944041253089052',
					
                ],
                'twitter' => [
                    'class' => 'yii\authclient\clients\Twitter',
                    'consumerKey' => '7Ux71x8mKqRY3m66S1CvajaSr',
                    'consumerSecret' => '0bWU15ZOU3soRanJkUpodsi7PRGJFZHf8H3TOJWqrk1SrPHQk1',
                ],
                'vkontakte' => [
                    'class' => 'yii\authclient\clients\VKontakte',
                    'clientId' => '5214563',
                    'clientSecret' => 'G13eqcoFiKnmFvmdfNz7',
                    'scope' => 'email'
                ],
            ],
        ],
        'reCaptcha' => [
            'name' => 'reCaptcha',
            'class' => 'shadow\widgets\ReCaptcha\ReCaptcha',
            'siteKey' => '6LcpRwwTAAAAABMCAcF9SkJD3tmP7UTdfWzoi4HT',
            'secret' => '6LcpRwwTAAAAANPOlE03nxapsbLY2ILY1bkdaRMO',
        ],
    ],
];

if (false) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class'=>'yii\debug\Module',
        'allowedIPs'=>['*']
    ];
    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = 'yii\gii\Module';
}

return $config;
