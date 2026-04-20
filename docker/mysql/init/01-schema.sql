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
    slug VARCHAR(160) NULL UNIQUE,
    name VARCHAR(180) NOT NULL,
    brand VARCHAR(100) NOT NULL,
    category VARCHAR(100) NOT NULL,
    gender VARCHAR(30) NOT NULL,
    description TEXT NULL,
    image_url VARCHAR(255) NULL,
    accent_color VARCHAR(20) NULL DEFAULT '#0f766e',
    featured TINYINT(1) NOT NULL DEFAULT 0,
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
    sales_channel ENUM('pos', 'online') NOT NULL DEFAULT 'pos',
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

SET @add_products_slug = (
    SELECT IF(
        COUNT(*) = 0,
        'ALTER TABLE products ADD COLUMN slug VARCHAR(160) NULL UNIQUE AFTER sku',
        'SELECT 1'
    )
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'products' AND COLUMN_NAME = 'slug'
);
PREPARE stmt FROM @add_products_slug;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @add_products_image = (
    SELECT IF(
        COUNT(*) = 0,
        'ALTER TABLE products ADD COLUMN image_url VARCHAR(255) NULL AFTER description',
        'SELECT 1'
    )
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'products' AND COLUMN_NAME = 'image_url'
);
PREPARE stmt FROM @add_products_image;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @add_products_accent = (
    SELECT IF(
        COUNT(*) = 0,
        'ALTER TABLE products ADD COLUMN accent_color VARCHAR(20) NULL DEFAULT ''#0f766e'' AFTER image_url',
        'SELECT 1'
    )
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'products' AND COLUMN_NAME = 'accent_color'
);
PREPARE stmt FROM @add_products_accent;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @add_products_featured = (
    SELECT IF(
        COUNT(*) = 0,
        'ALTER TABLE products ADD COLUMN featured TINYINT(1) NOT NULL DEFAULT 0 AFTER accent_color',
        'SELECT 1'
    )
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'products' AND COLUMN_NAME = 'featured'
);
PREPARE stmt FROM @add_products_featured;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @add_sales_channel = (
    SELECT IF(
        COUNT(*) = 0,
        'ALTER TABLE sales ADD COLUMN sales_channel ENUM(''pos'', ''online'') NOT NULL DEFAULT ''pos'' AFTER payment_method',
        'SELECT 1'
    )
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'sales' AND COLUMN_NAME = 'sales_channel'
);
PREPARE stmt FROM @add_sales_channel;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

INSERT INTO users (name, email, password_hash, role)
VALUES
('Administrador Principal', 'admin@mikepos.local', '$2y$10$2lnwEnj6BkXaOPyku/C7JeeASQEzkLbT8/VKTSpMxvveNUlAsJc/.', 'admin'),
('Caja Principal', 'caja@mikepos.local', '$2y$10$2lnwEnj6BkXaOPyku/C7JeeASQEzkLbT8/VKTSpMxvveNUlAsJc/.', 'cashier'),
('Tienda Online', 'store@mikepos.local', '$2y$10$2lnwEnj6BkXaOPyku/C7JeeASQEzkLbT8/VKTSpMxvveNUlAsJc/.', 'manager')
ON DUPLICATE KEY UPDATE
    name = VALUES(name),
    password_hash = VALUES(password_hash),
    role = VALUES(role);

INSERT INTO customers (full_name, document_type, document_number, email, phone, address, loyalty_points)
VALUES
('Cliente Mostrador', 'DNI', '00000000', 'mostrador@mikepos.local', '999111222', 'Venta presencial', 0),
('Mariana Flores', 'DNI', '45678912', 'mariana@example.com', '987654321', 'Miraflores, Lima', 45),
('Ariana Torres', 'DNI', '70124589', 'ariana@example.com', '955111222', 'San Miguel, Lima', 15),
('Luis Mendoza', 'DNI', '70246813', 'luis@example.com', '955222333', 'Los Olivos, Lima', 20)
ON DUPLICATE KEY UPDATE
    full_name = VALUES(full_name),
    email = VALUES(email),
    phone = VALUES(phone),
    address = VALUES(address),
    loyalty_points = VALUES(loyalty_points);

INSERT INTO products (sku, slug, name, brand, category, gender, description, image_url, accent_color, featured, cost, price, is_active)
VALUES
('AD-TRAIL-001', 'adidas-terrex-trailmaker', 'Adidas Terrex Trailmaker', 'Adidas', 'Outdoor', 'hombre', 'Zapatilla outdoor pensada para trail y uso mixto en ciudad.', 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?auto=format&fit=crop&w=900&q=80', '#0f766e', 1, 180.00, 259.90, 1),
('NK-WAFFLE-002', 'nike-waffle-debut', 'Nike Waffle Debut', 'Nike', 'Urbano', 'hombre', 'Modelo urbano con look retro, ideal para outfits diarios.', 'https://images.unsplash.com/photo-1543508282-6319a3e2621f?auto=format&fit=crop&w=900&q=80', '#111827', 1, 170.00, 253.91, 1),
('PM-RUN-003', 'puma-flyer-runner-mesh', 'Puma Flyer Runner Mesh', 'Puma', 'Running', 'hombre', 'Zapatilla ligera para running y entrenamiento funcional.', 'https://images.unsplash.com/photo-1600185365483-26d7a4cc7519?auto=format&fit=crop&w=900&q=80', '#ea580c', 0, 90.00, 139.00, 1),
('MI-URBAN-004', 'michelin-pilot-sport-w', 'Michelin Pilot Sport W', 'Michelin', 'Urbano', 'mujeres', 'Modelo urbano femenino con silueta estilizada y comoda.', 'https://images.unsplash.com/photo-1460353581641-37baddab0fa2?auto=format&fit=crop&w=900&q=80', '#be123c', 1, 95.00, 139.00, 1),
('UA-HOVR-005', 'under-armour-hovr-sonic-4', 'Under Armour Hovr Sonic 4', 'Under Armour', 'Running', 'mujeres', 'Running con amortiguacion HOVR y rebote constante.', 'https://images.unsplash.com/photo-1595950653106-6c9ebd614d3a?auto=format&fit=crop&w=900&q=80', '#7c3aed', 0, 140.00, 189.90, 1),
('AD-KIDS-006', 'adidas-grand-court-kids', 'Adidas Grand Court 2.0 EL', 'Adidas', 'Urbano', 'ninos', 'Modelo infantil para uso diario, resistente y facil de poner.', 'https://images.unsplash.com/photo-1514989940723-e8e51635b782?auto=format&fit=crop&w=900&q=80', '#2563eb', 0, 110.00, 169.90, 1),
('NB-RUN-007', 'new-balance-fuelcell-rebel', 'New Balance FuelCell Rebel', 'New Balance', 'Running', 'hombre', 'Silueta agresiva para corredores que buscan velocidad y frescura.', 'https://images.unsplash.com/photo-1556906781-9a412961c28c?auto=format&fit=crop&w=900&q=80', '#dc2626', 1, 210.00, 299.90, 1),
('AD-URBAN-008', 'adidas-campus-luxe', 'Adidas Campus Luxe', 'Adidas', 'Urbano', 'mujeres', 'Streetwear premium con suede suave y postura clasica.', 'https://images.unsplash.com/photo-1525966222134-fcfa99b8ae77?auto=format&fit=crop&w=900&q=80', '#1d4ed8', 1, 160.00, 249.90, 1),
('AS-TRAIL-009', 'asics-gel-sonoma-7', 'Asics Gel Sonoma 7', 'Asics', 'Outdoor', 'mujeres', 'Pensada para senderos ligeros con buen agarre y soporte.', 'https://images.unsplash.com/photo-1605408499391-6368c628ef42?auto=format&fit=crop&w=900&q=80', '#15803d', 0, 150.00, 229.90, 1),
('NK-COURT-010', 'nike-court-borough-low-jr', 'Nike Court Borough Low Jr', 'Nike', 'Urbano', 'ninos', 'Sneaker junior inspirada en basket para uso diario.', 'https://images.unsplash.com/photo-1560769629-975ec94e6a86?auto=format&fit=crop&w=900&q=80', '#0f172a', 0, 108.00, 179.90, 1),
('PM-TRAIN-011', 'puma-retaliate-train', 'Puma Retaliate Train', 'Puma', 'Training', 'mujeres', 'Entrenamiento indoor con estabilidad lateral y upper flexible.', 'https://images.unsplash.com/photo-1491553895911-0055eca6402d?auto=format&fit=crop&w=900&q=80', '#f97316', 0, 118.00, 169.90, 1),
('MI-OUT-012', 'michelin-desert-race-pro', 'Michelin Desert Race Pro', 'Michelin', 'Outdoor', 'hombre', 'Bota ligera outdoor con inspiracion rally y suela de alto agarre.', 'https://images.unsplash.com/photo-1525966222134-fcfa99b8ae77?auto=format&fit=crop&w=900&q=80', '#78350f', 1, 235.00, 329.90, 1)
ON DUPLICATE KEY UPDATE
    slug = VALUES(slug),
    name = VALUES(name),
    brand = VALUES(brand),
    category = VALUES(category),
    gender = VALUES(gender),
    description = VALUES(description),
    image_url = VALUES(image_url),
    accent_color = VALUES(accent_color),
    featured = VALUES(featured),
    cost = VALUES(cost),
    price = VALUES(price),
    is_active = VALUES(is_active);

INSERT INTO product_variants (product_id, size_label, barcode, stock, min_stock)
SELECT p.id, v.size_label, v.barcode, v.stock, v.min_stock
FROM products p
JOIN (
    SELECT 'AD-TRAIL-001' AS sku, '40' AS size_label, '775000000401' AS barcode, 12 AS stock, 3 AS min_stock
    UNION ALL SELECT 'AD-TRAIL-001', '41', '775000000402', 14, 3
    UNION ALL SELECT 'AD-TRAIL-001', '42', '775000000403', 10, 3
    UNION ALL SELECT 'NK-WAFFLE-002', '40', '775000000404', 11, 3
    UNION ALL SELECT 'NK-WAFFLE-002', '41', '775000000405', 12, 3
    UNION ALL SELECT 'NK-WAFFLE-002', '42', '775000000406', 9, 3
    UNION ALL SELECT 'PM-RUN-003', '42', '775000000407', 18, 4
    UNION ALL SELECT 'PM-RUN-003', '43', '775000000408', 14, 4
    UNION ALL SELECT 'PM-RUN-003', '44', '775000000409', 10, 4
    UNION ALL SELECT 'MI-URBAN-004', '37', '775000000410', 10, 2
    UNION ALL SELECT 'MI-URBAN-004', '38', '775000000411', 11, 2
    UNION ALL SELECT 'MI-URBAN-004', '39', '775000000412', 9, 2
    UNION ALL SELECT 'UA-HOVR-005', '38', '775000000413', 8, 2
    UNION ALL SELECT 'UA-HOVR-005', '39', '775000000414', 7, 2
    UNION ALL SELECT 'UA-HOVR-005', '40', '775000000415', 6, 2
    UNION ALL SELECT 'AD-KIDS-006', '31', '775000000416', 14, 3
    UNION ALL SELECT 'AD-KIDS-006', '32', '775000000417', 12, 3
    UNION ALL SELECT 'AD-KIDS-006', '33', '775000000418', 10, 3
    UNION ALL SELECT 'NB-RUN-007', '41', '775000000419', 7, 2
    UNION ALL SELECT 'NB-RUN-007', '42', '775000000420', 8, 2
    UNION ALL SELECT 'NB-RUN-007', '43', '775000000421', 6, 2
    UNION ALL SELECT 'AD-URBAN-008', '36', '775000000422', 7, 2
    UNION ALL SELECT 'AD-URBAN-008', '37', '775000000423', 9, 2
    UNION ALL SELECT 'AD-URBAN-008', '38', '775000000424', 8, 2
    UNION ALL SELECT 'AS-TRAIL-009', '37', '775000000425', 6, 2
    UNION ALL SELECT 'AS-TRAIL-009', '38', '775000000426', 8, 2
    UNION ALL SELECT 'AS-TRAIL-009', '39', '775000000427', 7, 2
    UNION ALL SELECT 'NK-COURT-010', '32', '775000000428', 11, 3
    UNION ALL SELECT 'NK-COURT-010', '33', '775000000429', 10, 3
    UNION ALL SELECT 'NK-COURT-010', '34', '775000000430', 8, 3
    UNION ALL SELECT 'PM-TRAIN-011', '37', '775000000431', 8, 2
    UNION ALL SELECT 'PM-TRAIN-011', '38', '775000000432', 7, 2
    UNION ALL SELECT 'PM-TRAIN-011', '39', '775000000433', 5, 2
    UNION ALL SELECT 'MI-OUT-012', '41', '775000000434', 5, 2
    UNION ALL SELECT 'MI-OUT-012', '42', '775000000435', 6, 2
    UNION ALL SELECT 'MI-OUT-012', '43', '775000000436', 4, 2
) v ON v.sku = p.sku
ON DUPLICATE KEY UPDATE
    barcode = VALUES(barcode),
    stock = VALUES(stock),
    min_stock = VALUES(min_stock);

INSERT INTO inventory_movements (variant_id, movement_type, quantity, note, user_id)
SELECT pv.id, 'purchase', pv.stock, 'Carga inicial de inventario', 1
FROM product_variants pv
LEFT JOIN inventory_movements im
    ON im.variant_id = pv.id
    AND im.note = 'Carga inicial de inventario'
WHERE im.id IS NULL;
