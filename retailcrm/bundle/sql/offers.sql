SELECT
    i.id as `id`,
    i.cid as `categoryId`,
    i.name,
    i.article as `article`,
    i.price,
    i.purch_price as `purchasePrice`,
    i.count as `quantity`,
    i.img_list as `picture`,
    i.weight,
    IF(i.isVisible > 0, 'Y', 'N') as `productActivity`,
    IF(i.measure > 0, 'pc', 'kg') as `unit`,
    IF(i.measure_price > 0, 'pc', 'kg') as `unit_value`
FROM 
    `items` as i