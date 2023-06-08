<?php

use common\components\Debugger as d;

$data = '<table><tr><td>ID</td><td>Ответственный менеджер</td><td>ФИО</td><td>Количество заказов</td><td>Телефон</td><td>Город</td><td>E-Mail</td><td>Статус</td><td>Сумма заказов</td><td>Сумма бонусов</td><td>Последний заказ</td></tr>';
foreach ($items as $result) {

    $orders_sum = $result->sumOrders($result->id);

    $data .= '<tr>'
        . '<td>' . $result->id . '</td>'
        . '<td>' . $result->username . '</td>'
        . '<td>' . (($result->manager != null) ? $result->manager->username : '') . '</td>'
        . '<td style="text-align:center;">' . $result->showCountOrders($result->id) . '</td>'
        . '<td>' . $result->phone . '</td>'
        . '<td>' . (isset($city_all[$result->city_id]) ? $city_all[$result->city_id]->name : 'Не выбран') . '</td>'
        . '<td>' . $result->email . '</td>'
        . '<td>' . (($result->isWholesale == 1) ? 'Оптовый' : 'Розничный') . '</td>'
        . '<td>' . number_format($orders_sum, 0, '', ' ') . '</td>'
        . '<td>' . (($result->bonus) ? $result->bonus : 0) . '</td>'
        . '<td>' . (($result->lastUserOrder != null) ? date('Y-m-d', $result->lastUserOrder->created_at) : '') . '</td></tr>';
}

$data .= '</table>';

echo $data;