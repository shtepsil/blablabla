<?php
/**
 * Created by PhpStorm.
 * Project: morkovka
 * User: lxShaDoWxl
 * Date: 18.09.15
 * Time: 10:51
 */
namespace frontend\form;

use common\models\Callback;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

class CallbackSend extends Model
{
    public $phone;

    /**
     * @inheritdoc
     */
    public function formName()
    {
        return 'callback';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['phone'], 'required'],
            [['phone'], 'string', 'max' => 255],
            [['phone'],'match','pattern' => '/^((\+?7)(\(?\d{3})\)-?)?(\d{3})(-?\d{4})$/','message'=>\Yii::t('main','Некорректный формат поля {attribute}')],
            [['phone'], 'safe'],
        ];
    }
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'phone' => 'Телефон',
        ];
    }
    public function send()
    {
        /**
         * @var $record \common\models\Reviews
         * @var $mailer \yii\swiftmailer\Message
         */
        $result = [];
        $attrs = $this->attributes;
        $record= new Callback();
        foreach ($attrs as $key => $val) {
            if (!$record->hasAttribute($key) || $key == 'id') {
                unset($attrs[$key]);
            }
        }
        $attrs = ArrayHelper::htmlEncode($attrs);
        $record->setAttributes($attrs);
        if ($record->save(false)) {
            $data['item'] = $record;
            $send_mails=explode(',',\Yii::$app->settings->get('manager_emails','viktor@instinct.kz'));
            foreach ($send_mails as $key_email=> &$value_email) {
                if(!($value_email=trim($value_email," \t\n\r\0\x0B"))){
                    unset($send_mails[$key_email]);
                }
            }
            /**@var $mailer \yii\swiftmailer\Message**/
            $mailer = \Yii::$app->mailer->compose(['html' => 'callback-html'], $data)
                ->setFrom([\Yii::$app->params['supportEmail'] => \Yii::$app->params['siteName'] . ' info'])
                ->setTo($send_mails)
                ->setSubject('Заказ звонка с сайта ' . \Yii::$app->params['siteName']);
            $mailer->send();
            $result['message']['success'] = "<p>Спасибо</p><p>Ваша заявка отправлена администратору</p>";
            $result['js'] = <<<JS
popup({block_id: '#popupCallback', action: 'close'});
\$form.resetForm();
JS;
        } else {
            $result['message']['error'] = 'Произошла ошибка на стороне сервера!';
        }
        return $result;
    }
}