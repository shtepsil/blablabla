DELETE 
FROM 
    `orders_items`
WHERE
    item_id = :item_id
AND
    order_id = :order_id
