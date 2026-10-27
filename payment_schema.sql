USE storyspill_db1;

-- Run once. If column already exists, ignore the error on price line.

ALTER TABLE books ADD COLUMN price DECIMAL(10,2) NOT NULL DEFAULT 299.00 AFTER genre;

CREATE TABLE IF NOT EXISTS purchases (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  book_id INT NOT NULL,
  amount DECIMAL(10,2) NOT NULL,
  payment_method VARCHAR(50) DEFAULT 'Card',
  transaction_id VARCHAR(64) NOT NULL,
  status ENUM('paid','failed','refunded') DEFAULT 'paid',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE CASCADE,
  UNIQUE KEY unique_purchase (user_id, book_id)
);

CREATE TABLE IF NOT EXISTS rentals (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  book_id INT NOT NULL,
  days INT NOT NULL,
  rent_amount DECIMAL(10,2) NOT NULL,
  penalty_amount DECIMAL(10,2) NOT NULL DEFAULT 0,
  penalty_paid DECIMAL(10,2) NOT NULL DEFAULT 0,
  start_date DATE NOT NULL,
  end_date DATE NOT NULL,
  returned_at DATETIME NULL,
  payment_method VARCHAR(50) DEFAULT 'Card',
  transaction_id VARCHAR(64) NOT NULL,
  status ENUM('active','expired','returned','overdue') DEFAULT 'active',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE CASCADE
);

UPDATE books SET price = 199.00 + (id % 10) * 50;
