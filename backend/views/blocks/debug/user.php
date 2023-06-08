<?php

use common\components\Debugger as d;
use yii\helpers\Url;
use yii\bootstrap\Html;

$user_id = 19299;
$user_id = 21277;
$context = $this->context;

$tabs = [
    1 => [
        'name' => 'Debug скрипты',
        'path' => 'main',
    ],
    [
        'name' => 'Пользователь',
        'path' => 'user',
    ],
    [
        'name' => 'Заказы',
        'path' => 'orders',
    ],
//    [
//        'name' => '',
//        'path' => '',
//    ],
];

?>
<style>
    .tab-header{
        margin: -10px 0 10px;
    }
</style>
<div class="row">
    <div class="col-md-12">
        <div class="tab<?=$tab_index?>-buttons" style="position: relative;">
            <?=Html::img('/images/animate/loading.gif', [
                'class' => 'loading'
            ])?>

            <div class="form-gorup">

                <ul class="nav nav-tabs">
                    <li>
                        <a href="#other-content" data-toggle="tab">Прочее</a>
                    </li>
                    <li class="active">
                        <a href="#unloading-content" data-toggle="tab">Выгрузка</a>
                    </li>
                </ul>

                <div class="tab-content">
                    <!-- Мои скрипты -->
                    <div id="other-content" class="tab-pane fade">
                        <div class="tab-content">

                            <div class="mini-form">
                                <input
                                    type="text" name="user_id"
                                    class="form-control w150" placeholder="ID пользователя"
                                    value="<?=$user_id?>"
                                >
                                <button name="get_user" class="btn btn-primary">Получить</button>
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
                                <button name="set_wholesale" class="btn btn-primary">Установить</button>
                                &nbsp;&nbsp;&nbsp;
                            </div>

                            <br>
                            <div class="mini-form">
                                <button name="test_user" class="btn btn-primary">Нажать</button>
                            </div>

                        </div>
                    </div>
                    <!-- /мои скрипты -->

                    <!-- Выгрузка -->
                    <div id="unloading-content" class="tab-pane fade in active">
                        <div class="tab-content">

                            <form action="">
                                <div class="mini-form">
                                    <div class="h5 tab-header">Выгрузка всех клиентов делавших заказы</div>
                                    <div class="input-group">
                                        &nbsp;&nbsp;<label for="part_2">Загрузить вторую часть</label>
                                        <input type="checkbox" id="part_2"
                                               name="part_2" class="form-control"
                                               style="width: 16px;position:relative;top:-9px;"
                                        ><br><br>
                                        <button class="btn btn-default" type="submit" data-ajax="false"
                                                name="export_users"><i class="fa fa-upload"></i>
                                            Выгрузка
                                        </button>
                                    </div>
                                </div>
                                <hr>
                                <div class="mini-form">
                                    <div class="h5 tab-header">Выгрузка клиентов делавших заказы с устрицами</div>
                                    <div class="input-group">
                                        <button class="btn btn-default" type="submit" data-ajax="false"
                                                name="export_users_oysters"><i class="fa fa-upload"></i>
                                            Выгрузка
                                        </button>
                                    </div>
                                </div>
                            </form>

                        </div>
                    </div>
                    <!-- /выгрузка -->

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
