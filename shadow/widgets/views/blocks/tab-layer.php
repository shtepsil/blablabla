<?php

use common\components\Debugger as d;

?>
<style>
    .<?=$form_name?>-group-tab-layer{
        display: <?=$css_params['display']?>;
        position: absolute;
        width: 100%;
        height: 100%;
        z-index: 8;
        background: rgba(255,255,255,.5);
    }
</style>
<div class="<?=$form_name?>-group-tab-layer"></div>
