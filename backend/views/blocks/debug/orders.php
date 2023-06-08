<?php

use common\components\Debugger as d;
use yii\helpers\Url;
use yii\bootstrap\Html;

$user_id = 19299;
$user_id = 21276;

?>
<style>

</style>
<div class="row">
    <div class="col-md-12">
        <div class="tab<?=$tab_index?>-buttons" style="position: relative;">
            <?=Html::img('/images/animate/loading.gif', [
                'class' => 'loading'
            ])?>
            <div class="form-gorup">

                <div class="mini-form">
                    <input
                        type="text" name="order_id"
                        class="form-control w150 float-left" placeholder="ID заказа"
                        value=""
                    >
                    <button name="get_order" class="btn btn-primary">Получить</button>
                    &nbsp;&nbsp;&nbsp;
                    <button name="delete_order" class="btn btn-danger">Удалить</button>
                    &nbsp;&nbsp;&nbsp;
                </div>
                <br>
            </div>
            <br>
        </div>
        <?=d::res(false, 'res-tab' . $tab_index);?>
    </div>
</div>
<br><br>
<?php
$action = 'orders';
$this->registerJs(<<<JS
//JS
$(function(){});
var params = {};
params['action'] = '{$action}';
tabsAjax('{$tab_index}', params);

JS
)
?>
