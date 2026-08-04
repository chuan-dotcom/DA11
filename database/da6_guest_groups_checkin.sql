ALTER TABLE departures
    ADD COLUMN group_name VARCHAR(255) NULL AFTER tour_id;

ALTER TABLE bookings
    ADD COLUMN departure_id INT NULL AFTER tour_id,
    ADD COLUMN check_in_status TINYINT(1) NOT NULL DEFAULT 0 AFTER status,
    ADD COLUMN checked_in_at DATETIME NULL AFTER check_in_status,
    ADD INDEX idx_bookings_departure_id (departure_id);
   