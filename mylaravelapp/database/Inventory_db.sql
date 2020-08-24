-- --------------------------------------------------------------------------------------------------------------------------
-- DATABASE 1 --------------------------------------------------------------------------------------------------------------
-- --------------------------------------------------------------------------------------------------------------------------

DROP DATABASE IF EXISTS `test_history`;
CREATE DATABASE `test_history`; 
USE `test_history`;

SET NAMES utf8 ;
SET character_set_client = utf8mb4 ;

-- Contacts ----------------------------------------------------------------------------------------------------------------
-- List of the natural persons that purchase from the SMEs
CREATE TABLE `Natural_Person` (
  social_id VARCHAR(10) NOT NULL,
  first_name VARCHAR(30) NOT NULL,
  last_name VARCHAR(30) NOT NULL,
  street VARCHAR(50) NOT NULL,
  city VARCHAR(40) NOT NULL,
  state VARCHAR(30) NOT NULL,
  phone VARCHAR(20) NOT NULL,
  birth_date DATE NOT NULL,
  sex ENUM('Hombre', 'Mujer') NOT NULL,
  date_entered DATE,
  PRIMARY KEY (`social_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- List of the organizations that purchase from and supply to the SMEs
CREATE TABLE `Legal_Person` (
  taxpayer_id VARCHAR(13) NOT NULL,
  company_name VARCHAR(30) NOT NULL,
  street VARCHAR(50) NOT NULL,
  city VARCHAR(40) NOT NULL,
  state VARCHAR(30) NOT NULL,
  email VARCHAR(20) NOT NULL,
  date_entered DATE,
  PRIMARY KEY (`taxpayer_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


CREATE TABLE `Customers` (
  customer_id MEDIUMINT(13) NOT NULL AUTO_INCREMENT,
  social_id VARCHAR(10),
  taxpayer_id VARCHAR(13),
  PRIMARY KEY (`customer_id`),
  FOREIGN KEY (social_id) REFERENCES Natural_person(social_id) ON DELETE CASCADE,
  FOREIGN KEY (taxpayer_id) REFERENCES Legal_person(taxpayer_id) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Add Customer after insert in Natural Customer
DELIMITER $$
CREATE TRIGGER create_natural_customer
AFTER INSERT
ON Natural_Person FOR EACH ROW
BEGIN
    INSERT INTO Customers (social_id, taxpayer_id) VALUES
    (new.social_id, NULL);
END$$
DELIMITER ;

-- Add Customer after insert in Legal Customer
DELIMITER $$
CREATE TRIGGER create_legal_customer
AFTER INSERT
ON Legal_Person FOR EACH ROW
BEGIN
    INSERT INTO Customers (social_id, taxpayer_id) VALUES
    (NULL, new.taxpayer_id);
END$$
DELIMITER ;

-- Insert customer samples
INSERT INTO Legal_Person (taxpayer_id, company_name, street, city, state, email) 
VALUES	('0930345568001', 'Alv', 'Wall Street', 'Queretarock', 'Qro', 'alv@hotmail.com');


-- List of employees of the SMEs
CREATE TABLE `Employees` (
  employee_id MEDIUMINT(16) NOT NULL AUTO_INCREMENT,
  first_name VARCHAR(30) NOT NULL,
  last_name VARCHAR(30) NOT NULL,
  job_title VARCHAR(30) NOT NULL,
  salary DECIMAL(10,2) NOT NULL,
  reports_to SMALLINT unsigned,
  PRIMARY KEY (`employee_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
INSERT INTO Employees (first_name, last_name, job_title, salary) 
VALUES	('Jorge', 'Alan', 'Student', 700.00);

-- Invoices --------------------------------------------------------------------------------------------------------------
-- Invoices for the SMEs sales. 
CREATE TABLE `Sales_Invoice` (
  invoice_id MEDIUMINT(11) NOT NULL auto_increment,
  invoice_number VARCHAR(30) NOT NULL,
  customer_id MEDIUMINT(13) NOT NULL,
  employee_id MEDIUMINT(16) NOT NULL,
  payment_status TINYINT(3) NOT NULL,
  subtotal DECIMAL(10,2) NOT NULL,
  taxes DECIMAL(10,2) NOT NULL,
  total_amount DECIMAL(10,2) NOT NULL,
  balance DECIMAL(10,2) NOT NULL,
  date_emitted DATE,
  PRIMARY KEY (`invoice_id`),
  FOREIGN KEY (customer_id) REFERENCES Customers(customer_id) ON DELETE CASCADE,
  FOREIGN KEY (employee_id) REFERENCES Employees(employee_id) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
-- Insert a sample
INSERT INTO `Sales_Invoice` (invoice_number, customer_id, employee_id, subtotal, taxes, total_amount, balance, payment_status, date_emitted)
VALUES ('001-001-025423', 1, 1, 27.31, subtotal*0.12, subtotal+taxes, 30.59, balance >= total_amount, NOW());

-- Invoices for the SMEs purchases.
CREATE TABLE `Purchase_Invoice` (
  invoice_id MEDIUMINT(11) NOT NULL auto_increment,
  invoice_number VARCHAR(30) NOT NULL,
  supplier_id VARCHAR(13) NOT NULL,
  employee_id MEDIUMINT(16) NOT NULL,
  payment_status TINYINT(3) NOT NULL,
  amount DECIMAL(10,2) NOT NULL,
  balance DECIMAL(10,2) NOT NULL,
  date_emitted DATE,
  PRIMARY KEY (`invoice_id`),
  FOREIGN KEY (supplier_id) REFERENCES Legal_Person(taxpayer_id) ON DELETE CASCADE,
  FOREIGN KEY (employee_id) REFERENCES Employees(employee_id) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
-- Insert a sample
INSERT INTO `Purchase_Invoice` (invoice_number, supplier_id, employee_id, amount, balance, payment_status, date_emitted)
VALUES ('001-002-025696', '0930345568001', 1, 180.6, 50.42, balance >= amount, NOW());

-- Payment status names conversion
CREATE TABLE `Invoice_Status` (
  id TINYINT(3),
  payment_status VARCHAR(10));
INSERT INTO `invoice_status` VALUES (0, 'PENDIENTE'),(1, 'PAGADO'),(2, 'ANULADO');

-- --------------------------------------------------------------------------------------------------------------------------
-- DATABASE 2 ---------------------------------------------------------------------------------------------------------------
-- --------------------------------------------------------------------------------------------------------------------------

DROP DATABASE IF EXISTS `test_inventory`;
CREATE DATABASE `test_inventory`; 
USE `test_inventory`;

SET NAMES utf8 ;
SET character_set_client = utf8mb4 ;

-- Inventory Control -------------------------------------------------------------------------------------------------------
-- Registers product labels and discounts
CREATE TABLE `Labels` (
  label_id MEDIUMINT(11) NOT NULL auto_increment,
  label_name VARCHAR(20) NOT NULL,
  label_discount SMALLINT UNSIGNED,
  PRIMARY KEY (`label_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Samples
INSERT INTO `Labels` (label_name, label_discount) VALUES ('Iluminacion', 30);
INSERT INTO `Labels` (label_name, label_discount) VALUES ('Tuberias', NULL);
-- Turn NULL to zero
SET SQL_SAFE_UPDATES = 0;
UPDATE `Labels` SET `label_discount`=0 WHERE `label_discount` is NULL;
SET SQL_SAFE_UPDATES = 1;

-- Shows the inventory
CREATE TABLE `Inventory` (
  product_code VARCHAR(16) NOT NULL,
  description VARCHAR(30) NOT NULL,
  units VARCHAR(10) NOT NULL,
  quantity SMALLINT UNSIGNED NOT NULL,
  price_sales DECIMAL(10,2) NOT NULL,
  price_wholesales DECIMAL(10,2) NOT NULL,
  product_discount DECIMAL(10,2) NOT NULL,
  PRIMARY KEY (`product_code`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
-- Samples
INSERT INTO `Inventory` VALUES ('GJ4230', 'Seguro medico', 'licencia', 10, '100.00',  '95.99', '300');

-- Registers product label and inventory combinations
CREATE TABLE `Inventory_Label` (
  inventory_label_id MEDIUMINT(11) NOT NULL auto_increment,
  product_code VARCHAR(16) NOT NULL,
  label_id MEDIUMINT(11) NOT NULL,
  PRIMARY KEY (`inventory_label_id`),
  FOREIGN KEY (product_code) REFERENCES Inventory(product_code) ON DELETE CASCADE,
  FOREIGN KEY (label_id) REFERENCES Labels(label_id) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Registers the purchases by product type
CREATE TABLE `Purchase_Product` (
  purchase_product_id MEDIUMINT(11) NOT NULL auto_increment,
  purchase_id MEDIUMINT(11) NOT NULL,
  description VARCHAR(30) NOT NULL,
  product_code VARCHAR(16) NOT NULL,
  units VARCHAR(10) NOT NULL,
  quant_bought SMALLINT UNSIGNED NOT NULL,
  price_bought DECIMAL(10,2) NOT NULL,
  PRIMARY KEY (`purchase_product_id`),
  -- FOREIGN KEY (purchase_id) REFERENCES Purchase_Invoice(invoice_id) ON DELETE CASCADE,
  FOREIGN KEY (product_code) REFERENCES Inventory(product_code) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Registers the sales by product type
CREATE TABLE `Sales_Product` (
  sales_product_id MEDIUMINT(11) NOT NULL auto_increment,
  sales_id MEDIUMINT(11) NOT NULL,
  product_code VARCHAR(16) NOT NULL,
  units VARCHAR(10) NOT NULL,
  quant_sold SMALLINT UNSIGNED NOT NULL,
  price_sales DECIMAL(10,2) NOT NULL,
  discount DECIMAL(10,2) NOT NULL,
  subtotal_product DECIMAL(10,2) NOT NULL,
  PRIMARY KEY (`sales_product_id`),
--  FOREIGN KEY (sales_id) REFERENCES Sales_Invoice(invoice_id) ON DELETE CASCADE,
  FOREIGN KEY (product_code) REFERENCES Inventory(product_code) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Registers the payments for a sale
CREATE TABLE `Sales_Payment` (
  payment_id MEDIUMINT(16) NOT NULL auto_increment,
  sales_id MEDIUMINT(16) NOT NULL,
  payment_method MEDIUMINT(16) NOT NULL,
  date_time DATE NOT NULL,
  partial_amount DECIMAL(10,2),
  PRIMARY KEY (`payment_id`)
--  FOREIGN KEY (sales_id) REFERENCES Sales_Invoice(invoice_id) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Registers the payments for a purchase
CREATE TABLE `Purchase_Payment` (
  payment_id MEDIUMINT(16) NOT NULL auto_increment,
  purchase_id MEDIUMINT(16) NOT NULL,
  payment_method MEDIUMINT(16) NOT NULL,
  date_time DATE NOT NULL,
  partial_amount DECIMAL(10,2),
  PRIMARY KEY (`payment_id`)
--  FOREIGN KEY (purchase_id) REFERENCES Purchase_Invoice(invoice_id) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Event triggers -----------------------------------------------------------------------------------------------------------------------
-- Adds the purchased products to the inventory with its description
DELIMITER $$
CREATE TRIGGER update_inventory_purchases
AFTER INSERT ON Purchase_product
FOR EACH ROW
BEGIN		
    INSERT INTO `Inventory` (product_code, description, units, quantity, price_bought, price_sales)
		VALUES (NEW.product_code,NEW.description,NEW.units,NEW.quant_bought,NEW.price_bought,price_sales)
    ON DUPLICATE KEY UPDATE    
      quantity = quantity + NEW.quant_bought,
      price_bought = NEW.price_bought;
END$$

-- Substracts the products in the inventory according to the quantity sold
DELIMITER $$
CREATE TRIGGER update_inventory_sales
AFTER INSERT ON Sales_product FOR EACH ROW
BEGIN
    UPDATE Inventory SET
        quantity = quantity - NEW.quant_sold,
        price_sales = NEW.price_sales
    WHERE product_code = NEW.product_code;
END$$
DELIMITER ;