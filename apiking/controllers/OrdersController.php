<?php


namespace apiking\controllers;

use apiking\models\resources\OrderResource;
use common\models\Orders;


class OrdersController extends BaseApiController
{
    public $modelClass = OrderResource::class;
}