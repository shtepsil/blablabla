SELECT
    c.id as `externalId`,
    c.id as `number`,
    FROM_UNIXTIME(c.`created_at`, '%Y-%m-%d %H:%i:%s') as `createdAt`,
    IF(c.status > 0, 'called','need-call') as `status`,
    c.`name` as `fio`,
    c.phone
FROM
    `callback` as c
WHERE 
    FIND_IN_SET(c.id, :callIds)
ORDER BY
    c.id

