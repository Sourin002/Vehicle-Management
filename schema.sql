CREATE DATABASE IF NOT EXISTS vehicle_service CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE vehicle_service;

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  phone VARCHAR(30) DEFAULT NULL,
  password VARCHAR(255) NOT NULL,
  role ENUM('customer','admin') NOT NULL DEFAULT 'customer',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE vehicles (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  make VARCHAR(60) NOT NULL,
  model VARCHAR(60) NOT NULL,
  year SMALLINT NOT NULL,
  registration_no VARCHAR(30) NOT NULL UNIQUE,
  color VARCHAR(30) DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE service_requests (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  vehicle_id INT NOT NULL,
  service_type VARCHAR(100) NOT NULL,
  preferred_date DATE NOT NULL,
  notes TEXT DEFAULT NULL,
  status ENUM('Pending','Approved','In Progress','Completed','Cancelled') NOT NULL DEFAULT 'Pending',
  admin_notes TEXT DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (vehicle_id) REFERENCES vehicles(id) ON DELETE CASCADE
);

-- Demo password for both accounts: password
INSERT INTO users (name, email, phone, password, role) VALUES
('System Administrator','admin@autocare.test','555-0100','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC1kZVvC8A2ZxM6wLr2','admin'),
('Demo Customer','customer@autocare.test','555-0110','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC1kZVvC8A2ZxM6wLr2','customer');

INSERT INTO vehicles (user_id, make, model, year, registration_no, color) VALUES
(2,'Toyota','Corolla',2021,'DEMO-2048','Pearl White');

INSERT INTO service_requests (user_id, vehicle_id, service_type, preferred_date, notes, status) VALUES
(2,1,'Full Service','2026-10-02','Please inspect brakes and top up fluids.','Approved');

-- Demo credentials: admin@autocare.test / password, customer@autocare.test / password
-- The application always uses password_hash() for newly registered or updated passwords.
