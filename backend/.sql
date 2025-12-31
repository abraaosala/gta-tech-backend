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
('user-1', 'Administrador Principal', 'admin@gtatech.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'ADMIN', NOW()),
('user-2', 'João Vendedor', 'vendedor@gtatech.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'SELLER', NOW());

-- Insert default products
INSERT INTO products (id, name, description, price_in_cents, stock, category_id, image_url, created_at) VALUES
('101', 'Smartphone X Pro', 'Última geração, 256GB', 350000, 15, 'cat-1', 'https://picsum.photos/200/200?random=1', NOW()),
('102', 'Notebook Gamer GTA', 'i7 12th Gen, RTX 3060', 780000, 5, 'cat-2', 'https://picsum.photos/200/200?random=2', NOW()),
('103', 'Fone Bluetooth NoiseCancel', 'Isolamento acústico ativo', 45000, 30, 'cat-3', 'https://picsum.photos/200/200?random=3', NOW()),
('104', 'Monitor 4K 27"', 'IPS, 144Hz', 220000, 8, 'cat-4', 'https://picsum.photos/200/200?random=4', NOW()),
('105', 'Teclado Mecânico RGB', 'Switch Blue', 35000, 20, 'cat-4', 'https://picsum.photos/200/200?random=5', NOW());
