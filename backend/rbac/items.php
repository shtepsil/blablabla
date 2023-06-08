<?php

return [
    'loginAdminPanel' => [
        'type' => 2,
        'description' => 'Вход в админ панель',
    ],
    'admin' => [
        'type' => 1,
        'description' => 'Администратор',
        'children' => [
            'loginAdminPanel',
            'manager',
            'collector',
            'driver',
            'kassir',
            'change_price_item_order',
            'appointment_new',
            'appointment_on_confirmation',
            'appointment_confirmed',
            'all_change_status',
            'set_status_shaping',
            'set_status_confirmed',
            'set_status_on_confirmation',
            'set_status_delivery',
            'set_status_success',
            'set_status_fail',
            'set_status_not_responding',
            'set_status_recovery',
            'set_status_rollback',
            'appointment_success',
            'appointment_fail',
            'appointment_rollback',
            'appointment_not_responding',
            'appointment_partial_return',
            'appointment_',
            'appointment_double',
        ],
    ],
    'senior_manager' => [
        'type' => 1,
        'description' => 'Старший Менеджер',
        'children' => [
            'loginAdminPanel',
            'manager',
            'collector',
            'driver',
            'force_set_status_success',
        ],
    ],
    'manager' => [
        'type' => 1,
        'description' => 'Менеджер',
        'children' => [
            'loginAdminPanel',
            'appointment_new',
            'set_status_shaping',
            'set_status_confirmed',
            'set_status_success',
            'set_status_fail',
            'set_status_not_responding',
            'set_status_recovery',
            'set_status_rollback',
        ],
    ],
    'collector' => [
        'type' => 1,
        'description' => 'Сборщик',
        'children' => [
            'loginAdminPanel',
            'appointment_shaping',
            'set_status_on_confirmation',
            'set_status_confirmed',
            'set_status_delivery',
        ],
    ],
    'driver' => [
        'type' => 1,
        'description' => 'Водитель',
        'children' => [
            'loginAdminPanel',
            'appointment_delivery',
            'set_status_success',
            'set_status_rollback',
            'set_status_not_responding',
        ],
    ],
    'kassir' => [
        'type' => 1,
        'description' => 'Кассир',
        'children' => [
            'loginAdminPanel',
        ],
    ],
    'change_price_item_order' => [
        'type' => 2,
        'description' => 'change_price_item_order',
    ],
    'appointment_new' => [
        'type' => 2,
        'description' => 'Принятие новый заказов',
    ],
    'appointment_shaping' => [
        'type' => 2,
        'description' => 'Принятие заказов на сборку',
    ],
    'appointment_delivery' => [
        'type' => 2,
        'description' => 'Принятие заказов на доставку',
    ],
    'appointment_on_confirmation' => [
        'type' => 2,
        'description' => 'appointment_on_confirmation',
    ],
    'appointment_confirmed' => [
        'type' => 2,
        'description' => 'appointment_confirmed',
    ],
    'all_change_status' => [
        'type' => 2,
        'description' => 'Смена на любой статус без проверки блокировки',
    ],
    'set_status_shaping' => [
        'type' => 2,
        'description' => 'Переход на статус сборки',
    ],
    'set_status_confirmed' => [
        'type' => 2,
        'description' => 'Переход на статус подтверждён клиентом',
    ],
    'set_status_on_confirmation' => [
        'type' => 2,
        'description' => 'Переход на статус подтверждения',
    ],
    'set_status_delivery' => [
        'type' => 2,
        'description' => 'Переход на статус доставки',
    ],
    'set_status_success' => [
        'type' => 2,
        'description' => 'Переход на статус выполнен/оплачен',
    ],
    'force_set_status_success' => [
        'type' => 2,
        'description' => 'Жесткий переход на статус выполнен даже если не являешься менеджером этого заказа',
    ],
    'set_status_fail' => [
        'type' => 2,
        'description' => 'Переход на статус отказ клиента',
    ],
    'set_status_not_responding' => [
        'type' => 2,
        'description' => 'Переход на статус не отвечает',
    ],
    'set_status_recovery' => [
        'type' => 2,
        'description' => 'Переход на псевдо статус восстановление заказа',
    ],
    'set_status_rollback' => [
        'type' => 2,
        'description' => 'Переход на статус возврат',
    ],
    'appointment_success' => [
        'type' => 2,
        'description' => 'appointment_success',
    ],
    'appointment_fail' => [
        'type' => 2,
        'description' => 'appointment_fail',
    ],
    'appointment_rollback' => [
        'type' => 2,
        'description' => 'appointment_rollback',
    ],
    'appointment_not_responding' => [
        'type' => 2,
        'description' => 'appointment_not_responding',
    ],
    'appointment_partial_return' => [
        'type' => 2,
        'description' => 'appointment_partial_return',
    ],
    'copywriter' => [
        'type' => 1,
        'description' => 'Копирайтер',
    ],
    'appointment_' => [
        'type' => 2,
        'description' => 'appointment_',
    ],
    'appointment_double' => [
        'type' => 2,
        'description' => 'appointment_double',
    ],
];
