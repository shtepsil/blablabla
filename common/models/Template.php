<?php

namespace common\models;

use shadow\helpers\GeneratorHelper;
use shadow\helpers\StringHelper;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\helpers\Inflector;

/**
 * This is the model class for table "template".
 *
 * @property integer $id
 * @property string $name
 * @property string $unique_name
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $noDeleted
 */
class Template extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'template';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'unique_name'], 'required'],
            [['unique_name'],'unique'],
            [['unique_name'],'match','not' => true, 'pattern' => '/[^a-z]/','message' => 'Можно использовать только латиницу нижнего регистра',],
            [['created_at', 'updated_at', 'noDeleted'], 'integer'],
            [['name', 'unique_name'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Название',
            'unique_name' => 'Уникальное название файла',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'noDeleted' => 'No Deleted',
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
    public function FormParams()
    {
        $template ='';
        $js = $css =[];
        if (!$this->isNewRecord) {
            $front_views = Yii::getAlias('@frontend/views/layouts');
            $file = $front_views .'/'. $this->unique_name.'.php';
            if (file_exists($file)) {
                $template = file_get_contents($file);
            }
            $name =Inflector::id2camel( strtolower(ucfirst($this->unique_name)), '_').'Assets';
            if(is_file(Yii::getAlias('@frontend/assets/'.$name.'.php'))){
                $assets = Yii::createObject('\frontend\assets\\'.$name);
                $js_a = $assets->js;
                $css_a = $assets->css;
                foreach ($js_a as $value) {
                    $js[] = [
                        'name' => $value,
                        'size' =>0,
                        'type' => 'text/javascript',
                        'url' => $value,
                    ];
                }
                foreach ($css_a as $value) {
                    $css[] = [
                        'name' => $value,
                        'size' =>0,
                        'type' => 'text/css',
                        'url' => $value,
                    ];
                }
            }
        }
        $result = [
            'form_action' => ['template/save'],
            'cancel' => ['site/template'],
//			'fields' => [
//				'name' => []
//			],
            'groups' => [
                'main' => [
                    'title'=>'Основное',
                    'icon' => 'suitcase',
                    'options' => [],
                    'fields' => [
                        'name' => [],
                        'template_body' => [
                            'title'=>'Шаблон',
                            'panel'=>true,
                            'widget'=>[
                                'class'=>'shadow\widgets\AceEditor',
                                'config'=>[
                                    'name' => 'template_body',
                                    'value' => $template,
                                    'aceOptions'=>[
                                        'minLines'=>16,
                                        "maxLines" => 100,
                                    ]
                                ]
                            ]
                        ],

                    ],
                ],
                'settings'=>[
                    'title'=>'Настройки',
                    'icon' => 'cogs',
                    'options' => [],
                    'fields'=>[
                        'js_files' => [
                            'files'=>[
                                'title'=>'Js Файлы',
                                'name'=>'js',
                                'filters'=>[
                                    'jsFilter'=>true,
                                ],
                                'value'=>$js
                            ]
                        ],
                        'css_files' => [
                            'files'=>[
                                'title'=>'Css Файлы',
                                'name'=>'css',
                                'filters'=>[
                                    'cssFilter'=>true,
                                ],
                                'value'=>$css
                            ]
                        ],
                    ]
                ]
            ]
        ];
        if($this->id!=1&&$this->isNewRecord){
            $result['fields']['unique_name'] = [];
        }
        return $result;
    }
    public function saveUniqueName(){
        $this->unique_name = StringHelper::TranslitRuToEn($this->unique_name);
    }
    /**
     * @var bool Хранит обновлёно ли название файла
     */
    public $update_file = false;
    public function saveTemplate()
    {
        $this->saveUniqueName();
        $this->saveFiles();
        if(($template=Yii::$app->request->post('template_body'))&&($this->isNewRecord||$this->noDeleted==0)){
            $front_views = Yii::getAlias('@frontend/views/layouts');
            $file = $front_views .'/'. $this->unique_name.'.php';
            file_put_contents($file, $template);
        }
    }
    public function saveClear()
    {
        if($this->update_file){
            $front_views = Yii::getAlias('@frontend/views');
            @unlink($front_views . $this->update_file);
        }
    }
    public function saveFiles(){
        /**
         * @var \yii\web\AssetBundle $assets
         */
        $name =Inflector::id2camel( strtolower(ucfirst($this->unique_name)), '_').'Assets';
        $assets = false;
        $update = false;

        $save_assets = new GeneratorHelper();
        $path = '@frontend/assets/' . $this->unique_name;
        $params = [
            'patch'=>$path,
            'js'=>[],
            'css'=>[],
            'depends'=>[
                'yii\web\JqueryAsset'
            ]
        ];
        if(is_file(Yii::getAlias('@frontend/assets/'.$name.'.php'))){
            $assets = Yii::createObject('\frontend\assets\\'.$name);
            $params['js'] = $assets->js;
            $params['css'] = $assets->css;
        }
        $css_a = Yii::$app->request->post('css');
        $js_a = Yii::$app->request->post('js');
        if($js_a||$css_a){
            $tmp_path = Yii::getAlias('@backend/tmp');
            $path_save= Yii::getAlias($path);
            if(!is_dir($path_save)){
                $mask = @umask(0);
                @mkdir(Yii::getAlias($path), 0777, true);
                @umask($mask);

            }
            if(isset($js_a)&&$js_a){
                foreach ($js_a as $value) {
                    $value_new = StringHelper::basename($value);
                    if(in_array($value_new,$params['js'])){
                        continue;
                    }
                    if (is_file($tmp_path.$value)&&strtolower(pathinfo($value, PATHINFO_EXTENSION))=='js'&&
                        !is_file($path_save .'/' . $value_new)
                    ) {
                        @rename($tmp_path . $value, $path_save .'/' . $value_new);
                        $params['js'][] = $value_new;
                    }
                }
            }
            if(isset($css_a)&&$css_a){
                foreach ($css_a as $value) {
                    $value_new = StringHelper::basename($value);
                    if(in_array($value_new,$params['css'])){
                        continue;
                    }
                    if (is_file($tmp_path.$value)&&strtolower(pathinfo($value, PATHINFO_EXTENSION))=='css'&&
                        !is_file($path_save .'/' . $value_new)
                    ) {
                        @rename($tmp_path . $value, $path_save .'/' . $value_new);
                        $params['css'][] =$value_new;
                    }
                }
            }
            $update = true;
        }else{
            if($assets&&($assets->js||$assets->css)){
                $update = true;
            }
        }
        if($update==true){
            $save_assets->start('assets', $name, $params);
            $save_assets->save();
        }
    }
}
