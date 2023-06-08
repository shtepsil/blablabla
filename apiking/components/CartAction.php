<?php
/**
 * Created by PhpStorm.
 * Project: morkovka
 * User: lxShaDoWxl
 * Date: 19.08.15
 * Time: 11:45
 */
namespace apiking\components;

use common\models\Items;
use common\models\Orders;
use common\models\PromoCode;
use common\models\Sets;
use Yii;
use yii\base\Action;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\BadRequestHttpException;
use yii\web\Response;

class CartAction extends Action
{
    public function run()
    {
        $request = Yii::$app->request;
        if (!$request->isAjax) {
            return $this->controller->goBack();
        }
        $result = array();
        $action = $request->get('action');
        if ($action) {
            switch ($action) {
                case 'add':
                    $result = $this->addCart($request->get('id'), $request->get('count', 1));
                    break;
                case 'changeBasket':
                    $result = $this->changeBasket();
                    break;
                case 'editBasket':
                    $result = $this->editBasket();
                    break;
                case 'addMulti':
                    $result = $this->addMulti($request->get('items'));
                    break;
                case 'check_promo':
                    $result = $this->checkPromo();
                    break;
                case 'add_discount':
                    $result = $this->addDiscount($request->get('id'));
                    break;
                case 'del':
                    $result = $this->delCart($request->get('id'));
                    break;
                case 'add_set':
                    $result = $this->addSet($request->get('id'), $request->get('count', 1));
                    break;
                case 'del_set':
                    $result = $this->delSet($request->get('id'));
                    break;
                case 'clear':
                    Yii::$app->session->set('items', []);
                    break;
                case 'type_handling':
                    $result = $this->TypeHandling($request->get('id'), $request->get('type_handling'));
                    break;
            }
        } else {
            throw new BadRequestHttpException();
        }
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $result;
    }
    public function delSet($id)
    {
        $sets = Yii::$app->session->get('sets', []);
        $new_id = [];
        if (isset($sets[$id])) {
            unset($sets[$id]);
        }
        return $this->UpdateSum($sets, $new_id, 'sets');
    }

    public function addSet($id, $count)
    {
        $sets = Yii::$app->session->get('sets', []);
        $new_id = [];
        if (isset($sets[$id])) {
            $sets[$id] = $sets[$id] + $count;
            $new_id[$id] = 'count';
        } else {
            $sets[$id] = $count;
            $new_id[$id] = 'new';
        }
        return $this->UpdateSum($sets, $new_id, 'sets');
    }
    public function delCart($id)
    {
        $items = Yii::$app->session->get('items', []);
        $type_handlings = Yii::$app->session->get('type_handling', []);
        $new_id = [];
        if (isset($items[$id])) {
            unset($items[$id]);
        }
        if (isset($type_handlings[$id])) {
            unset($type_handlings[$id]);
            Yii::$app->session->set('type_handling', $type_handlings);
        }
        return $this->UpdateSum($items, $new_id);
    }
    public function addCart($id, $count)
    {
        $items = Yii::$app->session->get('items', []);
        $new_id = [];
        if (isset($items[$id])) {
            $items[$id] = $items[$id] + $count;
            $new_id[$id] = 'count';
        } else {
            $items[$id] = $count;
            $new_id[$id] = 'new';
        }
        return $this->UpdateSum($items, $new_id);
    }
    public function addMulti($items_new)
    {
        $items = Yii::$app->session->get('items', []);
        $new_id = [];
        foreach ($items_new as $id => $count) {
            if (isset($items[$id])) {
                $items[$id] = $items[$id] + $count;
                $new_id[$id] = 'count';
            } else {
                $items[$id] = $count;
                $new_id[$id] = 'new';
            }
        }
        return $this->UpdateSum($items, $new_id);
    }
    public function addDiscount($id)
    {
        /**
         * @var $item Items
         */
        $item = Items::findOne($id);
        $items = Yii::$app->session->get('items', []);
        $new_id = [];
        if ($item && $item->isVisible && $item->itemsTogethers) {
            foreach ($item->itemsTogethers as $items_together) {
                $count = (double)$items_together->count;
                if (isset($items[$items_together->item_id])) {
                    $items[$items_together->item_id] = $items[$items_together->item_id] + $count;
                    $new_id[$items_together->item_id] = 'count';
                } else {
                    $items[$items_together->item_id] = $count;
                    $new_id[$items_together->item_id] = 'new';
                }
            }
        }
        return $this->UpdateSum($items, $new_id);
    }
    public function TypeHandling($id, $type_handling = [])
    {
        $type_handlings = Yii::$app->session->get('type_handling', []);
//        if (isset($type_handlings[$id])) {
//            $type_handling = array_unique(ArrayHelper::merge($type_handlings[$id], $type_handling));
//        }
        $type_handlings[$id] = $type_handling;
        Yii::$app->session->set('type_handling', $type_handlings);
        $js_type_handling = Json::encode($type_handling);
        $js = <<<JS
var radio_button = $('.cartBlock[data-item_id={$id}]', '#cartWindow').find('input[type=radio]');
var radio_select = {$js_type_handling}
$.each(radio_button, function (i, el) {
    if ($.inArray($(el).val(), radio_select)!=-1) {
        $(el).prop('checked', true);
    }
})
JS;
        return ['success' => 'OK', 'js' => $js];
    }
    public function UpdateSum($items, $new_id = [], $type = 'items')
    {
        /**
         * @var Items[] $db_items
         * @var Items $target_item
         */
        if ($type == 'sets') {
            $sets = $items;
            $items = Yii::$app->session->get('items', []);
        } else {
            $sets = Yii::$app->session->get('sets', []);
        }
//        $data['count_items'] = $items;
        $count_items = count($items);
        $count_sets = count($sets);
        $all_count = $count_items + $count_sets;
        $data['count_string'] = Yii::t('main', 'count_items', ['n' => $all_count]);
        $data['count'] = $all_count;
        $result_items = $result_sets = $db_items = [];
        $sum = $sum_normal = 0;
        if ($items) {
            $q = new ActiveQuery(Items::className());
            $q->indexBy('id')
//                ->select(['price', 'id', 'name', 'measure'])
                ->with('itemsTogethers')
                ->andWhere(['id' => array_keys($items)]);
            $db_items = $q->all();
            /**
             * @var $functions \frontend\components\FunctionComponent
             */
            $functions = Yii::$app->function_system;
            if ($this->enable_discount) {
                if (!Yii::$app->user->isGuest && doubleval(Yii::$app->user->identity->discount)) {
                    $discount = [];
                } else {
                    $discount = $functions->discount_sale_items($db_items, $items);
                }
            } else {
                $discount = [];
            }
            foreach ($db_items as $item_id => $item) {
                $count = $items[$item_id];
                $full_price_item = $functions->full_item_price($discount, $item, $count);
                if (($type == 'items' && isset($new_id[$item_id]))) {
                    $result_items[$item_id]['price_full'] = number_format($full_price_item, 0, '', ' ') . ' т.';
                    switch ($new_id[$item_id]) {
                        case 'new':
                            $type_handlings = $item->typeHandlings;
                            if ($type_handlings) {
                                $type_handling_html = Html::hiddenInput('id', $item->id);
                                foreach ($type_handlings as $type_handling) {
                                    $type_handling_html .= '<div class="col">';
                                    $type_handling_html .= Html::input('radio', 'type_handling[]', $type_handling->id, [
                                        'id' => "type_handling_{$type_handling->id}"
                                    ]);
                                    $type_handling_html .= <<<HTML
<label for="type_handling_{$type_handling->id}">
	<div class="image">
		<img src="{$type_handling->img}" alt="" />
	</div>
	{$type_handling->name}
</label>
HTML;
                                    $type_handling_html .= '</div>';
                                }
                                $data['type_handling'] = $type_handling_html;
                            }
                            $result_items[$item_id]['new'] = $this->controller->view->render('//blocks/item_cart', ['item' => $item, 'count' => $count]);
                            break;
                        case 'count':
                            $result_items[$item_id]['count'] = $count;
                            break;
                        default:
                            $result_items[$item_id]['count'] = $count;
                            break;
                    }
                } else {
                    $result_items[$item_id]['count'] = $count;
                    $result_items[$item_id]['price_full'] = number_format($full_price_item, 0, '', ' ') . ' т.';
                }
                $sum += $full_price_item;
                $sum_normal += $item->sum_price($count);
            }
        }
        if ($sets) {
            $q = new ActiveQuery(Sets::className());
            $q->indexBy('id')
                ->andWhere(['id' => array_keys($sets)]);
            $db_items = $q->all();
            foreach ($db_items as $item_id => $item) {
                $count = $sets[$item_id];
                if (($type == 'sets' && isset($new_id[$item_id]))) {
                    $result_sets[$item_id]['price_full'] = number_format(round($count * $item->real_price()), 0, '', ' ') . ' т.';
                    switch ($new_id[$item_id]) {
                        case 'new':
                            $result_sets[$item_id]['new'] = $this->controller->view->render('//blocks/item_cart', ['item' => $item, 'count' => $count]);
                            break;
                        case 'count':
                            $result_sets[$item_id]['count'] = $count;
                            break;
                        default:
                            $result_sets[$item_id]['count'] = $count;
                            break;
                    }
                } else {
                    $result_sets[$item_id]['count'] = $count;
                    $result_sets[$item_id]['price_full'] = number_format(round($count * $item->real_price()), 0, '', ' ') . ' т.';
                }
                $sum += round($count * $item->real_price());
                $sum_normal += round($count * $item->real_price());
            }
        }
        if ($type == 'items') {
            Yii::$app->session->set($type, $items);
        } else {
            Yii::$app->session->set($type, $sets);
        }
        if ($this->enable_discount) {
            if (!Yii::$app->user->isGuest && doubleval(Yii::$app->user->identity->discount)) {
                $order = new Orders(['discount' => Yii::$app->user->identity->discount . '%']);
                $sum = $sum - $order->discount($sum);
            }
        }
        $data['items'] = $result_items;
        $data['sets'] = $result_sets;
        $data['sum'] = number_format($sum, 0, '', ' ') . ' т.';
        $data['sum_int'] = $sum;
        $data['sum_normal'] = $sum_normal;
        $percent_bonus = $this->controller->function_system->percent();
        $add_bonus = floor(((int)$sum * ($percent_bonus)) / 100);
        $data['add_bonus'] = $add_bonus;
        if (Yii::$app->id == 'app-frontend' && Yii::$app->request->get('cart_small')) {
            $sum_full = (int)$sum;
            $sum_normal = (int)$sum_normal;
            $discount_price = (int)($sum_normal - (int)$sum);
            if ($discount_price <= 0) {
                $discount_price = 0;
            } else {
                $data['discount_price'] = number_format($discount_price, 0, '', ' ') . ' т.';
            }
            $data['sum_full'] = number_format($sum_full, 0, '', ' ') . ' т.';
            $data['sum'] = number_format((int)$sum + $discount_price, 0, '', ' ') . ' т.';
        }
        $max_price_delivery = (int)\Yii::$app->settings->get('max_price_delivery');
        $data['price_delivery_popup'] = 0;
        if ($sum <= \Yii::$app->settings->get('max_price_delivery')) {
            $data['price_delivery_popup'] = number_format($max_price_delivery - $sum, 0, '', ' ') . ' т.';
        }
        $data['min_sum_delivery'] = number_format($max_price_delivery, 0, '', ' ') . ' т.';
        return $data;
    }
    public function changeBasket()
    {
        $items = Yii::$app->request->get('items', []);
        $type_handling = Yii::$app->request->get('type_handling', []);
        $sets = Yii::$app->request->get('sets', []);
        Yii::$app->session->set('items', $items);
        Yii::$app->session->set('type_handling', $type_handling);
        Yii::$app->session->set('sets', $sets);
        return ['success' => 'OK'];
    }
    public function editBasket()
    {
        $old_items = $old_sets = $old_type_handling = [];
        if (Yii::$app->request->get('session', '') == 'no') {
            $old_items = Yii::$app->session->get('items');
            $old_sets = Yii::$app->session->get('sets');
            $old_type_handling = Yii::$app->session->get('type_handling');
        }
        $items = Yii::$app->request->get('items', []);
        $type_handling = Yii::$app->request->get('type_handling', []);
        $sets = Yii::$app->request->get('sets', []);
        Yii::$app->session->set('type_handling', $type_handling);
        Yii::$app->session->set('sets', $sets);
        $result = $this->UpdateSum($items);
        $sum_full = (int)$result['sum_int'];
        $city = Yii::$app->request->get('city', 1);
        $sum_normal = (int)$result['sum_normal'];
        $discount_price = (int)($sum_normal - (int)$result['sum_int']);
        if ($discount_price <= 0) {
            $discount_price = 0;
        } else {
            $result['discount_price'] = number_format($discount_price, 0, '', ' ') . ' т.';
        }
        $result['delivery'] = Yii::$app->function_system->delivery_price($sum_full, $city);
        $result['sum_full'] = number_format($sum_full, 0, '', ' ') . ' т.';
        $result['sum'] = number_format((int)$result['sum_int'] + $discount_price, 0, '', ' ') . ' т.';
        if (Yii::$app->request->get('session', '') == 'no') {
            Yii::$app->session->set('items', $old_items);
            Yii::$app->session->set('sets', $old_sets);
            Yii::$app->session->set('type_handling', $old_type_handling);
        }
        return $result;
    }
    public $enable_discount = true;
    public function checkPromo()
    {
        /**
         * @var $code_model PromoCode
         */
        if (($code = \Yii::$app->request->get('code'))
            && ($code_model = PromoCode::find()->andWhere(['code' => $code])->one())
            && $code_model->check_enable()
        ) {
            $items = Yii::$app->session->get('items', []);
            $this->enable_discount = false;
            $result = $this->UpdateSum($items);
            $sum_full = (int)$result['sum_int'];
            $discount_price = $code_model->discount($sum_full);
            if ($discount_price <= 0) {
                $discount_price = 0;
            } else {
                $result['discount_price'] = number_format($discount_price, 0, '', ' ') . ' т.';
                $sum_full = $sum_full - $discount_price;
            }
            $city = Yii::$app->request->get('city', 1);
            $result['delivery'] = Yii::$app->function_system->delivery_price($sum_full, $city);
            $result['sum_full'] = number_format($sum_full, 0, '', ' ') . ' т.';
            $result['sum'] = number_format((int)$sum_full + $discount_price, 0, '', ' ') . ' т.';
            return $result;
        } else {
            return [
                'errors' => 'Данный код не действителен!'
            ];
        }
    }
}