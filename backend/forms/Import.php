<?php
namespace backend\forms;

use common\components\Debugger as d;
use yii\db\ActiveQuery;
//use yii\web\UploadedFile;

/**
 * Created by PhpStorm.
 * Project: kingfisher
 * User: lxShaDoWxl
 * Date: 11.01.16
 * Time: 16:29
 */


use common\models\City;
use common\models\Items;
use common\models\ItemsCount;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use yii\web\UploadedFile;

class Import extends Model
{
    public $file;

    /**
     * Returns the form name that this model class should use.
     *
     * The form name is mainly used by [[\yii\widgets\ActiveForm]] to determine how to name
     * the input fields for the attributes in a model. If the form name is "A" and an attribute
     * name is "b", then the corresponding input name would be "A[b]". If the form name is
     * an empty string, then the input name would be "b".
     *
     * By default, this method returns the model class name (without the namespace part)
     * as the form name. You may override it when the model is used in different forms.
     *
     * @return string the form name of this model class.
     */
    public function formName()
    {
        return 'import';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['file', 'file', 'extensions' => ['xlsx', 'xls'], 'skipOnEmpty' => false, 'checkExtensionByMimeType' => false],
            [['file'], 'safe'],
        ];
    }
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'file' => 'Файл',
        ];
    }
    public function import()
    {
        /**@var $file UploadedFile */
        $file = $this->file;
        $path_file = \Yii::getAlias('@backend/tmp/') . 'import.' . $file->getExtension();
        if ($file && $file->saveAs($path_file)) {
            return $this->excel($path_file);
        } else {
            return false;
        }
    }
    public function excel($file)
    {
        $format = \PHPExcel_IOFactory::identify($file);
        $objectreader = \PHPExcel_IOFactory::createReader($format);
        $objectPhpExcel = $objectreader->load($file);
        $data = $objectPhpExcel->setActiveSheetIndex(0)->toArray(null, true, true, false);
        if ($data) {
            /**
             * @var $items Items[]
             * @var $city_all City[]
             */
            $items = Items::find()->indexBy('id')->with([
                'itemsCounts' => function ($q) {
                    /**@var $q \yii\db\ActiveQuery */
                    $q->indexBy('city_id');
                }
            ])->all();
            $city_all = City::find()->indexBy('name')->all();
            // Этот массив задаёт поля которые нужно обновить
            $columns = [
                0 => 'id',
                1 => 'article',
                2 => 'name',
//                3 => 'body',
                4 => 'wholesale_price',
                5 => 'price',
                6 => 'purch_price',
                7 => 'bonus_manager',
//                8 => 'isVisible',
            ];
            $title_columns = $data[0];
            //удаляем заголовки из основного массива
            unset($data[0]);
            ArrayHelper::remove($title_columns, 2);
            foreach ($columns as $column_key => $column) {
                //оставляем те строки которые мы не указали и получим название городов
                ArrayHelper::remove($title_columns, $column_key);
            }
            unset($columns[0]);//удаляем id из списка изменяемых столбцов
            $q_builder = \Yii::$app->db->queryBuilder;
            $q_builder_count = \Yii::$app->db->queryBuilder;
            $update_sql = $update_count = '';
            $update_params = $insert_count = $update_count_params = [];
            $count_table = (new ItemsCount())->tableName();
            foreach ($data as $value) {
                /*
                 * Если поле ID существует в массиве,
                 * если id true,
                 * если в массиве $items есть товар с $id
                 */
                if (isset($value[0]) && ($id = $value[0]) && isset($items[$id])) {
                    unset($value[0]);//удаляем id из списка проверяемых столбцов
                    $item = $items[$id];
                    $item_counts = $item->itemsCounts;
                    $update = [];
                    foreach ($columns as $key_column => $column) {
                        $val = $value[$key_column];
                        if ($column == 'bonus_manager') {
                            $val = str_replace(',', '.', $val);
                            $value[$key_column] = $val;
                        } elseif ($column == 'isVisible') {
                            $val = (base64_encode(mb_strtolower(trim($val), "UTF-8")) == base64_encode('вкл')) ? 1 : 0;
                            $value[$key_column] = $val;
                        }
                        if ($val != $item->getAttribute($column)) {
                            $update[$column] = $val;
                        }
                    }
                    if ($update) {
                        $update_sql .= $q_builder->update($item->tableName(), $update, ['id' => $id], $update_params) . ";\n";
                    }
                    foreach ($title_columns as $key_title => $title_column) {
                        if (isset($city_all[$title_column])) {
                            $city = $city_all[$title_column];
                        } else {
                            continue;
                        }
                        $val = $value[$key_title];
                        if (isset($item_counts[$city->id])) {
                            $item_count = $item_counts[$city->id];
                            if ($item_count->count != $val) {
                                $update_count .= $q_builder_count->update($count_table, ['count' => $val], ['id' => $item_count->id], $update_count_params) . ";\n";
                            }
                        } else {
                            $insert_count[] = [
                                'item_id' => $id,
                                'city_id' => $city->id,
                                'count' => str_replace(',', '.', $val),
                            ];
                        }
                    }
                }
            }
            if ($update_sql) {
                \Yii::$app->db->createCommand($update_sql, $update_params)->execute();
            }
            if ($update_count) {
                \Yii::$app->db->createCommand($update_count, $update_count_params)->execute();
            }
            if ($insert_count) {
                \Yii::$app->db->createCommand()->batchInsert($count_table, [
                    'item_id',
                    'city_id',
                    'count'
                ], $insert_count)->execute();
            }
            return true;
        } else {
            return true;
        }
    }
}