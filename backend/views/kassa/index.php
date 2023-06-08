<?php
/**
 * @var $this yii\web\View
 * @var $context yii\web\View
 * @var $items SHistoryMoney[]
 */
use common\models\SHistoryMoney;
use yii\helpers\Url;

$url = 'kassa';
$items = SHistoryMoney::find()
    ->where(['or', ['send_user_id' => Yii::$app->user->id], ['recipient_user_id' => Yii::$app->user->id]])
    ->orderBy(['created_at' => SORT_DESC, 'status' => SORT_ASC])
    ->all();
?>
<?= $this->render('//blocks/breadcrumb') ?>
<section id="content" class="container-fluid">
    <div class="panel">
        <div class="panel-heading">
            <a href="<?= Url::to([$url . '/control']) ?>" class="btn-primary btn">
                <i class="fa fa-arrow-up"></i> <span class="hidden-xs hidden-sm">Передать</span></a>
        </div>
        <div class="panel-body">
            <div class="row">
                <kbd> В кассе:<?=Yii::$app->user->identity->kassa?></kbd>
            </div>
            <div class="table-responsive table-primary row">
                <table class="table table-striped table-hover">
                    <colgroup>
                        <col>
                        <col>
                        <col>
                        <col>
                    </colgroup>
                    <thead>
                    <tr>
                        <th>Отправитель</th>
                        <th>Причина</th>
                        <th>Получатель</th>
                        <th>Сумма</th>
                        <th>Статус</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td>
                                <?= $item->send() ?>
                            </td>
                            <td><?= $item->types() ?></td>
                            <td><?=$item->recipient()?></td>
                            <td><?= $item->sum ?></td>
                            <td><?= $item->typeStatus() ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
<?php
$url_check = Url::to(['kassa/check-money']);
$this->registerJs(<<<JS
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
                $.growl.notice({title: 'Успех', message: 'Перевод подтверждёт'});
                obj.replaceWith(data.content)
            } else {
                $.growl.error({title: 'Ошибка', message: data.error, duration: 5000});
            }
        },
        error: function () {
            $.growl.error({title: 'Ошибка', message: 'Произошла ошибка на стороне сервера!', duration: 5000});
        }
    });
})
JS
)
?>