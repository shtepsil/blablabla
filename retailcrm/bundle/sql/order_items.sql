SELECT
    i.item_id as `productId`,
    i.count,
    i.weight,
    i.price,
    offer.weight as `initialWeight`,
    IF(offer.measure > 0, 'pc', 'kg') as `unit`,
    IF(offer.measure_price > 0, 'pc', 'kg') as `unit_value`
FROM    
    `orders_items` as i
LEFT JOIN 
    `items` as offer
ON
    (offer.id = i.item_id)
WHERE
    i.order_id = :orderId
