<?php

namespace common\models;

use Yii;
use yii\web\UploadedFile;

/**
 * This is the model class for table "recipes_method".
 *
 * @property integer $id
 * @property integer $recipe_id
 * @property string $name
 * @property string $body
 * @property string $img
 * @property integer $sort
 *
 * @property Recipes $recipe
 */
class RecipesMethod extends \shadow\SActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'recipes_method';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['recipe_id', 'body'], 'required'],
            [['recipe_id', 'sort'], 'integer'],
            [['body'], 'string'],
            [['name', 'img'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'recipe_id' => 'Recipe ID',
            'name' => 'Заголовок',
            'body' => 'Body',
            'img' => 'Img',
            'sort' => 'Sort',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRecipe()
    {
        return $this->hasOne(Recipes::className(), ['id' => 'recipe_id']);
    }
    public function behaviors()
    {
        return [
            [
                'class' => '\shadow\behaviors\UploadFileBehavior',
                'attributes' => ['img'],
            ],
        ];
    }
}
