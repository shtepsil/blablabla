<?php

namespace backend\models;

use Yii;
use yii\helpers\Html;
use yii\helpers\Inflector;
use yii\helpers\Url;

/**
 * This is the model class for table "menu".
 *
 * @property integer $id
 * @property string $name
 * @property string $type
 * @property integer $owner_id
 * @property string $url
 * @property integer $isVisible
 * @property integer $sort
 * @property integer $parent_id
 *
 * @property Menu $parent
 * @property Menu[] $menus
 */
class Menu extends BaseMenu
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'menu';
    }
    public $no_parent = true;

}
