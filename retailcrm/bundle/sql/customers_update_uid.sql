SELECT 
    u.id as `externalId`,
    u.email,
    u.phone,
    u.discount as `personalDiscount`,    
    u.bonus
FROM 
    `user` as u 
WHERE 
    FIND_IN_SET(u.id, :customerIds)
ORDER BY 
    u.id