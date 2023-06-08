<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "import_items".
 *
 * @property integer $id
 * @property string $data
 * @property integer $count_update
 * @property integer $date_created
 */
class ImportItems extends \shadow\SActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'import_items';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['data', 'count_update', 'date_created'], 'required'],
            [['data'], 'string'],
            [['count_update', 'date_created'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'data' => 'Data',
            'count_update' => 'Count Update',
            'date_created' => 'Date Created',
        ];
    }
}
