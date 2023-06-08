<?php

namespace common\models;
use common\models\Orders;

use Yii;

/**
 * This is the model class for table "monitoring_sms".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $created_at
 *
 * @property User $user
 */
class MonitoringSms extends \shadow\SActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'monitoring_sms';
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id','created_at'], 'required'],
            [['user_id','created_at'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'Пользователь',
            'created_at' => 'Дата создания',
        ];
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
	
	public static function export($items)
    {
  
        $columns = [
            [
                'attribute' => 'user_id',
                'header' => 'id пользователя',
                'format' => 'text',
                'value' => function ($model) {
                    return ($model->user_id) ? $model->user_id : '';
                },
            ],
			[
                'attribute' => 'email',
                'header' => 'Email',
                'format' => 'text',
                'value' => function ($model) {
                    return ($model->user->email) ? $model->user->email : '';
                },
            ],
			[
                'attribute' => 'phone',
                'header' => 'Телефон',
                'format' => 'text',
                'value' => function ($model) {
                    return ($model->user->phone) ? $model->user->phone : '';
                },
            ],
			[
                'attribute' => 'created_at',
                'header' => 'Дата смс',
                'format' => 'text',
                'value' => function ($model) {
                    return ($model->created_at) ? date('d.m.Y', $model->created_at) : '';
                },
            ],
			[
                'attribute' => 'created_at_',
                'header' => 'Дата успешной покупки',
                'format' => 'text',
                'value' => function ($model) {
					$data_successfull = '';
					$ord_ = Orders::getLastOrder($model->id);
						if (isset($ord_)) {
							$data_successfull = date('Y-m-d', $ord_);
						}													
                    return $data_successfull;
                },
            ],
			[
                'attribute' => 'created_at__',
                'header' => 'Результат акции',
                'format' => 'text',
                'value' => function ($model) {
					$data_successfull_result = '-';
					$ord_ = Orders::getLastOrder($model->id);
					
					if ($model->created_at <= $ord_) {
						$data_successfull_result = '+';
					}																		
                    return $data_successfull_result;
                },
            ]			           
        ];
		
        \moonland\phpexcel\Excel::export([
            'fileName' => 'export_monitoringsms',
            'models' => $items,
            'columns' => $columns,
        ]);
    }


}
