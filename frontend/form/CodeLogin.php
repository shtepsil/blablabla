<?php

namespace frontend\form;
use common\models\User;
use Yii;
use yii\base\Model;

class CodeLogin extends Model
{

    public $code;

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
        return 'code_login';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
		 [['code'], 'required'],
            [['code'], 'required', 'on' => ['popup']],
            [['code'], 'string', 'max' => 255],
       ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'code' => 'Код',
        ];
    }
    public function send()
    {
        $code = \Yii::$app->session->get('code', []);
		$phone = \Yii::$app->session->get('phone', []);
		  
		$user = User::find()->andWhere(['phone' => $phone, 'status' => User::STATUS_ACTIVE])->one();
		
		if ($this->code == $code) {;
			if(Yii::$app->user->login($user)){
				$result['message']['success'] = 'Вы успешно вошли!';
				$result['js']=<<<JS
location.reload();
JS;
			}else{
				$result['message']['error'] = 'Произошла ошибка на стороне сервера!';
			}
		} else {
			$result['message']['error'] = 'Вы ввели не верный код';
		}   
        return $result;
    }
}