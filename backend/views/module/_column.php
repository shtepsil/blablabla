<?php
/**
 * Created by PhpStorm.
 * Project: yii2-cms
 * User: lxShaDoWxl
 * Date: 12.05.15
 * Time: 17:29
 * @var common\models\Columns $item
 */
use yii\helpers\Html;
use yii\helpers\Url;


?>
<tr id="field-<?= $item->id ?>" data-id="<?= $item->id ?>">
    <td>
        <?=Html::checkbox('field[]',false,['disabled'=>$item->isDefault!=0,'id'=>"f_{$item->key}",'value'=>$item->id,'class'=>'check_deleted'])?>
    </td>
    <td class="position">
        <?php if ($item->isDefault==0): ?>
            <span class="editable-position editable editable-click"><?= $item->order ?></span>
            <input type="hidden" name="fields[<?= $item->id ?>][order]" value="<?=$item->order ?>" />
        <?php else: ?>
            <span><?= $item->order ?></span>
        <?php endif; ?>
    </td>
    <td class="sys">
        <label for="f_<?= $item->key ?>"><?= $item->key ?> </label>
    </td>
    <td>
        <?php if ($item->isDefault==0): ?>
            <a href="<?= Url::to(['columns/edit', 'id' => $item->id]) ?>"><?= $item->name ?></a>
        <?php else: ?>
            <?= $item->name ?>
        <?php endif; ?>
    </td>
    <td>
        <span class="label label-info"><?= $item->type() ?></span></td>
    <td>
        <?=Html::checkbox("fields[{$item->id}][isLine]",$item->isLine,['disabled'=>$item->isDefault!=0])?>
    </td>
</tr>
