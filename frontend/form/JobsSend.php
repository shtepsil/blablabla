<?php
/**
 * Created by PhpStorm.
 * Project: morkovka
 * User: lxShaDoWxl
 * Date: 15.09.15
 * Time: 16:59
 */
namespace frontend\form;

use shadow\widgets\ReCaptcha\ReCaptchaValidator;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\UploadedFile;

class JobsSend extends Model
{
    public $name;
    public $dob;
    public $phone;
    public $email;
    public $country;
    public $citizenship;
    public $education;
    public $position;
    public $resume;
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
        return 'jobs';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'phone', 'email'], 'required'],
            [['phone'],'match','pattern' => '/^((\+?7)(\(?\d{3})\)-?)?(\d{3})(-?\d{4})$/','message'=>\Yii::t('main','Некорректный формат поля {attribute}')],
            ['verifyCode', ReCaptchaValidator::className()],
            ['resume', 'file', 'extensions' => ['doc', 'docx', 'pdf']],
            [['name', 'phone', 'email', 'dob', 'country', 'citizenship', 'education', 'position'], 'string', 'max' => 255],
            ['email', 'email'],
            [['name', 'phone', 'email', 'resume', 'dob', 'country', 'citizenship', 'education', 'position'], 'safe'],
        ];
    }
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'name' => 'ФИО',
            'phone' => 'Телефон',
            'dob' => 'Дата рождения',
            'email' => 'Эл. почта',
            'country' => 'Место жительства',
            'citizenship' => 'Гражданство',
            'education' => 'Образование',
            'position' => 'Должность',
            'resume' => 'Резюме',
            'verifyCode' => 'Капча',
        ];
    }
    public function send()
    {
        /**
         * @var $mailer \yii\swiftmailer\Message
         */
        $result = $data = [];
        $file = UploadedFile::getInstance($this, 'resume');
        $this->attributes = ArrayHelper::htmlEncode($this->attributes);
        $data['resume'] = $this;
        $mailer = \Yii::$app->mailer->compose(['html' => 'jobs-html', 'text' => 'jobs-text'], $data)
            ->setFrom([\Yii::$app->params['supportEmail'] => \Yii::$app->name . ' info'])
            ->setTo('developer@instinct.kz')
            ->setSubject('Резюме с сайта ' . \Yii::$app->params['siteName']);
        if ($file) {
            $path = \Yii::getAlias('@frontend/tmp/') . uniqid() . '.' . $file->getExtension();
            $file->saveAs($path);
            $mailer->attach($path);
        }
        if ($mailer->send()) {
            $message = Json::encode('<p>Спасибо</p>
                <p>Резюме успешно отправленно</p>');
            $result['js'] = <<<JS
$('.popup_order_ok','#popup_1').html({$message})
$.colorbox({href:"#popup_1",open:true,inline: true, width: "350px"});
\$form.resetForm();
$('#change_resume').html('Обзор')
JS;

        } else {
            $result['message']['error'] = 'Произошла ошибка на стороне сервера!';
        }
        if(isset($path)){
            @unlink($path);
        }
        return $result;
    }
}