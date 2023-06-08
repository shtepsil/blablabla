INSERT INTO
    `orders_items`
(
    `order_id`,
    `item_id`,
    `count`,
    `weight`,
    `price`,
    `purch_price`
)

VALUES

(
    :order_id,
    :item_id,
    :quantity,
    :weight,
    :price,
    :purch_price
)

