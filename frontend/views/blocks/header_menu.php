<?php
/**
 * @var $context \frontend\controllers\SiteController
 * @var $this \yii\web\View
 */
use common\components\Debugger as d;
use backend\models\Menu;
use backend\models\MenuCategory;
use common\models\Category;
use yii\helpers\Html;
use yii\helpers\Url;
use frontend\widgets\Modal;
use shadow\helpers\Json;

$context = $this->context;
$body_login_registration = $this->render('//popups/login_registration');
/**
 * @var $menus Menu[]
 */
$menus = Menu::find()->orderBy(['sort' => SORT_ASC])->where(['isVisible' => 1, 'parent_id' => null])->all();

?>
<header class="header">
    <a href="<?= Url::to(['site/index']) ?>" class="logotype">
        <img src="<?= $context->AppAsset->baseUrl ?>/images/logotype.png" alt="site.ru" />
    </a>
    <ul class="topLinks">
        <?php
        foreach ($menus as $menu) {
            $options = [];
            if ($context->activeMenu($menu)) {
                $options['class'] = 'current';
            }
            /*
             * Если в текущей итерации пункт меню "Оплата и доставка"
             * то в этот пункт меню вставим ссылку
             * на страницу "Оплата и доставка" выбранного города
             */
            if($menu->owner_id == 4){
                $city_url = Url::to(['site/delivery','id' => $context->city_model->id]);
            }else{
                $city_url = $menu->createUrl();
            }

            echo Html::tag('li', Html::a($menu->name, $city_url), $options);
        }
        ?>
    </ul>
    <?php if (Yii::$app->user->isGuest): ?>
        <div class="iconMenu"></div>
        <?= $this->render('//blocks/basket') ?>
        <div class="topEnter_icon"></div>
        <div class="topEnter">
            <span class="open-ent-reg">Вход и регистрация</span>
        </div>
    <?php else: ?>
        <div class="wrapperOptions">
            <div class="iconMenu"></div>
            <?= $this->render('//blocks/basket') ?>
            <div class="topEnter_icon"></div>
            <div class="topEnter">
                <div class="cabinetMenu"><span>Мой кабинет</span></div>
            </div>
            <ul class="cabinetSubmenu">
                <li>
                    <a href="<?= Url::to(['user/orders']) ?>">Мои заказы</a>
                </li>
                <? if ($context->user->isWholesale): ?>
                    <li>
                        <a href="<?= Url::to(['user/wholesale']) ?>">Мой прайс-лист</a>
                    </li>
                    <li>
                        <a href="<?= Url::to(['user/requisites']) ?>">Мои реквизиты</a>
                    </li>
                <? endif ?>
                <li>
                    <a href="<?= Url::to(['user/bonus']) ?>">Мои бонусы</a>
                </li>
                <li>
                    <a href="<?= Url::to(['user/address']) ?>">Мои адреса</a>
                </li>
                <li>
                    <a href="<?= Url::to(['user/settings']) ?>">Мои настройки</a>
                </li>
                <? if (DEBUG_MENU_FOR_ID > 0 AND DEBUG_MENU_FOR_ID == $context->user->getId()): ?>
                    <li>
                        <a href="<?= Url::to(['site/debug']) ?>">Debug</a>
                    </li>
<!--                    <li>-->
<!--                        <a href="--><?//= Url::to(['user/requisites']) ?><!--">Мои реквизиты</a>-->
<!--                    </li>-->
                <? endif ?>
                <li>
                    <a href="<?= Url::to(['site/logout']) ?>">Выйти</a>
                </li>
            </ul>
        </div>
    <?php endif; ?>
    <div class="topContact">
        <div class="wrapperLeft">
            <?=Modal::widget([
                'id' => 'selCity',
                'windowOptions' => [ 'id' => 'popupSelCity' ],
                'toggleElement' => [
                    'class' => 'popupBtn_city',
                    'label' => '<span>' . $context->city_model->name . '</span>',
                ],
                'body' => $this->render('//popups/sel_city', ['context' => $context]),
            ])?>
            <?
            if (!\Yii::$app->session->get('city_select')):
//            if (1):
                $footer_your_city = <<<HTML
                <div class="btn_addToCart is_success_almaty">Да, верно!</div>
                <div class="btn_buyToClick not_success_almaty">Выбрать другой город</div>
HTML;
?>
            <?Modal::widget([
                'id' => 'popupYourCity',
                'toggleElement' => [
                    'tag' => 'button',
                    'label' => 'Вызвать',
                ],
                'description' => 'Ваш город — Алматы?',
                'footer' => $footer_your_city,
                'footerOptions' => [
                    'class' => 'popupBottom'
                ]
            ])?>
                <?
                $url_city_change = Json::encode(Url::to(['api/city']));
                $url_city_delivery = Json::encode(Url::to(['site/delivery','id'=>1]));
                $this->registerJs(<<<JS
//popup({block_id: '#popupYourCity', action: 'open'});
$('.is_success_almaty').on('click', function (e) {
    $.ajax({
        url: {$url_city_change},
        type: 'GET',
        data: {id: 1}
    });
    window.location = {$url_city_delivery};
    $('#popupYourCity').popup('close');

})
$('.not_success_almaty').on('click', function (e) {
    popup({block_id: '#popupYourCity', action: 'close'});
    setTimeout(function () {
        $('#selCity').popup('open');
    }, 700)
})

JS
                )
                ?>
            <?endif?>

            <div class="number"><a class="number" href="tel:<?= ($context->city_model->phone) ? $context->city_model->phone : $context->settings->get('main_phone') ?>"><?= ($context->city_model->phone) ? $context->city_model->phone : $context->settings->get('main_phone') ?></a></div>
        </div>
        <div class="wrapperRight">
            <?=Modal::widget([
                'header' => 'Обратный звонок',
                'toggleElement' => [
                    'class' => 'popupLink',
                    'label' => '<span>Заказать обратный звонок</span>',
                ],
                'body' => $this->render('//popups/callback'),
                'description' => '<p>Укажите свой контактный телефон, и мы Вам перезвоним</p>'
            ]);?>
        </div>
    </div>
</header>
<nav class="navMenu">
    <form action="<?= Url::to(['site/search']) ?>" method="get" class="formSearch_menu header__form-search" id="form__header__search">
        <input type="text" placeholder="Поиск по сайту" name="query" autocomplete="off" data-change="search"/>
        <button type="submit"></button>	
    </form>
	<div class="__wrapper__search__result" id="wrapper__search__result"><br>
		<div class="wrapper__scroll wrapper__scroll__search" id="wrapper__scroll__search"><br>
		</div>
		<button id="view_count" class="btn_Form blue" href="javascript:void(0)" onclick="$('#form__header__search').submit()">Смотреть все
		<span class="search_count"></span>
		</button>
	</div>	
    <div id="header_logo_visa"><img src="<?= $context->AppAsset->baseUrl ?>/images/visa.png"></div>
    <? if (Yii::$app->user->isGuest): ?>
        <div class="login_panel">
            <span class="open-ent-reg">Вход и регистрация</span>
        </div>
    <? endif ?>
    <ul class="topMenu">
        <?php
        /**
         * @var $cat Category
         * @var $sub_cat Category
         * @var $sub_sub_cat Category
         * @var $cat_menus MenuCategory[]
         */
        $cat_menus = MenuCategory::find()
            ->orderBy(['menu_category.sort' => SORT_ASC])
            ->with(['cat', 'menus'])
            ->where(['menu_category.isVisible' => 1, 'menu_category.parent_id' => null])
            ->all();

        foreach ($cat_menus as $cat_menu) {
            $class = '';
            $content = '';
            if ($cat_menu->type == 'category' && !$cat_menu->menus) {
                $cat = $cat_menu->cat;
                if ($cat) {

                    // Если категория для оптовиков
                    if($cat->isWholesale > 0 AND !Yii::$app->user->isGuest){
                        /*
                         * Проверим пользователя на тип (обычный/оптовик)
                         * ==============================================
                         * Если это обыный пользователь, то пропускаем итерацию,
                         * т.е. эту категорию, обычному пользователю, не показываем.
                         */
                        if(!$context->user->isWholesale()) continue;
                    }

                    if ($cat->type == 'cats') {
                        $class .= ' dropmenu';
                        $sub_content = '';
                        foreach ($cat->getCategories()->andWhere(['isVisible' => 1, 'isHideincatalog' => 0])->orderBy(['sort' => SORT_ASC])->all() as $sub_cat) {
                            $options_sub_cat = [];
                            $a_sub = Html::a($sub_cat->name, $sub_cat->url());

                            // Если категория для оптовиков
                            if($sub_cat->isWholesale > 0 AND !Yii::$app->user->isGuest){
                                /*
                                 * Проверим пользователя на тип (обычный/оптовик)
                                 * ==============================================
                                 * Если это обыный пользователь, то пропускаем итерацию,
                                 * т.е. эту категорию, обычному пользователю, не показываем.
                                 */
                                if(!$context->user->isWholesale()) continue;
                            }

                            if ($sub_cat->type == 'cats' && ($sub_cats = $sub_cat->getCategories()->orderBy(['sort' => SORT_ASC])->andWhere(['isVisible' => 1])->all())) {
                                if ($sub_cats) {
                                    $content_subs_cat = '';
                                    foreach ($sub_cats as $sub_sub_cat) {

                                        // Если категория для оптовиков
                                        if($sub_sub_cat->isWholesale > 0 AND !Yii::$app->user->isGuest){
                                            /*
                                             * Проверим пользователя на тип (обычный/оптовик)
                                             * ==============================================
                                             * Если это обыный пользователь, то пропускаем итерацию,
                                             * т.е. эту категорию, обычному пользователю, не показываем.
                                             */
                                            if(!$context->user->isWholesale()) continue;
                                        }

                                        $content_subs_cat .= Html::tag('li', Html::a($sub_sub_cat->name, $sub_sub_cat->url()));
                                    }
                                    $options_sub_cat['data']['subsub'] = 'true';
                                    $a_sub .= Html::tag('ul', '<li><span>Назад</span></li>' . $content_subs_cat, ['class' => 'subsub']);
                                }
                            }
                            $sub_content .= Html::tag('li', $a_sub, $options_sub_cat);
                        }
                        if ($sub_content) {
                            $content = Html::tag('span', $cat->name);
                            $content .= Html::tag('ul', $sub_content, ['class' => 'submenu']);
                        }
                    } else {
                        $content = Html::a($cat->name, ['site/catalog', 'id' => $cat->id]);
                    }
                }
            } else {
                if ($context->activeMenu($cat_menu)) {
                    $class .= ' current';
                }
                if ($cat_menu->menus) {
                    $class .= ' dropmenu';
                    $sub_content = '';
                    foreach ($cat_menu->menus as $sub_menu) {
                        $sub_content .= Html::tag('li', Html::a($sub_menu->name, $sub_menu->createUrl()));
                    }
                    if ($sub_content) {
                        $content = Html::tag('span', $cat->name);
                        $content .= Html::tag('ul', $sub_content, ['class' => 'submenu']);
                    }
                } else {
                    $content = Html::a($cat_menu->name, $cat_menu->createUrl());
                }
            }
            if ($content) {
                echo Html::tag('li', $content, ['class' => $class]);
            }
        }
        ?>
    </ul>
</nav>
<?=Modal::widget([
    'id' => 'popup_entreg',
    'windowOptions' => [ 'id' => 'popupEntreg' ],
    'body' => $body_login_registration,
])?>
<?=Modal::widget([
    'id' => 'order_off',
    'windowOptions' => [ 'id' => 'popupOrderOff' ],
    'header' => 'Заказы отключены',
    'description' => '<p>Извините...<br>С 30 декабря по 2 января, доставка заказов не работает.<br>Приходите в наши магазины.</p>'
])?>
<?
$this->registerJs(<<<JS
$(function(){
    $('.open-ent-reg').on('click', function(){
        $('#popup_entreg').popup('open');
    });
});
JS
)
?>
