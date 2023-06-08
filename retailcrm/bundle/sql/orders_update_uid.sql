SELECT 
    o.id as `externalId`,
    o.user_id as `customerId`,
    o.user_name as `fio`,
    o.user_phone as `phone`,
    o.user_mail as `email`,
    o.user_address as `delivery_text`,
    o.user_comments as `customerComment`,
    c.name as `delivery_city`,
    FROM_UNIXTIME(o.date_delivery, '%Y-%m-%d') as `delivery_date`,
    o.time_delivery as `delivery_time`,
    o.payment,
    o.status,
    FROM_UNIXTIME(o.created_at, '%Y-%m-%d %H:%i:%s') as `createdAt`,
    FROM_UNIXTIME(o.updated_at, '%Y-%m-%d %H:%i:%s') as `modificatedAt`,
    o.admin_comments as `managerComment`,
    o.bonus_use as `discount`,
    IF (o.isFast > 0, 'one-click', 'shopping-cart') as `orderMethod`,
    o.manager_id as `managerId`
FROM 
    `orders` as o
LEFT JOIN `city` as c
    ON (o.city_id = c.id)
WHERE
    FIND_IN_SET(o.id, :orderIds)
ORDER BY 
    o.id
