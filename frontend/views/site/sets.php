<?php
/**
 * @var $this yii\web\View
 * @var $context \frontend\controllers\SiteController
 * @var $items \common\models\Sets[]
 *
 */
use frontend\form\CallbackSetSend;
use frontend\form\ReviewSend;
use frontend\widgets\ActiveField;
use frontend\widgets\ActiveForm;
use shadow\widgets\ReCaptcha\ReCaptcha;
use yii\helpers\Html;
use yii\helpers\Url;

$context = $this->context;
?>
<h1 class="title padSpace">Сеты</h1>
<div class="Goods goodssets bgWave padSpace">
    <div class="goodsBlocks">
        <? foreach ($items as $item): ?>
            <div class="goodsBlock">
                <a class="image" style="background-image: url(<?= $item->img(true,'mini_list') ?>);" href="<?= Url::to(['site/set', 'id' => $item->id]) ?>">
                    <span class="title"><i><?= $item->name ?></i></span>
                </a>
                    <span class="wrapperPad">
                        <span class="pricePosition">
                            <span class="price">
                                <span class="new"><?= number_format($item->real_price(), 0, '', ' ') ?> т.</span>
                                <span class="eco">Экономия <?= number_format($item->saving_price(), 0, '', ' ') ?> т.</span>
                            </span>
                        </span>
                        <span class="dynamicBlock">
                            <span class="btn_addToCart addSets" data-id="<?= $item->id ?>">В корзину</span>
                            <span class="btn_buyToClick fastSets" data-id="<?= $item->id ?>">Купить в 1 клик</span>
                        </span>
                    </span>
            </div>
        <? endforeach; ?>
    </div>
</div>
<div class="Form callOrder bgWave_blue padSpace">
    <div class="title">Заказ по телефону</div>
    <div class="text">Укажите свой контактный телефон, и мы Вам перезвоним</div>
    <?
    $model = new CallbackSetSend()
    ?>
    <?php $form = ActiveForm::begin([
        'action' => Url::to(['site/send-form', 'f' => 'callback_set']),
        'enableAjaxValidation' => false,
        'options' => ['enctype' => 'multipart/form-data'],
        'fieldClass' => ActiveField::className(),
        'fieldConfig' => [
            'options' => ['class' => 'col'],
            'template' => <<<HTML
{label}{input}
HTML
            ,
        ]
    ]); ?>
    <div class="string twoCol">
        <?= $form->field($model, 'phone')->widget(\yii\widgets\MaskedInput::className(), [
            'mask' => '+7(999)-999-9999',
            'definitions' => [
                'maskSymbol' => '_'
            ],
            'options' => [
                'class' => ''
            ]
        ]); ?>
        <?= $form->field($model, 'name'); ?>
    </div>
    <div class="string">
        <button class="btn_Form blue" type="submit">Я жду звонка</button>
    </div>
    <?php ActiveForm::end(); ?>
</div>
