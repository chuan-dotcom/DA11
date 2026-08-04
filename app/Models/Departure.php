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
                    departure_id,
                    COUNT(id) AS assigned_bookings,
                    COALESCE(SUM(num_people), 0) AS assigned_people,
                    COALESCE(SUM(CASE WHEN check_in_status = 1 THEN num_people ELSE 0 END), 0) AS checked_in_people
                FROM bookings
                WHERE departure_id IS NOT NULL
                GROUP BY departure_id
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
                    departure_id,
                    COUNT(id) AS assigned_bookings,
                    COALESCE(SUM(num_people), 0) AS assigned_people,
                    COALESCE(SUM(CASE WHEN check_in_status = 1 THEN num_people ELSE 0 END), 0) AS checked_in_people
                FROM bookings
                WHERE departure_id IS NOT NULL
                GROUP BY departure_id
            ) gs
                ON gs.departure_id = d.id
            WHERE d.id = ?
            LIMIT 1
        ";

        return $this->connection->fetchAssociative($sql, [$id]) ?: null;
    }
}

