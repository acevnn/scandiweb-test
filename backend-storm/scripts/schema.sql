DROP TABLE IF EXISTS product_attribute_sets;
DROP TABLE IF EXISTS attribute_items;
DROP TABLE IF EXISTS attributes;
DROP TABLE IF EXISTS product_images;
DROP TABLE IF EXISTS prices;
DROP TABLE IF EXISTS products;
DROP TABLE IF EXISTS categories;

CREATE TABLE IF NOT EXISTS categories
(
    id   INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE
);

CREATE TABLE IF NOT EXISTS products
(
    id          VARCHAR(100) PRIMARY KEY,
    name        VARCHAR(255) NOT NULL,
    description TEXT,
    in_stock    BOOLEAN      NOT NULL,
    brand       VARCHAR(255),
    category_id INT,
    FOREIGN KEY (category_id) REFERENCES categories (id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS product_images
(
    id         INT AUTO_INCREMENT PRIMARY KEY,
    product_id VARCHAR(100),
    image_url  VARCHAR(512) NOT NULL,
    UNIQUE (product_id, image_url),
    FOREIGN KEY (product_id) REFERENCES products (id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS prices
(
    id              INT AUTO_INCREMENT PRIMARY KEY,
    product_id      VARCHAR(100),
    amount          DECIMAL(10, 2) NOT NULL,
    currency_label  VARCHAR(10),
    currency_symbol VARCHAR(5),
    UNIQUE (product_id, currency_label),
    FOREIGN KEY (product_id) REFERENCES products (id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS attributes
(
    id   INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    type VARCHAR(50),
    UNIQUE (name)
);

CREATE TABLE IF NOT EXISTS attribute_items
(
    id            INT AUTO_INCREMENT PRIMARY KEY,
    attribute_id  INT,
    display_value VARCHAR(100),
    value         VARCHAR(100),
    item_key      VARCHAR(255),
    UNIQUE (attribute_id, item_key),
    FOREIGN KEY (attribute_id) REFERENCES attributes (id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS product_attribute_sets
(
    id           INT AUTO_INCREMENT PRIMARY KEY,
    product_id   VARCHAR(100),
    attribute_id INT,
    UNIQUE (product_id, attribute_id),
    FOREIGN KEY (product_id) REFERENCES products (id) ON DELETE CASCADE,
    FOREIGN KEY (attribute_id) REFERENCES attributes (id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS orders
(
    id         INT AUTO_INCREMENT PRIMARY KEY,
    product_id VARCHAR(100) NOT NULL,
    quantity   INT          NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products (id) ON DELETE CASCADE
);
