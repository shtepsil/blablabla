<?php
return [
    ''=>'site/index',
	'sitemap.xml' => 'sitemap/xml',
    'google_fid.xml' => 'xml/fid-google-adwords',
    'debug' => 'site/debug',
    'updatedeleted' => 'site/updatedeleted',
    'tab-debug-ajax' => 'site/tab-debug-ajax',
	//'catalog/<slug:.+>-<id:\d+>'=>'site/item',
	//'<slug>-<id:\d+>'=>'site/catalog',
	'<action>.html'=>'site/<action>',
	'<controller>/<action>.html'=>'<controller>/<action>',
    [
        'pattern' => 'lk',
        'route' => 'user/index', 
        'suffix'=>'.html'
    ],
	
    [
        'pattern' => 'promo/<code:\w+>',
        'route' => 'site/promo',
    ],
    [
        'pattern' => 'send-promo/<code:\w+>',
        'route' => 'site/send-promo',
    ],
    [
        'pattern' => 'send-code/<code:\w+>',
        'route' => 'site/send-code',
    ],
    [
        'pattern' => 'enter-code/<code:\w+>',
        'route' => 'site/enter-code',
    ],
    [
        'pattern' => 'promo-winner/<code:\w+>',
        'route' => 'site/promo-winner',
    ],
	[
        'pattern' => 'about',
        'route' => 'site/about',
		'suffix'=>'/',
    ],
	[
        'pattern' => 'optovikam-<id:\d+>',
        'route' => 'site/page',
		'suffix'=>'/',
    ],
	[
        'pattern' => 'news',
        'route' => 'site/news',
		'suffix'=>'/',
    ],
	[
        'pattern' => 'payment-delivery',
        'route' => 'site/payment-delivery',
		'suffix'=>'/',
    ],
	[
        'pattern' => 'actions',
        'route' => 'site/actions',
		'suffix'=>'/',
    ],
	[
        'pattern' => 'recipes',
        'route' => 'site/recipes',
		'suffix'=>'/',
    ],
	[
        'pattern' => 'contacts',
        'route' => 'site/contacts',
		'suffix'=>'/',
    ],
	[
        'pattern' => 'catalog/<slug:.+>-<id:\d+>',
        'route' => 'site/item',
		'suffix'=>'/',
    ],
    [
        'pattern' => 'lk/<action:(?(?=index)|.*)>',
        'route' => 'user/<action>',
        'suffix'=>'.html',
    ],
	[
        'pattern' => '<slug:.+>-<id:\d+>',
        'route' => 'site/catalog',
		'suffix'=>'/',
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
];