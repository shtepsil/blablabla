<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "banners_cities".
 *
 * @property integer $id
 * @property integer $banner_id
 * @property integer $city_id
 *
 * @property City $city
 * @property Banners $banner
 */
class BannersCities extends \shadow\SActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'banners_cities';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'banner_id', 'city_id'], 'required'],
            [['id', 'banner_id', 'city_id'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'banner_id' => 'banner ID',
            'city_id' => 'city ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBanners()
    {
        return $this->hasOne(Banners::className(), ['id' => 'banner_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCities()
    {
        return $this->hasOne(City::className(), ['id' => 'city_id']);
    }
}
