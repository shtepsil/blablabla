<p class="popup-header">Выбрать пункт</p>
<ul class="pickup-list">
    <?php foreach ($pickpoints as $key => $pickpoint) :?>
        <?php
        $coord = $pickpoint['coordinate'];
        $name = $pickpoint['name'];
        $address = $pickpoint['desc'];?>
        <li data-id="<?=$pickpoint['id']?>" data-content="<?=$coord?>">
            <?=$name?>
        </li>
        <?php
        $content = '<div class="baloon-poup" data-id="'.$pickpoint['id'].'">'.$address.'<br><p>Заберу от сюда</p></div>';
        $this->registerJs(<<<JS
                myMap.geoObjects.add(new ymaps.Placemark([$coord], {
                    //balloonContentHeader: "$name",
                    balloonContentBody: '$content',
                    hintContent: "$name"
                }, {
                    preset: 'islands#dotIcon',
                    iconColor: '#4686cc'
                }));
JS
        );
        ?>
    <?php endforeach;?>
</ul>
<?php
$this->registerJs(<<<JS
    $("#popupPickUp ul.pickup-list li").on('click', function (e) {
        e.preventDefault();
        var coord = $(this).attr('data-content').split(',');
        myMap.geoObjects.get($(this).index()).balloon.open();
        myMap.setCenter([coord[0], coord[1]], 14, {checkZoomRange: true});
        return false;
    });
JS
); ?>
<div id="map"></div>