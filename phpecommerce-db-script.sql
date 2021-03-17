CREATE TABLE product (
  id int(11) NOT NULL,
  name varchar(50) NOT NULL,
  description text NOT NULL,
  category_id int(11) NOT NULL,
  price decimal(10,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY(id)
) ;

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

CREATE TABLE user_type (
  id INT NOT NULL AUTO_INCREMENT,
  name VARCHAR(50) NOT NULL,
  PRIMARY KEY(id)
);

INSERT INTO user_type (
  id, name
) VALUES (
  1, 'Administrator'
),(
  2, 'Regular'
);

CREATE TABLE user
(
  id INT NOT NULL AUTO_INCREMENT,
  email VARCHAR(255) NOT NULL,
  password VARCHAR(255) NOT NULL,
  user_type_id INT NOT NULL,
  PRIMARY KEY(id),
  FOREIGN KEY (user_type_id) REFERENCES user_type(id)
);

INSERT INTO user (
  email,
  password,
  user_type_id
) VALUES (
  'admin@email.com',
  MD5('password'),
  1
),
(
  'regular@email.com',
  MD5('password'),
  2
);
