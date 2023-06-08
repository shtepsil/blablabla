<?php

use common\components\Debugger as d;

$data = '<table><tr><td>ID</td><td>ФИО</td><td>Телефон</td><td>Последний заказ</td></tr>';
foreach ($items as $result) {

    $orders_sum = $result->sumOrders($result->id);

    $data .= '<tr>'
        . '<td>' . $result->id . '</td>'
        . '<td>' . $result->username . '</td>'
        . '<td>' . $result->phone . '</td>'
        . '<td style="text-align:right;">' . (($result->lastUserOrder != null) ? date('Y-m-d', $result->lastUserOrder->created_at) : '') . '</td></tr>';
}

$data .= '</table>';

echo $data;