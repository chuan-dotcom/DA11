SET NAMES utf8mb4;
SET time_zone = '+07:00';

SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS staff_assignments;
DROP TABLE IF EXISTS departures;

CREATE TABLE departures (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tour_id INT NOT NULL,
    departure_date DATE NOT NULL,
    return_date DATE NULL,
    max_participants INT NOT NULL DEFAULT 0,
    meeting_point VARCHAR(255) NULL,
    meeting_time TIME NULL,
    vehicle VARCHAR(100) NULL,
    notes TEXT NULL,
    status ENUM('scheduled','in_progress','completed','cancelled') NOT NULL DEFAULT 'scheduled',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_departures_tour_id (tour_id),

    CONSTRAINT fk_departures_tour
        FOREIGN KEY (tour_id)
        REFERENCES tours(id)
        ON UPDATE CASCADE
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE staff_assignments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    departure_id INT NOT NULL,
    staff_id INT NOT NULL,
    role ENUM('lead_guide','assistant_guide','driver','photographer','other')
        NOT NULL DEFAULT 'other',
    responsibilities TEXT,
    notes TEXT,
    status ENUM('assigned','confirmed','completed','rejected')
        NOT NULL DEFAULT 'assigned',
    assigned_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,

    UNIQUE KEY uniq_departure_staff (departure_id, staff_id),
    INDEX idx_staff_assignments_departure_id (departure_id),
    INDEX idx_staff_assignments_staff_id (staff_id),

    CONSTRAINT fk_staff_departure
        FOREIGN KEY (departure_id)
        REFERENCES departures(id)
        ON UPDATE CASCADE
        ON DELETE CASCADE,

    CONSTRAINT fk_staff_hdv
        FOREIGN KEY (staff_id)
        REFERENCES hdv(HDV_id)
        ON UPDATE CASCADE
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET @tour_id := (SELECT id FROM tours ORDER BY id LIMIT 1);

INSERT INTO departures
(tour_id, departure_date, return_date, max_participants, meeting_point, meeting_time, vehicle, notes, status)
SELECT
    @tour_id, '2026-08-10', '2026-08-12', 30, 'Văn phòng công ty', '08:00:00', 'Xe du lịch', 'Chuyến mẫu', 'scheduled'
WHERE @tour_id IS NOT NULL;

SET @departure_id := (SELECT id FROM departures ORDER BY id DESC LIMIT 1);
SET @staff_id_1 := (SELECT HDV_id FROM hdv ORDER BY HDV_id LIMIT 1);
SET @staff_id_2 := (SELECT HDV_id FROM hdv ORDER BY HDV_id LIMIT 1 OFFSET 1);

INSERT INTO staff_assignments
(departure_id, staff_id, role, responsibilities, status)
SELECT
    @departure_id, @staff_id_1, 'lead_guide', 'Dẫn đoàn chính', 'assigned'
WHERE @departure_id IS NOT NULL AND @staff_id_1 IS NOT NULL;

INSERT INTO staff_assignments
(departure_id, staff_id, role, responsibilities, status)
SELECT
    @departure_id, @staff_id_2, 'assistant_guide', 'Hỗ trợ dẫn đoàn', 'assigned'
WHERE @departure_id IS NOT NULL AND @staff_id_2 IS NOT NULL;

SET FOREIGN_KEY_CHECKS = 1;   
