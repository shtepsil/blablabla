<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "recipes_item".
 *
 * @property integer $id
 * @property integer $recipe_id
 * @property string $name
 * @property string $count
 * @property integer $item_id
 * @property double $item_count
 *
 * @property Items $item
 * @property Recipes $recipe
 */
class RecipesItem extends \shadow\SActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'recipes_item';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['recipe_id', 'name'], 'required'],
            [['recipe_id', 'item_id'], 'integer'],
            [['item_count'], 'number'],
            [['name', 'count'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'recipe_id' => 'Рецепт',
            'name' => 'Название',
            'count' => 'Количество',
            'item_id' => 'Товар',
            'item_count' => 'Количество товара',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getItem()
    {
        return $this->hasOne(Items::className(), ['id' => 'item_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRecipe()
    {
        return $this->hasOne(Recipes::className(), ['id' => 'recipe_id']);
    }
}
