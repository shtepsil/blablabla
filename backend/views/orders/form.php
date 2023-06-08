<?php
/**
 * @var $item    common\models\Orders
 * @var $this    yii\web\View
 * @var $context backend\controllers\OrdersController
 */

use common\components\Debugger as d;
use backend\models\SUser;
use common\models\City;
use common\models\OrdersHistory;
use common\models\SHistoryMoney;
use shadow\plugins\datetimepicker\DateTimePicker;
use shadow\assets\CKEditorAsset;
use shadow\widgets\AdminActiveForm;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
CKEditorAsset::register($this);
$context  = $this->context;
$get = Yii::$app->request->get();
$cancel   = $context->url['back'];
$is_admin = Yii::$app->user->can('admin');
if ($item->isNewRecord) {
    $item->date_delivery = date('d.m.Y', time());
    $groups = [
        'main'  => [
            'title' => 'Информация',
            'icon'  => 'suitcase',
        ],
        'items' => [
            'title' => 'Товары',
            'icon'  => 'th-list',
        ],
    ];
} else {
    $item->date_delivery = date('d.m.Y', $item->date_delivery);
    $groups = [
        'items' => [
            'title' => 'Товары',
            'icon'  => 'th-list',
        ],
        'main'  => [
            'title' => 'Информация',
            'icon'  => 'suitcase',
        ],
    ];
}
$undo = false;
if ((Yii::$app->user->can('manager') || Yii::$app->user->can('collector')) && !$item->isNewRecord) {
    $groups['responsible'] = [
        'title' => 'Ответственные',
        'icon'  => 'users',
    ];


    // ВКЛАДКА ВОЗВРАТ =======================================
    $undo = true;
    $groups['undo'] = [
        'title' => 'Возврат',
        'icon'  => 'undo',
    ];
//    if((
//            // Если ID авторизованного пользователя равно manager_id заказа
//            Yii::$app->user->id == $item->manager_id
//            // Если ID авторизованного пользователя равно collector_id заказа
//            || Yii::$app->user->id == $item->collector_id
//            // Только пользователь со статусом senior_manager имеет доступ.
//            || Yii::$app->user->can('senior_manager')
//        )
//        && !$item->isNewRecord && in_array($item->status, [6, 7])
//    ){
//
//
//    }
//    if (in_array($item->status, [6, 7])) {
//        $groups['undo'] = [
//            'title' => 'Возврат',
//            'icon'  => 'undo',
//        ];
//    }
    // =======================================================
}
if (!$item->isNewRecord) {
    $groups['history'] = [
        'title' => 'История изменения',
        'icon'  => 'clock-o',
    ];
}

if ($item->isWholesale > 0) {
    $groups['consignment-note'] = [
        'title' => 'Накладная',
        'icon' => 'file-text',
    ];
}

if($item->invoice_file){
    $groups['consignment-note']['header_info'] = ' (Загружена)';
}

$is_requisites = false;
if(isset($user->entity_name) OR isset($user->parent_data)){
    $is_requisites = true;
}

?>
<?= $this->render('//blocks/breadcrumb') ?>
<?//=d::res('test_wholesale', 'res-wholesale')?>
<?if(ADMIN_FORM_DEBUG_RES) echo d::res();?>
<style>
	#mapyandex {
		width:100%;
		height: 700px;
		display:none;
	}
</style>
<script src="https://api-maps.yandex.ru/2.1/?apikey=a800cece-8fcc-4d1c-bc91-da2061eb8d3e&lang=ru_RU" type="text/javascript"></script>
    <section id="content">  
        <div id="order_edit">
            <div class="layer-loading"></div>
            <?php $form = AdminActiveForm::begin([
                'action'               => ['orders/save'],
                'enableAjaxValidation' => false,
                'options'              => ['enctype' => 'multipart/form-data'],
                'fieldConfig'          => [
                    'options'      => ['class' => 'form-group simple'],
                    'template'     => "{label}<div class=\"col-md-10\">{input}\n{error}</div>",
                    'labelOptions' => ['class' => 'col-md-2 control-label'],
                ],
            ]); ?>
            <?= Html::hiddenInput('id', $item->id) ?>
            <? if (Yii::$app->request->get('user_id')): ?>
                <?= Html::hiddenInput('user_id', $item->user_id) ?>
            <? endif; ?>
            <div style="position: relative;">
                <div class="form-actions panel-heading" style="padding-left: 0px;padding-top: 0px;">
                    <? if ($item->isNewRecord || Yii::$app->user->can('admin') || $item->manager_id == Yii::$app->user->id): ?>
                        <div class="row">
                            <?= Html::submitButton('<i class="fa fa-retweet"></i> Сохранить', ['class' => 'btn-success btn-save btn-lg btn', 'data-hotkeys' => 'ctrl+s', 'name' => 'continue']) ?>
                            &nbsp;&nbsp;
                            <button name="commit" type="submit" class="btn-save-close btn-default btn" onclick="$(this).val(1)" title="Сохранить и Закрыть">
                                <i class="fa fa-check"></i>
                                <span class="hidden-xs hidden-sm">Сохранить и Закрыть</span>
                            </button>
                        </div>
                    <? endif ?>
                    <? if (!$item->isNewRecord): ?>
                        <div class="row" style="padding-top: 10px">
	  					
							<? if ($item->delivery_method == 2): ?><br>
								<a class="btn btn-primary btn-sm ajax_call_taxi" title="Создание яндекс заявки" href="#">
									<i class="fa fa-taxi"></i> <span>Создание яндекс заявки</span>
								</a>
								<a class="btn btn-primary btn-sm ajax_call_taxi_update" title="Обновление яндекс заявки" href="#">
									<i class="fa fa-taxi"></i> <span>Обновление яндекс заявки</span>
								</a>
								<a class="btn btn-primary btn-sm ajax_call_taxi_info" title="Инфо" href="#">
									<i class="fa fa-taxi"></i> <span>Инфо о яндекс заявке</span>
								</a>
								<a class="btn btn-primary btn-sm ajax_call_taxi_accept" title="Подтверждение заявки(реальный вызов)" href="#">
									<i class="fa fa-taxi"></i> <span>Подтверждение заявки(реальный вызов)</span>
								</a>
								<a class="btn btn-primary btn-sm ajax_call_taxi_cancel" title="Отмена яндекс заявки" href="#">
									<i class="fa fa-taxi"></i> <span>Отмена яндекс заявки</span>
								</a>
								<br><br><a class="btn btn-primary btn-sm ajax_recall_taxi" title="Повторное создание яндекс заявки" href="#">
									<i class="fa fa-taxi"></i> <span>Повторное создание яндекс заявки(если была отмена)</span>
								</a>	
								<br><br>
							<?php endif; ?>						
                            <a class="btn btn-primary btn-sm" title="Печать" href="<?= Url::to(['orders/print', 'id' => $item->id]) ?>" target="_blank">
                                <i class="fa fa-print"></i>
                                <span>Печать</span>
                            </a>
                            <? if ((
                                $item->manager_id == Yii::$app->user->id
                                || $item->collector_id == Yii::$app->user->id
                                || Yii::$app->user->can('admin')
                            )): ?>
                                <button name="send_message" type="submit" class="btn btn-primary btn-sm" onclick="$(this).val(1)" title="Написать">
                                    <i class="fa fa-envelope-o"></i>
                                    <span>Написать</span>
                                </button>
								<a class="btn btn-primary btn-sm ajax_set_duplicate" title="Создание яндекс заявки" href="#">
									<i class="fa fa-sort-amount-desc"></i> <span>Дубликат</span>
								</a>
                            <? endif ?>
                            <? if (Yii::$app->user->can('admin') && in_array($item->status, [6, 7]) && $item->pay_status != 'success_rollback'): ?>
                                <a class="btn btn-primary btn-sm ajax_rollback_pay" title="Подтвердить возврат" href="#">
                                    <i class="fa fa-check"></i>
                                    <span>Подтвердить возврат</span>
                                </a>
                            <? endif ?>
                            <? if (Yii::$app->user->can('admin') && $item->pay_status == 'wait_surcharge'): ?>
                                <a class="btn btn-primary btn-sm" title="Проверить статус оплаты" href="<?= Url::to(['orders/check-pay-status', 'id' => $item->id]) ?>">
                                    <i class="fa fa-check"></i>
                                    <span>Проверить статус оплаты</span>
                                </a>
                            <? endif ?>
                        </div>
                    <? endif ?>
                    <? if (
                        !$item->isNewRecord
                        && !Yii::$app->user->can('admin')
                    ): ?>
                        <div class="row" style="padding-top: 10px">
                            <?php if ($item->canLockPermission()): ?>
                                <? if ($item->canLock()): ?>
                                    <a data-ajax="get" class="btn btn-success btn-sm" href="<?= Url::to(['orders/lock', 'id' => $item->id]) ?>" title="Принять">
                                        <i class="fa fa-lock"></i>
                                         <span>Принять</span>
                                    </a>
                                <? endif ?>
                                <? if ($item->canUnLock()): ?>
                                    <a data-ajax="get" class="btn btn-danger btn-sm" href="<?= Url::to(['orders/un-lock', 'id' => $item->id]) ?>" title="Отмена">
                                        <i class="fa fa-unlock"></i>
                                        <span>Отмена</span>
                                    </a>
                                <? endif ?>
                            <?php endif ?>
							<? if (Yii::$app->user->identity->withYandexWork != 1) : ?>
								<? if ($item->canChangeStatus(1)): ?>
							  
									<button name="change_status" type="submit" class="btn btn-success btn-sm" onclick="$(this).val(1)" title="На сборку">
										<i class="fa fa-check"></i>
										<span>На сборку</span>
									</button>
								
									
								<? endif ?>
							
							<? endif ?>
                            <? if ($item->canChangeStatus(2)): ?>
                                <button name="change_status" type="submit" class="btn btn-success btn-sm" onclick="$(this).val(2)" title="На подтвержение">
                                    <i class="fa fa-check"></i>
                                    <span>На подтвержение</span>
                                </button>
                            <? endif ?>
                            <? if ($item->canChangeStatus(3)): ?>
                                <button name="change_status" type="submit" class="btn btn-success btn-sm" onclick="$(this).val(3)" title="Подтвердить">
                                    <i class="fa fa-check"></i>
                                    <span>Подтвердить</span>
                                </button>
                            <? endif ?> 
                            <? if ($item->canChangeStatus(4)): ?>
                                <button name="change_status" type="submit" class="btn btn-success btn-sm" onclick="$(this).val(4)" title="На доставку">
                                    <i class="fa fa-check"></i>
                                    <span>На доставку</span>
                                </button>
                            <? endif ?>
                            <? if ($item->canChangeStatus(5)): ?>
                                <button name="change_status" type="submit" class="btn btn-success btn-sm" onclick="$(this).val(5)" title="Заказ оплачен">
                                    <i class="fa fa-check"></i>
									 <? if ($item->delivery_method == 2): ?>							 
										<span>Заказ выполнен</span>
									 <? else: ?>
										<span>Заказ оплачен</span> 
									 <? endif ?>									 
                                </button>
                            <? endif ?>
                        </div>
                        <? if (($item->manager_id == Yii::$app->user->id)
                            || ($item->driver_id == Yii::$app->user->id)
                        ): ?>
                            <div class="row" style="padding-top: 10px">
                                <? if ($item->canChangeStatus(8)): ?>
                                    <button name="change_status" type="submit" class="btn btn-danger btn-sm" onclick="$(this).val(8)" title="Отказ клиента">
                                        <i class="fa fa-check"></i>
                                        <span>Отказ клиента</span>
                                    </button>
                                <? endif ?>
                                <? if ($item->canChangeStatus(9)): ?>
                                    <button name="change_status" type="submit" class="btn btn-danger btn-sm" onclick="$(this).val(9)" title="Клиент не отвечает">
                                        <i class="fa fa-phone"></i>
                                        <span>Клиент не отвечает</span>
                                    </button>
                                <? elseif ($item->canChangeStatus(10)): ?>
                                    <button name="change_status" type="submit" class="btn btn-primary btn-sm" onclick="$(this).val(10)" title="Возобновить">
                                        <i class="fa fa-refresh"></i>
                                        <span>Возобновить</span>
                                    </button>
                                <? endif ?>
                                <? if ($item->canChangeStatus(6)): ?>
                                    <a href="<?= Url::to(['orders/rollback-items', 'id' => $item->id]) ?>" class="btn btn-danger btn-sm" title="Возврат">
                                        <i class="fa fa-arrow-down"></i>
                                        <span>Возврат</span>
                                    </a>
                                <? endif ?>
                            </div>
                        <? endif ?>
                    <? endif ?>
                    <?if(
                        (Yii::$app->user->can('admin')
                        OR Yii::$app->user->can('senior_manager'))
                        AND in_array($item->status, [5])
                    ):?>
                        <div class="row" style="padding-top: 10px">
                            <a data-ajax="get" class="btn btn-warning btn-sm" href="<?= Url::to(['orders/order-to-shaping', 'id' => $item->id]) ?>" title="Вернуть к статусу (К сборке)">
                                <i class="fa fa-unlock"></i>
                                <span>Вернуть к статусу (К сборке)</span>
                            </a>
                        </div>
                    <?endif?>
                </div>
                <?php $i_li = 0; ?>
                <?= Html::ul($groups,
                    [
                        'class' => 'nav nav-tabs tabs-generated',
                        'item'  => function ($item, $index) use (&$i_li) {
                            $context = '';
                            $tab_header_info = $label_html = '';
                            if(isset($item['header_info'])){
                                $tab_header_info = $item['header_info'];
                            }
                            if($index == 'consignment-note'){
                                $label_html = 'label-consignment-note';
                            }
                            if(isset($item['header_info'])){
                                $tab_header_info = $item['header_info'];
                            }
                            $options = ['id' => "page-$index-panel-li"];
                            if (isset($item['icon']) && $item['icon']) {
                                $context = "<i class=\"fa fa-{$item['icon']}\"></i> ";
                            }
                            $context .= Html::tag('span', isset($item['title']) ? $item['title'] : $index, ['class' => 'hidden-xs hidden-sm']);
                            if ($i_li == 0) {
                                $options['class'] = 'active';
                            }
                            $result = Html::tag('li', Html::a($context . '<span class="tab-header-info">' . $tab_header_info . '</span>', "#page-$index-panel", ['class' => $label_html, 'data-toggle' => 'tab']), $options);
                            $i_li++;
                            return $result;
                        }
                    ]) ?>
            </div>  
            <div class="panel form-horizontal">
                <div class="tab-content no-padding-vr">
                    <div class="tab-pane <?=(isset($get['id'])) ? 'active' : 'fade'?>" id="page-items-panel">
                        <div class="panel-body">
                            <?= $this->render('items', [
                                'order' => $item,
                                'form' => $form,
                                'user_types' => $user_types,
                                'user' => $user,
                            ]) ?>
                        </div>
                    </div>
                    <div class="tab-pane <?=(!isset($get['id'])) ? 'active' : 'fade'?>" id="page-main-panel">
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-8">
                                    <?= $form->field($item, 'enable_bonus')->checkbox([], false) ?>
                                    <?= $form
                                        ->field($item, 'isWholesale')
                                        ->dropDownList(ArrayHelper::map( $user_types, 'type', 'label'))
                                        ->label('Статус пользователя')
                                    ?>
                                    <?= $form->field($item, 'status')->dropDownList($item->data_status, ['disabled' => true]) ?>
                                    <?= $form->field($item, 'bonus_use', ['inputOptions' => ['disabled' => !$is_admin]]) ?>
                                    <?= $form->field($item, 'isEntity')->checkbox([], false) ?>
                                    <?= $form->field($item, 'city_id')->dropDownList(
                                        ArrayHelper::merge(
                                            ['' => "Нет выбран"],
                                            City::find()->select(['name', 'id'])->indexBy('id')->column()
                                        )) ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <?= $form->field($item, 'user_name') ?>
									<?= $form->field($item, 'user_phone', [
                                        'template' => '{label} <div class="col-sm-10">{input}{error}<span class="user-phone-exists" data-value="" data-state=""></span></div>',
                                    ])->widget(\yii\widgets\MaskedInput::className(),[
										'mask' => '+7(999)-999-9999',
										'definitions' => [
											'maskSymbol' => '_'
										],
									]);?>
                                    <?= $form->field($item, 'date_delivery')->widget(DateTimePicker::className(), [
                                        'language'       => 'ru',
                                        'size'           => 'ms',
                                        'template'       => '{input}',
                                        'pickButtonIcon' => 'glyphicon glyphicon-time',
                                        'clientOptions'  => [
                                            'format'    => 'dd.mm.yyyy',
                                            'minView'   => 2,
                                            'autoclose' => true,
                                            'todayBtn'  => true
                                        ]
                                    ]); ?>
                                    <?= $form->field($item, 'time_delivery') ?>
                                    <?= $form->field($item, 'pickpoint_id')->dropDownList(
                                    //$pickpoints[$item->city_id], [
                                        $pickpoints, [
                                        'prompt' => 'Выбрать'
                                    ]) ?>
                                    <? if (Yii::$app->user->can('collector')): ?>
                                        <?= $form->field($item, 'id_1c') ?>
                                    <? endif; ?>
                                </div>
                                <div class="col-md-6">
                                    <?= $form->field($item, 'user_mail', [
                                        'template' => '{label} <div class="col-sm-10">{input}{error}<span class="user-mail-exists" data-value="" data-state=""></span></div>',
                                    ]) ?>
                                    <?= $form->field($item, 'user_address') ?>
                                    <?= $form->field($item, 'payment')->dropDownList($item->data_payment) ?>
                                    <?= $form->field($item, 'user_comments')->textarea() ?>
                                    <?= $form->field($item, 'admin_comments')->textarea() ?>
									<?= $form->field($item, 'delivery_method')->dropDownList($item->delivery_method_) ?>
									<?php  echo $form->field($item, 'coordinates_json_yandex', ['options' => ['id' => 'coordinates_json_yandex']])->hiddenInput(['value' => null])->label(false); ?>			
                                </div>
                            </div>
							<div class="string addr address_delivery_yandex">
							  
								
								<div id="mapyandex">
									<?php if ($item->coordinates_json_yandex) {
										echo '<span style="color:red">ИНСТРУКЦИЯ для создания яндекс заявки:<br>'.
										'1. Выбрать пункт самовывоза<br>'.
										'2. Ввести адрес на карте для расчета стоимости доставки<br>'.
										'3. Если надо записать остальные данные клиента<br>'.
										'4. Нажать кнопку - Сохранить<br>'.
										'5. Нажать кнопку - Создание заявки<br>'.
										'Дополнение: если необходимо в процессе сменить пункт самовывоза, то начинайте сначала с пункта 1.</span><br>'.
										'Дополнение(КВАРТИРА): квартиру нужно самому добавлять в адрес через запятую. Правило заполнения - микрорайон Коктем-2(улица), 34(дом), кв.30(квартира).</span><br>'.
										'<span style="color:green">Внимание!!! Данные по доставе (точка назначения('.
										$item->user_address
										.') и сумма доставки ('.
										$item->price_delivery.
										'т. )) уже были сохранены. Если Вы хотите изменить эти данные, то выберите на карте новую точку назначения и сохраните заказ!!!</span>';
									} ?>
								
								</div>
							</div>
                        </div>
                    </div>
                    <? if (
                        (
                            Yii::$app->user->id == $item->manager_id ||
                            Yii::$app->user->id == $item->collector_id ||
                            Yii::$app->user->can('senior_manager') ||
                            Yii::$app->user->can('admin')
                        ) &&
                        !$item->isNewRecord
                    ): ?>
                        <?php
                        /**
                         * @var $relation_data SUser[]
                         */
                        $query         = new ActiveQuery(SUser::className());
                        $relation_data = $query->all();
                        $all_manager   = $all_collector = $all_driver = ['Нет'];
                        foreach ($relation_data as $key => $value) {
                            switch ($value->role) {
                                case 'manager':
                                    $all_manager[$value->id] = $value->username;
                                    break;
                                case 'collector':
                                    $all_collector[$value->id] = $value->username;
                                    break;
                                case 'driver':
                                    $all_driver[$value->id] = $value->username;
                                    break;
                                default:
                                    $all_manager[$value->id] = $value->username;
                                    break;
                            }
                        }
                        ?>
                        <div class="tab-pane fade" id="page-responsible-panel">
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-md-8">
                                        <?= $form->field($item, 'manager_id')->dropDownList($all_manager, ['disabled' => !$is_admin]) ?>
                                        <?= $form->field($item, 'collector_id')->dropDownList($all_collector, ['disabled' => !$is_admin]) ?>
                                        <?= $form->field($item, 'driver_id', [
                                            'enableAjaxValidation' => true
                                        ])
                                            ->dropDownList($all_driver, ['disabled' => !($is_admin || Yii::$app->user->id == $item->collector_id)]) ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <? endif ?>

                    <? if ($undo): ?>
                        <div class="tab-pane fade" id="page-undo-panel">
                            <div class="panel-body">
                                <?= $this->render('view_rollback_items', ['order' => $item, 'form' => $form]) ?>
                            </div>
                        </div>
                    <? endif ?>
                    <? if (!$item->isNewRecord): ?>
                        <?php
                        /**
                         * @var $order_history OrdersHistory[]
                         */
                        $order_history = OrdersHistory::find()->with(['user'])->where(['order_id' => $item->id])->orderBy(['created_at' => SORT_ASC])->all()
                        ?>
                        <div class="tab-pane fade" id="page-history-panel">
                            <div class="panel-body">
                                <div class="table-responsive table-primary row col-xs-6">
                                    <table class="table table-striped table-hover">
                                        <colgroup>
                                            <col width="250px">
                                            <col>
                                            <col>
                                        </colgroup>
                                        <? if (false): ?>
                                            <thead>
                                            <tr>
                                                <th>Отправитель</th>
                                                <th>Причина</th>
                                                <th>Получатель</th>
                                                <th>Сумма</th>
                                                <th>Статус</th>
                                            </tr>
                                            </thead>
                                        <? endif ?>
                                        <tbody>
                                        <?php foreach ($order_history as $history): ?>
                                            <tr>
                                                <td>
                                                    <?= Yii::$app->formatter->asDate($history->created_at, 'd MMMM Y г. HH:mm'); ?>
                                                </td>
                                                <td>
													<?= $history->data_action[$history->action] ?>
													<?=(!empty($history->claim_id) ? $history->claim_id : "")?>
												</td>
                                                <td><?= $history->user_name ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <?php
                        $url_check = Url::to(['kassa/check-money']);
                        $pickpoints = \yii\helpers\Json::encode($pickpoints);
                        $this->registerJs(<<<JS
//JS
$('.ajax_check_money').on('click', function (e) {
    e.preventDefault();
    var obj = $(this);
    var id = $(this).data('id');
    $.ajax({
        url: '{$url_check}',
        type: 'POST',
        dataType: 'JSON',
        data: {id: id},
        success: function (data) {
            if (typeof data.success != 'undefined') {
                $.growl.notice({title: 'Успех', message: 'Перевод подтверждён'});
                obj.replaceWith(data.content)
            } else {
                $.growl.error({title: 'Ошибка', message: data.error, duration: 5000});
            }
        },
        error: function () {
            $.growl.error({title: 'Ошибка', message: 'Произошла ошибка на стороне сервера!', duration: 5000});
        }
    });
});

var pickpoints = {$pickpoints};
$('#orders-city_id').on('change', function (e) {
    
});
JS
                        )
                        ?>
                    <? endif ?>
                    <div class="tab-pane" id="page-consignment-note-panel">
                        <div class="panel-body">
                            <div class="content-consignment">
                                <div class="row">






                                        <div class="download-consignment-note <?=($item->invoice_file) ? 'dn' : ''?>">
                                            <?=$form->field($item, 'consignment_note_file', [
                                                'template' => '
{label}<div class="col-md-10" style="margin-bottom: 20px;">
    <div class="btn choose-file" style="border: 1px solid #23638c;background-color:transparent;border-radius:3px;">Выберите файл...</div>
    <div style="position: absolute;left: -3000px;">{input}</div>
    <span class="label label-info" id="upload-file-info" style="position:absolute;bottom:-20px;left:15px;"></span>
    ' . Html::button(
                                                        Html::img(
                                                            '@web/images/animate/loading.gif',
                                                            ['alt' => 'Загрузка',
                                                                'style' => 'display:none;position:absolute;width:25px;right:-35px;top:2px;']
                                                        ) . 'Загрузить',
                                                        ['class' => 'btn btn-primary send-file', 'style' => 'position: relative;']
                                                    ) . '
    <span class="progress-percent" style="margin-left:40px;"></span>
    {error}
</div>'
                                            ])->fileInput([
                                                'class' => 'upload-file',
                                                'onchange' => '$("#upload-file-info").html($(this).val());',
                                                'accept' => '.jpg, .jpeg, .png, .pdf'
                                            ]) ?>
                                        </div>
                                        <div class="view-consignment-note <?=(!$item->invoice_file) ? 'dn' : ''?>">
                                            <label class="col-md-2 control-label"><b>Файл накладной</b></label>
                                            <div class="col-md-10 btns-consignment-note">
                                                <div class="h4">Файл загружен</div>
                                                <a href="<?=($item->invoice_file?:'#')?>" target="_blank">
                                                    <button type="button" class="btn btn-primary invoice-link-download">Скачать</button>
                                                </a>
                                                &nbsp;&nbsp;&nbsp;
                                                <?
                                                echo Html::button( 'Удалить', [
                                                    'class' => 'btn btn-danger invoice-delete',
                                                    'data-type' => 'delete'
                                                ]);
                                                echo Html::img( '@web/images/animate/loading.gif',
                                                    [
                                                        'alt' => 'Загрузка',
                                                        'class' => 'loading',
                                                        'style' => 'display:none;position:absolute;width:25px;left:-20px;bottom:2px;'
                                                    ]
                                                );
                                                ?>
                                            </div>
                                            <br><br><br><br>
                                        </div>
                                    <hr>
                                    <div class="row">
                                        <label class="col-md-2 control-label"><b>Счёт для оплаты</b></label>
                                        <div class="col-md-8">
                                            Распечатать или сохранить в PDF<br>
                                            <?if(!$is_requisites):?>
                                            <span style="color: red;">Пользователь, сделавший заказ, не имеет реквизитов.</span>
                                            <br>
                                            <?endif?>
                                            <br>
                                            <a href="<?=Url::to(['orders/print-invoice-payment', 'id' => $item->id])?>" target="_blank">
                                                <button type="button" class="btn btn-primary" <?=(!$is_requisites)?'disabled':''?>>Печать</button>
                                            </a>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <?php AdminActiveForm::end(); ?>
        </div>
    </section>
	
<?php

$coordinates = explode(',', '76.91835764257804,43.24041321668576');//43.233781,76.935404

$url_api_assessmentyandex = Url::to(['api/assessmentyandex']);
$url_call_taxi_info = Url::to(['orders/calltaxiinfo', 'id' => $item->id]);
$url_call_taxi =Url::to(['orders/calltaxi', 'id' => $item->id, 'type' => 'create', 'repeat' => false]);
$url_call_taxi_update =Url::to(['orders/calltaxi', 'id' => $item->id, 'type' => 'edit', 'repeat' => false]);
$url_call_taxi_cancel =Url::to(['orders/calltaxicancel', 'id' => $item->id]);
$url_call_taxi_accept =Url::to(['orders/calltaxiaccept', 'id' => $item->id]);
$url_recall_taxi =Url::to(['orders/calltaxi', 'id' => $item->id, 'type' => 'create', 'repeat' => true]);
$url_rollback = Url::to(['orders/rollback-pay', 'id' => $item->id]);
$url_set_duplicate= Url::to(['orders/setduplicate', 'id' => $item->id]);
$url_consignment_note_file = Url::to(['orders/load-consignment-note']);
$url_invoice_operations = Url::to(['orders/invoice']);
$urlGetUser = Url::to(['users/get-user']);
$sum = 0;
$this->registerJs(<<<JS
//JS
$('.ajax_set_duplicate').on('click', function (e) {
    e.preventDefault();
	bootbox.confirm({
        message: '<h4>Внимание</h4>Команда "Дубликат" - процедура необратимая!<br><font color="red">ВЫ УВЕРЕНЫ?</font>',
        callback: function (result) {
            if (result) {
                $.ajax({
					url: '{$url_set_duplicate}',
					type: 'GET',
					dataType: 'JSON',
					success: function (data) {
						var mes = data.message;
						$.growl.notice({title: 'Успех', message: mes, duration: 5000});
						location.reload();
					},
					error: function () {
						$.growl.error({title: 'Ошибка', message: 'Произошла ошибка на стороне сервера!', duration: 5000});
					}
				});
            }
        },
        className: "bootbox-sm"
    });
});

$('.ajax_rollback_pay').on('click', function (e) {
    e.preventDefault();
    $.ajax({
        url: '{$url_rollback}',
        type: 'GET',
        dataType: 'JSON',
        success: function (data) {
            if (typeof data.js != 'undefined') {
                eval(data.js)
            }
            if (typeof data.error != 'undefined') {
                $.growl.error({title: 'Ошибка', message: data.error, duration: 5000});
            }
        },
        error: function () {
            $.growl.error({title: 'Ошибка', message: 'Произошла ошибка на стороне сервера!', duration: 5000});
        }
    });
});

$('.ajax_call_taxi').on('click', function (e) {
    e.preventDefault();
    $.ajax({
        url: '{$url_call_taxi}',
        type: 'GET',
        dataType: 'JSON',
        success: function (data) {
			var mes = data.code;
			$.growl.notice({title: 'Успех', message: mes, duration: 5000});
			
        console.log(data); 
        },
        error: function () {
            $.growl.error({title: 'Ошибка', message: 'Произошла ошибка на стороне сервера!', duration: 5000});
        }
    });
});

$('.ajax_call_taxi_update').on('click', function (e) {
    e.preventDefault();
    $.ajax({
        url: '{$url_call_taxi_update}',
        type: 'GET',
        dataType: 'JSON',
        success: function (data) {
			var mes = data.code;
			$.growl.notice({title: 'Успех', message: mes, duration: 5000});
        },
        error: function () {
            $.growl.error({title: 'Ошибка', message: 'Произошла ошибка на стороне сервера!', duration: 5000});
        }
    });
});

$('.ajax_recall_taxi').on('click', function (e) {
    e.preventDefault();
    $.ajax({
        url: '{$url_recall_taxi}',
        type: 'GET',
        dataType: 'JSON',
        success: function (data) {
			var mes = data.code;
			$.growl.notice({title: 'Успех', message: mes, duration: 5000});
        },
        error: function () {
            $.growl.error({title: 'Ошибка', message: 'Произошла ошибка на стороне сервера!', duration: 5000});
        }
    });
});

$('.ajax_call_taxi_cancel').on('click', function (e) {
    e.preventDefault();
    $.ajax({
        url: '{$url_call_taxi_cancel}',
        type: 'GET',
        dataType: 'JSON',
        success: function (data) {
			var mes = data.code;
			$.growl.notice({title: 'Успех', message: mes, duration: 5000});
			
        console.log(data); 
        },
        error: function () {
            $.growl.error({title: 'Ошибка', message: 'Произошла ошибка на стороне сервера!', duration: 5000});
        }
    });
});


$('.ajax_call_taxi_info').on('click', function (e) {
    e.preventDefault();
    $.ajax({
        url: '{$url_call_taxi_info}',
        type: 'GET',
        dataType: 'JSON',
        success: function (data) {
			var mes = data.code;
			$.growl.notice({title: 'Успех', message: mes, duration: 5000});
			
        console.log(data); 
        },
        error: function () {
            $.growl.error({title: 'Ошибка', message: 'Произошла ошибка на стороне сервера!', duration: 5000});
        }
    });
});

$('.ajax_call_taxi_accept').on('click', function (e) {
    e.preventDefault();
    $.ajax({
        url: '{$url_call_taxi_accept}',
        type: 'GET',
        dataType: 'JSON',
        success: function (data) {
			var mes = data.code;
			$.growl.notice({title: 'Успех', message: mes, duration: 5000});
			
        console.log(data); 
        },
        error: function () {
            $.growl.error({title: 'Ошибка', message: 'Произошла ошибка на стороне сервера!', duration: 5000});
        }
    });
});

//ymaps.ready(init_yandex);

function init_yandex() {   

    var myMap = new ymaps.Map('mapyandex', {
            center: [{$coordinates[0]}, {$coordinates[1]}],
            zoom: 12,
            controls: []
        }),  

        routePanelControl = new ymaps.control.RoutePanel({
            options: {
                showHeader: true,
				maxWidth: '380px', 
                title: 'Расчёт доставки'
            }
        }),
        zoomControl = new ymaps.control.ZoomControl({
            options: {
                size: 'small',
                float: 'none',
                position: {
                    bottom: 145,
                    right: 10
                }
            }
        });
    // Пользователь сможет построить только автомобильный маршрут.
    routePanelControl.routePanel.options.set({
        types: {auto: true}
    });

    // Если вы хотите задать неизменяемую точку "откуда", раскомментируйте код ниже.
    routePanelControl.routePanel.state.set({
        fromEnabled: true,
        from: 'проспект Сакена Сейфуллина, 617'
     });
 
    myMap.controls.add(routePanelControl).add(zoomControl);

    routePanelControl.routePanel.getRouteAsync().then(function (route) {

        route.model.setParams({results: 1}, true);

        route.model.events.add('requestsuccess', function () {
	
			var activeRoute = route.getActiveRoute();  
				  
			if (activeRoute) {
								
				var jsonString = JSON.parse(JSON.stringify(activeRoute.properties._data.boundedBy));

				$('#orders-coordinates_json_yandex').attr('value',jsonString);
						
				var name = $('.ymaps-2-1-78-route-panel-input__input[placeholder*=Куда]').val();
 
				 var request_data = {
					'longitude_from':'76.935404',
					'latitude_from':'43.233781',
					'longitude_to':activeRoute.properties._data.boundedBy[1][1],
					'latitude_to':activeRoute.properties._data.boundedBy[1][0],
					'name': name
				};
   
				$.ajax({
					url: '{$url_api_assessmentyandex}',
					type: 'POST',
					dataType: 'JSON',
					data: request_data,
					success: function (data_return) {
		
						if (!data_return.code) {
							$.growl.error({title: 'Ошибка', message: "Выберите другое назначение", duration: 5000});
					
						} else {
							
							if (!data_return.coord_answer) {
								$.growl.error({title: 'Ошибка', message: "Выберите другое назначение", duration: 5000});
								$('#loader').hide();
							}

							$('#orders-coordinates_json_yandex').attr('value',data_return.coord_answer);
					
							$('#delivery_').html('<b>' + data_return.code + ' т.</b>');
							$('#order-delivery_yandex').val(data_return.code);
							$('#delivery_clone').html(data_return.code);

							var sum_yandex = Number({$sum}) + Number(data_return.code);

							$('#cart_right .cartLine:nth-child(5) .basket_sum_full').html('' + sum_yandex+ ' т.');

							$('#pr_delivery').html('<b>Стоимость доставки: ' + data_return.code + ' т.</b>');
							$.growl.notice({title: 'Первичная оценка', message: 'Стоимость яндекс доставки: ' + data_return.code, duration: 5000});

							var name = $('.ymaps-2-1-78-route-panel-input__input[placeholder*=Куда]').val();
							
							
							var order_city = $('#orders-city_id option:selected').text();
							
							$('#orders-user_address').val(name);
							
							$('#price_delivery').val(sum_yandex);
						
							var order_sum = $('#order_sum').text();
							
							var result = Number(order_sum) + Number(sum_yandex);
							$('#full_price').html(result);						
							
						}		
						
					},
					error: function () {
					
					}
				}); 
    
				var length = route.getActiveRoute().properties.get("distance"),

				balloonContentLayout = ymaps.templateLayoutFactory.createClass(
				'<span>Расстояние: ' + length.text + '.</span><br/>' +
				'<span id="pr_delivery" style="font-weight: bold; height:200px; font-style: italic">Стоимость доставки: 0 т.</span>');

				route.options.set('routeBalloonContentLayout', balloonContentLayout);

				activeRoute.balloon.open(); }			
		});
    });

}

ymaps.ready(init_);

function init_() {

	  var myMap = new ymaps.Map('mapyandex', {
             center: [43.224729,76.93376],
            zoom: 12,
            controls: ['geolocationControl', 'searchControl']
        }),
        deliveryPoint = new ymaps.GeoObject({
            geometry: {type: 'Point'},
            properties: {iconCaption: 'Адрес'}
        }, {
            preset: 'islands#blackDotIconWithCaption',
            draggable: true,
            iconCaptionMaxWidth: '215'
        }),
        searchControl = myMap.controls.get('searchControl');
    searchControl.options.set({noPlacemark: true, placeholderContent: 'Введите адрес доставки'});
    myMap.geoObjects.add(deliveryPoint);
  
    function onZonesLoad(json) {
        // Добавляем зоны на карту.
        var deliveryZones = ymaps.geoQuery(json).addToMap(myMap);
        // Задаём цвет и контент балунов полигонов.
        deliveryZones.each(function (obj) {
            obj.options.set({
                fillColor: obj.properties.get('fill'),
                fillOpacity: obj.properties.get('fill-opacity'),
                strokeColor: obj.properties.get('stroke'),
                strokeWidth: obj.properties.get('stroke-width'),
                strokeOpacity: obj.properties.get('stroke-opacity')
            });
            obj.properties.set('balloonContent', obj.properties.get('description'));
        });

        // Проверим попадание результата поиска в одну из зон доставки.
        searchControl.events.add('resultshow', function (e) {
            highlightResult(searchControl.getResultsArray()[e.get('index')]);
        });

        // Проверим попадание метки геолокации в одну из зон доставки.
        myMap.controls.get('geolocationControl').events.add('locationchange', function (e) {
            highlightResult(e.get('geoObjects').get(0));
        });

        // При перемещении метки сбрасываем подпись, содержимое балуна и перекрашиваем метку.
        deliveryPoint.events.add('dragstart', function () {
            deliveryPoint.properties.set({iconCaption: '', balloonContent: ''});
            deliveryPoint.options.set('iconColor', 'black');
        });

        // По окончании перемещения метки вызываем функцию выделения зоны доставки.
        deliveryPoint.events.add('dragend', function () {
            highlightResult(deliveryPoint);
        });

        function highlightResult(obj) {
            // Сохраняем координаты переданного объекта.
            var coords = obj.geometry.getCoordinates(),
            // Находим полигон, в который входят переданные координаты.
                polygon = deliveryZones.searchContaining(coords).get(0);

            if (polygon) { 
	
				console.log(polygon);	
				console.log(polygon.properties._data.lat);
					
                // Уменьшаем прозрачность всех полигонов, кроме того, в который входят переданные координаты.
                deliveryZones.setOptions('fillOpacity', 0.4);
                polygon.options.set('fillOpacity', 0.8);
                // Перемещаем метку с подписью в переданные координаты и перекрашиваем её в цвет полигона.
                deliveryPoint.geometry.setCoordinates(coords);
                deliveryPoint.options.set('iconColor', polygon.properties.get('fill'));
                // Задаем подпись для метки.
                if (typeof(obj.getThoroughfare) === 'function') {
					console.log(obj.geometry._coordinates[0]);
					
                    setData(obj);
					
					var address = [obj.getThoroughfare(), obj.getPremiseNumber(), obj.getPremise()].join(',').slice(0,-1);
											
					//  var address = [obj.getThoroughfare(), obj.getPremiseNumber(), obj.getPremise()].join(' ');
					if (address.trim().length < 5) {
						address_ = obj.getAddressLine().split(",");
						address = address_[2] + ',' + address_[3]; 									
					}
					
					 var request_data = {
						'longitude_from':polygon.properties._data.lat,
						'latitude_from':polygon.properties._data.lon,
						'longitude_to':obj.geometry._coordinates[1],
						'latitude_to':obj.geometry._coordinates[0],
						'name':address,
						'pick':polygon.properties._data.pick,
						'pickselect': $('#orders-pickpoint_id').val()
					};
							
					$('#loader').show();
						$.ajax({
						url: '{$url_api_assessmentyandex}',
						type: 'POST',
						dataType: 'JSON',
						data: request_data,
						success: function (data_return) {
			
							if (!data_return.code) {
								$.growl.error({title: 'Ошибка', message: "Выберите другое назначение", duration: 5000});
						
							} else {
								
								if (!data_return.coord_answer) {
									$.growl.error({title: 'Ошибка', message: "Выберите другое назначение", duration: 5000});
									$('#loader').hide();
								}

								$('#orders-coordinates_json_yandex').attr('value',data_return.coord_answer);
						
								$('#delivery_').html('<b>' + data_return.code + ' т.</b>');
								$('#order-delivery_yandex').val(data_return.code);
								$('#delivery_clone').html(data_return.code);

								var sum_yandex = Number({$sum}) + Number(data_return.code);

								$('#cart_right .cartLine:nth-child(5) .basket_sum_full').html('' + sum_yandex+ ' т.');

								$('#pr_delivery').html('<b>Стоимость доставки: ' + data_return.code + ' т.</b>');
								$.growl.notice({title: 'Первичная оценка', message: 'Стоимость яндекс доставки: ' + data_return.code, duration: 5000});

							//	var name = $('.ymaps-2-1-78-route-panel-input__input[placeholder*=Куда]').val();
								
								 							  
							//	$('#orders-pickpoint_id option[value="' + data_return.pick + '"]').prop('selected', true);
								
								var order_city = $('#orders-city_id option:selected').text();
								
								$('#orders-user_address').val(data_return.name);
								
								$('#price_delivery').val(sum_yandex);
							
								var order_sum = $('#order_sum').text();
								
								var result = Number(order_sum) + Number(sum_yandex);
								$('#full_price').html(result);						
								
							}									
						},
						error: function () {
						
						}
					}); 									
                } else {				
                    // Если вы не хотите, чтобы при каждом перемещении метки отправлялся запрос к геокодеру,
                    // закомментируйте код ниже.
                    ymaps.geocode(coords, {results: 1}).then(function (res) {
                        var obj = res.geoObjects.get(0);
						
                        setData(obj);
                    });
                }
            } else {
                // Если переданные координаты не попадают в полигон, то задаём стандартную прозрачность полигонов.
                deliveryZones.setOptions('fillOpacity', 0.4);
                // Перемещаем метку по переданным координатам.
                deliveryPoint.geometry.setCoordinates(coords);
                // Задаём контент балуна и метки.
                deliveryPoint.properties.set({
                    iconCaption: 'Доставка транспортной компанией',
                    balloonContent: 'Cвяжитесь с оператором',
                    balloonContentHeader: ''
                });
                // Перекрашиваем метку в чёрный цвет.
                deliveryPoint.options.set('iconColor', 'black');
            }

            function setData(obj){
                var address = [obj.getThoroughfare(), obj.getPremiseNumber(), obj.getPremise()].join(' ');
                if (address.trim() === '') {
                    address = obj.getAddressLine();
                }
                var price = polygon.properties.get('description');
                price = price.match(/<strong>(.+)<\/strong>/)[1];
                deliveryPoint.properties.set({
                    iconCaption: address,
                    balloonContent: address,
                    balloonContentHeader: price
                });
            }
        }
    }
 

    $.ajax({
        url: '../../uploads/data.geojson',
        dataType: 'json',
        success: onZonesLoad
    });
}

if ($('#orders-delivery_method').val() == 2) {
	$('#mapyandex').css('display', 'block');
} else {
	$('#mapyandex').css('display', 'none');
}

$('#orders-delivery_method').change(function(){	 
	if ($(this).val() == 2) {
		$('#mapyandex').css('display', 'block');
	} else {
		$('#mapyandex').css('display', 'none');
	}
});

$('#orders-pickpoint_id').change(function(){	 
	var name = $('#orders-pickpoint_id option:selected').text();
	var address = name.split("~"); 
	$('.ymaps-2-1-78-route-panel-input__input[placeholder*=Откуда]').val(address[1]);
});

$("form.savez").on('beforeSubmit', function(){  

    var url = $(this).attr('action');

	var data = $(this).serialize();
	console.log(data);
	$.ajax({
		url: url,
		type: 'POST', 
		data: data,
		success: function(res){ 
		
			console.log(res);
			
			if (res.js) {
				eval(res.js);
			}
		
			if (res.message['success']) {
				$.growl.notice({title: 'Успех', message: res.message['success']});
			//	window.location.href = '/';
			}else if (res.message['error']) {  
					$.growl.error({title: res.message['error'], message: "Что-то пошло не так!!!", duration: 5000});
			} 
		
		},
		error: function(res){
			
			console.log(res);
			$.growl.error({title: 'Ошибка', message: "Произошла ошибка на стороне сервера!", duration: 5000});
		}
	});      
	return false;
});

$('.choose-file').on('click', function(){
    $('.upload-file').trigger('click');
});

$('.send-file').on('click', function (e) {
    var tthis = $(this),
        res = $('.res'),
        wrap = $('#order_edit'),
        tabLabel = wrap.find('.label-consignment-note'),
        btnDownload = wrap.find('.invoice-link-download'),
        blockDownload = wrap.find('.download-consignment-note'),
        blockInfo = wrap.find('.view-consignment-note'),
        label = wrap.find('#upload-file-info'),
        inputFile = wrap.find('.upload-file'),
        progress = wrap.find('.download-consignment-note .progress-percent'),
        load = tthis.find('img'),
        file = $('.upload-file');
    
    res.html('result');

    if (file.val() == '') {
        $.growl.error({title: 'Внимание', message: 'Выберите файл', duration: 5000});
        return;
    }

    // Проверка файла на тип
    var accept = ['image/png', 'image/jpg', 'image/jpeg', 'application/pdf'];
    if (!accept.includes(file[0].files[0].type)) {
        $.growl.error({title: 'Внимание', message: 'Не верный тип файла', duration: 5000});
        return;
    }

    var formData = new FormData();
    formData.append('consignment_note_file', file[0].files[0]);

    var Data = {
        url: '{$url_consignment_note_file}?order_id={$item->id}',
        formData: formData,
        method: 'post',
    };

    //cl(Data);
    //return;

    $.ajax({
        url: Data.url,
        type: Data.method,
        contentType: false,
        processData: false,
        data: formData,
        dataType: 'json',
        beforeSend: function () {
            load.fadeIn(100);
            tthis.prop('disabled', true);
        },
        xhr: function () {
            var xhr = $.ajaxSettings.xhr(); // получаем объект XMLHttpRequest
            xhr.upload.addEventListener('progress', function (evt) { // добавляем обработчик события progress (onprogress)
                if (evt.lengthComputable) { // если известно количество байт
                    // высчитываем процент загруженного
                    var percentComplete = Math.ceil(evt.loaded / evt.total * 100);
                    // устанавливаем значение в атрибут value тега progress
                    // и это же значение альтернативным текстом для браузеров, не поддерживающих &lt;progress&gt;
                    progress.html(percentComplete + '%');
                }
            }, false);
            return xhr;
        }
    }).done(function (data) {
        //cl(data);
        //res.html('Done<br><pre>' + prettyPrintJson.toHtml(data) + '</pre>');
        if (data.message.success) {
            $.growl.notice({title: 'Успешно', message: data.message.success, duration: 5000});
        }else{
            $.growl.warning({title: 'Внимание', message: data.message.warning, duration: 7000});
        }
        if (data.js) { eval(data.js); }

        blockDownload.fadeOut(100, function(){
            blockInfo.fadeIn(100);
        });

        label.html('');
        inputFile.val('');
        tabLabel.find('span.tab-header-info').html(' (Загружена)');
        btnDownload.closest('a').attr('href', data.invoice_file);

    }).fail(function (data) {
        //cl(data);
        //res.html('fail<br>' + JSON.stringify(data));
        $.growl.error({title: 'Внимение', message: 'Произошла ошибка на стороне сервера', duration: 5000});
    }).always(function(){
        load.fadeOut(100);
        tthis.prop('disabled', false);
        setTimeout(function(){
            progress.html('');
        }, 2000);
    });
});

$('#order_edit .view-consignment-note button.invoice-delete').on('click', function (e) {
    confirmConsignmentNoteDelete($(this))
});

function confirmConsignmentNoteDelete(btnContext){
    var deleteFileMessage = 'Вы действительно хотите удалить файл?';
    bootbox.confirm({
        message: deleteFileMessage,
        buttons: {
            confirm: {
                label: 'Удалить',
                className: 'btn-success'
            },
            cancel: {
                label: 'Отмена',
                className: 'btn-danger'
            }
        },
        callback: function (result) {
            if(result){
				// Если нажата кнопка подтверждения
				// Отправляем запрос на удаление
                sendConsignmentNoteOperation(btnContext)
            }else{}
        },
        className: "bootbox-sm"
    });
}

function sendConsignmentNoteOperation(context){
    var res = $('.res'),
        wrap = $('#order_edit'),
        tabLabel = wrap.find('.label-consignment-note'),
        btnDownload = wrap.find('.invoice-link-download'),
        blockDownload = wrap.find('.download-consignment-note'),
        blockInfo = wrap.find('.view-consignment-note'),
        load = context.parent().find('img.loading'),
        Data = {
            url: '{$url_invoice_operations}?order_id={$item->id}',
            method: 'post',
            operation: context.attr('data-type')
        };

    //cl(Data);
    //return;

    $.ajax({
        url: Data.url,
        method: Data.method,
        dataType: 'json',
        cache: false,
        data: Data,
        beforeSend: function () {
            load.fadeIn(100);
            context.prop('disabled', true);
        },
    }).done(function (data) {
        //cl(data);
        //res.html('Done<br><pre>' + prettyPrintJson.toHtml(data) + '</pre>');
        if (data.message.success) {
            $.growl.notice({title: 'Успешно', message: data.message.success, duration: 5000});
        }else{
            $.growl.warning({title: 'Внимание', message: data.message.warning, duration: 7000});
        }
        if (data.js) { eval(data.js); }

        blockInfo.fadeOut(100, function(){
            blockDownload.fadeIn(100);
        });

        btnDownload.closest('a').attr('href', '#');
        tabLabel.find('span.tab-header-info').html('');

    }).fail(function (data) {
        //cl(data);
        //res.html('fail<br>' + JSON.stringify(data));
        $.growl.error({title: 'Внимение', message: 'Произошла ошибка на стороне сервера', duration: 5000});
    }).always(function(){
        load.fadeOut(100);
        context.prop('disabled', false);
    });
}

$('#orders-user_mail, #orders-user_phone', '#order_edit').blur(function(){
    var tthis = $(this),
        attribute = tthis.attr('id').replace('orders-', ''),
        copy = tthis.parent().find('span');

    if(tthis.val() !== copy.attr('data-value')){
        getUser.call(this, attribute, tthis.val());
    }

});

/**
 * Атрибут data-value фиксирует содержимое поля,
 * если вводится другое содержимое поля, и если data-value не пусто,
 * то происходит проверка на соответствие вводимого содержимого с содержимым data-value,
 * если нет совпадения, то отправляем запрос на сервер за новыми данными,
 * если соответствие есть, то не делаем запрос, а просто вставляем data-value в поле.
 */
function getUser(attribute, value){
    if(value != ''){
        var tthis = $(this),
            load = $('.layer-loading'),

            iswholesale = $('#orders-iswholesale'),
            isentity = $('#orders-isentity'),
            userName = $('#orders-user_name'),
            city = $('#orders-city_id'),

            userPhone = $('#orders-user_phone'),
            userMail = $('#orders-user_mail'),
            noticeMail = $('.user-mail-exists'),
            noticePhone = $('.user-phone-exists')
            notice = tthis.parent().find('span');
            
        var res = $('.res');
        
        $.ajax({
            url: '{$urlGetUser}',
            type: 'post',
            dataType: 'json',
            cache: false,
            data: { attribute: attribute, value: value},
            beforeSend: function(){ load.fadeIn(100); }
        }).done(function(data){
            // res.html('<pre>' + prettyPrintJson.toHtml(data) + '</pre>');
            if(data.user != '0' && data.user != null){
                // Заполняем телефон
                userPhone.val(data.user.phone);
                // Заполняем email
                userMail.val(data.user.email);
                // Заполняем data атрибут предупреждения поля номера телефона
                noticePhone.attr('data-value', data.user.phone);
                // Заполняем data атрибут предупреждения поля email
                noticeMail.attr('data-value', data.user.email);

                // 
                // if(data.user.phone == null && noticePhone.attr('data-state') == 'exists'){
                if(data.user.phone == null || data.user.phone == ''){
                    noticePhone.html('').attr('data-value', '').attr('data-state', '');
                }else{
                    if(data.user.phone != null && data.user.phone != ''){
                        noticePhone.html('Пользователь с таким номером телефона найден')
                            .attr('data-state', 'exists');
                    }
                }
                
                // if(data.user.email == null && noticeMail.attr('data-state') == 'exists'){
                if(data.user.email == null || data.user.email == ''){
                    noticeMail.html('').attr('data-value', '').attr('data-state', '');
                }else{
                    if(data.user.email != null && data.user.email != ''){
                        noticeMail.html('Пользователь с таким email найден')
                            .attr('data-state', 'exists');
                    }
                }

                iswholesale.val(data.user.isWholesale);
                if(data.user.isEntity == '1'){
                    isentity.prop('checked', true);
                }else{
                    isentity.prop('checked', false);
                }
                userName.val(data.user.username);

                city.val(data.user.city_id);

            }else{
                /**
                 * Если введён номер телефона не существующего пользователя,
                 * то сбросим все поля...
                 */
                if(tthis.attr('id') == 'orders-user_phone'){
                    // Сброс поля email
                    userMail.val('');
                    // Сброс предупреждения email
                    noticeMail.html('').attr('data-value', '').attr('data-state', '');
                    // Сброс поля статуса оптовый/оптовый2/пользователь
                    iswholesale.val(0);
                    // Сброс поля "Юр лицо"
                    isentity.prop('checked', false);
                    // Сброс поля имя
                    userName.val('');
                    // Сброс поля город
                    city.val('');
                }
            }
        }).fail(function(data){
            res.html(JSON.stringify(data));
        }).always(function(){
            load.fadeOut(100);
        });
    }

    return;
}

JS
)
?>