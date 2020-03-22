UPDATE order_item oi
INNER JOIN orders o
  ON oi.order_id = o.id
INNER JOIN product p
  ON oi.product_id = p.id
SET single_price = IF(p.sconto > 0 AND p.data_inizio_sconto <= o.created_at AND p.data_fine_sconto >=  o.created_at,
                    CAST((p.price - (p.price * p.sconto)/100) AS DECIMAL(8,2)) 
                    , ifnull(p.price, 0)) 
WHERE oi.single_price IS NULL;