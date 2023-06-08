<? foreach($brands as $brand): ?>
    <a class="goods__block__mini" href="<?= $brand->url()?>">	
	   <? if ($brand->img): ?>
			<img style="margin-left:30px;margin-top:-20px" width="100" src="<?= $brand->img ?>">
		<? endif ?>
       <!-- <div class="__name" style="margin:10px; float:left"><?php //echo $brand->name?></div>-->
    </a><hr>
<? endforeach; ?>