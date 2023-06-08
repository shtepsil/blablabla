<?php
use yii\rest\UrlRule as RestRule;
return [
    '/'=>'site/index',

    'catalog/<slug:.+>-<id:\d+>'=>'site/item',
    '<slug:.+>-<id:\d+>'=>'site/catalog',
    [
        'pattern' => 'lk',
        'route' => 'user/index',
        'suffix'=>'.html'
    ],

    [
        'pattern' => 'lk/<action:(?(?=index)|.*)>',
        'route' => 'user/<action>',
        'suffix'=>'.html',
    ],
    [
        'pattern' => 'catalog/<slug:.+>-<id:\d+>',
        'route' => 'site/item',
    ],
    [
        'pattern' => '<action:(?(?=(index|site\/index))|(?(?!.*[\/].*).*))>',
//        'pattern' => '<action:(?(?=index)|(.*))>',
//        'pattern' => '^(?P<action>(?(?=index)|.*\/(.*)))$',
        'route' => 'site/<action>',
        'suffix'=>'.html',
    ],
    [
        'pattern' => 'api/<action>',
        'route' => 'api/<action>',
        'suffix'=>'.html',
    ],


//    [
//        'pattern' => 'lk',
//        'route' => 'user/index',
//        'suffix'=>'.html'
//    ],

//    '<controller>/<action>.html'=>'<controller>/<action>'

    [
        'class' => RestRule::class,
        'controller' => 'orders',
        'except' => ['delete', 'create', 'update'],
    ],
    [
        'class' => RestRule::class,
        'controller' => 'best-api',
        'except' => ['delete', 'create', 'update', 'index', 'view']
    ]
];