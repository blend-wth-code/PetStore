
DROP DATABASE if exists pet_shop;

CREATE DATABASE if not exists pet_shop;

USE pet_shop;

CREATE TABLE if not exists `products` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `description` varchar(200) NOT NULL,
  `quantity` int NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `imageName` varchar(30) default 'Default Image.jpg',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4;



Insert into products(`name`, `description`, `quantity`, `price`, `imageName`)
values('Pedigree Dog Food', '1kg dog food', 80, 47.97, 'Dog Food.jpg'),
('Whiskas Cat Food', '1.5kg cat food', 65, 34.97, 'Cat Food.jpg'),
('Pioneer Dog Leash', 'Single dog leash', 32, 7.99, 'Dog Leash.jpg'),
('Snoop Dog Collar','Single dog collar', 78, 20.95, 'Dog Collar.jpg'),
('Bella Cat Collar', 'Pack of 3 cat collars', 47, 13.49, 'Cat Collar.jpg');


DROP DATABASE IF EXISTS USER;

CREATE TABLE IF NOT exists USERS (

  ID INT PRIMARY KEY AUTO_INCREMENT,
  FIRST_NAME NVARCHAR(50) NOT NULL,
  LAST_NAME NVARCHAR(50) NOT NULL,
  EMAIL NVARCHAR(50) NOT NULL UNIQUE,
  PASSWORD NVARCHAR(50) NOT NULL
) ENGINE = InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4;


CREATE TABLE IF NOT EXISTS ORDERS(
  ID INT PRIMARY KEY AUTO_INCREMENT,
  EMAIL NVARCHAR(50) NOT NULL,
  PROD_ID INT NOT NULL,
  QUANTITY INT NOT NULL,
  STATUS INT NOT NULL DEFAULT 0
) ENGINE = InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET = utf8mb4;


-- set sql_safe_updates = 0;
-- select * from products;
-- delete from products;