<?php
/**
 * Created by PhpStorm.
 * Project: morkovka
 * User: lxShaDoWxl
 * Date: 21.09.15
 * Time: 15:45
 */
namespace frontend\form;

use common\models\User;
use common\models\UserInvited;
use yii\base\Model;
use yii\helpers\Html;
use yii\helpers\Json;

class InvitedSend extends Model
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
        return 'invited';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'unique', 'targetClass' => User::className(), 'targetAttribute' => 'email', 'message' => 'На данный E-Mail уже отправляли приглошение'],
            ['email', 'unique', 'targetClass' => UserInvited::className(), 'targetAttribute' => 'email', 'message' => 'На данный E-Mail уже отправляли приглошение'],
        ];
    }
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'email' => 'Электронная почта',
        ];
    }
    public function send()
    {
        /**
         * @var $record \common\models\UserInvited
         * @var $mailer \yii\swiftmailer\Message
         */
        $result = [];
        $record = new UserInvited();
        $record->email = Html::encode($this->email);
        $record->user_id = \Yii::$app->user->id;
        $record->status = 0;
        $data['item'] = $record;
        $data['user'] = \Yii::$app->user->identity;
        $mailer = \Yii::$app->mailer->compose(['html' => 'invited-html'], $data)
            ->setFrom([\Yii::$app->params['supportEmail'] => \Yii::$app->params['siteName'] . ' info'])
            ->setTo($record->email)
            ->setSubject('Приглашение с сайта ' . \Yii::$app->params['siteName']);
        if ($record->save(false) && $mailer->send()) {
            $message = Json::encode('<p>Успех</p>
                <p>Ваше приглашение отправлено</p>');
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