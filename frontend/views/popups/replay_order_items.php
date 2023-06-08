<?php
/**
 * @var $this yii\web\View
 * @var $context \frontend\controllers\UserController
 * @var $order \common\models\Orders
 */
use yii\helpers\Url;

$context = $this->context;
?>
<div class="popup_content">
    <div class="popup_basket_top">
        <div class="popup_basket_title_1"><span>Номер заказа:</span><?=str_pad($order->id, 9, '0', STR_PAD_LEFT);?></div>
    </div>
    <div class="popup_basket_content_wrapper">
        <div class="popup_basket_content">
            <div class="popup_basket_table">
                <div class="popup_basket_labels">
                    <div class="item_3">Итого</div>
                    <div class="item_2">Цена</div>
                    <div class="item_1">Количество</div>
                </div>
                <div class="popup_basket_list">
                    <ol>
                        <?php
                        $sum = 0;
                        ?>
                        <?php foreach($order->ordersItems as $order_item): ?>
                            <?php
                            if(!$order_item->item){
                                continue;
                            }
                            $item = $order_item->item;
                            $count = $order_item->count;
                            if($item->measure == 1){
                                $count = number_format($count);
                            }else{
                                $count = number_format($count,1);
                            }
                            $price = round($item->price * $count);
                            $sum += $price;
                            ?>
                            <li>
                                <div class="item_1">
                                    <div class="popup_basket_image">
                                        <a href="<?= Url::to(['site/item','id'=>$item->id]) ?>" target="_blank">
                                            <img alt="" src="<?=$item->img(false)?>">
                                        </a>
                                    </div>
                                    <div class="popup_basket_name">
                                        <a href="<?= Url::to(['site/item','id'=>$item->id]) ?>" target="_blank"><?=$item->name?></a>
                                    </div>
                                </div>
                                <div class="item_4">
                                    <span id="sum_item_<?=$item->id?>"><?=$price?> тнг</span>
                                    <a class="popup_basket_close" href="#"></a>
                                </div>
                                <div class="item_3">
                                    <span><?=$item->price?> тнг</span>
                                </div>
                                <div class="item_2">
                                    <div class="plus_minus styled_1">
                                        <div class="minus_button"></div>
                                        <input type="text" name="items[<?=$item->id?>]" value="<?=$count?>" data-type="<?= $item->measure ?>" data-id="<?=$item->id?>" data-price="<?=$item->price?>">
                                        <div class="plus_button"></div>
                                        <?php if ($item->measure == 1): ?>
                                            <span>шт</span>
                                        <?php else: ?>
                                            <span>кг</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="popup_basket_bottom">
        <div class="popup_basket_numbers">
            <span>Сумма</span><span id="sum_replay_order"><?=$sum?> тнг</span>
        </div>
        <input type="submit" value="Повторить заказ" id="send_replay">
    </div>
</div>
