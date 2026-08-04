<?php

namespace App\Models;

use App\Model;

class TourLog extends Model
{
    public function __construct()
    {
        parent::__construct();
        $this->ensureTableExists();
    }

    private function ensureTableExists()
    {
        try {
            $this->connection->fetchAssociative('SELECT 1 FROM tour_logs LIMIT 1');
        } catch (\Throwable $e) {
            try {
                $this->connection->executeStatement("
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
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
                ");
            } catch (\Throwable $e2) {
            }
        }
    }

    public function getAll($departureId = null, $status = null)
    {
        $stmt = $this->connection->createQueryBuilder();
        $stmt->select('tl.*', 'd.group_name', 't.name as tour_name', 'u.name as author_name')
            ->from('tour_logs', 'tl')
            ->leftJoin('tl', 'departures', 'd', 'd.id = tl.departure_id')
            ->leftJoin('d', 'tours', 't', 't.id = d.tour_id')
            ->leftJoin('tl', 'users', 'u', 'u.id = tl.author_id');

        if (!empty($departureId)) {
            $stmt->andWhere('tl.departure_id = :departure_id')
                ->setParameter('departure_id', (int) $departureId);
        }

        if ($status !== null && $status !== '') {
            $stmt->andWhere('tl.status = :status')
                ->setParameter('status', $status);
        }

        $stmt->orderBy('tl.log_date', 'DESC');
        $stmt->addOrderBy('tl.id', 'DESC');

        return $stmt->fetchAllAssociative();
    }

    public function findById($id)
    {
        $stmt = $this->connection->createQueryBuilder();
        $stmt->select('tl.*', 'd.group_name', 'd.departure_date', 'd.return_date',
            't.name as tour_name', 't.duration as tour_duration', 'u.name as author_name')
            ->from('tour_logs', 'tl')
            ->leftJoin('tl', 'departures', 'd', 'd.id = tl.departure_id')
            ->leftJoin('d', 'tours', 't', 't.id = d.tour_id')
            ->leftJoin('tl', 'users', 'u', 'u.id = tl.author_id')
            ->where('tl.id = :id')
            ->setParameter('id', $id);

        return $stmt->fetchAssociative();
    }

    public function insert($data)
    {
        return $this->connection->insert('tour_logs', [
            'departure_id' => (int) $data['departure_id'],
            'title'        => $data['title'],
            'content'      => $data['content'],
            'log_date'     => $data['log_date'],
            'location'     => !empty($data['location']) ? $data['location'] : null,
            'weather'      => !empty($data['weather']) ? $data['weather'] : null,
            'mood'         => !empty($data['mood']) ? $data['mood'] : null,
            'images'       => !empty($data['images']) ? $data['images'] : null,
            'author_id'    => !empty($data['author_id']) ? (int) $data['author_id'] : null,
            'status'       => $data['status'] ?? 'published',
            'created_at'   => date('Y-m-d H:i:s'),
        ]);
    }

    public function update($id, $data)
    {
        $updateData = [
            'departure_id' => (int) $data['departure_id'],
            'title'        => $data['title'],
            'content'      => $data['content'],
            'log_date'     => $data['log_date'],
            'location'     => !empty($data['location']) ? $data['location'] : null,
            'weather'      => !empty($data['weather']) ? $data['weather'] : null,
            'mood'         => !empty($data['mood']) ? $data['mood'] : null,
            'images'       => isset($data['images']) ? $data['images'] : null,
            'author_id'    => !empty($data['author_id']) ? (int) $data['author_id'] : null,
            'status'       => $data['status'] ?? 'published',
            'updated_at'   => date('Y-m-d H:i:s'),
        ];

        return $this->connection->update('tour_logs', $updateData, ['id' => $id]);
    }

    public function delete($id)
    {
        $log = $this->findById($id);
        $deleted = $this->connection->delete('tour_logs', ['id' => $id]);

        if ($deleted && $log && !empty($log['images'])) {
            $images = json_decode($log['images'], true);
            if (is_array($images)) {
                foreach ($images as $img) {
                    if (!empty($img) && file_exists($img)) {
                        try { unlink($img); } catch (\Throwable $e) {}
                    }
                }
            }
        }

        return $deleted;
    }

    public function getByDepartureId($departureId)
    {
        return $this->getAll($departureId);
    }

    public function getLogsCountByDeparture($departureId)
    {
        $stmt = $this->connection->createQueryBuilder();
        $stmt->select('COUNT(id) as total')
            ->from('tour_logs')
            ->where('departure_id = :departure_id')
            ->setParameter('departure_id', (int) $departureId);

        return (int) ($stmt->fetchAssociative()['total'] ?? 0);
    }

    public function getTotalLogs()
    {
        $stmt = $this->connection->createQueryBuilder();
        $stmt->select('COUNT(id) as total')->from('tour_logs');
        return (int) ($stmt->fetchAssociative()['total'] ?? 0);
    }

    public function getRecentLogs($limit = 10)
    {
        $stmt = $this->connection->createQueryBuilder();
        $stmt->select('tl.*', 'd.group_name', 't.name as tour_name')
            ->from('tour_logs', 'tl')
            ->leftJoin('tl', 'departures', 'd', 'd.id = tl.departure_id')
            ->leftJoin('d', 'tours', 't', 't.id = d.tour_id')
            ->orderBy('tl.log_date', 'DESC')
            ->setMaxResults((int) $limit);

        return $stmt->fetchAllAssociative();
    }
}
