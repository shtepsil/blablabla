<?php

use common\components\Debugger as d;
use yii\helpers\Url;
use yii\bootstrap\Html;

$user_id = 19299;
$user_id = 21276;
$user_id = '';

?>
<style>

</style>
<div class="row">
    <div class="col-md-12">
        <div class="tab<?=$tab_index?>-buttons" style="position: relative;">
            <?=Html::img($context->AppAsset->baseUrl . '/images/animate/loading.gif', [
                'class' => 'loading'
            ])?>
            <div class="form-gorup">

                <div class="mini-form">
                    <input
                        type="text" name="user_data"
                        class="form-control w150" placeholder="ID или телефон"
                        value="<?=$user_id?>"
                    >
                    <button name="get_user" class="btn_debug blue">Получить</button>
                    &nbsp;&nbsp;&nbsp;
                    <button name="get_users" class="btn_debug blue">Получить всех</button>
                    &nbsp;&nbsp;&nbsp;
                </div>

                <div class="mini-form">
                    <input
                        type="text" name="user_id"
                        class="form-control w150" placeholder="ID пользователя"
                        value="<?=$user_id?>"
                    >
                    <input
                        type="text" name="is_wholesale"
                        class="form-control w150" placeholder="Тип isWholesale"
                        value=""
                    >
                    <button name="set_wholesale" class="btn_debug blue">Установить</button>
                    &nbsp;&nbsp;&nbsp;
                </div>

                <br>
                <div class="mini-form">
                    <label>Всем дубликатам установить status = 0 </label><br>
                    <button name="set_deleted" class="btn_debug blue">Установить</button>
                    &nbsp;&nbsp;&nbsp;
                </div>

                <br>
                <div class="mini-form">
                    <label>Обновить оригиналы </label><br>
                    <button name="update_origin" class="btn_debug blue">Обновить</button>
                    &nbsp;&nbsp;&nbsp;
                </div>

                <br>
                <div class="mini-form">
                    <button name="test_user" class="btn_debug blue">Нажать</button>
                    &nbsp;&nbsp;&nbsp;
                </div>


            </div>
            <br>
        </div>
        <?=d::res(false, 'res-tab' . $tab_index);?>
    </div>
</div>
<br><br>
<?php
$action = 'user';
$this->registerJs(<<<JS
//JS
$(function(){});
var params = {};
params['action'] = '{$action}';
tabsAjax('{$tab_index}', params);
JS
)
?>
