<?php

namespace App\Models;

use App\Model;

class TourDiary extends Model
{
    public function __construct()
    {
        parent::__construct();
        $this->ensureTableExists();
    }

    private function ensureTableExists()
    {
        try {
            $tableExists = $this->connection->fetchAssociative(
                "SHOW TABLES LIKE 'tour_diaries'"
            );

            if (!$tableExists) {
                $this->connection->executeStatement("
                    CREATE TABLE tour_diaries (
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
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
                ");
            }
        } catch (\Throwable $e) {
        }
    }

    public function getAll($departureId = null)
    {
        $stmt = $this->connection->createQueryBuilder();
        $stmt->select('td.*', 'd.group_name as departure_group_name', 't.name as tour_name')
            ->from('tour_diaries', 'td')
            ->leftJoin('td', 'departures', 'd', 'd.id = td.departure_id')
            ->leftJoin('d', 'tours', 't', 't.id = d.tour_id');

        if (!empty($departureId)) {
            $stmt->andWhere('td.departure_id = :departure_id')
                ->setParameter('departure_id', (int) $departureId);
        }

        $stmt->orderBy('td.diary_date', 'DESC')
            ->addOrderBy('td.id', 'DESC');

        return $stmt->fetchAllAssociative();
    }

    public function findById($id)
    {
        $stmt = $this->connection->createQueryBuilder();
        $stmt->select('td.*', 'd.group_name as departure_group_name', 't.name as tour_name',
                'd.departure_date as tour_departure_date', 'd.return_date as tour_return_date')
            ->from('tour_diaries', 'td')
            ->leftJoin('td', 'departures', 'd', 'd.id = td.departure_id')
            ->leftJoin('d', 'tours', 't', 't.id = d.tour_id')
            ->where('td.id = :id')
            ->setParameter('id', $id);

        return $stmt->fetchAssociative();
    }

    public function getByDepartureId($departureId)
    {
        $stmt = $this->connection->createQueryBuilder();
        $stmt->select('td.*')
            ->from('tour_diaries', 'td')
            ->where('td.departure_id = :departure_id')
            ->setParameter('departure_id', (int) $departureId)
            ->orderBy('td.diary_date', 'ASC')
            ->addOrderBy('td.id', 'ASC');

        return $stmt->fetchAllAssociative();
    }

    public function insert($data)
    {
        $photos = !empty($data['photos']) && is_array($data['photos'])
            ? implode(',', $data['photos'])
            : ($data['photos'] ?? null);

        return $this->connection->insert('tour_diaries', [
            'departure_id' => (int) $data['departure_id'],
            'title'        => $data['title'],
            'content'      => $data['content'],
            'diary_date'   => $data['diary_date'],
            'weather'      => !empty($data['weather']) ? $data['weather'] : null,
            'mood'         => !empty($data['mood']) ? $data['mood'] : null,
            'photos'       => $photos,
            'created_at'   => date('Y-m-d H:i:s'),
        ]);
    }

    public function update($id, $data)
    {
        $current = $this->findById($id);
        $existingPhotos = [];
        if (!empty($current['photos'])) {
            $existingPhotos = explode(',', $current['photos']);
        }

        $newPhotos = !empty($data['photos']) && is_array($data['photos']) ? $data['photos'] : [];
        $allPhotos = array_unique(array_merge($existingPhotos, $newPhotos));
        $photosString = !empty($allPhotos) ? implode(',', $allPhotos) : null;

        if (!empty($data['delete_photos']) && is_array($data['delete_photos'])) {
            foreach ($data['delete_photos'] as $photoPath) {
                if (file_exists($photoPath)) {
                    unlink($photoPath);
                }
                $allPhotos = array_filter($allPhotos, function ($p) use ($photoPath) {
                    return $p !== $photoPath;
                });
            }
            $photosString = !empty($allPhotos) ? implode(',', $allPhotos) : null;
        }

        $updateData = [
            'departure_id' => (int) $data['departure_id'],
            'title'        => $data['title'],
            'content'      => $data['content'],
            'diary_date'   => $data['diary_date'],
            'weather'      => !empty($data['weather']) ? $data['weather'] : null,
            'mood'         => !empty($data['mood']) ? $data['mood'] : null,
            'photos'       => $photosString,
            'updated_at'   => date('Y-m-d H:i:s'),
        ];

        return $this->connection->update('tour_diaries', $updateData, ['id' => $id]);
    }

    public function delete($id)
    {
        $diary = $this->findById($id);

        if ($diary && !empty($diary['photos'])) {
            $photos = explode(',', $diary['photos']);
            foreach ($photos as $photo) {
                if (file_exists($photo)) {
                    unlink($photo);
                }
            }
        }

        return $this->connection->delete('tour_diaries', ['id' => $id]);
    }

    public function getTotalDiaries()
    {
        $stmt = $this->connection->createQueryBuilder();
        $stmt->select('COUNT(id) as total')->from('tour_diaries');
        return (int) ($stmt->fetchAssociative()['total'] ?? 0);
    }

    public function getRecentDiaries($limit = 5)
    {
        $stmt = $this->connection->createQueryBuilder();
        $stmt->select('td.*', 'd.group_name as departure_group_name', 't.name as tour_name')
            ->from('tour_diaries', 'td')
            ->leftJoin('td', 'departures', 'd', 'd.id = td.departure_id')
            ->leftJoin('d', 'tours', 't', 't.id = d.tour_id')
            ->orderBy('td.diary_date', 'DESC')
            ->setMaxResults((int) $limit);

        return $stmt->fetchAllAssociative();
    }
}
