CREATE TABLE IF NOT EXISTS tour_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    departure_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    log_date DATETIME NOT NULL,
    location VARCHAR(255) NULL,
    weather VARCHAR(100) NULL,
    mood VARCHAR(50) NULL,
    images TEXT NULL,
    author_id INT NULL,
    status VARCHAR(20) DEFAULT 'published',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_departure_id (departure_id),
    INDEX idx_log_date (log_date),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
