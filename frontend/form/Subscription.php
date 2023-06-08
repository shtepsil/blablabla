<?php
/**
 * Created by PhpStorm.
 * Project: kingfisher
 * User: lxShaDoWxl
 * Date: 11.12.15
 * Time: 11:04
 */
namespace frontend\form;

use common\components\retailcrm\ApiHelper;
use common\models\Subscriptions;
use yii\base\Model;

class Subscription extends Model
{
    public $email;
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
        return 'subs';
    }
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['email'], 'required'],
            ['email', 'email'],
            ['email', 'unique','targetClass'=>Subscriptions::className(),'targetAttribute'=>'email'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'email'=>'E-Mail',
        ];
    }

    public function send()
    {
        $result = [];
        $record = new Subscriptions();
        $record->email = $this->email;
        if($record->save()){
            if($record->email){
                \Yii::$app->function_system->send_promo_code('sub', $record->email);
            }
            if (!empty($record->email)) {
                $customer = array(
                    'externalId' => $record->email,
                    'email' => $record->email,
                    'firstName' => 'Подписчик '.$record->email,
                );
                if(\Yii::$app->params['RetailCRM']['enable'] === true){
                    $crmApiHelper = new ApiHelper();
                    $crmApiHelper->createCustomer($customer);
                }
            }
            $result['message']['success'] = 'Вы успешно подписались!';
            $result['js']=<<<JS
\$form.resetForm();
JS;
        }else{
            $result['message']['error'] = 'Произошла ошибка на стороне сервера!';
        }
        return $result;
    }
}