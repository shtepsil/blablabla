SELECT  
    s.id as `externalId`,
    s.email,
    FROM_UNIXTIME(s.created_at, '%Y-%m-%d %H:%i:%s') as `createdAt`
FROM 
    `subscriptions` as s 
WHERE 
    s.created_at >= UNIX_TIMESTAMP(:lastSync)
ORDER BY 
    s.id

