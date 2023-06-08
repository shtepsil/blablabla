<?php

namespace common\models;

use shadow\helpers\GeneratorHelper;
use shadow\helpers\StringHelper;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\helpers\Html;
use yii\helpers\Inflector;
use yii\helpers\Json;

/**
 * This is the model class for table "structure".
 *
 * @property integer $id
 * @property string $url
 * @property string $name
 * @property integer $parent
 * @property integer $order
 * @property string $template
 * @property integer $status
 * @property string $seo
 * @property string $meta_tag
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property Template $idTemplate
 * @property Structure $parent0
 * @property Structure[] $structures
 */
class Structure extends \yii\db\ActiveRecord
{
    public $data_status = array(
        1 => 'Опубликована'
    );
    public function status()
    {
        return $this->data_status[$this->status];
    }
    public static function getListItems()
    {
        /**
         * @var Structure[] $items
         */
        $result['columns']=[
            'created_at'=>[
                'options'=>[
                    'class'=>'text-muted'
                ],
                'function'=>function($item){
                    return date('d.m.Y',$item->created_at);
                }
            ],
            'status'=>[
                'function'=>function($item){
                    return Html::tag('span', $item->status(), ['class' => 'label label-success editable-status editable editable-click','data-id'=>$item->id]);
                }
            ]
        ];
        $result['controls']=[
            'add_parent'=>[
                'url'=>['structure/add','parent'=>'{id}'],
                'icon'=>'plus',
                'options'=>[
                    'class'=>'btn-default btn-xs'
                ]
            ],
            'deleted'=>[
                'url'=>['structure/deleted','id'=>'{id}'],
                'icon'=>'times fa-inverse',
                'options'=>[
                    'class'=>'btn-xs btn-confirm btn-danger'
                ]
            ]
        ];
        $items = Structure::find()->orderBy('`order` ASC')->where('`id`<>1')->all();
        foreach ($items as $item) {
            $result['items'][$item->id]=[
                'model'=>$item,
                'link'=>[
                    'title'=>$item->name,
                    'url'=>['structure/edit','id'=>$item->id],
                    'prev'=>'site/'.$item->url,
                ]
            ];
        }
        return $result;
    }
    public $template_body = '';
	public function FormParams()
	{
        $template ='';
        if (!$this->isNewRecord) {
            $front_views = Yii::getAlias('@frontend/views');
            if (($file = $front_views . $this->template) && file_exists($file)) {
                $template = file_get_contents($file);
            }
        }else{
            $this->order = 0;
            $this->parent = Yii::$app->request->get('parent');
        }
        $this->template_body = $template;
        $result = [
			'form_action' => ['structure/save'],
			'cancel' => ['site/structure'],
//			'fields' => [
//				'name' => []
//			],
			'groups' => [
				'main' => [
                    'title'=>'Основное',
					'icon' => 'suitcase',
					'options' => [],
					'fields' => [
                        'name' => [
                            'title'=>'Название'
                        ],
                        'order'=>[],
                        'parent'=>[
                            'relation'=>[
                                'class'=>'common\models\Structure',
                                'query'=>[
                                    'where'=>'id<>1'
                                ]
                            ]
                        ],
						'template_body' => [
                            'title'=>'Вид',
                            'panel'=>true,
                            'widget'=>[
                                'class'=>'shadow\widgets\AceEditor',
                                'config'=>[
                                    'name' => 'template_body',
                                    'value' => $this->template_body,
                                    'aceOptions'=>[
                                        'minLines'=>16,
                                        "maxLines" => 100,
                                    ]
                                ]
                            ]
                        ],
					],
				],
                'meta'=>[
                    'title'=>'Метаданные',
                    'icon'=>'leaf',
                    'meta'=>true
                ],
                'settings'=>[
                    'title'=>'Настройки',
                    'icon' => 'cogs',
                    'options' => [],
                    'fields'=>[
                        'id_template'=>[
                            'relation'=>[
                                'class'=>'common\models\Template',
                            ]
                        ]
                    ]
                ]
			]
		];
        if($this->id!=1){
            $result['fields']['url'] = [];
        }
		return $result;
	}
//    public function validateAll()
//    {
//        if(isset($this->errors['template'])){
//            unset($this->errors['template']);
//        }
//    }
    public function saveUrl(){
        $this->url = StringHelper::TranslitRuToEn($this->url);
    }
    /**
     * @var bool Хранит обновлёно ли название файла
     */
    public $update_file = false;
    public function saveTemplate()
    {
        $this->saveUrl();
        $this->saveSeo();
        if($template=Yii::$app->request->post('template_body')){
            $update = false;
            if($this->isNewRecord){
                $this->template = '/site/' . $this->url . '.php';
                $update = true;
            }else{
                if($this->oldAttributes['url']!=$this->url ){
                    $this->update_file = $this->template;
                    $this->template='/site/' . $this->url . '.php';
                    $update = true;
                }
            }
            $front_views = Yii::getAlias('@frontend/views');
            $file = $front_views . $this->template;
            file_put_contents($file, $template);
            if($update&&$this->url!='index'||true){
                $name =Inflector::id2camel( strtolower(ucfirst($this->url)), '_').'Action';

                $actions = require(Yii::getAlias('@frontend/config/actions.php'));
                if(!is_array($actions)){
                    $actions = array();
                }
                if(!$this->isNewRecord&&isset($actions[$this->oldAttributes['url']])){
                    unset($actions[$this->oldAttributes['url']]);
                    $name_old =Inflector::id2camel( strtolower(ucfirst($this->oldAttributes['url'])), '_').'Action';
                    @unlink(\Yii::getAlias('@frontend/components/actions/' . $name_old . '.php'));
                }
                $actions[$this->url] = [
                    'class' => 'frontend\components\actions\\' . $name,
                ];
                $save_action = new GeneratorHelper();
                $params = [
                    'template'=>$this->url,
                ];
                $save_actions = new GeneratorHelper();
                $save_actions->start('actions', 'actions', ['actions' => $actions]);
                $save_actions->save();
//                file_put_contents(Yii::getAlias('@frontend/config/actions.php'), "<?php\nreturn ".var_export($actions,true). ";\n");
                $save_action->start('action', $name, $params);
                $save_action->save();
            }
        }
    }
    public function saveClear()
    {
        if($this->update_file){
            $front_views = Yii::getAlias('@frontend/views');
            @unlink($front_views . $this->update_file);
        }
    }
    public function saveSeo()
    {
        if($seo=Yii::$app->request->post('seo')){
            $this->seo = Json::encode($seo);
        }else{
            $this->seo = null;
        }
    }
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'structure';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['url', 'name','id_template'], 'required'],
            [['url'],'unique'],
            [['parent', 'order', 'status','id_template'], 'integer'],
            [['seo', 'meta_tag'], 'string'],
            [['url', 'name', 'template'], 'string', 'max' => 255]
        ];
    }
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'url' => 'URL',
            'name' => 'Название',
            'parent' => 'Родитель',
            'order' => 'Порядок',
            'template' => 'Вид',
            'id_template' => 'Шаблон',
            'status' => 'Status',
            'seo' => 'Seo',
            'meta_tag' => 'Meta Tag',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIdTemplate()
    {
        return $this->hasOne(Template::className(), ['id' => 'id_template']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParent0()
    {
        return $this->hasOne(Structure::className(), ['id' => 'parent']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStructures()
    {
        return $this->hasMany(Structure::className(), ['parent' => 'id']);
    }
}
