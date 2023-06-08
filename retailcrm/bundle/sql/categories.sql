SELECT
    id,
    name,
    parent_id
FROM 
    `category`
WHERE 
    isVisible = 1
ORDER BY
    id
