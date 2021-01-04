CREATE TABLE `product` (
  `id` int(11) NOT NULL,
  `name` varchar(50) CHARACTER SET latin1 NOT NULL,
  `description` text CHARACTER SET latin1 NOT NULL,
  `category_id` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT '0.00'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE cart
(
  id int NOT NULL AUTO_INCREMENT,
  client_id varchar(50) NOT NULL,
  PRIMARY KEY(id)
);

CREATE TABLE cart_item
(
 id int NOT NULL AUTO_INCREMENT,
 cart_id int NOT NULL,
 product_id int NOT NULL,
 quantity int NOT NULL,
 PRIMARY KEY(id),
 FOREIGN KEY (cart_id) REFERENCES cart(id),
 FOREIGN KEY (product_id) REFERENCES product(id)
);