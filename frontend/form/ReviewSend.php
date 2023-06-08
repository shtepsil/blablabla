<?php
/**
 * Created by PhpStorm.
 * Project: morkovka
 * User: lxShaDoWxl
 * Date: 11.09.15
 * Time: 13:54
 */
namespace frontend\form;

use common\models\Reviews;
use shadow\widgets\ReCaptcha\ReCaptchaValidator;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

class ReviewSend extends Model
{
    public $name;
    public $rate;
    public $plus_body;
    public $minus_body;
    public $body;
    public $verifyCode;

    /**
     * Returns the form name that this model class should use.
     *
     * The form name is mainly used by [[\yii\widgets\ActiveForm]] to determine how to name
     * the input fields for the attributes in a model. If the form name is "A" and an attribute
     * name is "b", then the corresponding input name would be "A[b]". If the form name is
     * an empty string, then the input name would be "b".
     *
     * By default, this method returns the model class name (without the namespace part)
     * as the form name. You may override it when the model is used in different forms.
     *
     * @return string the form name of this model class.
     */
    public function formName()
    {
        return 'review';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'body'], 'required'],
            ['verifyCode', ReCaptchaValidator::className()],
            [['name'], 'string', 'max' => 255],
            [['plus_body', 'minus_body', 'body'], 'string', 'max' => 1000],
            [['rate'], 'integer', 'min' => 1, 'max' => 5],
            [['name', 'rate', 'plus_body', 'minus_body', 'body'], 'safe'],
        ];
    }
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'name'=>'Имя',
            'rate'=>'Оценка',
            'plus_body'=>'Достоинства',
            'minus_body'=>'Недостатки',
            'body'=>'Коментарий',
            'verifyCode' => 'Капча',

        ];
    }
    public function send()
    {
        /**
         * @var $record \common\models\Reviews
         */
        $result = [];
        $attrs = $this->attributes;
        $record= new Reviews();
        foreach ($attrs as $key => $val) {
            if (!$record->hasAttribute($key) || $key == 'id') {
                unset($attrs[$key]);
            }
        }
        $attrs = ArrayHelper::htmlEncode($attrs);
        $record->setAttributes($attrs);
        $record->isVisible = 0;
        if ($record->save(false)) {
            $message = Json::encode('<p>Спасибо</p>
                <p>Ваш отзыв проверяеться администрацией</p>');
            $result['js'] = <<<JS
$('.popup_order_ok','#popup_1').html({$message})
$.colorbox({href:"#popup_1",open:true,inline: true, width: "350px"});
\$form.resetForm();
JS;
        } else {
            $result['message']['error'] = 'Произошла ошибка на стороне сервера!';
        }
        return $result;
    }
}