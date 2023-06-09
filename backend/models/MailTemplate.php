<?php
/**
 * Created by PhpStorm.
 * Project: kingfisher
 * User: lxShaDoWxl
 * Date: 04.01.16
 * Time: 15:27
 */
namespace backend\models;

use shadow\widgets\CKEditor;
use yii\helpers\ArrayHelper;
use yii\helpers\FileHelper;
use yii\web\UploadedFile;

class MailTemplate extends Settings
{
    public function FormParams()
    {
        $settings = Settings::find()->indexBy('key')->all();
        $old_settings = ArrayHelper::map($settings,
            function ($el) {
                return $el->key;
            },
            function ($el) {
                return $el->value;
            }
        );
        if ($old_settings) {
            $this->setOldAttributes($old_settings);
            $this->setAttributes($old_settings);
        }
        $groups = [];
        foreach ($this->data as $key => $val) {
            $group_key = (isset($val['group']) ? $val['group'] : 'main');
            $params = (isset($val['params']) ? $val['params'] : []);
            if (!isset($groups[$group_key])) {
                $groups[$group_key] = $this->groups[$group_key];
                $groups[$group_key]['fields'] = [];
            }
            $groups[$group_key]['fields'][$key] = $params;
        }
        $result = [
            'form_action' => ['mail-template/save'],
//            'cancel' => ['settings/control'],
            'groups' => $groups
        ];
        return $result;
    }
    protected $groups = [
        'main' => [
            'title' => 'Заказ',
            'icon' => 'shopping-cart',
            'options' => [],
        ],

    ];
    /**
     * PHP getter magic method.
     * This method is overridden so that attributes and related objects can be accessed like properties.
     *
     * @param string $name property name
     * @throws \yii\base\InvalidParamException if relation name is wrong
     * @return mixed property value
     * @see getAttribute()
     */
    public function __get($name)
    {
        if($name=='data'){
            return $this->getData();
        }
        return parent::__get($name); // TODO: Change the autogenerated stub
    }

    public function getData()
    {
        return [
            'mail_order_header' => [
                'name' => 'Шапка',
                'group' => 'main',
                'params' => [
                    'type' => 'textArea',
                    'widget' => [
                        'class' => CKEditor::className(),
                        'config'=>[
                            'editorOptions'=>[
                                'enterMode'=>1
                            ]
                        ]
                    ],
                ],
            ],
            'mail_order_footer' => [
                'name' => 'Подвал',
                'group' => 'main',
                'params' => [
                    'type' => 'textArea',
                    'widget' => [
                        'class' => CKEditor::className(),
                        'config'=>[
                            'editorOptions'=>[
                                'enterMode'=>1
                            ]
                        ]
                    ],
                ],
            ],
            'mail_order_weight' => [
                'name' => 'Весовой товар в заказе',
                'group' => 'main',
                'params' => [
                    'type' => 'textArea',
                    'widget' => [
                        'class' => CKEditor::className(),
                        'config'=>[
                            'editorOptions'=>[
                                'enterMode'=>1
                            ]
                        ]
                    ],
                ],
            ],
        ];
    }
    public $id = null;
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
        return 'settings'; // TODO: Change the autogenerated stub
    }

    /**
     * Returns the list of all attribute names of the model.
     * The default implementation will return all column names of the table associated with this AR class.
     * @return array list of attribute names.
     */
    public function attributes()
    {
        return array_keys($this->data);
    }
    public function attributeLabels()
    {
        $result = [];
        foreach ($this->data as $key => $val) {
            if (isset($val['name'])) {
                $result[$key] = $val['name'];
            }
        }
        return $result;
    }
    /**
     * @inheritdoc
     */
    public function rules()
    {
        $result = [];
        foreach ($this->data as $key => $val) {
            $rule = (isset($val['rule'])) ? $val['rule'] : ([[$key], 'string']);
            $result[] = $rule;
        }
        return $result;
    }
    /**
     * Saves the current record.
     *
     * This method will call [[insert()]] when [[isNewRecord]] is true, or [[update()]]
     * when [[isNewRecord]] is false.
     *
     * For example, to save a customer record:
     *
     * ```php
     * $customer = new Customer; // or $customer = Customer::findOne($id);
     * $customer->name = $name;
     * $customer->email = $email;
     * $customer->save();
     * ```
     *
     * @param boolean $runValidation whether to perform validation (calling [[validate()]])
     * before saving the record. Defaults to `true`. If the validation fails, the record
     * will not be saved to the database and this method will return `false`.
     * @param array $attributeNames list of attribute names that need to be saved. Defaults to null,
     * meaning all attributes that are loaded from DB will be saved.
     * @return boolean whether the saving succeeded (i.e. no validation errors occurred).
     */
    public function save($runValidation = true, $attributeNames = null)
    {
        /**
         * @var $settings Settings
         */
        $settings = Settings::find()->indexBy('key')->all();
        \Yii::$app->frontend_cache->delete('settings_cache');
        $old_attributes = $this->oldAttributes;
        foreach ($this->attributes as $key => $value) {
            $val = $this->saveAttribute($key, $value);
            if (!isset($old_attributes[$key]) || (isset($old_attributes[$key]) && $old_attributes[$key] != $val)) {
                if (!isset($settings[$key])) {
                    $record = new Settings();
                    $record->group = (isset($this->data[$key]['group'])) ? $this->data[$key]['group'] : 'main';
                    $record->key = $key;
                    $record->value = $val;
                    $record->save(false);
                } else {
                    $record = $settings[$key];
                    $record->group = (isset($this->data[$key]['group'])) ? $this->data[$key]['group'] : 'main';
                    $record->key = $key;
                    $record->value = $val;
                    $record->save(false);
                }
            }
        }
        return true;
    }
    public function saveAttribute($attribute, $value)
    {
        $type = (isset($this->data[$attribute]['type'])) ? $this->data[$attribute]['type'] : 'text';
        switch ($type) {
            case 'file':
                $file = UploadedFile::getInstance($this, $attribute);
                if ($file) {
                    $path = \Yii::getAlias("@web_frontend/uploads/settings/{$attribute}.{$file->getExtension()}");
                    if (!is_dir(pathinfo($path, PATHINFO_DIRNAME))) {
                        FileHelper::createDirectory(pathinfo($path, PATHINFO_DIRNAME), 0775, true);
                    }
                    $file->saveAs($path);
                    $value = "/uploads/settings/{$attribute}.{$file->getExtension()}";
                }
        }
        return $value;
    }
}