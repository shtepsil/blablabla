<?php

namespace backend\controllers;

use backend\AdminController;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\View;
use shadow\plugins\seo\behaviors\SSeoBehavior;
use common\models\Orders;
use backend\models\SUser;
use common\models\OrdersItems;

class AnaliticsController extends AdminController
{
    public function init()
    {
        $this->view->title = 'Аналитика';
        $this->breadcrumb[] = [
            'url' => ['analitics/index'],
            'label' => $this->view->title
        ];

        parent::init(); // TODO: Change the autogenerated stub
    }
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
         $result = [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['admin'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'control' => ['post', 'get'],
                    'filter' => ['post', 'get'],
                ],
            ],
        ];
		 if (SSeoBehavior::enableSeoEdit()) {
            $result['seo'] = [
                'class' => SSeoBehavior::className(),
                'nameTranslate' => 'name',
                'controller' => 'site',
                'action' => 'catalog',
                'parentRelation' => 'parent',
                'childrenRelation' => [
                    'categories',
                    'items',
                ],
            ];
        }

        return $result;
    }
	 
	 public function actionControl()
    {
        $this->view->title = 'Категория';
        $this->breadcrumb[] = [
            'url' => ['category/control'],
            'label' => $this->view->title
        ];
        $item = $this->model;
        if ($id = \Yii::$app->request->get('id')) {
            $item = $item->findOne($id);
        }
        $data['item'] = $item;
        if ($data['item']) {
            return $this->render('//control/form', $data);
        } else {
            return false;
        }
    }
		
    public function actionGetinfo()
    {  
		$date_from = strtotime(Yii::$app->request->get('date_from'));
		$date_to = strtotime(Yii::$app->request->get('date_to'));		
		$only_success_orders = Yii::$app->request->get('only_success_orders');
		$out_duplicate_orders = Yii::$app->request->get('out_duplicate_orders');
		
     	$type = Yii::$app->request->get('type');
	 
		if ($type == 'status') {
			$leadsCount_ = Orders::find()
			->select(['`orders`.status AS status', 'COUNT(*) AS cnt', 'SUM(full_price) AS full_price, SUM(price_delivery) AS price_delivery'])
			->andWhere(
				[  
					'>=',										
					'`orders`.created_at',
					$date_from						
				]) 
				->andWhere(
				[  
					'<=',										
					'`orders`.created_at',
					$date_to						
				]	
			)
			->groupBy(['`orders`.status']);
			
			if (!empty($only_success_orders)) {
				$leadsCount_->andWhere(
					[  
						'`orders`.status' => 5					
					]	
				);
			}
			
			if (!empty($out_duplicate_orders)) {
				$leadsCount_->andWhere(
					[  
						'!=',
						'`orders`.status',
						11
					]	
				);
			}
							
			$count =0;
			$leadsCount = $leadsCount_->all();

			$result_for_table_str = '';
			$result_for_table_str_ = '';
			$data_status = $leadsCount[0]['data_status'];

			foreach ($leadsCount as $result) {
				$count+=(integer)$result['cnt'];			

				$full_price = number_format(((integer)$result['full_price'] + (integer)$result['price_delivery']), 0, '', ' ');
					
				$result_for_table_str.= "['". $data_status[$result['status']]. " (сумма: " . $full_price . " т)'," . (integer)$result['cnt']. '],';
				$result_for_table_str_.= "{'country':\"". $data_status[$result['status']]. " (сумма: " . $full_price . " т)\", 'litres':" . (integer)$result['cnt']. '},';
			}
			$result_for_table_str_ = substr($result_for_table_str_,0,-1);
			return $this->render('index', [
				'result_for_table_str' => $result_for_table_str,
				'result_for_table_str_' => $result_for_table_str_,
				'count' => $count, 
				'type' => $type
			]);
		} elseif ($type == 'statistic') {

			$leadsCount_ = Orders::find()->andWhere(
				[  
					'>=',										
					'`orders`.created_at',
					$date_from						
				]) 
				->andWhere(
				[  
					'<=',										
					'`orders`.created_at',
					$date_to						
				]	
			);
			
			if (!empty($only_success_orders)) {
				$leadsCount_->andWhere(
					[  
						'`orders`.status' => 5					
					]	
				);
			}
			
			if (!empty($out_duplicate_orders)) {
				$leadsCount_->andWhere(
					[  
						'!=',
						'`orders`.status',
						11
					]	
				);
			}
			
			$leadsCount = $leadsCount_->all();

			$orders_created_at = [];
			$orders_created_at_day_week = [];
			$orders_created_at_hour = [];
			$orders_created_at_delivery_method = [];
					
			foreach ($leadsCount as $result) {
				$date_orders = date('Y-m-d',$result->created_at);
				$orders_created_at[$date_orders][] = $result->full_price + $result->price_delivery; 
								
				$date_orders_day_week = date('l',$result->created_at);
				$orders_created_at_day_week[$date_orders_day_week][] = $result->full_price + $result->price_delivery; 
				
				$date_orders_hour = date('H',$result->created_at);
				$orders_created_at_hour[(integer)$date_orders_hour][] = $result->full_price + $result->price_delivery; 
				$orders_created_at_delivery_method[$result->delivery_method][] = $result->full_price + $result->price_delivery; 
			}

			ksort($orders_created_at_hour);
			$final = [];
			$str = '';
			$str_sta = '';
			$str_sta_day_week = '';  
			$str_sta_hour = ''; 
			$str_sta_delivery_method = ''; 			
		  	
			foreach ($orders_created_at as $key => $result) {
				$final[strtotime(date($key))] = array_sum($result);

				$str.='{date:' .strtotime(date($key)). ', value:' . array_sum($result) . '},';

				$y = date('Y',strtotime($key));

				$n = date('n',strtotime($key));

				$j = date('j',strtotime($key));

				$str_sta.='[Date.UTC('.$y.', '.($n-1).', '.$j.'),'.array_sum($result).'],';
			}
							
			$languages_days = [
				'Monday' => 'Понедельник',
				'Tuesday' => 'Вторник',
				'Wednesday' => 'Среда',
				'Thursday' => 'Четверг',
				'Friday' => 'Пятница',
				'Saturday' => 'Суббота',
				'Sunday' => 'Воскресенье'
			];

			$languages_delivery_method = [
				0 => 'Не выбрано',
				1 => 'Самовывоз',
				2 => 'ЯндексДоставка',
				3 => 'Курьер до двери'	
			];
				
			foreach ($orders_created_at_day_week as $key => $result) {				
				$str_sta_day_week.='["' . $languages_days[$key] . '", ' . array_sum($result) . '],';
			}
			
			foreach ($orders_created_at_hour as $key => $result) {				
				$str_sta_hour.='["' . $key . ' ч.", ' . array_sum($result) . '],';
			}
			
			foreach ($orders_created_at_delivery_method as $key => $result) {				
				$str_sta_delivery_method.='["' . $languages_delivery_method[$key] . ' ч.", ' . array_sum($result) . '],';
			}
			
			$str = substr($str,0,-1);
			$str_sta = substr($str_sta,0,-1);
			$str_sta_day_week = substr($str_sta_day_week,0,-1);
			$str_sta_delivery_method = substr($str_sta_delivery_method,0,-1);
			 
			return $this->render('index', [
				'str' => $str,
				'str_sta' => $str_sta,
				'str_sta_day_week' => $str_sta_day_week,
				'str_sta_hour' => $str_sta_hour,
				'str_sta_delivery_method' => $str_sta_delivery_method,
				'type' => $type
			]);
		} elseif ($type == 'statmanager') {
			
			$susers = SUser::find()->all();
			$susers_name_id = [];
			foreach ($susers as $result) {
				$susers_name_id[$result->id] = [
				'username' => $result->username,
				'id' => $result->id,
				];
			}
			
			$leadsCount_failure = Orders::find()
				->select(['`orders`.manager_id AS manager_id', 'COUNT(*) AS cnt', 'SUM(full_price) AS full_price, SUM(price_delivery) AS price_delivery'])
				->andWhere(
					[  
						'>=',										
						'`orders`.created_at',
						$date_from						
					]) 
					->andWhere(
					[  
						'<=',										
						'`orders`.created_at',
						$date_to						
					]	
					)
						->andWhere(
					[  									
						'`orders`.status' => 8				
					]	
					)
				->groupBy(['`orders`.manager_id']);
			
			// if (!empty($only_success_orders)) {

				// $leadsCount_failure->andWhere(
					// [  
						// '`orders`.status' => 5					
					// ]	
				// );
			// }	

			if (!empty($leadsCount_failure)) {
				$leadsCount_failure->andWhere(
					[  
						'!=',
						'`orders`.status',
						11
					]	
				);
			}
			
			$leadsCount_failure = $leadsCount_failure->all();
			$result_statmanager_failure = [];
			
			foreach ($leadsCount_failure as $result) {
				if (!empty($result['manager_id'])) {			
					$result_statmanager_failure[$result['manager_id']] = (integer)$result['full_price'] + (integer)$result['price_delivery'];					
				}				
			}
						
			$leadsCount_success = Orders::find()
				->select(['`orders`.manager_id AS manager_id', 'COUNT(*) AS cnt', 'SUM(full_price) AS full_price, SUM(price_delivery) AS price_delivery'])
				->andWhere(
					[  
						'>=',										
						'`orders`.created_at',
						$date_from						
					]) 
					->andWhere(
					[  
						'<=',										
						'`orders`.created_at',
						$date_to						
					]	
					)
						->andWhere(
					[  									
						'`orders`.status' => 5					
					]	
					)
				->groupBy(['`orders`.manager_id']);
				
			// if (!empty($only_success_orders)) {
				// $leadsCount_success->andWhere(
					// [  
						// '`orders`.status' => 5					
					// ]	
				// );
			// }	
			
			if (!empty($leadsCount_success)) {
				$leadsCount_success->andWhere(
					[  
						'!=',
						'`orders`.status',
						11
					]	
				);
			}
			
			$leadsCount_success = $leadsCount_success->all();
			$result_statmanager_success = [];
			
			foreach ($leadsCount_success as $result) {
				if (!empty($result['manager_id'])) {			
					$result_statmanager_success[$result['manager_id']] = (integer)$result['full_price'] + (integer)$result['price_delivery'];					
				}				
			}
			
			$leadsCount_ = Orders::find()
			->select(['`orders`.manager_id AS manager_id', 'COUNT(*) AS cnt', 'SUM(full_price) AS full_price, SUM(price_delivery) AS price_delivery'])
			->andWhere(
				[  
					'>=',										
					'`orders`.created_at',
					$date_from						
				]) 
				->andWhere(
				[  
					'<=',										
					'`orders`.created_at',
					$date_to						
				]	
			)
			// ->andWhere(
					// [  	'NOT LIKE',								
						// '`orders`.status',
						// 8				
					// ]	
					// )
			->groupBy(['`orders`.manager_id']);
  
			// if (!empty($only_success_orders)) {
				// $leadsCount_->andWhere(
					// [  
						// '`orders`.status' => 5					
					// ]	
				// );
			// }
			
			if (!empty($out_duplicate_orders)) {
				$leadsCount_->andWhere(
					[  
						'!=',
						'`orders`.status',
						11
					]	
				);
			}
			
			$count =0;
			$leadsCount = $leadsCount_->all();
			$result_statmanager = [];
			
			foreach ($leadsCount as $result) {
				if (!empty($result['manager_id'])) {
					$count+=(integer)$result['cnt'];			
					$result_statmanager[] = [
						'cnt' => $result['cnt'],
						'manager_id' => $susers_name_id[$result['manager_id']]['username'] . ' (' .  $susers_name_id[$result['manager_id']]['id']. ')',	
						'manager_id_' => $result['manager_id'],	
						'summa_failure' => isset($result_statmanager_failure[$result['manager_id']]) ? $result_statmanager_failure[$result['manager_id']] : 0,
						'summa_success' => isset($result_statmanager_success[$result['manager_id']]) ? $result_statmanager_success[$result['manager_id']] : 0,
						'summa' => (integer)$result['full_price'] + (integer)$result['price_delivery'],
						'average_check' => ceil(((integer)$result['full_price'] + (integer)$result['price_delivery']) / $result['cnt'])
					];	
				}				
			}  		
	
			return $this->render('index', [
				'result_statmanager' => $result_statmanager,
				'count' => $count, 
				'type' => $type
			]);
		} else if ($type == 'statitems') {
				
			$leadsCount_ = Orders::find()
			->andWhere(
				[  
					'>=',										
					'`orders`.created_at',
					$date_from						
				]) 
				->andWhere(
				[  
					'<=',										
					'`orders`.created_at',
					$date_to						
				]	
			);
			
			if (!empty($only_success_orders)) {

				$leadsCount_->andWhere(
					[  
						'`orders`.status' => 5					
					]	
				);
			}
			
			if (!empty($leadsCount_failure)) {
				$leadsCount_->andWhere(
					[  
						'!=',
						'`orders`.status',
						11
					]	
				);
			}
			
			$count =0;
			$leadsCount = $leadsCount_->all();

			$result_statitems = [];
			
			$str_orders = '';
			
			foreach ($leadsCount as $result) {
				
				$r = OrdersItems::find()->andWhere(['order_id' => $result->id])->all();

				foreach ($r as $items_result) {
					$item_model = json_decode($items_result['data']);
					
					$item_price_ = $items_result->item->sum_price($items_result['count'], 'main', $items_result['price'], $items_result['weight']);
					
					$result_statitems [$item_model->name]['summ'][] = $item_price_;
					$measure_price = '';
					
					$result_statitems [$item_model->name]['count_base'][] = $items_result['count'];
					$result_statitems [$item_model->name]['weight_base'][] = $items_result['weight'];
					
					if ($item_model->measure_price == 0) {
						$measure_price = ' кг.';
						$result_statitems [$item_model->name]['count'][] = $items_result['weight'];
					} elseif ($item_model->measure_price == 1) {
						$measure_price = ' шт.';
						$result_statitems [$item_model->name]['count'][] = $items_result['count'];
					}
					
					$result_statitems [$item_model->name]['orders'][] = '<a href="https://kingfisher.kz/admin/orders/control.html?id='.$result->id. '">' .$result->id. '</a>';
					$result_statitems [$item_model->name]['measure_price'] = $measure_price;
				}					     
			}
			$result_statitems_ = [];
			  
			foreach ($result_statitems as $key => $result) {
				
				$orders_ = array_unique($result['orders']);
				
				$summa = array_sum($result['summ']);
				$result_statitems_[$summa] = [
				'name' => $key,
					'summa' => $summa,
					'count' => array_sum($result['count']),
					
					'count_base' => array_sum($result['count_base']),
					'weight_base' => array_sum($result['weight_base']),
					
					'measure_price' => $result['measure_price'],
					'orders_' => implode(', ', $orders_)
				]; 
			}
			krsort($result_statitems_);
			  
			return $this->render('index', [
				'result_statitems_' => $result_statitems_,

				'type' => $type
			]);
		} elseif ($type == 'appanalitic') {  
			$leadsCount_ = Orders::find()
			->select(['`orders`.isApp AS isApp', '`orders`.status AS status', 'COUNT(*) AS cnt', 'SUM(full_price) AS full_price, SUM(price_delivery) AS price_delivery'])
			->andWhere(
				[  
					'>=',										
					'`orders`.created_at',
					$date_from						
				]) 
				->andWhere(
				[  
					'<=',										
					'`orders`.created_at',
					$date_to						
				]	
			)
			->groupBy(['`orders`.isApp']);
			
			if (!empty($only_success_orders)) {
				$leadsCount_->andWhere(
					[  
						'`orders`.status' => 5					
					]	
				);
			}
			
			if (!empty($out_duplicate_orders)) {
				$leadsCount_->andWhere(
					[  
						'!=',
						'`orders`.status',
						11
					]	
				);
			}
							
			$count =0;
			$leadsCount = $leadsCount_->all();

			$result_for_table_str = '';
			$result_for_table_str_ = '';
			$data_status = ['0' => 'Сайт', '1' => 'Приложение'];  

			foreach ($leadsCount as $result) {
				$count+=(integer)$result['cnt'];			

				$full_price = number_format(((integer)$result['full_price'] + (integer)$result['price_delivery']), 0, '', ' ');
					
				$result_for_table_str.= "['". $data_status[$result['isApp']]. " (сумма: " . $full_price . " т)'," . (integer)$result['cnt']. '],';
				$result_for_table_str_.= "{'country':\"". $data_status[$result['isApp']]. " (сумма: " . $full_price . " т)\", 'litres':" . (integer)$result['cnt']. '},';
			}
			$result_for_table_str_ = substr($result_for_table_str_,0,-1);
			return $this->render('index', [
				'result_for_table_str' => $result_for_table_str,
				'result_for_table_str_' => $result_for_table_str_,
				'count' => $count, 
				'type' => $type
			]);
		}
    }
	
	public function actionDetailcheckmanager()
	{
		$manager_id = Yii::$app->request->get('manager_id');
		$date_from = strtotime(Yii::$app->request->get('date_from'));
		$date_to = strtotime(Yii::$app->request->get('date_to'));		
		$out_duplicate_orders = Yii::$app->request->get('out_duplicate_orders');

		$leadsCount_ = Orders::find()->andWhere(
			[  
				'>=',										
				'`orders`.created_at',
				$date_from						
			]) 
			->andWhere(
			[  
				'<=',										
				'`orders`.created_at',
				$date_to						
			]	
		)
		 ->andWhere(
				[  							
					'`orders`.manager_id' => $manager_id				
				]	
				);
		
		if (!empty($out_duplicate_orders)) {
			$leadsCount_->andWhere(
				[  
					'!=',
					'`orders`.status',
					11
				]	
			);
		}
			
		$leadsCount = $leadsCount_->all();
		$result_statmanager = [];

		foreach ($leadsCount as $result) {
										
				$result_statmanager[] = [
					'id' => $result['id'],
					'summa' => (integer)$result['full_price'] + (integer)$result['price_delivery'] - (integer)$result['bonus_use']
			];	
							
		}  		

		return $this->render('detailcheckmanager', [
			'result_statmanager' => $result_statmanager
		]);
	}
		
	public function actionIndex()
    { 
        return $this->render('index');
    }
}