CREATE DATABASE IF NOT EXISTS painting_company CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE painting_company;

CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  email VARCHAR(120) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role ENUM('admin','staff') NOT NULL DEFAULT 'admin',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS settings (
  id INT AUTO_INCREMENT PRIMARY KEY,
  site_name VARCHAR(150) NOT NULL DEFAULT 'PaintPro',
  site_logo VARCHAR(255) DEFAULT NULL,
  favicon VARCHAR(255) DEFAULT NULL,
  site_description TEXT,
  contact_email VARCHAR(120) DEFAULT NULL,
  phone VARCHAR(40) DEFAULT NULL,
  address VARCHAR(255) DEFAULT NULL,
  payment_instructions TEXT,
  whatsapp_number VARCHAR(30) DEFAULT NULL,
  whatsapp_message VARCHAR(255) DEFAULT 'Hi, I need help with wallpaper and painting services.',
  email_sender_name VARCHAR(120) DEFAULT 'PaintPro',
  email_sender_email VARCHAR(120) DEFAULT 'noreply@paintpro.local',
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS categories (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  type ENUM('product','gallery','service') NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY unique_name_type (name, type)
);

CREATE TABLE IF NOT EXISTS services (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(150) NOT NULL,
  description TEXT NOT NULL,
  image VARCHAR(255) DEFAULT NULL,
  category_id INT DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_service_category FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS gallery (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(150) NOT NULL,
  category_id INT DEFAULT NULL,
  before_image VARCHAR(255) DEFAULT NULL,
  after_image VARCHAR(255) DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_gallery_category FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS products (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150) NOT NULL,
  slug VARCHAR(180) NOT NULL UNIQUE,
  price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  main_image VARCHAR(255) DEFAULT NULL,
  description LONGTEXT NOT NULL,
  category_id INT DEFAULT NULL,
  is_virtual TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_product_category FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS product_images (
  id INT AUTO_INCREMENT PRIMARY KEY,
  product_id INT NOT NULL,
  image VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_product_images_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS customers (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  phone VARCHAR(30) NOT NULL,
  email VARCHAR(120) DEFAULT NULL,
  address VARCHAR(255) DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS leads (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  phone VARCHAR(30) NOT NULL,
  email VARCHAR(120) NOT NULL,
  product_id INT DEFAULT NULL,
  service_type VARCHAR(120) DEFAULT NULL,
  message TEXT NOT NULL,
  room_size VARCHAR(120) DEFAULT NULL,
  image VARCHAR(255) DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_leads_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS testimonials (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  review TEXT NOT NULL,
  rating TINYINT NOT NULL,
  image VARCHAR(255) DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS orders (
  id INT AUTO_INCREMENT PRIMARY KEY,
  customer_id INT DEFAULT NULL,
  name VARCHAR(120) NOT NULL,
  phone VARCHAR(30) NOT NULL,
  email VARCHAR(120) NOT NULL,
  address VARCHAR(255) NOT NULL,
  total_amount DECIMAL(10,2) NOT NULL,
  status ENUM('Pending','Processing','Completed','Cancelled') DEFAULT 'Pending',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_orders_customer FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS order_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  order_id INT NOT NULL,
  product_id INT NOT NULL,
  quantity INT NOT NULL DEFAULT 1,
  price DECIMAL(10,2) NOT NULL,
  CONSTRAINT fk_order_items_order FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
  CONSTRAINT fk_order_items_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS email_templates (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  subject VARCHAR(255) NOT NULL,
  body MEDIUMTEXT NOT NULL,
  trigger_type ENUM('order_created','order_status_updated','new_lead') NOT NULL,
  status ENUM('enabled','disabled') DEFAULT 'enabled',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS notifications (
  id INT AUTO_INCREMENT PRIMARY KEY,
  type ENUM('lead','order') NOT NULL,
  title VARCHAR(160) NOT NULL,
  message VARCHAR(255) NOT NULL,
  link VARCHAR(255) DEFAULT NULL,
  is_read TINYINT(1) DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS blog_categories (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  slug VARCHAR(140) NOT NULL UNIQUE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS blogs (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(180) NOT NULL,
  slug VARCHAR(200) NOT NULL UNIQUE,
  content LONGTEXT NOT NULL,
  featured_image VARCHAR(255) DEFAULT NULL,
  category_id INT DEFAULT NULL,
  meta_title VARCHAR(180) DEFAULT NULL,
  meta_description VARCHAR(255) DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_blog_category FOREIGN KEY (category_id) REFERENCES blog_categories(id) ON DELETE SET NULL
);

INSERT INTO settings (site_name, site_description, contact_email, phone, address, payment_instructions, whatsapp_number, whatsapp_message, email_sender_name, email_sender_email)
SELECT 'PaintPro', 'Professional painting and wallpaper solutions.', 'hello@paintpro.com', '+1 (555) 321-9988', '245 Design Street, New York, USA', 'Cash on Delivery (COD). Our team will contact you to confirm order details.', '15553219988', 'Hi, I want to request painting/wallpaper service.', 'PaintPro', 'noreply@paintpro.local'
WHERE NOT EXISTS (SELECT 1 FROM settings);

INSERT INTO users (name, email, password, role)
SELECT 'Administrator', 'admin@paintpro.com', '$2y$10$R5j0f6Jf9n9jsiTnQM0a7OdGf1t5O6LQkT2eA7UIv4BfUo7xM3v7y', 'admin'
WHERE NOT EXISTS (SELECT 1 FROM users WHERE email='admin@paintpro.com');
