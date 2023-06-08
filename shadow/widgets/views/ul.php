<?php

use common\components\Debugger as d;
use yii\helpers\Url;

$content = '';
$url = '';
if(count($fields)){
    foreach($fields as $field_name => $field_tpl){
        if($field_name == 'id'){
            $url = Url::to(['users/control', 'id' => $item->$field_name]);
        }
        $content .= str_replace('{' . $field_name . '}', $item->$field_name, $field_tpl);
    }
}
?>
<?if($url != ''):?>
    <a href="<?=$url?>" target="_blank"><?=$content?></a>
<?else:?>
    <?=$content?>
<?endif?>
