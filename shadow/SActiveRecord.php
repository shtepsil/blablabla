<?php
/**
 * Created by PhpStorm.
 * Project: yii2-cms
 * User: lxShaDoWxl
 * Date: 08.05.15
 * Time: 11:30
 */
namespace shadow;

use common\components\Debugger as d;
use shadow\helpers\StringHelper;
use yii\db\ActiveRecord;

class SActiveRecord extends ActiveRecord
{
    public function saveUniqueName()
    {
        if (isset($this->unique_name)) {
            $this->unique_name = StringHelper::TranslitRuToEn($this->unique_name);
        }
    }
    public function saveAll($event)
    {
//        $this->saveUniqueName();
    }
    public function saveClear($event)
    {
    }
    public function validateAll()
    {
        return true;
    }
    /**
     * @param $name string
     * @param $new_relation array
     * @param $event \yii\base\Event
     * @param $extraColumns array
     */
    public function saveRelation($name, $new_relation, $event, $extraColumns=[])
    {
        $relation = $this->getRelation($name);
        if ($relation->via !== null && is_array($relation->via)) {
            /* @var $viaRelation \yii\db\ActiveQuery */
            list($viaName, $viaRelation) = $relation->via;
            $viaClass = $viaRelation->modelClass;
            /**
             * @var $old_relation \yii\db\ActiveRecord
             * @var $model \yii\db\ActiveRecord
             */
            $insert_data = [];
            $model = new $viaClass();
            if($event->name==$this::EVENT_AFTER_INSERT){
                $old_relation = [];
            }else{
                $old_relation = $viaRelation->indexBy($relation->link['id'])->all();
            }
            $table_columns = [];
            foreach ($new_relation as $key => $value) {
                if (!isset($old_relation[$value])) {
                    $columns = [];
                    foreach ($viaRelation->link as $a => $b) {
                        $columns[$a] = $this->$b;
                    }
                    foreach ($relation->link as $a => $b) {
                        $columns[$b] = $value;
                    }
                    foreach ($extraColumns as $k => $v) {
                        $columns[$k] = $v;
                    }
                    if (!$table_columns) {
                        $table_columns = array_keys($columns);
                    }
                    $insert_data[] = $columns;
                } else {
                    unset($old_relation[$value]);
                }
            }
            if ($old_relation) {
                $delete_data = [];
                foreach ($old_relation as $key => $value) {
                    $delete_data[] = $value->id;
                }
                if ($delete_data) {
                    \Yii::$app->db->createCommand()->delete($model->tableName(), ['id' => $delete_data])->execute();
                }
            }
            if ($insert_data && $table_columns) {
                \Yii::$app->db->createCommand()->batchInsert($model->tableName(),
                    $table_columns,
                    $insert_data)->execute();
            }
        }
    }
}