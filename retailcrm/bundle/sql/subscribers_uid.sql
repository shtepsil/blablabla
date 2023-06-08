SELECT  
    s.id as `externalId`,
    s.email,
    FROM_UNIXTIME(s.created_at, '%Y-%m-%d %H:%i:%s') as `createdAt`
FROM 
    `subscriptions` as s 
WHERE 
    FIND_IN_SET(s.id, :subscriberIds)
ORDER BY 
    s.id

