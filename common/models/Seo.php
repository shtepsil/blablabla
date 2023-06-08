<?php

namespace common\models;

use shadow\widgets\CKEditor;
use Yii;
use yii\helpers\Inflector;

/**
 * This is the model class for table "seo".
 *
 * @property integer $id
 * @property string $type
 * @property integer $owner_id
 * @property string $description
 * @property string $keywords
 * @property string $title
 *
 */
class Seo extends \shadow\SActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'seo';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type','owner_id'], 'required'],
            [['description','keywords','title'], 'string', 'max' => 500],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type' => 'Тип',
            'owner_id' => 'Родитель',
            'description' => 'Дескриптор',
            'keywords' => 'Ключи',
            'title' => 'Тайтл',
        ];
    }
}
