<?php

namespace common\models;

use Yii;
use yii\db\Schema;

/**
 * This is the model class for table "columns".
 *
 * @property integer $id
 * @property integer $module_id
 * @property integer $order
 * @property integer $isDefault
 * @property integer $isLine
 * @property string $name
 * @property string $key
 * @property string $type
 * @property string $settings
 *
 * @property Module $module
 */
class Columns extends \shadow\SActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'columns';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['key','match','not' => true, 'pattern' => '/[^a-zA-Z_]/','message' => 'Можно использовать только латиницу',],
            ['key','match','not' => true, 'pattern' => '/^id$|^name$/','message' => 'Данные ключи не возможно использовать',],
            ['key', 'string', 'length' => [3, 255]],

            [['module_id', 'order', 'isDefault', 'isLine'], 'integer'],
            [['isLine'], 'boolean'],
            [['order'], 'default','value'=>0],
            [['name', 'key', 'type'], 'required'],
            [['settings'], 'string'],
            [['name', 'type'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'module_id' => 'Module ID',
            'name' => 'Название',
            'key' => 'Ключ',
            'type' => 'Тип поля',
            'settings' => 'Settings',
            'order' => 'Порядок',
            'isDefault' => 'Is Default',
            'isLine' => 'Is Line',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getModule()
    {
        return $this->hasOne(Module::className(), ['id' => 'module_id']);
    }
    /**
     * @var array
     *
     */
    public $data_types = [
        'integer'=>'Число',
        'string' => 'Строка',
        'html_text' => 'HTML текст',
        'text' => 'Простой текст',
    ];
    public function type()
    {
        return $this->data_types[$this->type];
    }
    public $default = [1, 2];
    public function sqlType()
    {
        switch($this->type){
            case 'integer':
                $result = 'int(11)';
                break;
            case 'string':
                $result = 'varchar(255)';
                break;
            case 'html_text':
                $result = 'text';
                break;
            case 'text':
                $result = 'text';
                break;
            default:
                $result = 'varchar(255)';
                break;
        }
        $result .= " NOT NULL";
        $result .= " COMMENT '{$this->type}'";
        return $result;
    }
    /**
     * @param $target \yii\db\ColumnSchema
     * @return array|false
     */
    public function compare($target)
    {
        $default = ['id', 'name', 'isVisible', 'created_at', 'updated_at'];
        if(in_array($target->name,$default)&&$target->comment==''){
            $result = false;
        }else{
            if($target->comment!=$this->type){
                $result = true;
            }else{
                $result = false;
            }
        }
        return $result;
    }
}
