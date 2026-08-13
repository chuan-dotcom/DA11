<?php

namespace App\Models;

use App\Model;
                       
class TourDiary extends Model
{
    public function __construct()
    {
        parent::__construct();
        $this->ensureTableExists();  
        $this->ensureAuditColumns();
        $this->ensureTimelineLinkColumn();
        $this->ensureExpenseColumns();
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
                        tour_log_id INT NULL,
                        created_by_hdv_id INT NULL,
                        title VARCHAR(255) NOT NULL,
                        content TEXT NOT NULL,
                        diary_date DATE NOT NULL,
                        weather VARCHAR(100) NULL,
                        mood VARCHAR(100) NULL,
                        photos TEXT NULL,
                        actual_cost BIGINT DEFAULT 0,
                        expense_amount BIGINT DEFAULT 0,
                        expense_category VARCHAR(100) NULL,
                        receipt_photo VARCHAR(255) NULL,
                        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                        INDEX idx_departure_id (departure_id),
                        INDEX idx_tour_log_id (tour_log_id),
                        INDEX idx_diary_date (diary_date),
                        FOREIGN KEY (departure_id) REFERENCES departures(id) ON DELETE CASCADE
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
                ");
            }
        } catch (\Throwable $e) {
        }
    }

    private function ensureAuditColumns()
    {
        try {
            $column = $this->connection->fetchAssociative(
                "SHOW COLUMNS FROM tour_diaries LIKE 'created_by_hdv_id'"
            );

            if (!$column) {
                $this->connection->executeStatement(
                    "ALTER TABLE tour_diaries ADD COLUMN created_by_hdv_id INT NULL AFTER departure_id"
                );
            }
        } catch (\Throwable $e) {
        }

        try {
            $index = $this->connection->fetchAssociative(
                "SHOW INDEX FROM tour_diaries WHERE Key_name = 'idx_created_by_hdv_id'"
            );

            if (!$index) {
                $this->connection->executeStatement(
                    "ALTER TABLE tour_diaries ADD INDEX idx_created_by_hdv_id (created_by_hdv_id)"
                );
            }
        } catch (\Throwable $e) {
        }
    }

    private function ensureTimelineLinkColumn()
    {
        try {
            $column = $this->connection->fetchAssociative("SHOW COLUMNS FROM tour_diaries LIKE 'tour_log_id'");
            if (!$column) {
                $this->connection->executeStatement('ALTER TABLE tour_diaries ADD COLUMN tour_log_id INT NULL AFTER departure_id');
            }

            $index = $this->connection->fetchAssociative("SHOW INDEX FROM tour_diaries WHERE Key_name = 'idx_tour_log_id'");
            if (!$index) {
                $this->connection->executeStatement('ALTER TABLE tour_diaries ADD INDEX idx_tour_log_id (tour_log_id)');
            }
        } catch (\Throwable $e) {
        }
    }

    private function ensureExpenseColumns()
    {
        try {
            $col0 = $this->connection->fetchAssociative("SHOW COLUMNS FROM tour_diaries LIKE 'actual_cost'");
            if (!$col0) {
                $this->connection->executeStatement("ALTER TABLE tour_diaries ADD COLUMN actual_cost BIGINT DEFAULT 0 AFTER photos");
            }
            $col1 = $this->connection->fetchAssociative("SHOW COLUMNS FROM tour_diaries LIKE 'expense_amount'");
            if (!$col1) {
                $this->connection->executeStatement("ALTER TABLE tour_diaries ADD COLUMN expense_amount BIGINT DEFAULT 0 AFTER actual_cost");
            }
            $col2 = $this->connection->fetchAssociative("SHOW COLUMNS FROM tour_diaries LIKE 'expense_category'");
            if (!$col2) {
                $this->connection->executeStatement("ALTER TABLE tour_diaries ADD COLUMN expense_category VARCHAR(100) NULL AFTER expense_amount");
            }
            $col3 = $this->connection->fetchAssociative("SHOW COLUMNS FROM tour_diaries LIKE 'receipt_photo'");
            if (!$col3) {
                $this->connection->executeStatement("ALTER TABLE tour_diaries ADD COLUMN receipt_photo VARCHAR(255) NULL AFTER expense_category");
            }
        } catch (\Throwable $e) {
        }
    }

    public function getAll($departureId = null)
    {
        $stmt = $this->connection->createQueryBuilder();
        $stmt->select(
            'td.*',
            'd.group_name as departure_group_name',
            'd.departure_date as tour_departure_date',
            'd.return_date as tour_return_date',
            't.id as tour_id',
            't.name as tour_name',
            't.category_id as category_id',
            'tc.name as category_name',
            'author_h.Hoten as author_hdv_name',
            'author_h.Lienhe as author_hdv_phone'
        )
            ->from('tour_diaries', 'td')
            ->leftJoin('td', 'departures', 'd', 'd.id = td.departure_id')
            ->leftJoin('d', 'tours', 't', 't.id = d.tour_id')
            ->leftJoin('t', 'tour_categories', 'tc', 'tc.id = t.category_id')
            ->leftJoin('td', 'hdv', 'author_h', 'author_h.HDV_id = td.created_by_hdv_id');

        if (!empty($departureId)) {
            $stmt->andWhere('td.departure_id = :departure_id')
                ->setParameter('departure_id', (int) $departureId);
        }

        $stmt->orderBy('td.diary_date', 'ASC')
            ->addOrderBy('td.id', 'ASC');

        return $stmt->fetchAllAssociative();
    }

    public function findById($id)
    {
        $stmt = $this->connection->createQueryBuilder();
        $stmt->select(
                'td.*',
                'd.group_name as departure_group_name',
                'd.departure_date as tour_departure_date',
                'd.return_date as tour_return_date',
                'd.meeting_point',
                'd.meeting_time',
                'd.vehicle',
                't.id as tour_id',
                't.name as tour_name',
                't.category_id as category_id',
                'tc.name as category_name',
                'author_h.Hoten as author_hdv_name',
                'author_h.Lienhe as author_hdv_phone',
                'tl.title as diary_title'
            )
            ->from('tour_diaries', 'td')
            ->leftJoin('td', 'departures', 'd', 'd.id = td.departure_id')
            ->leftJoin('d', 'tours', 't', 't.id = d.tour_id')
            ->leftJoin('t', 'tour_categories', 'tc', 'tc.id = t.category_id')
            ->leftJoin('td', 'hdv', 'author_h', 'author_h.HDV_id = td.created_by_hdv_id')
            ->leftJoin('td', 'tour_logs', 'tl', 'tl.id = td.tour_log_id')
            ->where('td.id = :id')
            ->setParameter('id', (int) $id);

        return $stmt->fetchAssociative() ?: null;
    }

    public function getByDepartureId($departureId)
    {
        $stmt = $this->connection->createQueryBuilder();
        $stmt->select(
            'td.*',
            'd.group_name as departure_group_name',
            't.name as tour_name',
            't.category_id as category_id',
            'tc.name as category_name',
            'author_h.Hoten as author_hdv_name'
        )
            ->from('tour_diaries', 'td')
            ->leftJoin('td', 'departures', 'd', 'd.id = td.departure_id')
            ->leftJoin('d', 'tours', 't', 't.id = d.tour_id')
            ->leftJoin('t', 'tour_categories', 'tc', 'tc.id = t.category_id')
            ->leftJoin('td', 'hdv', 'author_h', 'author_h.HDV_id = td.created_by_hdv_id')
            ->where('td.departure_id = :departure_id')
            ->setParameter('departure_id', (int) $departureId)
            ->orderBy('td.diary_date', 'ASC')
            ->addOrderBy('td.id', 'ASC');

        return $stmt->fetchAllAssociative();
    }

    public function insert($data)
    {
        $this->ensureExpenseColumns();

        $photos = !empty($data['photos']) && is_array($data['photos'])
            ? implode(',', $data['photos'])
            : ($data['photos'] ?? null);

        $actualCost = !empty($data['actual_cost']) ? (int) preg_replace('/[^\d]/', '', $data['actual_cost']) : 0;
        $expenseAmount = !empty($data['expense_amount']) ? (int) preg_replace('/[^\d]/', '', $data['expense_amount']) : 0;

        return $this->connection->insert('tour_diaries', [
            'departure_id'      => (int) $data['departure_id'],
            'tour_log_id'       => !empty($data['tour_log_id']) ? (int) $data['tour_log_id'] : null,
            'created_by_hdv_id' => !empty($data['created_by_hdv_id']) ? (int) $data['created_by_hdv_id'] : null,
            'title'             => $data['title'],
            'content'           => $data['content'],
            'diary_date'        => $data['diary_date'],
            'weather'           => !empty($data['weather']) ? $data['weather'] : null,
            'mood'              => !empty($data['mood']) ? $data['mood'] : null,
            'photos'            => $photos,
            'actual_cost'       => $actualCost,
            'expense_amount'    => $expenseAmount,
            'expense_category'  => !empty($data['expense_category']) ? $data['expense_category'] : null,
            'receipt_photo'     => !empty($data['receipt_photo']) ? $data['receipt_photo'] : null,
            'created_at'        => date('Y-m-d H:i:s'),
        ]);
    }

    public function update($id, $data)
    {
        $this->ensureExpenseColumns();

        $current = $this->findById($id);
        $existingPhotos = [];
        if (!empty($current['photos'])) {
            $existingPhotos = explode(',', $current['photos']);
        }

        $newPhotos = !empty($data['photos']) && is_array($data['photos']) ? $data['photos'] : [];
        $allPhotos = array_unique(array_merge($existingPhotos, $newPhotos));
        $photosString = !empty($allPhotos) ? implode(',', $allPhotos) : null;

        if (!empty($data['delete_photos']) && is_array($data['delete_photos'])) {
            $allowedDeletePhotos = array_values(array_intersect($existingPhotos, $data['delete_photos']));

            foreach ($allowedDeletePhotos as $photoPath) {
                if (file_exists($photoPath)) {
                    unlink($photoPath);
                }
                $allPhotos = array_filter($allPhotos, function ($p) use ($photoPath) {
                    return $p !== $photoPath;
                });
            }
            $photosString = !empty($allPhotos) ? implode(',', $allPhotos) : null;
        }

        $actualCost = isset($data['actual_cost']) ? (int) preg_replace('/[^\d]/', '', $data['actual_cost']) : (int)($current['actual_cost'] ?? 0);
        $expenseAmount = isset($data['expense_amount']) ? (int) preg_replace('/[^\d]/', '', $data['expense_amount']) : (int)($current['expense_amount'] ?? 0);

        $updateData = [
            'departure_id'     => (int) $data['departure_id'],
            'tour_log_id'      => !empty($data['tour_log_id']) ? (int) $data['tour_log_id'] : null,
            'title'            => $data['title'],
            'content'          => $data['content'],
            'diary_date'       => $data['diary_date'],
            'weather'          => !empty($data['weather']) ? $data['weather'] : null,
            'mood'             => !empty($data['mood']) ? $data['mood'] : null,
            'photos'           => $photosString,
            'actual_cost'      => $actualCost,
            'expense_amount'   => $expenseAmount,
            'expense_category' => isset($data['expense_category']) ? $data['expense_category'] : ($current['expense_category'] ?? null),
            'receipt_photo'    => isset($data['receipt_photo']) ? $data['receipt_photo'] : ($current['receipt_photo'] ?? null),
            'updated_at'       => date('Y-m-d H:i:s'),
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
        $stmt->select('td.*', 'd.group_name as departure_group_name', 't.name as tour_name', 'tc.name as category_name', 'author_h.Hoten as author_hdv_name')
            ->from('tour_diaries', 'td')
            ->leftJoin('td', 'departures', 'd', 'd.id = td.departure_id')
            ->leftJoin('d', 'tours', 't', 't.id = d.tour_id')
            ->leftJoin('t', 'tour_categories', 'tc', 'tc.id = t.category_id')
            ->leftJoin('td', 'hdv', 'author_h', 'author_h.HDV_id = td.created_by_hdv_id')
            ->orderBy('td.diary_date', 'DESC')
            ->setMaxResults((int) $limit);

        return $stmt->fetchAllAssociative();
    }
}
