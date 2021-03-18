

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
(3, 2, 'Via Regular 2', 'Roma', '00100');

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
(2, 'Regolare', 'Utente', 'regular@email.com', '2019-05-02 16:34:56', 'regular', 'password');

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

CREATE TABLE `version` ( `version` VARCHAR(14) NOT NULL , PRIMARY KEY (`version`)) ENGINE = InnoDB;
INSERT INTO `version` (`version`) VALUES ('1');

alter table product_images drop foreign key FK_Product_Image;

DROP PROCEDURE all_orders;
DROP PROCEDURE cart_total;
DROP PROCEDURE cart_items;
DROP PROCEDURE cart_to_order;
DROP PROCEDURE get_order_email;
DROP PROCEDURE order_items;
DROP PROCEDURE order_total;
DROP PROCEDURE user_orders;

ALTER TABLE cart ADD last_interaction DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP; 

ALTER TABLE orders ADD payment_code VARCHAR(255) NULL, ADD payment_status VARCHAR(255) NULL;

ALTER TABLE orders ADD is_restored BIT NULL;

CREATE TABLE category ( id INT NOT NULL AUTO_INCREMENT , name TEXT NOT NULL , PRIMARY KEY (id)) ENGINE = InnoDB;

ALTER TABLE product_images ADD title VARCHAR(255) NULL, ADD alt VARCHAR(255) NULL, ADD order_number int(11) NULL;

ALTER TABLE order_item ADD single_price DECIMAL(10, 2) NULL;

UPDATE order_item oi
INNER JOIN orders o
  ON oi.order_id = o.id
INNER JOIN product p
  ON oi.product_id = p.id
SET single_price = IF(p.sconto > 0 AND p.data_inizio_sconto <= o.created_at AND p.data_fine_sconto >=  o.created_at,
                    CAST((p.price - (p.price * p.sconto)/100) AS DECIMAL(8,2)) 
                    , ifnull(p.price, 0)) 
WHERE oi.single_price IS NULL;

ALTER TABLE orders ADD payment_method VARCHAR(50) NULL;

ALTER TABLE orders ADD is_email_sent BIT NULL;

CREATE TABLE shipment ( id INT NOT NULL AUTO_INCREMENT , name VARCHAR(2000) NOT NULL , price DECIMAL(10,2) NOT NULL , PRIMARY KEY (id)) ;

ALTER TABLE product ADD mtitle TEXT NULL, ADD metadescription TEXT NULL;

ALTER TABLE orders MODIFY  COLUMN is_restored INT;
ALTER TABLE orders MODIFY  COLUMN is_email_sent INT;

ALTER TABLE cart ADD shipment_id INT;

ALTER TABLE orders ADD shipment_name VARCHAR(255) NULL, ADD shipment_price DECIMAL(10,2) NULL;

ALTER TABLE user ADD reset_link VARCHAR(255) NULL;


CREATE TABLE special_treatment_type
(
  code VARCHAR(50) NOT NULL
  , description VARCHAR(255) NOT NULL
  , special_treatment_name VARCHAR(255) NOT NULL
);
INSERT INTO special_treatment_type (code, description, special_treatment_name) VALUES ('extra-discount', 'Extra Sconto', 'Percentuale');
INSERT INTO special_treatment_type (code, description, special_treatment_name) VALUES ('delayed-payment', 'Pagamento Ritardato', 'Giorni');

CREATE TABLE special_treatment
(
  id INT AUTO_INCREMENT
  , type_code VARCHAR(50) NOT NULL
  , name VARCHAR(255) NOT NULL
  , special_treatment_value VARCHAR(255) NOT NULL
  , PRIMARY KEY (id)
);

CREATE TABLE profile
(
  id INT AUTO_INCREMENT
  , name VARCHAR(255) NOT NULL
  , PRIMARY KEY (id)
);

CREATE TABLE profile_treatments
(
  profile_id INT NOT NULL
  , special_treatment_id INT NOT NULL
);
ALTER TABLE user ADD profile_id INT NULL;

ALTER TABLE category ADD description LONGTEXT NULL AFTER name, ADD metadesc TEXT NULL AFTER description; 

ALTER TABLE category ADD parent_id INT NULL;


INSERT INTO `category`(`id`, `name`, `parent_id`) VALUES (100, 'Categoria Padre 1', NULL);

INSERT INTO `category`(`id`, `name`, `parent_id`) VALUES (110, 'Categoria Figlio 1_1', 100);
INSERT INTO `category`(`id`, `name`, `parent_id`) VALUES (120, 'Categoria Figlio 1_2', 100);
INSERT INTO `category`(`id`, `name`, `parent_id`) VALUES (130, 'Categoria Figlio 1_3', 100);
INSERT INTO `category`(`id`, `name`, `parent_id`) VALUES (140, 'Categoria Figlio 1_4', 100);
INSERT INTO `category`(`id`, `name`, `parent_id`) VALUES (150, 'Categoria Figlio 1_5', 100);
INSERT INTO `category`(`id`, `name`, `parent_id`) VALUES (160, 'Categoria Figlio 1_6', 100);
INSERT INTO `category`(`id`, `name`, `parent_id`) VALUES (170, 'Categoria Figlio 1_7', 100);
INSERT INTO `category`(`id`, `name`, `parent_id`) VALUES (180, 'Categoria Figlio 1_8', 100);
INSERT INTO `category`(`id`, `name`, `parent_id`) VALUES (190, 'Categoria Figlio 1_9', 100);

INSERT INTO `category`(`id`, `name`, `parent_id`) VALUES (200, 'Categoria Padre 2', NULL);

INSERT INTO `category`(`id`, `name`, `parent_id`) VALUES (210, 'Categoria Figlio 2_1', 200);
INSERT INTO `category`(`id`, `name`, `parent_id`) VALUES (220, 'Categoria Figlio 2_2', 200);
INSERT INTO `category`(`id`, `name`, `parent_id`) VALUES (230, 'Categoria Figlio 2_3', 200);
INSERT INTO `category`(`id`, `name`, `parent_id`) VALUES (240, 'Categoria Figlio 2_4', 200);
INSERT INTO `category`(`id`, `name`, `parent_id`) VALUES (250, 'Categoria Figlio 2_5', 200);
INSERT INTO `category`(`id`, `name`, `parent_id`) VALUES (260, 'Categoria Figlio 2_6', 200);
INSERT INTO `category`(`id`, `name`, `parent_id`) VALUES (270, 'Categoria Figlio 2_7', 200);
INSERT INTO `category`(`id`, `name`, `parent_id`) VALUES (280, 'Categoria Figlio 2_8', 200);
INSERT INTO `category`(`id`, `name`, `parent_id`) VALUES (290, 'Categoria Figlio 2_9', 200);

INSERT INTO `category`(`id`, `name`, `parent_id`) VALUES (300, 'Categoria Padre 3', NULL);

INSERT INTO `category`(`id`, `name`, `parent_id`) VALUES (310, 'Categoria Figlio 3_1', 300);
INSERT INTO `category`(`id`, `name`, `parent_id`) VALUES (320, 'Categoria Figlio 3_2', 300);
INSERT INTO `category`(`id`, `name`, `parent_id`) VALUES (330, 'Categoria Figlio 3_3', 300);
INSERT INTO `category`(`id`, `name`, `parent_id`) VALUES (340, 'Categoria Figlio 3_4', 300);
INSERT INTO `category`(`id`, `name`, `parent_id`) VALUES (350, 'Categoria Figlio 3_5', 300);
INSERT INTO `category`(`id`, `name`, `parent_id`) VALUES (360, 'Categoria Figlio 3_6', 300);
INSERT INTO `category`(`id`, `name`, `parent_id`) VALUES (370, 'Categoria Figlio 3_7', 300);
INSERT INTO `category`(`id`, `name`, `parent_id`) VALUES (380, 'Categoria Figlio 3_8', 300);
INSERT INTO `category`(`id`, `name`, `parent_id`) VALUES (390, 'Categoria Figlio 3_9', 300);

CREATE TABLE product_categories
(
    product_id INT
    , subcategory_id INT
);

CREATE TABLE email
(
    id INT AUTO_INCREMENT
    , subject VARCHAR(255) NOT NULL
    , message TEXT NOT NULL
    , PRIMARY KEY (id) 
);
CREATE TABLE email_recipients
(
  email_id INT NOT NULL
  , recipient_id INT NOT NULL  
);

update `version` set `version` = '202003312300';

update `user` set `password` = '$2y$10$MKe7.DMwSgDsPcovj7Sds.vHmp6u7Y38liYMRhXiYndlZZxrDGrS6';


