<?php

namespace common\models;

/**
 * This is the model class for table "pickpoint_img".
 *
 * @property integer $id
 * @property integer $pickpoint_id
 * @property string $url
 * @property string $name
 * @property integer $sort
 *
 */
class PickpointImg extends \shadow\SActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'pickpoint_img';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pickpoint_id', 'sort'], 'integer'],
            [['pickpoint_id', 'url'], 'required'],
            [['url', 'name'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'pickpoint_id' => 'Pickpoint ID',
            'url' => 'Url',
            'name' => 'Название',
            'sort' => 'Порядок'
        ];
    }
}
