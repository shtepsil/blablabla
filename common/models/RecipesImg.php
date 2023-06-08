<?php

namespace common\models;

use Yii;
use shadow\SResizeImg;

/**
 * This is the model class for table "recipes_img".
 *
 * @property integer $id
 * @property integer $id_recipes
 * @property string $url
 *
 * @property Recipes $idRecipes
 */
class RecipesImg extends \shadow\SActiveRecord
{

    use SResizeImg;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'recipes_img';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_recipes', 'url'], 'required'],
            [['id_recipes'], 'integer'],
            [['url'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_recipes' => 'Id Recipes',
            'url' => 'Url',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIdRecipes()
    {
        return $this->hasOne(Recipes::className(), ['id' => 'id_recipes']);
    }
}
