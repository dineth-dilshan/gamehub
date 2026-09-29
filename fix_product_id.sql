-- Fix product_id column type from INT to VARCHAR
-- This will allow string-based product IDs

ALTER TABLE cart MODIFY COLUMN product_id VARCHAR(255) NOT NULL;
ALTER TABLE wishlist MODIFY COLUMN product_id VARCHAR(255) NOT NULL;
