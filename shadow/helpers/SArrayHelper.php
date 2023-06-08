<?php
/**
 * Created by PhpStorm.
 * Project: morkovka
 * User: lxShaDoWxl
 * Date: 12.10.15
 * Time: 10:33
 */
namespace shadow\helpers;

use yii\helpers\ArrayHelper;

class SArrayHelper extends ArrayHelper
{
    /**
     * Merges two or more arrays into one recursively.
     * If each array has an element with the same string key value, the latter
     * will overwrite the former (different from array_merge_recursive).
     * Recursive merging will be conducted if both arrays have an element of array
     * type and are having the same key.
     * For integer-keyed elements, the elements from the latter array will
     * be appended to the former array.
     * @param array $a array to be merged to
     * @param array $b array to be merged from. You can specify additional
     * arrays via third argument, fourth argument etc.
     * @return array the merged array (the original arrays are not changed.)
     */
    public static function mergeOptions($a, $b)
    {
        $args = func_get_args();
        $res = array_shift($args);
        while (!empty($args)) {
            $next = array_shift($args);
            foreach ($next as $k => $v) {
                if (is_int($k)) {
                    if (isset($res[$k])) {
                        $res[] = $v;
                    } else {
                        $res[$k] = $v;
                    }
                } elseif (is_array($v) && isset($res[$k]) && is_array($res[$k])) {
                    $res[$k] = self::mergeOptions($res[$k], $v);
                } else {
                    if($k=='class'&&isset($res[$k])){
                        $array = self::merge(explode(' ', $res[$k]),explode(' ', $v));
                        $class_a = [];
                        foreach ($array as $key => $value) {
                            $value = trim($value);
                            if($value&&!in_array($value,$class_a)){
                                $class_a[] = $value;
                            }
                        }
                        if($class_a){
                            $res[$k] = implode(' ',$class_a);
                        }
                    }else{
                        $res[$k] = $v;
                    }
                }
            }
        }
        return $res;
    }
}