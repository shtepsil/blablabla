<?php
/**
 * @var $this yii\web\View
 * @var $items common\models\User[]
 * @var $user backend\models\SUser
 * @var $context backend\controllers\UsersController
 * @var $city_all City[]
 */
use common\components\Debugger as d;
use backend\models\SUser;
use common\models\City;
use common\models\Orders;
use yii\bootstrap\Html;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\widgets\LinkPager;
use common\models\User;
use shadow\plugins\datetimepicker\DateTimePicker;
use yii\widgets\MaskedInput;

$url = 'users';
$context = $this->context;
$user = Yii::$app->user->identity;
$city_all = City::find()->indexBy('id')->all();
$is_admin = false;
if ($user->role == 'admin') {
    $is_admin = true;
}
if (!($select_manager = Yii::$app->request->get('manager'))) {
    //    $select_manager = $user->id;
}
if (ADMIN_FORM_DEBUG_RES) {
    echo d::res();
}
?>
<?= $this->render('//blocks/breadcrumb') ?>
<section id="content" class="list-clients">
    <div class="panel">
        <div class="panel-heading">
            <ul class="nav nav-pills">
                <?php foreach ($context->data_types as $key => $value): ?>
                    <?= Html::tag(
                        'li', Html::a($value['title'], ['users/index', 'sort' => $key]),
                        ['class' => (($context->current_type == $key) ? 'active' : '')]
                    ) ?>
                <?php endforeach; ?>
            </ul>
        </div>
        <div class="panel-heading">
            <form action="" id="form_users">
                <input type="hidden" name="sort" value="<?= $context->current_type ?>">
                <input type="hidden" name="size" value="<?= $size ?>">
                <div class="form-group simple wrap-search-by-phone">
                    <div class="col-md-1 wrap-checkbox-search-by-phone" style="width:43px;">
                        <input type="checkbox" id="search_user_by_phone">
                    </div>
                    <label class="col-md-11 control-label" for="search_user_by_phone">Поиск по номеру телефона
                        <span>(Включить маску-телефона для поля поиска)</span></label>
                </div>
                <div class="col-xs-12 col-md-2 no-padding-hr">
                    <input type="text" class="form-control search-universal" placeholder="Поиск" name="search"
                        value="<?= $search ?>">
                    <?= MaskedInput::widget([
                        'name' => 'search_by_phone',
                        'mask' => '+7(999)-999-9999',
                        'definitions' => [
                            'maskSymbol' => '_'
                        ],
                        'options' => [
                            'placeholder' => '+7(___)-___-____',
                            'class' => 'form-control input-search-by-phone dn'
                        ]
                    ]) ?>
                </div>
                <div class="input-group" id="search_form">
                    <div class="input-group-btn">
                        <select name="manager" class="form-control" style="width: 150px" tabindex="-1" title="">
                            <?
                            /**
                             * @var $all_manager SUser[]
                             */
                            $all_manager = SUser::find()->where(['role' => 'manager'])->select(['username', 'id'])->indexBy('id')->all()
                                ?>
                            <option value="">Все</option>
                            <? foreach ($all_manager as $manager): ?>
                                <option value="<?= $manager->id ?>" <?= ($select_manager == $manager->id ? 'selected' : '') ?>>
                                    <?= $manager->username ?></option>
                            <? endforeach; ?>
                        </select>
                    </div>
                    <div class="input-group-btn">
                        <select name="city" class="form-control" style="width: 150px" tabindex="-1" title="">
                            <option value="">Все города</option>
                            <? foreach ($city_all as $city): ?>
                                <option value="<?= $city->id ?>" <?= ($select_city == $city->id ? 'selected' : '') ?>><?=
                                              $city->name ?></option>
                            <? endforeach; ?>
                        </select>
                    </div>
                    <button class="btn btn-default" type="submit"><i class="fa fa-eye"></i> Показать</button>
                </div><br>
                По кол-ву заказов
                <div class="input-group">
                    <input type="text" class="form-control" style="width: 150px" placeholder="с" name="from_count"
                        value="<?= (isset($_GET['from_count']) ? $_GET['from_count'] : null) ?>">
                    <input type="text" class="form-control" style="width: 150px" placeholder="по" name="to_count"
                        value="<?= (isset($_GET['to_count']) ? $_GET['to_count'] : null) ?>">
                    <button class="btn btn-default" type="submit" onclick="$(this).val(1)" name="count_orders"><i
                            class="fa fa-eye"></i> Показать</button>
                </div><br>

                <!-- Блок экспорта -->
                <div class="toggle-export-block">Открыть блок экспорта<i class="fa fa-arrow-circle-o-down"
                        aria-hidden="true"></i></div>
                <div class="exports-block dn">
                    <br>
                    Экспорт по id
                    <div class="input-group">
                        <input type="text" class="form-control" style="width: 150px" placeholder="с" name="from_id">
                        <input type="text" class="form-control" style="width: 150px" placeholder="по" name="to_id">
                        <button class="btn btn-default" type="submit" onclick="$(this).val(1)" formtarget="_blank"
                            name="export"><i class="fa fa-upload"></i> Экспорт</button>
                    </div><br>
                    Экспорт по дате
                    <div class="input-group">
                        <?= DateTimePicker::widget([
                            'name' => 'from_date',
                            'language' => 'ru',
                            'size' => 'ms',
                            'template' => '{input}',
                            'pickButtonIcon' => 'glyphicon glyphicon-time',
                            'clientOptions' => [
                                'format' => 'dd-mm-yyyy',
                                'minView' => 2,
                                'autoclose' => true,
                                'todayBtn' => true
                            ],
                            'options' => [
                                'style' => 'width: 150px',
                                'placeholder' => 'с'
                            ]
                        ]) ?>
                        <?= DateTimePicker::widget([
                            'name' => 'to_date',
                            'language' => 'ru',
                            'size' => 'ms',
                            'template' => '{input}',
                            'pickButtonIcon' => 'glyphicon glyphicon-time',
                            'clientOptions' => [
                                'format' => 'dd-mm-yyyy',
                                'minView' => 2,
                                'autoclose' => true,
                                'todayBtn' => true
                            ],
                            'options' => [
                                'style' => 'width: 150px',
                                'placeholder' => 'по'
                            ]
                        ]) ?>
                        &nbsp;&nbsp;
                        <input type="checkbox" name="order" id="order" />&nbsp;
                        <label for="order">Выбрать пользователей<br>без заказов</label>&nbsp;&nbsp;
                        <button class="btn btn-default" type="submit" onclick="$(this).val(1)" formtarget="_blank"
                            name="export_date"><i class="fa fa-upload"></i> Экспорт по дате</button>
                    </div><br>
                    Экспорт пользователей, имеющих бонусы, но не делавших заказы более месяца.
                    <br>
                    <div class="input-group">
                        <button class="btn btn-default" type="submit" onclick="$(this).val(1)" formtarget="_blank"
                            name="export_users_with_bonuses_order_month"><i class="fa fa-upload"></i>
                            Экспорт пользователей</button>
                    </div>

                    <br>
                </div>
                <!-- /блок экспорта -->

            </form>
        </div>
        <div class="panel-heading">
            <div class="row">
                <a href="<?= Url::to([$url . '/control']) ?>" class="btn-primary btn">
                    <i class="fa fa-user-plus"></i> <span class="hidden-xs hidden-sm">Добавить</span></a>
                <div class="pull-right form-inline">
                    <label>Показывать по:
                        <?= Html::dropDownList('size', $size, [50 => 50, 100 => 100, 200 => 200], ['class' => 'form-control input-sm change_size']) ?>
                    </label>
                </div>
            </div>

        </div>
        <table class="table-primary table table-striped table-hover">
            <colgroup>
                <col>
                <col width="150px">
                <col width="150px">
                <col width="150px">
                <col width="150px">
                <col width="150px">
                <col width="150px">
                <col width="50px">
                <col width="50px">
                <col width="150px">
                <col width="50px">
            </colgroup>
            <thead>
                <tr>
                    <th class="text-right">Действия</th>
                    <th>Клиент</th>
                    <th>Юр.название</th>
                    <th>Кол-во заказов</th>
                    <th>Телефон</th>
                    <th>Город</th>
                    <th>E-Mail</th>
                    <th>Статус</th>
                    <th>Сумма заказов</th>
                    <th>Процент с заказа</th>
                    <th>Скидка</th>
                    <th>Последний заказ</th>
                </tr>
            </thead>
            <tbody>
                <?php //phpinfo();
                foreach ($items as $item): ?>
                    <? $orders = $item->lastUserOrder; ?>
                    <tr id="layout_normal">
                        <td class="actions text-right">
                            <? if (false): ?>
                                <a href="<?= Url::to([$url . '/login', 'id' => $item->id]) ?>" class="btn-success btn-xs btn"
                                    title="Войти">
                                    <i class="fa fa-sign-in"></i>
                                </a>
                            <? endif ?>
                            <a href="<?= Url::to(['orders/control', 'user_id' => $item->id]) ?>"
                                class="btn-primary btn-xs btn" target="_blank" title="Создать заказ">
                                <i class="fa fa-shopping-cart"></i>
                            </a>
                            <a href="<?= Url::to([$url . '/deleted', 'id' => $item->id]) ?>"
                                class="btn-danger btn-xs btn-confirm btn">
                                <i class="fa fa-times fa-inverse"></i>
                            </a>
                        </td>
                        <td class="name">
                            <a href="<?= Url::to([$url . '/control', 'id' => $item->id]) ?>"><?= $item->username ?></a>
                        </td>
                        <td>
                            <?= ($item->data != NULL) ? $item->entity_name : '' ?>
                        </td>
                        <td>
                            <a href="<?= Url::to([$url . '/control', 'id' => $item->id]) ?>#tab=page-history_order-panel">
                                <?php if (isset($item['cnt'])): ?>
                                    <?= $item['cnt'] ?>
                                <?php else: ?>
                                    <?= $item->getCountOrders()->count(); ?>
                                <?php endif; ?>
                            </a>
                        </td>
                        <td>
                            <?= $item->phone ?>
                        </td>
                        <td>
                            <?= (isset($city_all[$item->city_id]) ? $city_all[$item->city_id]->name : 'Не выбран') ?>
                        </td>
                        <td>
                            <?= $item->email ?>
                        </td>
                        <td>
                            <a href="#" class="isWholesale_select" data-type="select" data-pk="<?= $item->id ?>"
                                data-name="isWholesale" data-value="<?= $item->isWholesale ?>" data-title="Статус"></a>
                        </td>
                        <td>
                            <?= number_format($item->order_sum, 0, '', ' ') ?> тг
                        </td>
                        <td>
                            <?= Yii::$app->function_system->percent($item->id) ?>%
                        </td>
                        <td>
                            <?= ($item->discount ? ($item->discount . '%') : '') ?>
                        </td>
                        <td>
                            <?
                            if ($orders) {
                                /**@var Orders $order */
                                $order = $orders;
                                echo Html::a(date('d.m.Y', $order->created_at), ['orders/control', 'id' => $order->id], ['target' => '_blank']);
                            }
                            ?>
                        </td>

                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <div class="panel-footer">
            <?= LinkPager::widget([
                'pagination' => $pages,
            ]);
            ?>
        </div>
    </div>
</section>
<?
$url_change = Json::encode(Url::to(['users/change-field']));
$this->registerJs(<<<JS
$('.change_size').on('change',function(e) {
    $('input[name="size"]', '#form_users').val($(this).val());
    $('#form_users').submit();
})
$('.isWholesale_select').editable({
    source: [
        {value: 0, text: 'Розничный'},
        {value: 1, text: 'Оптовый'},
        {value: 2, text: 'Оптовый 2'},
    ],
url: {$url_change},
});

// Открыть/закрыть блок экспорта
$('.toggle-export-block', '.list-clients').on('click', function(){
    var tthis = $(this),
        wrap = $('.list-clients'),
        exportsBlock = wrap.find('.exports-block');

    if(!exportsBlock.is(':visible')){
        exportsBlock.slideDown(100);
        tthis.find('i').removeClass('fa-arrow-circle-o-down').addClass('fa-arrow-circle-o-up');
    }else{
        exportsBlock.slideUp(100);
        tthis.find('i').removeClass('fa-arrow-circle-o-up').addClass('fa-arrow-circle-o-down');
    }
});
// ===

// Вкл/откл маску для поля поиска
$('#search_user_by_phone', '.list-clients').on('change', function(){
    var tthis = $(this),
        wrap = $('.list-clients'),
        searchUniversal = wrap.find('.search-universal'),
        inputSearchByPhone = wrap.find('.input-search-by-phone');

    if(tthis.prop('checked')){
        searchUniversal.val('').hide().promise().done(function(){
            inputSearchByPhone.fadeIn(100);
        });
    }else{
        inputSearchByPhone.val('').hide().promise().done(function(){
            searchUniversal.fadeIn(100);
        });
    }
});
// ===

JS
)
    ?>