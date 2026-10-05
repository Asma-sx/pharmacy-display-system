-- Pharmacy Display System: full database setup
-- Import this file into an EMPTY database (phpMyAdmin > Import, or the SQL tab).
-- On localhost you can first run:  CREATE DATABASE pharmacy_db; USE pharmacy_db;

CREATE TABLE IF NOT EXISTS users (
  user_id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) UNIQUE NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('admin','pharmacist','viewer') DEFAULT 'viewer',
  full_name VARCHAR(100),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS medicines (
  med_id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150) NOT NULL,
  brand VARCHAR(100),
  category VARCHAR(100),
  description TEXT,
  active_ingredients TEXT,
  warnings TEXT,
  price DECIMAL(10,2),
  quantity INT DEFAULT 0,
  expiry_date DATE,
  image_url VARCHAR(255),
  added_by INT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (added_by) REFERENCES users(user_id) ON DELETE SET NULL
);

-- ---------------------------------------------------------------
-- Stored procedures: the Data Access Layer calls these for every CRUD operation
-- ---------------------------------------------------------------
DROP PROCEDURE IF EXISTS sp_get_medicines;
DROP PROCEDURE IF EXISTS sp_get_medicine_by_id;
DROP PROCEDURE IF EXISTS sp_add_medicine;
DROP PROCEDURE IF EXISTS sp_update_medicine;
DROP PROCEDURE IF EXISTS sp_delete_medicine;

DELIMITER $$

CREATE PROCEDURE sp_get_medicines(IN p_q VARCHAR(150), IN p_category VARCHAR(100))
BEGIN
  SELECT * FROM medicines
  WHERE (p_q IS NULL OR p_q = ''
         OR name  LIKE CONCAT('%', p_q, '%')
         OR brand LIKE CONCAT('%', p_q, '%'))
    AND (p_category IS NULL OR p_category = '' OR category = p_category)
  ORDER BY name;
END$$

CREATE PROCEDURE sp_get_medicine_by_id(IN p_id INT)
BEGIN
  SELECT * FROM medicines WHERE med_id = p_id;
END$$

CREATE PROCEDURE sp_add_medicine(
  IN p_name VARCHAR(150), IN p_brand VARCHAR(100), IN p_category VARCHAR(100),
  IN p_description TEXT, IN p_active_ingredients TEXT, IN p_warnings TEXT,
  IN p_price DECIMAL(10,2), IN p_quantity INT, IN p_expiry_date DATE,
  IN p_image_url VARCHAR(255), IN p_added_by INT)
BEGIN
  INSERT INTO medicines (name, brand, category, description, active_ingredients, warnings,
                         price, quantity, expiry_date, image_url, added_by)
  VALUES (p_name, p_brand, p_category, p_description, p_active_ingredients, p_warnings,
          p_price, p_quantity, p_expiry_date, p_image_url, p_added_by);
  SELECT LAST_INSERT_ID() AS new_id;
END$$

CREATE PROCEDURE sp_update_medicine(
  IN p_id INT, IN p_name VARCHAR(150), IN p_brand VARCHAR(100), IN p_category VARCHAR(100),
  IN p_description TEXT, IN p_active_ingredients TEXT, IN p_warnings TEXT,
  IN p_price DECIMAL(10,2), IN p_quantity INT, IN p_expiry_date DATE,
  IN p_image_url VARCHAR(255))
BEGIN
  UPDATE medicines
  SET name = p_name, brand = p_brand, category = p_category, description = p_description,
      active_ingredients = p_active_ingredients, warnings = p_warnings,
      price = p_price, quantity = p_quantity, expiry_date = p_expiry_date, image_url = p_image_url
  WHERE med_id = p_id;
END$$

CREATE PROCEDURE sp_delete_medicine(IN p_id INT)
BEGIN
  DELETE FROM medicines WHERE med_id = p_id;
END$$

DELIMITER ;

-- ---------------------------------------------------------------
-- Demo data
-- ---------------------------------------------------------------
INSERT INTO users (username, password_hash, role, full_name) VALUES
  ('admin', '$2y$10$PddyOcKeZqF6hM4APuPaduABt3K4CKSnRpSViNuM0OVS.XchIdY1S', 'admin', 'System Admin'),
  ('demo',  '$2y$10$wVNEyOhBEma07FKJT40IveCgavKlXMzX/uiSh7CrTAPG.Jge.4Ewy', 'pharmacist', 'Demo Pharmacist');

INSERT INTO medicines (name, brand, category, description, active_ingredients, warnings, price, quantity, expiry_date, added_by) VALUES
  ('Paracetamol 500mg', 'Panadol', 'Pain Relief', 'Relieves mild to moderate pain and reduces fever.', 'Paracetamol 500mg', 'Do not exceed 8 tablets in 24 hours.', 12.50, 120, '2028-06-30', 1),
  ('Ibuprofen 400mg', 'Brufen', 'Pain Relief', 'Anti-inflammatory for pain, swelling and fever.', 'Ibuprofen 400mg', 'Take with food. Not for patients with stomach ulcers.', 18.75, 80, '2028-03-31', 1),
  ('Amoxicillin/Clavulanate 625mg', 'Augmentin', 'Antibiotics', 'Broad-spectrum antibiotic for bacterial infections.', 'Amoxicillin 500mg, Clavulanic acid 125mg', 'Prescription only. Complete the full course.', 45.00, 40, '2027-12-31', 1),
  ('Salbutamol Inhaler 100mcg', 'Ventolin', 'Respiratory', 'Fast relief for asthma and breathing difficulty.', 'Salbutamol 100mcg/dose', 'Seek help if more than 4 doses a day are needed.', 23.00, 35, '2028-01-31', 1),
  ('Esomeprazole 40mg', 'Nexium', 'Digestive', 'Reduces stomach acid for reflux and ulcers.', 'Esomeprazole 40mg', 'Long-term use should be reviewed by a doctor.', 62.40, 50, '2027-11-30', 1),
  ('Cetirizine 10mg', 'Zyrtec', 'Allergy', 'Relieves allergy symptoms such as sneezing and itching.', 'Cetirizine 10mg', 'May cause drowsiness.', 15.25, 90, '2028-09-30', 1),
  ('Metformin 500mg', 'Glucophage', 'Diabetes', 'Controls blood sugar in type 2 diabetes.', 'Metformin hydrochloride 500mg', 'Prescription only. Take with meals.', 20.00, 60, '2028-04-30', 1),
  ('Atorvastatin 20mg', 'Lipitor', 'Cardiovascular', 'Lowers cholesterol and cardiovascular risk.', 'Atorvastatin 20mg', 'Prescription only. Avoid grapefruit juice.', 85.90, 30, '2027-10-31', 1);
