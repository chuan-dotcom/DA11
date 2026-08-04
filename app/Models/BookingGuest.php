<?php

namespace App\Models;

use App\Model;

class BookingGuest extends Model
{
    public function __construct()
    {
        parent::__construct();
        $this->ensureTable();
    }

    private function ensureTable()
    {
        try {
            $table = $this->connection->fetchAssociative("SHOW TABLES LIKE 'booking_guests'");
            if ($table) {
                return;
            }

            $this->connection->executeStatement("
                CREATE TABLE IF NOT EXISTS booking_guests (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    booking_id INT NOT NULL,
                    full_name VARCHAR(255) NOT NULL,
                    gender ENUM('male','female','other') NULL DEFAULT NULL,
                    dob DATE NULL DEFAULT NULL,
                    phone VARCHAR(50) NULL DEFAULT NULL,
                    email VARCHAR(255) NULL DEFAULT NULL,
                    identity_no VARCHAR(50) NULL DEFAULT NULL,
                    address VARCHAR(255) NULL DEFAULT NULL,
                    payment_status ENUM('unpaid','deposit','paid') NOT NULL DEFAULT 'unpaid',
                    check_in_status TINYINT(1) NOT NULL DEFAULT 0,
                    checked_in_at DATETIME NULL DEFAULT NULL,
                    note TEXT NULL,
                    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    updated_at DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
                    INDEX idx_booking_guests_booking_id (booking_id),
                    INDEX idx_booking_guests_check_in_status (check_in_status),
                    CONSTRAINT fk_booking_guests_booking
                        FOREIGN KEY (booking_id)
                        REFERENCES bookings(id)
                        ON UPDATE CASCADE
                        ON DELETE CASCADE
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ");
        } catch (\Throwable $e) {
        }
    }

    public function getByBookingId($bookingId)
    {
        $stmt = $this->connection->createQueryBuilder();
        $stmt->select('g.*')
            ->from('booking_guests', 'g')
            ->where('g.booking_id = :booking_id')
            ->setParameter('booking_id', (int) $bookingId)
            ->orderBy('g.id', 'ASC');

        return $stmt->fetchAllAssociative();
    }

    public function getByDepartureId($departureId)
    {
        $stmt = $this->connection->createQueryBuilder();
        $stmt->select(
                'g.*',
                'b.id AS booking_id',
                'b.customer_name AS booking_customer_name',
                'b.customer_phone AS booking_customer_phone',
                'b.customer_email AS booking_customer_email',
                't.name AS tour_name',
                'd.departure_date'
            )
            ->from('booking_guests', 'g')
            ->innerJoin('g', 'bookings', 'b', 'b.id = g.booking_id')
            ->leftJoin('b', 'tours', 't', 't.id = b.tour_id')
            ->leftJoin('b', 'departures', 'd', 'd.id = b.departure_id')
            ->where('b.departure_id = :departure_id')
            ->setParameter('departure_id', (int) $departureId)
            ->orderBy('g.check_in_status', 'ASC')
            ->addOrderBy('g.id', 'ASC');

        return $stmt->fetchAllAssociative();
    }

    public function findById($id)
    {
        $stmt = $this->connection->createQueryBuilder();
        $stmt->select('g.*', 'b.departure_id', 'b.tour_id')
            ->from('booking_guests', 'g')
            ->innerJoin('g', 'bookings', 'b', 'b.id = g.booking_id')
            ->where('g.id = :id')
            ->setParameter('id', (int) $id);

        return $stmt->fetchAssociative() ?: null;
    }

    public function insert($data)
    {
        return $this->connection->insert('booking_guests', [
            'booking_id' => (int) $data['booking_id'],
            'full_name' => $data['full_name'],
            'gender' => $data['gender'] ?: null,
            'dob' => $data['dob'] ?: null,
            'phone' => $data['phone'] ?: null,
            'email' => $data['email'] ?: null,
            'identity_no' => $data['identity_no'] ?: null,
            'address' => $data['address'] ?: null,
            'payment_status' => $data['payment_status'] ?: 'unpaid',
            'check_in_status' => (int) ($data['check_in_status'] ?? 0),
            'checked_in_at' => $data['checked_in_at'] ?: null,
            'note' => $data['note'] ?: null,
        ]);
    }

    public function update($id, $data)
    {
        return $this->connection->update('booking_guests', [
            'full_name' => $data['full_name'],
            'gender' => $data['gender'] ?: null,
            'dob' => $data['dob'] ?: null,
            'phone' => $data['phone'] ?: null,
            'email' => $data['email'] ?: null,
            'identity_no' => $data['identity_no'] ?: null,
            'address' => $data['address'] ?: null,
            'payment_status' => $data['payment_status'] ?: 'unpaid',
            'note' => $data['note'] ?: null,
        ], [
            'id' => (int) $id,
        ]);
    }

    public function delete($id)
    {
        return $this->connection->delete('booking_guests', [
            'id' => (int) $id,
        ]);
    }

    public function markCheckedIn($id)
    {
        return $this->connection->update('booking_guests', [
            'check_in_status' => 1,
            'checked_in_at' => date('Y-m-d H:i:s'),
        ], [
            'id' => (int) $id,
        ]);
    }

    public function cancelCheckedIn($id)
    {
        return $this->connection->update('booking_guests', [
            'check_in_status' => 0,
            'checked_in_at' => null,
        ], [
            'id' => (int) $id,
        ]);
    }

    public function getStatsByBookingId($bookingId)
    {
        try {
            $sql = "
                SELECT
                    COUNT(*) AS total_guests,
                    COALESCE(SUM(CASE WHEN check_in_status = 1 THEN 1 ELSE 0 END), 0) AS checked_in_guests
                FROM booking_guests
                WHERE booking_id = ?
            ";

            $row = $this->connection->fetchAssociative($sql, [(int) $bookingId]);

            return [
                'total_guests' => (int) ($row['total_guests'] ?? 0),
                'checked_in_guests' => (int) ($row['checked_in_guests'] ?? 0),
            ];
        } catch (\Throwable $e) {
            return [
                'total_guests' => 0,
                'checked_in_guests' => 0,
            ];
        }
    }

    public function ensureGuestsForBooking(array $booking)
    {
        $bookingId = (int) ($booking['id'] ?? 0);
        if ($bookingId <= 0) {
            return;
        }

        $expected = (int) ($booking['num_people'] ?? 0);
        if ($expected <= 0) {
            return;
        }

        try {
            $row = $this->connection->fetchAssociative(
                'SELECT COUNT(*) AS total FROM booking_guests WHERE booking_id = ?',
                [$bookingId]
            );
            $current = (int) ($row['total'] ?? 0);
            if ($current > 0) {
                return;
            }

            $nameBase = trim((string) ($booking['customer_name'] ?? 'Khách'));
            for ($i = 1; $i <= $expected; $i++) {
                $this->insert([
                    'booking_id' => $bookingId,
                    'full_name' => $i === 1 ? $nameBase : ($nameBase . ' ' . $i),
                    'gender' => null,
                    'dob' => null,
                    'phone' => $booking['customer_phone'] ?? null,
                    'email' => $booking['customer_email'] ?? null,
                    'identity_no' => null,
                    'address' => null,
                    'payment_status' => 'unpaid',
                    'check_in_status' => 0,
                    'checked_in_at' => null,
                    'note' => null,
                ]);
            }
        } catch (\Throwable $e) {
        }
    }
}

