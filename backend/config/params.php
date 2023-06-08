<?php

return [
    'adminEmail' => 'admin@example.com',
	'bearer' => 'AQAAAABduIjLAAVM1d1UoOp6r0_NruKkifi1oAk',
    /*
     * В формах есть блокирующий слой.
     * Некоторым пользователям нельзя редактировать всю форму,
     * а только некоторые поля.
     * Вносим в массив роли, которым доступно редактирвоание любой формы
     */
    'edit_fields'=>['admin', 'copywriter'],
    'admin_menu' => [
        'index' => [
            'type' => 'index',
            'url' => ['/site/index'],
            'label' => 'Рабочий стол',
            'icon' => 'fa-dashboard',
        ],
        'orders' => [
            'type' => 'orders',
            'url' => ['/orders/index'],
            'label' => 'Заказы',
            'icon' => 'fa-shopping-cart ',
            'items' => [
                'index' => [
                    'url' => ['/orders/index', 'filter_menu' => 'all'],
                    'label' => 'Все',
                ],
                'new' => [
                    'url' => ['/orders/index', 'filter_menu' => '0'],
                    'label' => 'Новый заказ',
                ],
                'ordering' => [
                    'url' => ['/orders/index', 'filter_menu' => '1'],
                    'label' => 'Заказ на формировании',
                ],
                'confirmation' => [
                    'url' => ['/orders/index', 'filter_menu' => '2'],
                    'label' => 'Заказ на подтверждение клиентом',
                ],
                'confirmed' => [
                    'url' => ['/orders/index', 'filter_menu' => '3'],
                    'label' => 'Заказ подтверждён клиентом',
                ],
                'delivery' => [
                    'url' => ['/orders/index', 'filter_menu' => '4'],
                    'label' => 'Доставка',
                ],
                'success' => [
                    'url' => ['/orders/index', 'filter_menu' => '5'],
                    'label' => 'Заказ оплачен/выполнен',
                ],
                'return' => [
                    'url' => ['/orders/index', 'filter_menu' => '6'],
                    'label' => 'Возврат',
                ],
                'partial_return' => [
                    'url' => ['/orders/index', 'filter_menu' => '7'],
                    'label' => 'Частичный возврат',
                ],
                'customer_failure' => [
                    'url' => ['/orders/index', 'filter_menu' => '8'],
                    'label' => 'Отказ клиента',
                ],
                'client_not_responding' => [
                    'url' => ['/orders/index', 'filter_menu' => '9'],
                    'label' => 'Клиент не отвечает',
                ],
                'recovery' => [
                    'url' => ['/orders/index', 'filter_menu' => '10'],
                    'label' => 'Восстановление',
                ],
                'duplicate' => [
                    'url' => ['/orders/index', 'filter_menu' => '11'],
                    'label' => 'Дубликат',
                ],
            ]
        ],
        'structure' => [
            'type' => 'structure',
            'url' => ['/site/footer-menu'],
            'label' => 'Меню',
            'icon' => 'fa-sitemap',
            'items' => [
                'menu' => [
                    'url' => ['/menu/index'],
                    'label' => 'Основное',
                    'icon' => 'fa-sitemap',
                ],
                'menu-category' => [
                    'url' => ['/menu-category/index'],
                    'label' => 'Категорий',
                    'icon' => 'fa-sitemap',
                ],
                'footer-menu' => [
                    'url' => ['/footer-menu/index'],
                    'label' => 'Нижнее',
                    'icon' => 'fa-sitemap',
                ],
            ]
        ],
        'modules' => [
            'type' => 'modules',
            'url' => ['/site/modules'],
            'label' => 'Модули',
            'icon' => 'fa-tasks',
            'items' => [
                'catalog' => [
                    'url' => ['/category/index'],
                    'label' => 'Каталог',
                ],
                'brands' => [
                    'url' => ['/site/brands'],
                    'label' => 'Бренды',
                ],
                'recipes' => [
                    'url' => ['/recipes/index'],
                    'label' => 'Рецепты',
                ],
                'sets' => [
                    'url' => ['/sets/index'],
                    'label' => 'Сеты',
                ],
                'actions' => [
                    'url' => ['/actions/index'],
                    'label' => 'Акции',
                ],
                'news' => [
                    'url' => ['/news/index'],
                    'label' => 'Новости',
                ],
                'banners' => [
                    'url' => ['/site/banners'],
                    'label' => 'Баннеры',
                ],
                'about-history' => [
                    'url' => ['/about-history/index'],
                    'label' => 'История компании',
                ],
                'callback' => [
                    'url' => ['/site/callback'],
                    'label' => 'Заказ звонка',
                ],
                'retailcrm' => [
                    'url' => ['/retailcrm/index'],
                    'label' => 'retailCRM',
                ],
                'special-action' => [
                    'url' => ['/special-action/index'],
                    'label' => 'Спец Акция',
                ],
                'monitoring' => [
                    'url' => ['/monitoring/index'],
                    'label' => 'Мониторинг',
                ],
				'monitoringsms' => [
                    'url' => ['/monitoringsms/index'],
                    'label' => 'МониторингСмс',
                ],
				'analitics' => [
                    'url' => ['/analitics/index'],
                    'label' => 'Аналитика',
                ]
            ]
        ],
        'pages' => [
            'type' => 'pages',
            'url' => ['/pages/index'],
            'label' => 'Текстовые страницы',
            'icon' => 'fa-file-text ',
        ],
        'reviews' => [
            'type' => 'reviews',
            'url' => ['/site/reviews_site'],
            'label' => 'Отзывы',
            'icon' => 'fa-comments-o',
            'items' => [
//                'reviews_site'=>[
//                    'url'=>['site/reviews-site'],
//                    'label'=>'Отзывы сайта',
//                    'icon'=>'fa-comment-o',
//                ],
                'reviews_item' => [
                    'url' => ['/site/reviews-item'],
                    'label' => 'Отзывы товаров',
                    'icon' => 'fa-comment-o',
                ],
            ]
        ],
		'seo'       => [
            'type' => 'seo',
            'url'   => '',
            'label' => 'SEO',
            'icon'  => 'fa-wrench',
            'items' => [
                'meta-tag'  => [
                    'url'   => ['/seo/meta-tag/index'],
                    'label' => 'Метатеги',
                    'icon'  => 'fa-eye'
                ],
                'redirects' => [
                    'url'   => ['/seo/redirects/index'],
                    'label' => 'Редиректы',
                    'icon'  => 'fa-arrows-h'
                ],
            ]
        ],
        'all_users' => [
            'type' => 'all_users',
            'url' => ['/site/s-users'],
            'label' => 'Пользователи',
            'icon' => 'fa-users ',
            'items' => [
                's-users' => [
                    'url' => ['/site/s-users'],
                    'label' => 'Сотрудники',
                    'icon' => 'fa-user-secret',
                ],
                'users' => [
                    'url' => ['/users/index'],
                    'label' => 'Клиенты',
                    'icon' => 'fa-user ',
                ],
                'subscriptions' => [
                    'url' => ['/site/subscriptions'],
                    'label' => 'Подписчики',
                    'icon' => 'fa-envelope-o',
                ]
            ]
        ],
        'systems' => [
            'type' => 'systems',
            'url' => '',
            'label' => 'Система',
            'icon' => 'fa-cogs',
            'role' => 'admin',
            'items' => [
                'rules' => [
                    'url' => ['/s-user-plan/index'],
                    'label' => 'Планы менеджеров',
                    'icon' => 'fa-money'
                ],
                'bonus-settings' => [
                    'url' => ['/bonus-settings/index'],
                    'label' => 'Бонусы',
                    'icon' => 'fa-money'
                ],
                'promo-code' => [
                    'url' => ['/promo-code/index'],
                    'label' => 'Промокоды',
                    'icon' => 'fa-money'
                ],
                'city' => [
                    'url' => ['/city/index'],
                    'label' => 'Города',
                    'icon' => 'fa-globe'
                ],
                'pickpoint' => [
                    'url' => ['/pickpoint/index'],
                    'label' => 'Пункты самовывоза',
                    'icon' => 'fa-globe'
                ],
//                'seo' => [
//                    'url' => ['/seo/index'],
//                    'label' => 'SEO',
//                    'icon' => 'fa-eye'
//                ],
                'mail-template' => [
                    'url' => ['/mail-template/control'],
                    'label' => 'Текста писем',
                    'icon' => 'fa-envelope'
                ],
                'settings' => [
                    'url' => ['/settings/control'],
                    'label' => 'Настройки',
                    'icon' => 'fa-wrench'
                ],
            ]
        ]
    ]
];
