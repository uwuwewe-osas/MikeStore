CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('admin', 'cashier', 'manager') NOT NULL DEFAULT 'cashier',
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS customers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(150) NOT NULL,
    document_type ENUM('DNI', 'RUC', 'CE', 'PASSPORT') DEFAULT 'DNI',
    document_number VARCHAR(25) NOT NULL,
    email VARCHAR(150) NULL,
    phone VARCHAR(30) NULL,
    address VARCHAR(255) NULL,
    loyalty_points INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_customer_document (document_type, document_number)
);

CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sku VARCHAR(40) NOT NULL UNIQUE,
    name VARCHAR(180) NOT NULL,
    brand VARCHAR(100) NOT NULL,
    category VARCHAR(100) NOT NULL,
    gender VARCHAR(30) NOT NULL,
    description TEXT NULL,
    cost DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    price DECIMAL(10,2) NOT NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS product_variants (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    size_label VARCHAR(20) NOT NULL,
    barcode VARCHAR(50) NULL,
    stock INT NOT NULL DEFAULT 0,
    min_stock INT NOT NULL DEFAULT 2,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_product_size (product_id, size_label),
    UNIQUE KEY unique_barcode (barcode),
    CONSTRAINT fk_variant_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS inventory_movements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    variant_id INT NOT NULL,
    movement_type ENUM('purchase', 'sale', 'adjustment', 'return') NOT NULL,
    quantity INT NOT NULL,
    note VARCHAR(255) NULL,
    user_id INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_inventory_variant FOREIGN KEY (variant_id) REFERENCES product_variants(id) ON DELETE CASCADE,
    CONSTRAINT fk_inventory_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS cash_sessions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    opening_amount DECIMAL(10,2) NOT NULL,
    closing_amount DECIMAL(10,2) NULL,
    expected_amount DECIMAL(10,2) NULL,
    difference_amount DECIMAL(10,2) NULL,
    status ENUM('open', 'closed') NOT NULL DEFAULT 'open',
    opened_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    closed_at TIMESTAMP NULL,
    CONSTRAINT fk_cash_session_user FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE IF NOT EXISTS cash_movements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cash_session_id INT NOT NULL,
    movement_type ENUM('opening', 'sale', 'income', 'expense', 'closing') NOT NULL,
    payment_method ENUM('cash', 'card', 'transfer', 'mixed') NOT NULL DEFAULT 'cash',
    amount DECIMAL(10,2) NOT NULL,
    note VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_cash_movement_session FOREIGN KEY (cash_session_id) REFERENCES cash_sessions(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS sales (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sale_number VARCHAR(40) NOT NULL UNIQUE,
    customer_id INT NULL,
    user_id INT NOT NULL,
    cash_session_id INT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    discount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    tax DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    total DECIMAL(10,2) NOT NULL,
    payment_method ENUM('cash', 'card', 'transfer', 'mixed') NOT NULL DEFAULT 'cash',
    sale_status ENUM('completed', 'cancelled') NOT NULL DEFAULT 'completed',
    notes VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_sale_customer FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE SET NULL,
    CONSTRAINT fk_sale_user FOREIGN KEY (user_id) REFERENCES users(id),
    CONSTRAINT fk_sale_cash_session FOREIGN KEY (cash_session_id) REFERENCES cash_sessions(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS sale_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sale_id INT NOT NULL,
    product_id INT NOT NULL,
    variant_id INT NOT NULL,
    product_name VARCHAR(180) NOT NULL,
    size_label VARCHAR(20) NOT NULL,
    quantity INT NOT NULL,
    unit_price DECIMAL(10,2) NOT NULL,
    line_total DECIMAL(10,2) NOT NULL,
    CONSTRAINT fk_sale_item_sale FOREIGN KEY (sale_id) REFERENCES sales(id) ON DELETE CASCADE,
    CONSTRAINT fk_sale_item_product FOREIGN KEY (product_id) REFERENCES products(id),
    CONSTRAINT fk_sale_item_variant FOREIGN KEY (variant_id) REFERENCES product_variants(id)
);

INSERT INTO users (name, email, password_hash, role)
VALUES
('Administrador Principal', 'admin@mikepos.local', '$2y$10$2lnwEnj6BkXaOPyku/C7JeeASQEzkLbT8/VKTSpMxvveNUlAsJc/.', 'admin'),
('Caja Principal', 'caja@mikepos.local', '$2y$10$2lnwEnj6BkXaOPyku/C7JeeASQEzkLbT8/VKTSpMxvveNUlAsJc/.', 'cashier')
ON DUPLICATE KEY UPDATE email = email;

INSERT INTO customers (full_name, document_type, document_number, email, phone, address, loyalty_points)
VALUES
('Cliente Mostrador', 'DNI', '00000000', 'mostrador@mikepos.local', '999111222', 'Venta presencial', 0),
('Mariana Flores', 'DNI', '45678912', 'mariana@example.com', '987654321', 'Miraflores, Lima', 45)
ON DUPLICATE KEY UPDATE document_number = document_number;

INSERT INTO products (sku, name, brand, category, gender, description, cost, price, is_active)
VALUES
('AD-TRAIL-001', 'Adidas Terrex Trailmaker', 'Adidas', 'Outdoor', 'hombre', 'Zapatilla outdoor para trail y uso mixto.', 180.00, 259.90, 1),
('NK-WAFFLE-002', 'Nike Waffle Debut', 'Nike', 'Urbano', 'hombre', 'Modelo urbano con look retro.', 170.00, 253.91, 1),
('PM-RUN-003', 'Puma Flyer Runner Mesh', 'Puma', 'Correr', 'hombre', 'Zapatilla ligera para running.', 90.00, 139.00, 1),
('MI-URBAN-004', 'Michelin Pilot Sport W', 'Michelin', 'Urbano', 'mujeres', 'Modelo urbano femenino.', 95.00, 139.00, 1),
('UA-HOVR-005', 'Under Armour Hovr Sonic 4', 'Under Armour', 'Correr', 'mujeres', 'Running con amortiguacion HOVR.', 140.00, 189.90, 1),
('AD-KIDS-006', 'Adidas Grand Court 2.0 EL', 'Adidas', 'Urbano', 'niños', 'Modelo infantil para uso diario.', 110.00, 169.90, 1)
ON DUPLICATE KEY UPDATE sku = sku;

INSERT INTO product_variants (product_id, size_label, barcode, stock, min_stock)
SELECT p.id, v.size_label, v.barcode, v.stock, v.min_stock
FROM products p
JOIN (
    SELECT 'AD-TRAIL-001' AS sku, '40' AS size_label, '775000000401' AS barcode, 8 AS stock, 2 AS min_stock
    UNION ALL SELECT 'AD-TRAIL-001', '41', '775000000402', 10, 2
    UNION ALL SELECT 'AD-TRAIL-001', '42', '775000000403', 6, 2
    UNION ALL SELECT 'NK-WAFFLE-002', '40', '775000000404', 7, 2
    UNION ALL SELECT 'NK-WAFFLE-002', '41', '775000000405', 9, 2
    UNION ALL SELECT 'PM-RUN-003', '42', '775000000406', 12, 3
    UNION ALL SELECT 'PM-RUN-003', '43', '775000000407', 6, 3
    UNION ALL SELECT 'MI-URBAN-004', '37', '775000000408', 8, 2
    UNION ALL SELECT 'MI-URBAN-004', '38', '775000000409', 7, 2
    UNION ALL SELECT 'UA-HOVR-005', '38', '775000000410', 5, 2
    UNION ALL SELECT 'UA-HOVR-005', '39', '775000000411', 4, 2
    UNION ALL SELECT 'AD-KIDS-006', '31', '775000000412', 9, 3
    UNION ALL SELECT 'AD-KIDS-006', '32', '775000000413', 8, 3
) v ON v.sku = p.sku
ON DUPLICATE KEY UPDATE barcode = barcode;

INSERT INTO inventory_movements (variant_id, movement_type, quantity, note, user_id)
SELECT pv.id, 'purchase', pv.stock, 'Carga inicial de inventario', 1
FROM product_variants pv
LEFT JOIN inventory_movements im
    ON im.variant_id = pv.id
    AND im.note = 'Carga inicial de inventario'
WHERE im.id IS NULL;
