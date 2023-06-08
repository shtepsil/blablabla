INSERT INTO
    `orders_items`
(
    `order_id`,
    `item_id`,
    `count`,
    `weight`,
    `price`,
    `purch_price`,
    `bonus_manager`,
    `data`
)

VALUES

(
    :orderId,
    :productId,
    :quantity,
    :weight,
    :price,
    :purchasePrice,
    0,
    ''
)
