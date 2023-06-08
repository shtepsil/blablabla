<?php

namespace frontend\form;

use common\components\Debugger as d;
use common\models\User;
use yii\base\Model;
use yii\helpers\Json;
use Yii;

class EditRequisites extends Model
{
    public $entity_name;
    public $entity_address;
    public $entity_bin;
    public $entity_iik;
    public $entity_bank;
    public $entity_bik;
    public $entity_contract;
    public $entity_nds;
    public $parent;

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
        return 'user';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [
                [
                    'entity_name',
                    'entity_address',
                    'entity_bin',
                    'entity_iik',
                    'entity_bank',
                    'entity_bik',
                    'entity_contract'
                ],
                'trim'
            ],
            [
                [
                    'entity_name',
                    'entity_address',
                    'entity_bin',
                    'entity_iik',
                    'entity_bank',
                    'entity_bik'
                ],
                'required'
            ],
            [['entity_bin', 'entity_iik', 'entity_bik'], 'integer'],
            [['entity_nds',], 'boolean'],
        ];

    }
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return EditRequisites::staticAttributeLabels();
    }
    /**
     * @inheritdoc
     */
    public static function staticAttributeLabels()
    {
        return [
            'entity_name' => 'Юр. название',
            'entity_address' => 'Юр. Адрес',
            'entity_bin' => 'БИН / ИИН',
            'entity_iik' => 'ИИК',
            'entity_bank' => 'Банк',
            'entity_bik' => 'БИК',
            'entity_contract' => 'Номер договора',
            'entity_nds' => 'Плательщик НДС',
            'parent' => ''
        ];
    }

    public static function getAttrs($get_form = false)
    {
        $attributes = [];
        if ($get_form === false) {
            $attributes = static::staticAttributeLabels();
        }
        if ($get_form === true) {
            $attributes = static::getAttrsForm();
        }
        return $attributes;
    }

    public static function getAttrsForm()
    {
        $attrs = [];
        foreach (static::getAttrs() as $k_attr => $label) {
            switch ($k_attr) {
                case 'entity_nds':
                    $type = 'checkbox';
                    break;
                case 'parent':
                    $type = 'hidden';
                    break;
                default:
                    $type = 'text';
            }

            $attrs[$k_attr] = [
                'type' => $type,
                'label' => $label
            ];
        }
        return $attrs;
    }

    public function send()
    {
        $post = Yii::$app->request->post();
        //        d::ajax($post);
        /**
         * @var $record \common\models\User
         */
        $record = \Yii::$app->user->identity;

        if (!$this->validate()) {
            $t = $this->errors;
            return $t;
        }

        $result = [];
        if (\Yii::$app->user->isGuest) {
            $result['message']['error'] = 'Произошла ошибка на стороне сервера!';
            return $result;
        }

        /*
         * Если пользователь прикреплён к оптовику-родителю,
         * то редактировать реквизиты оптовика-родителя запретим.
         */
        if ($record->opt_user_id) {
            $result['message']['error'] = 'Новые реквизиты для сохранения недоступны!';
            return $result;
        }

        $entity = [];
        foreach ($this->attributeLabels() as $prop => $label) {
            if ($prop == 'entity_nds') {
                $entity[$prop] = ($this->$prop) ? 1 : 0;
            } else {
                $entity[$prop] = $this->$prop;
            }
        }

        $record->data = Json::encode($entity, 256);
        /*
         * Если сохраняются реквизиты пользователя, которые он сам себе настроил,
         * то оптовика-родителя открепим.
         */
        $record->opt_user_id = NULL;

        if ($record->save(false)) {
            $result['message']['success'] = 'Успешно изменено!';
            // Можно сохранять и без перезагрузки страницы
//            $result['js'] = <<<JS
//location.reload();
//JS;
        } else {
            $result['message']['error'] = 'Произошла ошибка на стороне сервера!';
        }

        return $result;
    }
}