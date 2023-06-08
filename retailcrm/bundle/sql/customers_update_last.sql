SELECT 
    u.id as `externalId`,
    u.email,
    u.phone,
    u.discount as `personalDiscount`,    
    u.bonus
FROM 
    `user` as u 
WHERE 
    u.updated_at >= UNIX_TIMESTAMP(:lastSync)
ORDER BY 
    u.id