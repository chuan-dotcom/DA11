CREATE TABLE IF NOT EXISTS tour_diaries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    departure_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    diary_date DATE NOT NULL,
    weather VARCHAR(100) NULL,
    mood VARCHAR(100) NULL,
    photos TEXT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_departure_id (departure_id),
    INDEX idx_diary_date (diary_date),
    FOREIGN KEY (departure_id) REFERENCES departures(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
