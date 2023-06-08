SELECT
    oi.item_id as `productId`,
    h.name as `handlingName`
FROM
    `orders_items` as oi
INNER JOIN
    `orders_items_handing` as ih
ON
    (oi.id = ih.orders_items_id)
LEFT JOIN
    `type_handling` as h
ON
    (ih.type_handling_id = h.id)
WHERE 
    oi.order_id = :orderId
    