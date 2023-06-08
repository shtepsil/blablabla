<?php

use common\components\Debugger as d;
use frontend\form\EditRequisites;
use shadow\helpers\SNumberHelper;
use shadow\helpers\StringHelper;
use yii\helpers\Url;

$this->title = 'Печать Счёта на оплату';

$functoins = Yii::$app->function_system;

//d::pri($requisites);
//d::pri($attrs_requisites);

?>
<?if(!count($requisites)):?>
    <style>
        .wrap-requisites-print{
            font-family: Arial,serif;
            font-size:22pt;
        }
    </style>
    <div class="wrap-requisites-print">
        <center>
            <h1>Внимание</h1>
            <font color="red">
                У вас не заполнены реквизиты.<br>
                Перейдите по ссылке ниже и заполните реквизиты.
            </font><br><br>
            <a href="<?=Url::to('/user/requisites')?>.html" target="_blank">
                Заполнить реквизиты
            </a>
        </center>
    </div>

<?else:?>
    <? if(!d::isLocal()){ $this->registerJs('window.print()', $this::POS_LOAD); } ?>
    <style>
        .main
        {
            width: 978px;
            margin: 0 auto;
            font-size: 17px;
        }
    </style>
    <div class="main">
        <table width="100%" style="font-family: Arial;">
            <tbody><tr>
                <td></td>
                <td style="width: 68%; padding: 20px 0;">
                    <div style="text-align: justify; font-size: 11pt;">
                        Внимание! Оплата данного счета означает согласие с условиями поставки товара.
                        Уведомление об оплате обязательно, в противном случае
                        не гарантируется наличие товара на складе. Товар отпускается по факту
                        прихода денег на р/с Поставщика, самовывозом, при наличии доверенности
                        и документов удостоверяющих личность.
                    </div>
                </td>
                <td style="width: 32%; text-align: center; padding: 30px 0;display:none;"><img src="../../../../backend/web/index.php" style="width: 70%;"></td>
            </tr>

            </tbody>
        </table>

        <table width="100%" border="2" style="border-collapse: collapse; width: 100%; font-family: Arial;">
            <tbody>
            <tr style="">
                <td valign="top" style="font-weight:bold;padding-left: 10px;">
                    <div style="">Бенефициар:</div>
                </td>
                <td style="min-height:7mm;height:auto;width:25mm;text-align:center;font-weight:bold;">
                    <div>ИИK</div>
                </td>
                <td style="vertical-align: middle;text-align:center;font-weight:bold;">
                    <div style="vertical-align: middle;">Кбе</div>
                </td>
            </tr>
            <tr>
                <td valign="bottom" style="font-weight:bold;padding-left: 10px;">
                    <div>Товарищество с ограниченной ответственностью «King Fresh»</div>
                </td>
                <td rowspan="2" style="text-align:center;vertical-align:middle;font-weight:bold;width:60mm;">
                    <div>KZ3294806KZT22034805</div>
                </td>
                <td rowspan="2" style="width:25mm;text-align:center;font-weight:bold;">
                    <div>17</div>
                </td>
            </tr>
            <tr>
                <td valign="bottom" style="padding-left: 10px;">
                    <div style="">БИН: 171040006699</div>
                </td>
            </tr>
            </tbody>
        </table>
        <table border="2" style="border-collapse: collapse; width: 100%; font-family: Arial;">
            <tr>
                <td style="padding-left: 10px;">
                    Банк бенефициара:
                </td>
                <td style="text-align:center;">
                    <div style="font-weight:bold;">БИК</div>
                </td>
                <td style="text-align:center;">
                    <div style="font-weight:bold;">Код назначения</div>
                </td>
            </tr>
            <tr>
                <td style="padding-left: 10px;">
                    АО Евразийский Банк г.Алматы
                </td>
                <td style="text-align:center;">
                    <div style="font-weight:bold;">EURIKZKA</div>
                </td>
                <td style="text-align:center;">
                    <div style="font-weight:bold;">710</div>
                </td>
            </tr>
        </table>
        <br><br>
        <div style="font-weight: bold; font-size: 25pt; padding-left:5px; font-family: Arial;">
            Счет на оплату № <?=$order->id?> от <?=date('d.m.Y', $order->created_at)?></div>
        <br>

        <hr style="background-color:#000000; width:100%; font-size:1px; height:1px;margin-bottom:10px;">

        <table width="100%" style="font-family: Arial;">
            <tbody><tr>
                <td style="width: 30mm; vertical-align: top;">
                    <div style=" padding-left:2px; ">Поставщик:    </div>
                </td>
                <td>
                    <div style="font-weight:bold;  padding-left:2px;">
                        БИН / ИИН 171040006699, Товарищество с ограниченной ответственностью «King Fresh»<br>
                        Республика Казахстан,. Алматы, ул. Айманова, 155
                        <span style="font-weight: normal;display:none;">
                        Юр. адрес :   г. Алматы,  ул. Айманова, 155,<br>
                        тел.: 341-03-17
                    </span>
                    </div><br>
                </td>
            </tr>
            <tr>
                <td style="width: 30mm; vertical-align: top;">
                    <div style=" padding-left:2px;">Покупатель:    </div>
                </td>
                <td>
                    <div style="font-weight:bold;  padding-left:2px;">
                        <?if($requisites['entity_bin']['value'] != ''):?>
                            <?=$requisites['entity_bin']['name']?>
                            <?=$requisites['entity_bin']['value']?>,
                        <?endif?>
                        <?if($requisites['entity_name']['value'] != ''):?>
                            <?=$requisites['entity_name']['value']?>,
                        <?endif?>
                        <?if($requisites['entity_address']['value'] != ''):?>
                            <?=$requisites['entity_address']['value']?>
                        <?endif?>
                        <span style="font-weight: normal;display:none;">,<br>
                        пом. , тел.: +7() , факс: +7()
                    </span>

                    </div><br>
                </td>
            </tr>
            <tr>
                <td style="width: 30mm; vertical-align: top;">
                    <div style=" padding-left:2px;">Договор:    </div>
                </td>
                <td>
                    <div style="font-weight:bold;  padding-left:2px;">
                        <?=($requisites['entity_contract']['value'] == '') ? 'Без договора' : $requisites['entity_contract']['value']?>
                    </div><br>
                </td>
            </tr>
            </tbody></table>


        <table border="2" width="100%" cellpadding="2" cellspacing="2" style="border-collapse: collapse; width: 100%; font-family: Arial;">
            <thead>
            <tr>
                <th style="width:13mm;">№</th>
                <th>Код</th>
                <th>Наименование</th>
                <th style="width:20mm; ">Кол-во</th>
                <th style="width:17mm; ">Ед.</th>
                <th style="width:27mm;  ">Цена</th>
                <th style="width:27mm;  ">Сумма</th>
            </tr>
            </thead>
            <tbody>
            <?if(count($order_items)): $counter = 0; ?>
                <?foreach($order_items as $order_item):  $counter++; ?>
                    <tr>
                        <td style="text-align:center;"><?=$counter?></td>
                        <td style="width:13mm;text-align:center;"><?=$order_item['item']->id?></td>
                        <td style="padding-left: 10px;"><?=$order_item['item']->name?></td>
                        <td style="width:20mm;text-align:center;">
                            <? if ($order_item['item']->measure != $order_item['item']->measure_price): ?>
                                <?= doubleval($order_item['order_item']->weight) ?>
                            <? else: ?>
                                <?= doubleval($order_item['order_item']->count) ?>
                            <? endif; ?>
                        </td>
                        <td style="width:17mm;text-align:center;"><?=($order_item['item']->measure_price == 0) ? 'кг' : 'шт' ?></td>
                        <td style="width:27mm; text-align: center; "><?=number_format($order_item['item']->price, 2, ',' , ' ')?></td>
                        <td style="width:27mm; text-align: center; "><?=number_format($order_item['item_full_price'], 2, ',' , ' ')?></td>
                    </tr>
                <?endforeach?>
            <?endif?>
            </tbody>
        </table>
        <br>
        <table style="font-family: Arial;" border="0" width="100%" cellpadding="1" cellspacing="1">
            <tbody>
            <tr>
                <td colspan="3" style="width:27mm; font-weight:bold;text-align: right;">Итого: <?=number_format($order_full_sum, 2, ',', ' ')?></td>
            </tr>
            <tr>
                <td style="width:27mm; font-weight:bold;text-align: right;">В том числе НДС: <?=number_format(
                        $functoins->nds($order_full_sum), 2, ',', ' '
                    )?></td>
            </tr>
            </tbody></table>

        <br>
        <div style="font-family: Arial;">
            Всего наименований <?=$counter?>, на сумму <?=number_format($order_full_sum, 2, ',', ' ')?> <?=Yii::$app->params['currency']?>.</div>
        <div style="font-weight:bold;font-family: Arial;margin-bottom:5px;">
            Всего к выплате: <?=StringHelper::cucfirst(SNumberHelper::num2str($order_full_sum))?></div>
        <hr style="background-color:#000000; width:100%; font-size:1px; height:1px;">
        <br>
        <div style="font-family: Arial; font-size: 10pt;display:none;">
            1. Счет действителен в течении 5 (пяти) банковских дней, не считая дня выписки счета. В случае нарушения срока оплаты сохранение цены на товар и наличие товара на складе НЕ ГАРАНТИРУЕТСЯ.<br>
            2. Оплата данного счета означает согласие с условиями изложенными в п.1</div>
        <div style="background: url('<!--url печати в png сюда-->');  background-repeat: no-repeat; padding: 20px 10px; width: 700px; height: 250px;">
            <div style="font-weight:bold;">
                Исполнитель&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;_________________________________
                <span style="font-weight: normal;">/Бухгалтер/</span>
            </div>
            <br>
        </div>
        <br>  <br><br><br>  <br><br><br>  <br><br>
    </div>
<?endif?>
