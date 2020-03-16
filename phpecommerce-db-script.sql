

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


DELIMITER $$
--
-- Procedure
--
CREATE  PROCEDURE `all_orders` (`status_code` VARCHAR(10))  BEGIN
  SELECT 
    o.id as order_id
        , o.created_at as created_date
        , o.updated_at as shipped_date
        , o.status as status
        , o.user_id as user_id
        , u.email as user_descr
    FROM orders o
    INNER JOIN user u
    ON o.user_id = u.id
  WHERE
        (status_code is NULL OR status_code = o.status)
    ORDER BY
    o.created_at DESC;
END$$

CREATE  PROCEDURE `cart_items` (IN `cart_identifier` INT)  BEGIN
  SELECT 
    c.id as cart_id
        , ci.id as cart_item_id
        , p.name as product_name
        , p.id as product_id
        , p.description as product_description
        , ifnull(ci.quantity, 0) as quantity
        ,IF(p.`sconto`>0 AND p.`data_inizio_sconto` <= DATE(NOW()) AND p.`data_fine_sconto` >= DATE(NOW()),
            CAST((p.`price` -(p.`price`*p.`sconto`)/100) AS DECIMAL(8,2)) 
          ,ifnull(p.`price`, 0))AS single_price
        , ifnull(ci.quantity,0) * IF(p.`sconto`>0 AND `data_inizio_sconto` <= DATE(NOW()) AND `data_fine_sconto` >= DATE(NOW()),
           CAST((`price` -(`price`*`sconto`)/100) AS DECIMAL(8,2)) 
          ,ifnull(`price`, 0)) as total_price
    FROM
    cart as c
        INNER JOIN cart_item as ci
      ON c.id = ci.cart_id
        INNER JOIN product as p
      ON p.id = ci.product_id
     WHERE
    ifnull(cart_identifier, 0) = 0
        OR cart_identifier = c.id;
        
END$$

CREATE  PROCEDURE `cart_total` (`cart_identifier` INT)  BEGIN
 SELECT 
  c.id as cart_id
  , c.user_id as user_id
    , SUM(ifnull(ci.quantity, 0)) as num_products
    , SUM(ifnull(ci.quantity, 0) * ifnull(p.price, 0)) as total
 FROM 
  cart as c
  INNER JOIN cart_item as ci
    ON c.id = ci.cart_id
  INNER JOIN product as p
    ON ci.product_id = p.id
  WHERE
    cart_identifier = c.id;
END$$

CREATE  PROCEDURE `cart_to_order` (`cart_identifier` INT, `order_identifier` INT)  BEGIN
  INSERT INTO order_item (order_id, product_id, quantity)
    SELECT order_identifier, ci.product_id, ci.quantity
    FROM cart c
    INNER JOIN cart_item ci
      ON c.id = ci.cart_id
  WHERE
    c.id = cart_identifier;
        
  DELETE cart, cart_item
    FROM cart
    INNER JOIN cart_item
    ON cart.id = cart_item.cart_id
  WHERE
    cart.id = cart_identifier;
    
END$$

CREATE  PROCEDURE `get_order_email` (`order_identifier` INT)  BEGIN
  SELECT u.email, u.first_name
    FROM orders as o
    INNER JOIN user as u
    ON o.user_id = u.id
  WHERE 
    o.id = order_identifier;
END$$

CREATE  PROCEDURE `order_items` (`order_identifier` INT)  BEGIN
  SELECT 
    o.id as order_id
        , o.status as order_status
        , oi.id as order_item_id
        , p.name as product_name
        , p.id as product_id
        , p.description as product_description
        , ifnull(oi.quantity, 0) as quantity
        , ifnull(p.price, 0) as single_price
        , ifnull(oi.quantity,0) * ifnull(p.price, 0) as total_price
    FROM
    orders as o
        INNER JOIN order_item as oi
      ON o.id = oi.order_id
        INNER JOIN product as p
      ON p.id = oi.product_id
     WHERE
    ifnull(order_identifier, 0) = 0
        OR order_identifier = o.id;
        
END$$

CREATE  PROCEDURE `order_total` (`order_identifier` INT)  BEGIN
 SELECT 
  o.id as order_id
  , o.user_id as user_id
    , SUM(ifnull(oi.quantity, 0)) as num_products
    , SUM(ifnull(oi.quantity, 0) * ifnull(p.price, 0)) as total
 FROM 
  orders as o
  INNER JOIN order_item as oi
    ON o.id = oi.order_id
  INNER JOIN product as p
    ON oi.product_id = p.id
  WHERE
    order_identifier = o.id;
END$$

CREATE  PROCEDURE `user_orders` (`user_identifier` INT, `status_code` VARCHAR(10))  BEGIN
  SELECT 
    o.id as order_id
        , o.created_at as created_date
        , o.updated_at as shipped_date
        , o.status as status
    FROM orders o
  WHERE
    o.user_id = user_identifier
        AND (status_code is NULL OR status_code = o.status)
    ORDER BY
    o.created_at DESC;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Struttura della tabella `address`
--

CREATE TABLE `address` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `street` varchar(255) DEFAULT NULL,
  `city` varchar(50) DEFAULT NULL,
  `cap` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dump dei dati per la tabella `address`
--

INSERT INTO `address` (`id`, `user_id`, `street`, `city`, `cap`) VALUES
(2, 1, 'Via Admin 1', 'Roma', '00100'),
(3, 2, 'Via Regular 2', 'Roma', '00100')

-- --------------------------------------------------------

--
-- Struttura della tabella `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `client_id` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



-- --------------------------------------------------------

--
-- Struttura della tabella `cart_item`
--

CREATE TABLE `cart_item` (
  `id` int(11) NOT NULL,
  `cart_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


-- --------------------------------------------------------

--
-- Struttura della tabella `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL,
  `status` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


-- --------------------------------------------------------

--
-- Struttura della tabella `order_item`
--

CREATE TABLE `order_item` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



-- --------------------------------------------------------

--
-- Struttura della tabella `product`
--

CREATE TABLE `product` (
  `id` int(11) NOT NULL,
  `name` varchar(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `description` text CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `category_id` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `sconto` int(9) NOT NULL DEFAULT '0',
  `data_inizio_sconto` date DEFAULT NULL,
  `data_fine_sconto` date DEFAULT NULL,
  `qta` int(9) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

--
-- Dump dei dati per la tabella `product`
--

INSERT INTO `product` (`id`, `name`, `description`, `category_id`, `price`, `sconto`, `data_inizio_sconto`, `data_fine_sconto`, `qta`) VALUES
(6, 'Samsung A10 Black', 'Questo Ã¨ il prodotto numero 1 Lorem ipsum dolor sit, amet consectetur adipisicing elit. Quod quasi, illum esse obcaecati quisquam asperiores nemo eaque optio aliquid corporis soluta harum ad numquam. Exercitationem vero enim doloribus optio dolor? Lorem ipsum dolor sit, amet consectetur adipisicing elit. Quod quasi, illum esse obcaecati quisquam asperiores nemo eaque optio aliquid corporis soluta harum ad numquam. Exercitationem vero enim doloribus optio dolor?', 2, '3.90', 16, '2020-03-09', '2020-03-13', 0),
(14, 'Prodotto 7', 'Questo Ã¨ il prodotto numero 7\r\nLorem ipsum dolor sit amet consectetur adipisicing elit. Animi ipsam cum inventore dignissimos quisquam, earum omnis excepturi accusantium incidunt dicta veritatis, amet commodi atque saepe, laborum dolorem voluptatibus aliquid illo?\r\nLorem ipsum dolor sit amet consectetur adipisicing elit. Animi ipsam cum inventore dignissimos quisquam, earum omnis excepturi accusantium incidunt dicta veritatis, amet commodi atque saepe, laborum dolorem voluptatibus aliquid illo?', 1, '8.25', 20, '2020-03-06', '2020-05-16', 0),
(26, 'Prodotto 8', 'Questo Ã¨ il prodotto n. 8\r\nLorem ipsum dolor sit amet consectetur adipisicing elit. Animi ipsam cum inventore dignissimos quisquam, earum omnis excepturi accusantium incidunt dicta veritatis, amet commodi atque saepe, laborum dolorem voluptatibus aliquid illo?\r\n\r\nLorem ipsum dolor sit amet consectetur adipisicing elit. Animi ipsam cum inventore dignissimos quisquam, earum omnis excepturi accusantium incidunt dicta veritatis, amet commodi atque saepe, laborum dolorem voluptatibus aliquid illo?', 1, '5.90', 0, NULL, NULL, 0),
(33, 'Prodotto 9', 'questo Ã¨ il prodotto numero 9+', 2, '3.50', 0, NULL, NULL, 0),
(34, 'Prodotto 10', 'Questo Ã¨ il prodotto numero 10\r\nLorem ipsum dolor sit amet consectetur adipisicing elit. Soluta tempore asperiores assumenda laborum, reprehenderit repellat suscipit eligendi officia ea saepe praesentium nisi alias porro quas sint maiores recusandae, perferendis omnis.', 1, '10.00', 0, NULL, NULL, 0),
(35, 'Prodotto 11', 'Questo Ã¨ il prodotto numero 10\r\nLorem ipsum dolor sit amet consectetur adipisicing elit. Soluta tempore asperiores assumenda laborum, reprehenderit repellat suscipit eligendi officia ea saepe praesentium nisi alias porro quas sint maiores recusandae, perferendis omnis.', 1, '5.00', 0, NULL, NULL, 0),
(36, 'Prodotto 12', 'Questo Ã¨ il prodotto numero 12\r\nLorem ipsum dolor sit amet consectetur adipisicing elit. Soluta tempore asperiores assumenda laborum, reprehenderit repellat suscipit eligendi officia ea saepe praesentium nisi alias porro quas sint maiores recusandae, perferendis omnis.', 2, '8.00', 0, NULL, NULL, 0),
(37, 'Prodotto 13', 'Questo Ã¨ il prodotto numero 13\r\nLorem ipsum dolor sit amet consectetur adipisicing elit. Soluta tempore asperiores assumenda laborum, reprehenderit repellat suscipit eligendi officia ea saepe praesentium nisi alias porro quas sint maiores recusandae, perferendis omnis.', 2, '9.99', 0, NULL, NULL, 0),
(38, 'Prodotto 14', 'Questo Ã¨ il prodotto numero 14\r\nLorem ipsum dolor sit amet consectetur adipisicing elit. Soluta tempore asperiores assumenda laborum, reprehenderit repellat suscipit eligendi officia ea saepe praesentium nisi alias porro quas sint maiores recusandae, perferendis omnis.', 2, '3.99', 0, NULL, NULL, 0),


-- --------------------------------------------------------

--
-- Struttura della tabella `product_images`
--

CREATE TABLE `product_images` (
  `id` int(10) NOT NULL,
  `product_id` int(10) NOT NULL,
  `image_extension` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `product_images`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_type` varchar(50) NOT NULL,
  `password` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dump dei dati per la tabella `user`
--

INSERT INTO `user` (`id`, `first_name`, `last_name`, `email`, `created_at`, `user_type`, `password`) VALUES
(1, 'Amministratore', 'Di Sistema', 'admin@email.com', '2019-04-26 21:26:37', 'admin', 'password'),
(2, 'Regolare', 'Utente', 'regular@email.com', '2019-05-02 16:34:56', 'regular', 'password')

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `address`
--
ALTER TABLE `address`
  ADD PRIMARY KEY (`id`);

--
-- Indici per le tabelle `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`);

--
-- Indici per le tabelle `cart_item`
--
ALTER TABLE `cart_item`
  ADD PRIMARY KEY (`id`);

--
-- Indici per le tabelle `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Indici per le tabelle `order_item`
--
ALTER TABLE `order_item`
  ADD PRIMARY KEY (`id`);

--
-- Indici per le tabelle `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`id`);

--
-- Indici per le tabelle `product_images`
--
ALTER TABLE `product_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_Product_Image` (`product_id`);

--
-- Indici per le tabelle `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT per le tabelle scaricate
--

--
-- AUTO_INCREMENT per la tabella `address`
--
ALTER TABLE `address`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT per la tabella `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=74;

--
-- AUTO_INCREMENT per la tabella `cart_item`
--
ALTER TABLE `cart_item`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=110;

--
-- AUTO_INCREMENT per la tabella `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT per la tabella `order_item`
--
ALTER TABLE `order_item`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=131;

--
-- AUTO_INCREMENT per la tabella `product`
--
ALTER TABLE `product`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT per la tabella `product_images`
--
ALTER TABLE `product_images`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT per la tabella `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;


ALTER TABLE `product_images`
  ADD CONSTRAINT `FK_Product_Image` FOREIGN KEY (`product_id`) REFERENCES `product` (`id`);
COMMIT;

