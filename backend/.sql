-- Active: 1752165024260@@127.0.0.1@3306@gta_pos
-- =====================
-- USERS
-- =====================
CREATE TABLE users (
  id CHAR(36) PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  password_hash TEXT NOT NULL,
  role ENUM('ADMIN', 'SELLER') NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- =====================
-- CATEGORIES
-- =====================
CREATE TABLE categories (
  id CHAR(36) PRIMARY KEY,
  name VARCHAR(100) NOT NULL UNIQUE,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- =====================
-- PRODUCTS
-- =====================
CREATE TABLE products (
  id CHAR(36) PRIMARY KEY,
  name VARCHAR(150) NOT NULL,
  description TEXT,
  price_in_cents INT NOT NULL CHECK (price_in_cents >= 0),
  stock INT NOT NULL CHECK (stock >= 0),
  category_id CHAR(36) NOT NULL,
  image_url TEXT,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

  CONSTRAINT fk_category
    FOREIGN KEY (category_id)
    REFERENCES categories (id)
);

-- =====================
-- SALES
-- =====================
CREATE TABLE sales (
  id CHAR(36) PRIMARY KEY,
  seller_id CHAR(36) NOT NULL,
  total_in_cents INT NOT NULL CHECK (total_in_cents >= 0),
  status ENUM('COMPLETED', 'CANCELED') NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

  CONSTRAINT fk_seller
    FOREIGN KEY (seller_id)
    REFERENCES users (id)
);

-- =====================
-- SALE ITEMS
-- =====================
CREATE TABLE sale_items (
  id CHAR(36) PRIMARY KEY,
  sale_id CHAR(36) NOT NULL,
  product_id CHAR(36) NOT NULL,
  product_name VARCHAR(150) NOT NULL,
  price_in_cents INT NOT NULL CHECK (price_in_cents >= 0),
  quantity INT NOT NULL CHECK (quantity > 0),

  CONSTRAINT fk_sale
    FOREIGN KEY (sale_id)
    REFERENCES sales (id)
    ON DELETE CASCADE
);

-- =====================
-- SEED DATA
-- =====================

-- Insert default categories
INSERT INTO categories (id, name, created_at) VALUES
('cat-1', 'Eletrônicos', NOW()),
('cat-2', 'Computadores', NOW()),
('cat-3', 'Acessórios', NOW()),
('cat-4', 'Periféricos', NOW()),
('cat-5', 'Smartphones', NOW());

-- Insert default admin user (password: 123)
INSERT INTO users (id, name, email, password_hash, role, created_at) VALUES
('1', 'Administrador Principal', 'admin@gtatech.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'ADMIN', NOW()),
('1', 'João Vendedor', 'vendedor@gtatech.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'SELLER', NOW());
