SELECT 
    u.id as `externalId`,
    u.username as `fio`,
    u.email,
    u.phone,
    c.name as `address_city`,
    ua.street as `address_street`,
    ua.home as `address_building`,
    ua.house as `address_flat`,
    FROM_UNIXTIME(u.created_at, '%Y-%m-%d %H:%i:%s') as `createdAt`,
    u.discount as `personalDiscount`,    
    u.bonus
FROM 
    `user` as u 
LEFT JOIN `city` as c
    ON (u.city_id = c.id)
LEFT JOIN `user_address` as ua
    ON (u.id = ua.user_id)
WHERE 
    u.created_at >= UNIX_TIMESTAMP(:lastSync)
ORDER BY 
    u.id
