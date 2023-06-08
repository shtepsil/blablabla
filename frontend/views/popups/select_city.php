<?
// todo можно удалить этот файл, он ни где не используется. Старая система модальных окон была.
/**
 * @var $city_a City[]
 */
use shadow\helpers\Json;
use yii\helpers\Url;
?>
    <div id="popupSelCity" class="popup window">
        <div class="popupClose" onclick="popup({block_id: '#popupSelCity', action: 'close'});"></div>
        <ul class="switchCity">
            <? foreach ($context->function_system->getData_city() as $key => $city): ?>
                <li>
                    <a href="<?= Url::to(['site/index', 'city' => $key]) ?>"><?= $city ?></a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="<?= Url::to(['site/delivery', 'id' => $key]) ?>" class="delivery-info">
                        <div class="what-is-delivery" title="Узнать условия доставки<br>по городу  <?= $city ?>">!</div>
                    </a>
                </li>
            <? endforeach; ?>
        </ul>
    </div>
<? if (!\Yii::$app->session->get('city_select')): ?>
    <? //if (1): ?>
    <div id="popupYourCity" class="popup window active">
        <div class="popupClose" onclick="popup({block_id: '#popupYourCity', action: 'close'});"></div>
        <div class="popupText">Ваш город — Алматы?</div>
        <div class="popupBottom">
            <div class="btn_addToCart is_success_almaty">Да, верно!</div>
            <div class="btn_buyToClick not_success_almaty">Выбрать другой город</div>
        </div>
    </div>
    <?
    $url_city_change = Json::encode(Url::to(['api/city']));
    $url_city_delivery = Json::encode(Url::to(['site/delivery','id'=>1]));
    $this->registerJs(<<<JS
///popup({block_id: '#popupYourCity', action: 'open'});
$('.is_success_almaty').on('click', function (e) {
    $.ajax({
        url: {$url_city_change},
        type: 'GET',
        data: {id: 1}
    });
    window.location = {$url_city_delivery};
    popup({block_id: '#popupYourCity', action: 'close'});

})
$('.not_success_almaty').on('click', function (e) {
    popup({block_id: '#popupYourCity', action: 'close'});
    setTimeout(function () {
        popup({block_id: '#popupSelCity', action: 'open'});
    }, 700)
})

JS
    )
    ?>
<? endif ?>