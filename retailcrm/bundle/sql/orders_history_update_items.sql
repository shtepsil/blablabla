UPDATE
    `orders_items`
SET
    `count` = :count,
    `weight` = :weight,
    `price` = :price
WHERE
    item_id = :item_id
AND
    order_id = :order_id
