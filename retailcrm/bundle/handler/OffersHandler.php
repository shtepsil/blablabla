<?php

class OffersHandler implements HandlerInterface
{
    public function prepare($data) {
        $offers = array();
        $container = Container::getInstance();
        $measureMap = array(
            'kg' => array(
                'code' => 'kg',
                'name' => "Килограмм",
                'sym'=> "кг"
            ),
            'pc' => array(
                'code' => 'pc',
                'name' => 'Штука',
                'sym'=> 'шт.'
            )
        );
        $measurePriceMap = array(
            0 => 'kg',//'Взразвес',
            1 => 'pc',//'Поштучно'
        );
        
        foreach ($data as $record) {
            $offer = array(
                'id' => $record['id'],
                'productId' => $record['id'],
                'name' => $record['name'],
                'productName' => $record['name'],
                'categoryId' => $record['categoryId'],
                'price' => $record['price'],
                'productActivity' => $record['productActivity'],
                'purchasePrice' => $record['purchasePrice'],
                'quantity' => floatval($record['quantity']),
                'unit' => $measureMap[$record['unit_value']],
                'url' => $container->shopUrl . '/item.html?id='.$record['id']
            );
            
            if (isset($record['picture'])) {
                $offer['picture'] = $container->shopUrl . $record['picture'];
            }
            
            if (isset($record['article'])) {
                $offer['params'][] = array(
                    'name' => 'Артикул',
                    'code' => 'article',
                    'value' => $record['article']
                );
            }
            
            if (isset($record['weight'])) {
                $offer['params'][] = array(
                    'name' => 'Вес',
                    'code' => 'weight',
                    'value' => floatval($record['weight'])*1000
                );
            }
            
            if ($record['unit'] == 'pc' && $record['unit_value'] == 'kg') {
                $offers[] = $offer;
                $offer['id'] = '77'.$offer['productId'];
                $offer['unit'] = $measureMap['pc'];
                $offer['name'] = $record['name'].'(цена за штуку)'; 
                // цена за килограмм
                $offer['price'] *= floatval($record['weight']); 
                $offer['purchasePrice'] *= floatval($record['weight']); 
            }
            
            $offers[] = $offer;
        }
        
        return $offers;
    }
}
