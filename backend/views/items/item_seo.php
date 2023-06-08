<?php
/**
 *
 * @var \yii\web\View $this
 * @var               $context \shadow\widgets\AdminForm
 * @var               $item    \common\models\Items
 * @var string        $name
 */
?>
<div class="form-group simple">
    <label class="col-md-2 control-label" for="items-seo-title">Title</label>
    <div class="col-md-10">
        <input type="text" id="items-seo-title" class="form-control" name="Items[seo][title]" value="<?=($item->seo)?$item->seo->title:''?>" placeholder="Title" autocomplete="off">
    </div>
</div>
<div class="form-group simple">
    <label class="col-md-2 control-label" for="items-seo-description">Description</label>
    <div class="col-md-10">
        <textarea id="items-seo-description" class="form-control" name="Items[seo][description]"
                  placeholder="Description"
                  autocomplete="off" cols="30" rows="10"><?=($item->seo)?$item->seo->description:''?></textarea>
    </div>
</div>
<div class="form-group simple">
    <label class="col-md-2 control-label" for="items-seo-keywords">Keywords</label>
    <div class="col-md-10">
        <textarea id="items-seo-keywords" class="form-control" name="Items[seo][keywords]"
                  placeholder="Keywords"
                  autocomplete="off" cols="30" rows="10"><?=($item->seo)?$item->seo->keywords:''?></textarea>
    </div>
</div>