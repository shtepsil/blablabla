<?php
namespace common\models;

use shadow\widgets\CKEditor;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\helpers\Inflector;
use shadow\SResizeImg;

/**
 * This is the model class for table "sets".
 *
 * @property integer $id
 * @property string $name
 * @property string $body
 * @property string $img
 * @property double $discount
 * @property integer $isVisible
 * @property integer $price
 * @property integer $price_purch
 * @property integer $price_sale
 * @property integer $created_at
 * @property integer $updated_at
 * @property double $bonus_manager
 *
 * @property SetsItems[] $setsItems
 * @property Items[] $items
 */
class Sets extends \shadow\SActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'sets';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name','price','price_sale','bonus_manager','price_purch'], 'required'],
            ['img', 'image', 'extensions' => 'jpg, gif, png, jpeg', 'skipOnEmpty' => (!$this->isNewRecord)],
            [['discount', 'bonus_manager'], 'number'],
            [['isVisible'],'default','value'=>0],
            [['isVisible','price_purch','price','price_sale'], 'integer'],
            [['body'], 'string'],
            [['name'], 'string', 'max' => 255]
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
            'body' => 'Описание',
            'img' => 'Изображение',
            'discount' => 'Скидка %',
            'isVisible' => 'Видимость',
            'bonus_manager' => 'Бонус менеджеру',
            'isSale' => 'Купить вместе',
            'price' => 'Стоимость',
            'price_purch' => 'Закупочная',
            'price_sale' => 'Экономия',
        ];
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSetsItems()
    {
        return $this->hasMany(SetsItems::className(), ['set_id' => 'id']);
    }
    public function getItems()
    {
        return $this->hasMany(Items::className(), ['id' => 'item_id'])->via('setsItems');
    }
    public function FormParams()
    {
        if ($this->isNewRecord) {
            $this->loadDefaultValues(true);
        }
        $controller_name = Inflector::camel2id(Yii::$app->controller->id);
        $result = [
            'form_action' => ["$controller_name/save"],
            'cancel' => ["$controller_name/index"],
            'groups' => [
                'main' => [
                    'title' => 'Основное',
                    'icon' => 'suitcase',
                    'fields' => [
                        'isVisible' => [
                            'type' => 'checkbox'
                        ],
                        'price' => [],
                        'price_purch' => [],
                        'price_sale' => [],
                        'name' => [],
                        'discount' => [],
                        'bonus_manager' => [],
                        'img' => [
                            'type' => 'img'
                        ],
                        'body' => [
                            'type' => 'textArea',
                            'widget' => [
                                'class' => CKEditor::className()
                            ]
                        ],
                    ],
                ],
                'items' => [
                    'title' => 'Товары',
                    'icon' => 'th-list',
                    'render' => [
                        'view' => '//modules/sets_items'
                    ]
                ]
            ],
        ];
        return $result;
    }
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
            [
                'class' => '\shadow\behaviors\UploadFileBehavior',
                'attributes' => ['img'],
            ],
        ];
    }
    use SResizeImg;
    public function img($resize = false, $size_type = 'mini', $array = false)
    {
        if (!$array) {
            if ($this->img) {
                if ($resize && isset(ItemImg::$_size_img_a[$size_type])) {
                    $result = $this->resizeImg(ItemImg::$_size_img_a[$size_type], 'img');
                } else {
                    $result = $this->img;
                }
            } else {
                if (isset($this->itemImgs[0]->url)) {
                    if ($resize) {
                        $result = $this->itemImgs[0]->resizeImg($size_type);
                    } else {
                        $result = $this->itemImgs[0]->url;
                    }
                } else {
                    $result = '/uploads/no_photo.png';
                }
            }
            if (!$result) {
                $result = '/uploads/no_photo.png';
            }
            if ($result != '/uploads/no_photo.png' && !is_file(Yii::getAlias('@frontend/web') . $result)) {
                $result = '/uploads/no_photo.png';
            }
        } else {
            $result = [];
            if ($this->itemImgs) {
                $result = array();
                foreach ($this->itemImgs as $img) {
                    if ($resize) {
                        if (is_array($size_type)) {
                            $img_size = [];
                            foreach ($size_type as $value) {
                                if ($img_resize = $img->resizeImg($value)) {
                                    $img_size[$value] = $img_resize;
                                }else{
                                    $img_size[$value] = '';
                                }
                            }
                            $img_size['title'] = $img->name;
                            $result[] = $img_size;
                        } else {
                            if ($img_resize = $img->resizeImg($size_type)) {
                                $result[] = $img_resize;
                            }
                        }
                    } else {
                        if (is_file(Yii::getAlias('@frontend/web') . $img->url)) {
                            $result[] = $img->url;
                        }
                    }
                }
            } else {
                if ($this->img) {
                    if ($resize) {
                        if (is_array($size_type)) {
                            $img_size = [];
                            foreach ($size_type as $value) {
                                if (!isset(ItemImg::$_size_img_a[$value])) {
                                    if (is_file(Yii::getAlias('@frontend/web') . $this->img)) {
                                        $img_size[$value] = $this->img;
                                    }else{
                                        $img_size[$value] = null;
                                    }
                                    continue;
                                }
                                if ($img_resize = $this->resizeImg(ItemImg::$_size_img_a[$value], 'img')) {
                                    $img_size[$value] = $img_resize;
                                }
                            }
                            $img_size['title'] = $this->name;
                            $result[] = $img_size;
                        } else {
                            if (isset(ItemImg::$_size_img_a[$size_type]) && ($img_resize = $this->resizeImg(ItemImg::$_size_img_a[$size_type], 'img'))) {
                                $result[] = $img_resize;
                            }
                        }
                    } else {
                        if (is_file(Yii::getAlias('@frontend/web') . $this->img)) {
                            $result[] = $this->img;
                        }
                    }
                }
            }
            if (!$result) {
                $result[] = '/uploads/no_photo.png';
            }
        }
        return $result;
    }
    /**
     * @param $event \yii\base\ModelEvent
     */
    public function saveClear($event)
    {
        parent::saveClear($event); // TODO: Change the autogenerated stub
        $insert = ($event->name == self::EVENT_AFTER_INSERT);
        $this->saveItems($insert);
    }
    public function saveItems($insert)
    {
        /**
         * @var $target SetsItems
         * @var $old_relation SetsItems[]
         */
        $model = new SetsItems();
        $data = Yii::$app->request->post('SetsItems', []);
        $old_relation = $insert_data = [];
        if (!$insert) {
            $old_relation = $model->find()->indexBy('item_id')->where(['set_id' => $this->id])->all();
        }
        if ($data) {
            foreach ($data as $key => $value) {
                if (isset($old_relation[$key])) {
                    $target = $old_relation[$key];
                    $update = [];
                    foreach ($value as $key_attr => $val_attr) {
                        if ($target->hasAttribute($key_attr) && $target->getAttribute($key_attr) != $val_attr) {
                            $update[$key_attr] = $val_attr;
                        }
                    }
                    if ($update) {
                        Yii::$app->db->createCommand()->update($model->tableName(), $update, ['id' => $target->id])->execute();
                    }
                    unset($old_relation[$key]);
                } else {
                    $insert_data[] = [
                        'set_id' => $this->id,
                        'item_id' => $key,
                        'price' => $value['price'],
                        'count' => $value['count'],
                    ];
                }
            }
        }
        if ($old_relation) {
            $delete_data = [];
            foreach ($old_relation as $key => $value) {
                $delete_data[] = $value->id;
            }
            if ($delete_data) {
                Yii::$app->db->createCommand()->delete($model->tableName(), ['id' => $delete_data])->execute();
            }
        }
        if ($insert_data) {
            Yii::$app->db->createCommand()->batchInsert($model->tableName(),
                [
                    'set_id',
                    'item_id',
                    'price',
                    'count',
                ],
                $insert_data)->execute();
        }
    }
    private $_real_price = 0;
    private $_real_purch_price = 0;
    private $_saving_price = 0;
    public function real_price()
    {
        if (!$this->_real_price) {
            if (!$this->price||!$this->price_sale) {
                $full_price_set = $full_price = $full_purch_price = 0;
                foreach ($this->setsItems as $set_item) {
                    $count = (double)$set_item->count;
                    $full_price_set += $set_item->item->sum_price($count, 'main', $set_item->price);
                    $full_price += $set_item->item->sum_price($count);
                    $full_purch_price += $set_item->item->sum_price($count, 'purch');
                }
                if ($this->discount) {
                    $price_set = round((int)$full_price * (100 - $this->discount) / 100);
                } else {
                    $price_set = $full_price_set;
                }
                $this->_real_price = $price_set;
                $this->_real_purch_price = $full_purch_price;
                $this->_saving_price = round($full_price - $price_set);
            } else {
                $this->_real_price = $this->price;
                $this->_real_purch_price = $this->price_purch;
                $this->_saving_price = $this->price_sale;
            }
        }
        return $this->_real_price;
    }
    public function real_purch_price()
    {
        if (!$this->_real_purch_price) {
            $this->real_price();
        }
        return $this->_real_purch_price;
    }
    public function saving_price()
    {
        if (!$this->_saving_price) {
            $this->real_price();
        }
        return $this->_saving_price;
    }
    public function price_bonus_manager()
    {
        if ((double)$this->bonus_manager) {
            $bonus = (($this->real_price()) * (double)$this->bonus_manager)/100;
        } else {
            $bonus = 0;
        }
        return $bonus;
    }
}
