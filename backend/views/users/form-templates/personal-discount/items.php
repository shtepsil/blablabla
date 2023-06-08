<?php

use common\components\Debugger as d;

?>
{label}
<div class="col-md-10">{input}</div>
<br><br><br>
<div class="col-md-12">
    <div class="table-responsive table-primary row">
        <table class="table table-striped table-hover personal-discount-items">
            <colgroup>
                <col width="40%">
                <col width="100px">
                <col width="40px">
                <col width="60px">
                <col width="85px">
                <col width="100px">
                <col width="100px">
            </colgroup>
            <thead>
            <tr>
                <th>Название</th>
                <th>Цена<br> за ед.</th>
                <th>Ед.<br> расчёта</th>
                <th>Вес</th>
                <th>Скидка</th>
                <th>Стоимость</th>
                <th>Действия</th>
            </tr>
            </thead>
            <tbody id="items-<?= $form_name ?>"><?if(count($items) > 0):?>
                <?php foreach ($items as $item): ?>
                    <?= Yii::$app->controller->renderPartial(
                        'form-templates/personal-discount/item',
                        [
                            'item' => $item,
                            'form_name' => $form_name,
                            'discount' => $items_discount_ids[$item->id],
                            'user_id' => $user_id,
                        ]
                    );
                    ?>
                <?php endforeach; ?>
            <?endif?></tbody>
        </table>
    </div>
</div>