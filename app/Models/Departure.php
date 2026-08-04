<?php
   
namespace App\Models;

use App\Model;

class Departure extends Model
{
    public function __construct()
    {
        parent::__construct();
        $this->ensureGuestGroupColumns();
    }

    private function ensureGuestGroupColumns()
    {
        try {
            $column = $this->connection->fetchAssociative(
                'SHOW COLUMNS FROM departures LIKE ?',
                ['group_name']
            );

            if (!$column) {
                $this->connection->executeStatement(
                    "ALTER TABLE departures ADD COLUMN group_name VARCHAR(255) NULL AFTER tour_id"
                );
            }
        } catch (\Throwable $e) {
        }

        try {
            $table = $this->connection->fetchAssociative("SHOW TABLES LIKE 'booking_guests'");
            if (!$table) {
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
            }
        } catch (\Throwable $e) {
        }
    }

    public function getAll($categoryId = null)
    {
        $stmt = $this->connection->createQueryBuilder();
        $stmt->select('d.*', 't.name as tour_name', 't.duration as tour_duration')
            ->from('departures', 'd')
            ->leftJoin('d', 'tours', 't', 't.id = d.tour_id');

        if (!empty($categoryId)) {
            $stmt->andWhere('t.category_id = :category_id')
                ->setParameter('category_id', (int) $categoryId);
        }

        $stmt->orderBy('d.departure_date', 'DESC');

        return $stmt->fetchAllAssociative();
    }

    public function findById($id)
    {
        $stmt = $this->connection->createQueryBuilder();
        $stmt->select('d.*', 't.name as tour_name', 't.duration as tour_duration', 't.price as tour_price')
            ->from('departures', 'd')
            ->leftJoin('d', 'tours', 't', 't.id = d.tour_id')
            ->where('d.id = :id')
            ->setParameter('id', $id);

        return $stmt->fetchAssociative();
    }

    public function insert($data)
    {
        return $this->connection->insert('departures', [
            'tour_id'         => (int) $data['tour_id'],
            'group_name'      => !empty($data['group_name']) ? $data['group_name'] : null,
            'departure_date'  => $data['departure_date'],
            'return_date'     => !empty($data['return_date']) ? $data['return_date'] : null,
            'max_participants'=> isset($data['max_participants']) && $data['max_participants'] !== '' ? (int) $data['max_participants'] : 0,
            'meeting_point'   => $data['meeting_point'] ?? null,
            'meeting_time'    => $data['meeting_time'] ?? null,
            'vehicle'         => $data['vehicle'] ?? null,
            'notes'           => $data['notes'] ?? null,
            'status'          => $data['status'] ?? 'scheduled',
            'created_at'      => date('Y-m-d H:i:s'),
        ]);
    }

    public function update($id, $data)
    {
        $updateData = [
            'tour_id'         => (int) $data['tour_id'],
            'group_name'      => !empty($data['group_name']) ? $data['group_name'] : null,
            'departure_date'  => $data['departure_date'],
            'return_date'     => !empty($data['return_date']) ? $data['return_date'] : null,
            'max_participants'=> isset($data['max_participants']) && $data['max_participants'] !== '' ? (int) $data['max_participants'] : 0,
            'meeting_point'   => $data['meeting_point'] ?? null,
            'meeting_time'    => $data['meeting_time'] ?? null,
            'vehicle'         => $data['vehicle'] ?? null,
            'notes'           => $data['notes'] ?? null,
            'status'          => $data['status'] ?? 'scheduled',
            'updated_at'      => date('Y-m-d H:i:s'),
        ];

        return $this->connection->update('departures', $updateData, ['id' => $id]);
    }

    public function delete($id)
    {
        return $this->connection->delete('departures', ['id' => $id]);
    }

    public function getUpcomingDepartures($limit = 10)
    {
        $stmt = $this->connection->createQueryBuilder();
        $stmt->select('d.*', 't.name as tour_name')
            ->from('departures', 'd')
            ->leftJoin('d', 'tours', 't', 't.id = d.tour_id')
            ->where('d.departure_date >= :today')
            ->andWhere('d.status != :status_cancelled')
            ->setParameter('today', date('Y-m-d'))
            ->setParameter('status_cancelled', 'cancelled')
            ->orderBy('d.departure_date', 'ASC')
            ->setMaxResults((int) $limit);

        return $stmt->fetchAllAssociative();
    }

    public function getTotalDepartures()
    {
        $stmt = $this->connection->createQueryBuilder();
        $stmt->select('COUNT(id) as total')->from('departures');
        return (int) ($stmt->fetchAssociative()['total'] ?? 0);
    }

    public function getDeparturesByStatus($categoryId = null)
    {
        $stmt = $this->connection->createQueryBuilder();
        $stmt->select('d.status', 'COUNT(d.id) as count')
            ->from('departures', 'd')
            ->leftJoin('d', 'tours', 't', 't.id = d.tour_id');

        if (!empty($categoryId)) {
            $stmt->andWhere('t.category_id = :category_id')
                ->setParameter('category_id', (int) $categoryId);
        }

        $stmt->groupBy('d.status');
        return $stmt->fetchAllAssociative();
    }

    public function getAllWithGuestStats($tourId = null, $status = null)
    {
        $sql = "
            SELECT
                d.*,
                t.name AS tour_name,
                t.duration AS tour_duration,
                COALESCE(gs.assigned_bookings, 0) AS assigned_bookings,
                COALESCE(gs.assigned_people, 0) AS assigned_people,
                COALESCE(gs.checked_in_people, 0) AS checked_in_people
            FROM departures d
            LEFT JOIN tours t
                ON t.id = d.tour_id
            LEFT JOIN (
                SELECT
                    b.departure_id,
                    COUNT(DISTINCT b.id) AS assigned_bookings,
                    CASE
                        WHEN COUNT(g.id) > 0 THEN COUNT(g.id)
                        ELSE COALESCE(SUM(b.num_people), 0)
                    END AS assigned_people,
                    CASE
                        WHEN COUNT(g.id) > 0 THEN COALESCE(SUM(CASE WHEN g.check_in_status = 1 THEN 1 ELSE 0 END), 0)
                        ELSE COALESCE(SUM(CASE WHEN b.check_in_status = 1 THEN b.num_people ELSE 0 END), 0)
                    END AS checked_in_people
                FROM bookings b
                LEFT JOIN booking_guests g
                    ON g.booking_id = b.id
                WHERE b.departure_id IS NOT NULL
                GROUP BY b.departure_id
            ) gs
                ON gs.departure_id = d.id
        ";

        $params = [];
        $conditions = [];

        if (!empty($tourId)) {
            $conditions[] = "d.tour_id = ?";
            $params[] = (int) $tourId;
        }

        if ($status !== null && $status !== '') {
            $conditions[] = "d.status = ?";
            $params[] = $status;
        }

        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(' AND ', $conditions);
        }

        $sql .= " ORDER BY d.departure_date DESC, d.id DESC ";

        return $this->connection->fetchAllAssociative($sql, $params);
    }

    public function findWithGuestStatsById($id)
    {
        $sql = "
            SELECT
                d.*,
                t.name AS tour_name,
                t.duration AS tour_duration,
                t.price AS tour_price,
                COALESCE(gs.assigned_bookings, 0) AS assigned_bookings,
                COALESCE(gs.assigned_people, 0) AS assigned_people,
                COALESCE(gs.checked_in_people, 0) AS checked_in_people
            FROM departures d
            LEFT JOIN tours t
                ON t.id = d.tour_id
            LEFT JOIN (
                SELECT
                    b.departure_id,
                    COUNT(DISTINCT b.id) AS assigned_bookings,
                    CASE
                        WHEN COUNT(g.id) > 0 THEN COUNT(g.id)
                        ELSE COALESCE(SUM(b.num_people), 0)
                    END AS assigned_people,
                    CASE
                        WHEN COUNT(g.id) > 0 THEN COALESCE(SUM(CASE WHEN g.check_in_status = 1 THEN 1 ELSE 0 END), 0)
                        ELSE COALESCE(SUM(CASE WHEN b.check_in_status = 1 THEN b.num_people ELSE 0 END), 0)
                    END AS checked_in_people
                FROM bookings b
                LEFT JOIN booking_guests g
                    ON g.booking_id = b.id
                WHERE b.departure_id IS NOT NULL
                GROUP BY b.departure_id
            ) gs
                ON gs.departure_id = d.id
            WHERE d.id = ?
            LIMIT 1
        ";

        return $this->connection->fetchAssociative($sql, [$id]) ?: null;
    }
}

