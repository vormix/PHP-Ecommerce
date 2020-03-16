-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Creato il: Feb 12, 2020 alle 08:02
-- Versione del server: 10.1.36-MariaDB
-- Versione PHP: 7.2.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `website1`
--

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

CREATE  PROCEDURE `cart_items` (`cart_identifier` INT)  BEGIN
  SELECT 
    c.id as cart_id
        , ci.id as cart_item_id
        , p.name as product_name
        , p.id as product_id
        , p.description as product_description
        , ifnull(ci.quantity, 0) as quantity
        , ifnull(p.price, 0) as single_price
        , ifnull(ci.quantity,0) * ifnull(p.price, 0) as total_price
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

--
-- Dump dei dati per la tabella `cart`
--

-- INSERT INTO `cart` (`id`, `user_id`, `client_id`) VALUES



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

--
-- Dump dei dati per la tabella `cart_item`
--

INSERT INTO `cart_item` (`id`, `cart_id`, `product_id`, `quantity`) VALUES
(48, 19, 6, 3),
(49, 19, 14, 2),
(57, 25, 14, 20);

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

--
-- Dump dei dati per la tabella `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `created_at`, `updated_at`, `status`) VALUES
(4, 1, '2019-05-02 12:21:15', NULL, 'pending'),
(12, 2, '2019-05-02 14:20:31', NULL, 'pending'),
(13, 1, '2019-05-02 15:39:08', '2019-05-02 20:39:59', 'shipped');

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

--
-- Dump dei dati per la tabella `order_item`
--

INSERT INTO `order_item` (`id`, `order_id`, `product_id`, `quantity`) VALUES
(70, 4, 6, 10),
(71, 4, 14, 9),
(100, 12, 6, 9),
(101, 12, 26, 6),
(102, 12, 33, 1),
(103, 12, 14, 2),
(107, 13, 14, 2);

-- --------------------------------------------------------

--
-- Struttura della tabella `product`
--

CREATE TABLE `product` (
  `id` int(11) NOT NULL,
  `name` varchar(50) CHARACTER SET latin1 NOT NULL,
  `description` text CHARACTER SET latin1 NOT NULL,
  `category_id` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT '0.00'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

--
-- Dump dei dati per la tabella `product`
--

INSERT INTO `product` (`id`, `name`, `description`, `category_id`, `price`) VALUES
(6, 'Prodotto 1', 'Questo Ã¨ il prodotto numero 1\r\n\r\nLorem ipsum dolor sit, amet consectetur adipisicing elit. Quod quasi, illum esse obcaecati quisquam asperiores nemo eaque optio aliquid corporis soluta harum ad numquam. Exercitationem vero enim doloribus optio dolor?\r\n\r\nLorem ipsum dolor sit, amet consectetur adipisicing elit. Quod quasi, illum esse obcaecati quisquam asperiores nemo eaque optio aliquid corporis soluta harum ad numquam. Exercitationem vero enim doloribus optio dolor?', 1, '3.90'),
(14, 'Prodotto 7', 'Questo Ã¨ il prodotto numero 7\r\nLorem ipsum dolor sit amet consectetur adipisicing elit. Animi ipsam cum inventore dignissimos quisquam, earum omnis excepturi accusantium incidunt dicta veritatis, amet commodi atque saepe, laborum dolorem voluptatibus aliquid illo?\r\nLorem ipsum dolor sit amet consectetur adipisicing elit. Animi ipsam cum inventore dignissimos quisquam, earum omnis excepturi accusantium incidunt dicta veritatis, amet commodi atque saepe, laborum dolorem voluptatibus aliquid illo?', 1, '8.25'),
(26, 'Prodotto 8', 'Questo Ã¨ il prodotto n. 8\r\nLorem ipsum dolor sit amet consectetur adipisicing elit. Animi ipsam cum inventore dignissimos quisquam, earum omnis excepturi accusantium incidunt dicta veritatis, amet commodi atque saepe, laborum dolorem voluptatibus aliquid illo?\r\n\r\nLorem ipsum dolor sit amet consectetur adipisicing elit. Animi ipsam cum inventore dignissimos quisquam, earum omnis excepturi accusantium incidunt dicta veritatis, amet commodi atque saepe, laborum dolorem voluptatibus aliquid illo?', 1, '5.90'),
(33, 'Prodotto 9', 'questo Ã¨ il prodotto numero 9+', 2, '3.50'),
(34, 'Prodotto 10', 'Questo Ã¨ il prodotto numero 10\r\nLorem ipsum dolor sit amet consectetur adipisicing elit. Soluta tempore asperiores assumenda laborum, reprehenderit repellat suscipit eligendi officia ea saepe praesentium nisi alias porro quas sint maiores recusandae, perferendis omnis.', 1, '10.00'),
(35, 'Prodotto 11', 'Questo Ã¨ il prodotto numero 10\r\nLorem ipsum dolor sit amet consectetur adipisicing elit. Soluta tempore asperiores assumenda laborum, reprehenderit repellat suscipit eligendi officia ea saepe praesentium nisi alias porro quas sint maiores recusandae, perferendis omnis.', 1, '5.00'),
(36, 'Prodotto 12', 'Questo Ã¨ il prodotto numero 12\r\nLorem ipsum dolor sit amet consectetur adipisicing elit. Soluta tempore asperiores assumenda laborum, reprehenderit repellat suscipit eligendi officia ea saepe praesentium nisi alias porro quas sint maiores recusandae, perferendis omnis.', 2, '8.00'),
(37, 'Prodotto 13', 'Questo Ã¨ il prodotto numero 13\r\nLorem ipsum dolor sit amet consectetur adipisicing elit. Soluta tempore asperiores assumenda laborum, reprehenderit repellat suscipit eligendi officia ea saepe praesentium nisi alias porro quas sint maiores recusandae, perferendis omnis.', 2, '9.99'),
(38, 'Prodotto 14', 'Questo Ã¨ il prodotto numero 14\r\nLorem ipsum dolor sit amet consectetur adipisicing elit. Soluta tempore asperiores assumenda laborum, reprehenderit repellat suscipit eligendi officia ea saepe praesentium nisi alias porro quas sint maiores recusandae, perferendis omnis.', 2, '3.99'),
(39, 'Nuovo prodotto Figo', 'molto interessante per tutti', 1, '0.50');

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
  `password` varchar(255) NOT NULL
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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT per la tabella `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=69;

--
-- AUTO_INCREMENT per la tabella `cart_item`
--
ALTER TABLE `cart_item`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT per la tabella `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
