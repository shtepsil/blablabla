<?php
namespace shadow\widgets;

use common\components\Debugger as d;
use shadow\assets\Select2Assets;
use Yii;
use yii\bootstrap\Widget;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/**
 * Class AdminForm
 * @package shadow\widgets
 * @property \common\models\Structure $item
 */
class AdminForm extends Widget
{
    /**
     * @var array $params параметры формы
     */
    public $params;
    /**
     * @var \yii\db\ActiveRecord | \yii\base\Model $item
     */
    public $item;
    public $title;
    public $selected;
    public $maps = false;
	/**
     * @var bool | \shadow\plugins\seo\behaviors\SSeoBehavior
     */
    public $seo = false;
    private $no_input = array('dropDownList');
    public function run()
    {
		
		$this->seo = $this->item->getBehavior('seo');
		
        if (!$this->params) {
            $this->params = $this->item->FormParams();
        }
        if (!\Yii::$app->request->isAjax) {
            return $this->render('form/index', ArrayHelper::merge(
                [
                    'item' => $this->item,
                    'selected' => $this->selected,
                    'no_input' => $this->no_input
                ],
                $this->params
            ));
        }
//		if (Yii::app()->request->isAjaxRequest) {
//			Yii::app()->getClientScript()->reset();
//			$output = $this->render('//form/form', array_merge(array(
//				'item' => $this->item,
//				'selected' => $this->selected,
//				'no_input' => $this->no_input
//			), $this->params), true);
//			Yii::app()->getClientScript()->scriptMap=array(
//				'jquery.js'=>false,
//			);
//			Yii::app()->getClientScript()->render($output);
//			echo $output;
//		} else {
//			$this->render('//form/form', array_merge(array(
//				'item' => $this->item,
//				'selected' => $this->selected,
//				'no_input' => $this->no_input
//			), $this->params));
//		}
    }
    /**
     * @param $form \shadow\widgets\AdminActiveForm
     * @param $config array
     * @param $key string
     * @return string
     */
    public function getRow($form, $key, $config)
    {
        /**
         * @var $field \shadow\widgets\AdminActiveField
         */
        if ($result = $this->getFiles($form, $key, $config)) {
            return $result;
        }
        $panel = false;
        if (isset($config['title'])) {
            $name = $config['title'];
            unset($config['title']);
        } else {
            $name = $this->item->getAttributeLabel($key);
        }

        $placeholder = $name;
        $label = $name;

        if(isset($config['field_options'])) {
            $i_os = (isset($config['field_options']['inputOptions']) ? $config['field_options']['inputOptions'] : '');
            $placeholder = (isset($i_os['placeholder']) ? $i_os['placeholder'] : '');
        }
        if(isset($config['labelOptions'])){
                $l_os = $config['labelOptions'];
                $label = $l_os['name'];
        }

        $field_options = [
            'inputOptions' => [
                'placeholder' => $placeholder,
//                'autocomplete'=>'off'
            ],
            'labelOptions' => [
                'label' => $label,
            ]
        ];

        if(isset($config['field_options'])&&$config['field_options']){
            $config_field_options = $config['field_options'];
            if(isset($config_field_options['options']['class']) && is_array($form->fieldConfig)){
                Html::addCssClass($config_field_options['options'], $form->fieldConfig['options']['class']);
            }
            $field_options = ArrayHelper::merge($config_field_options, $field_options);
        }
        if (isset($config['panel']) && $config['panel'] == true) {
            $panel = true;
            $field_options['template'] = "{input}\n";
            $field_options['options']['class'] = "panel-collapse collapse";
            $field_options['options']['id'] = "collapseOne-{$key}";
            $field_options['options']['style'] = "height: 0px;";
        }
//        d::pri($field_options);
        $field = $form->field($this->item, $key, $field_options);

//        return $field;
        $relation = false;
        $data = array();
        if (isset($config['relation'])) {
            /**
             * @var \yii\db\ActiveRecord | \yii\base\Model $relation_model
             */
            $relation = $config['relation'];
            $query = new ActiveQuery($relation['class'], (isset($relation['query']) ? $relation['query'] : []));
            $relation_data = $query->all();
            $data = ArrayHelper::map($relation_data, 'id', isset($relation['label'])?$relation['label']:'name');
            unset($config['relation']);
            //TODO генерация и вывод селекта
        }
        if (!$this->item->hasAttribute($key) && false) {
            if (!isset($config['widget'])) {
                $value = '';
                $type_field = 'textInput';
                if (isset($config['value'])) {
                    $value = $config['value'];
                }
                if (isset($config['type_field'])) {
                    $type_field = $config['type_field'];
                }
                $config['widget'] = [
                    'class' => 'shadow\widgets\AdminField',
                    'config' => [
                        'field' => $type_field,
                        'name' => $key,
                        'value' => $value,
                        'inputOptions' => array(
                            'placeholder' => $placeholder,
                            'id' => Html::getInputId($this->item, $key)
                        )
                    ]
                ];
            }
        } else {
            if ($relation) {
                if (!$this->item->isAttributeRequired($key)) {
                    $data = ArrayHelper::merge(['' => 'Нет'], $data);
                }
                $field->dropDownList($data);
            }
        }
        if (isset($config['type'])) {
            $params_field = isset($config['params']) ? $config['params'] : [];
            switch ($config['type']) {
                case 'dropDownList':
                    $field->dropDownList(isset($config['data']) ? $config['data'] : [], $params_field);
                    break;
                case 'textArea':
                    $field->textarea($params_field);
                    break;
                case 'file':
                    $field->fileInput($params_field);
                    break;
                case 'img':
                    $field->imgInput($params_field);
                    break;
                case 'password':
                    $field->passwordInput($params_field);
                    break;
                case 'checkbox':
                    $field->checkbox($params_field, false);
                    break;
                case 'radioList':
                    if(!isset($config['radio_default']) OR !count($config['radio_default'])){
                        $config['radio_default'] = false;
                    }
                    $field->radioList($params_field, $config['radio_default']);
                    break;
                case 'ul':
                    $field = $this->ul(
                        isset($config['data']) ? $config['data'] : [],
                        isset($config['data_fields']) ? $config['data_fields'] : []
                    );
                    break;
                default:
                    $field->input($config['type'], $params_field);
                    break;
            }
        }
        if (isset($config['widget']) && $config['widget']) {
            $widget = $config['widget'];
            $field->widget($widget['class'], (isset($widget['config'])) ? $widget['config'] : []);
        }
        $result = $field;
//        d::pri($result);
        if ($panel) {
            $a_panel = Html::a($name, "#collapseOne-{$key}", [
                'class' => "accordion-toggle collapsed",
                'data-toggle' => "collapse",
                'data-parent' => "#accordion-{$key}"
            ]);
            $content_panel =
                Html::tag('div', $a_panel, ['class' => 'panel-heading']) . $field;
            $panel = Html::tag('div', Html::tag('div', $content_panel), ['class' => 'panel-group panel-group-success', 'id' => "accordion-{$key}"]);
            $result = $panel;
        }
		if ($this->seo && $this->seo->nameTranslate == $key) { 
			$result .= $this->getRow($form, 'seo_url', $this->seo->configField());
		}
        if(isset($config['type']) AND $config['type'] == 'submintButton'){

            $icon = '';
            if(isset($config['icon']) AND $config['icon'] != ''){
                $icon = '<i class="fa fa-' . $config['icon'] . '"></i>';
            }

            $button_options = ['class' => 'form-control', 'name' => 'button', 'value' => ''];
            if(isset($config['buttonOptions'])&&$config['buttonOptions']){
                $config_button_options = $config['buttonOptions'];
                $button_options = ArrayHelper::merge($button_options, $config_button_options);
            }

            $result = Html::submitButton($icon . ' ' . $name, $button_options);
        }
        return $result;
    }

    public function getFiles($form, $key, $config)
    {
        $result = false;
        if (isset($config['files']) && $config['files']) {
            if (!isset($config['files']['name'])) {
                $config['files']['name'] = $key;
            }
            $result = FilesUpload::widget($config['files']);
        }
        return $result;
    }
    protected $multiple;
    protected $model;
    /**
     * @param $config
     * @return string
     * @throws \yii\base\InvalidConfigException
     */

    public function getRelation($config)
    {
        /**
         * @var \yii\db\ActiveRecord | \yii\base\Model $model
         */
        $data = $config;
        $type = '';
        if (isset($config['type'])) {
            $type = $config['type'];
        }
        switch($type){
            //TODO сделать для MANY_MANY
            case 'MANY_MANY';
                /**
                 * Пример настройки
                 $group=[
                    'values' => [
                        'title' => 'Характеристики',
                        'icon' => 'th-list',
                        'options' => [],
                        'relation'=>[
                            'class'=>OptionsCategory::className(),
                            'type'=>'MANY_MANY',
                            'multiple'=>[
                                'class'=>ItemOptionsValue::className(),
                                'field'=>'item_id',
                                'field_group'=>'option_id',
                                'id'=>'option_id',
                                'field_value'=>function ($element) {
                                    return $element;
                                }
                            ],
                            'add'=>false,
                            'field'=>'cid',
                            'field_value'=>$this->cid,
                            'attributes'=>[
                                'option_id'=>[
                                    'type'=>'relation',
                                    'relation'=>'option',
                                    'field'=>'name'
                                ],
                                'option_value_id'=>[
                                    'isNull'=>false,
                                    'label'=>'Значение',
                                    'type'=>'dropDownList',
                                    'relation'=>[
                                        'class'=>OptionsValue::className(),
                                        'multiple_field'=>'option_id',
                                        'field'=>'value',
                                        'query'=>[
                                            'where'=>'option_id=:id',
                                            'params'=>[':id'=>true]
                                        ]
                                    ]
                                ],
                            ]
                        ]
                    ]
                ]
                 **/
                $this->multiple = $config['multiple'];
                Select2Assets::register($this->view);
                $this->view->registerJs(<<<JS
$('.widget-select2').select2({
    width: '250px',
    tags: true,
    language: 'ru'
});
JS
                );
                $query = new ActiveQuery($config['class'], (isset($config['query']) ? $config['query'] : []));
                $query->andWhere([$config['field'] => isset($config['field_value'])?$config['field_value']:$this->item->getPrimaryKey()]);
                $data['items'] = $query->all();

                $this->model = Yii::createObject($this->multiple['class']);
                $query_multiple = new ActiveQuery($this->multiple['class'], (isset($this->multiple['query'])) ? $this->multiple['query'] : []);
                $query_multiple->andWhere([$this->multiple['field'] => $this->item->getPrimaryKey()]);
                $this->multiple['items'] = ArrayHelper::map(
                    $query_multiple->all(),
                    'id',
                    $this->multiple['field_value'],
                    $this->multiple['field_group']);
                break;
            default:
                $this->model = Yii::createObject($config['class']);
                if (!$this->item->isNewRecord) {
                    $query = new ActiveQuery($config['class'], (isset($config['query']) ? $config['query'] : []));
                    $query->andWhere([$config['field'] => $this->item->getPrimaryKey()]);
                    $data['items'] = $query->all();
                } else {
                    $data['items'] = [];
                }
        }
        if (!isset($config['name'])) {
            $r = new \ReflectionClass($this->model->className());
            $data['name'] = lcfirst($r->getShortName());
        }
        $data['model'] = $this->model;
        return $this->render('form/relation', $data);
    }
    /**
     * @param null | \yii\db\ActiveRecord $item
     * @param $name
     * @param $attribute
     * @param $config
     * @param $clone
     * @return string
     */
    public function getRelationField($item, $name, $attribute, $config = [], $clone = false)
    {
        $id = 'new';
        $value = '';
        $options = ['class' => 'form-control', 'data-field' => $attribute];
        if (!$this->multiple) {
            if ($item) {
                $id = $item->getPrimaryKey();
                $value = $item->{$attribute};

            }
        } else {
            $id = $item->{$this->multiple['id']};
            $options['multiple'] = true;
            $options['class'] .= ' widget-select2';
            if(isset($this->multiple['items'][$id])){
                $value = array_keys($this->multiple['items'][$id]);
            }
        }
        if ($clone) {
            $name .= 'Clone';
        }
        $type = '';
        if (isset($config['type'])) {
            $type = $config['type'];
        }
        switch ($type) {
            case 'dropDownList':
                $data = isset($config['data']) ? $config['data'] : [];
                if (isset($config['relation'])) {
                    /**
                     * @var \yii\db\ActiveRecord | \yii\base\Model $relation_model
                     */
                    $relation = $config['relation'];
                    $query_array = (isset($relation['query']) ? $relation['query'] : []);
                    $field_relation = (isset($relation['field']) ? $relation['field'] : 'name');
                    $value_relation = (isset($relation['value']) ? $relation['value'] : 'id');
                    if(isset($query_array['params'][':id'])){
                        $query_array['params'][':id']=$item->{$relation['multiple_field']};
                    }
                    $query = new ActiveQuery($relation['class'], $query_array);
                    $relation_data = $query->all();
                    $data = ArrayHelper::map($relation_data, $value_relation, $field_relation);
                }
                if (!isset($config['isNull'])||(isset($config['isNull'])&&$config['isNull']==true)) {
                    $data = ArrayHelper::merge(['' => 'Нет'], $data);
                }
                $result = Html::dropDownList($name . '[' . $id . '][' . $attribute . ']',$value, $data,  $options);
                break;
            case 'relation':
                $result = $item->{$config['relation']}->{$config['field']};
                break;
            case 'name':
                $result = '';
                break;
            default:
                $result = Html::input('text',
                    $name . '[' . $id . '][' . $attribute . ']',
                    $value,
                    $options
                );
                break;
        }
        return $result;
    }

    /**
     * @param array $params
     * @return $this
     */
    public function ul($items = [], $fields = [])
    {
        $ul = '';
        if(is_array($items) AND is_array($fields) AND count($items) AND count($fields)){
            $ul = Html::ul($items, ['item' => function($item, $index) use ($fields) {
                return Html::tag(
                    'li',
                    $this->render('ul', [
                        'item' => $item,
                        'fields' => $fields
                    ]),
                    ['class' => 'list-group-item']
                );
            }, 'class' => 'list-group']);
        }
        return $ul;
    }
}