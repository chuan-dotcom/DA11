<?php

namespace App\Models;

use App\Model;

class Service extends Model  
{  
    public function __construct() 
    {
        parent::__construct();
        $this->ensureTableExists();
        $this->ensureColumnsExist();
    }

    private function ensureTableExists()
    {
        $tableExists = true;
        try {
            $this->connection->executeQuery('SELECT 1 FROM services LIMIT 1');
        } catch (\Throwable $e) {
            $tableExists = false;
            $sql = "
                CREATE TABLE IF NOT EXISTS services (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    tour_id INT NOT NULL,
                    service_types VARCHAR(255) NOT NULL,
                    supplier VARCHAR(255) NOT NULL,
                    quantity INT NOT NULL DEFAULT 1,
                    status TINYINT NOT NULL DEFAULT 0 COMMENT '0: Cho, 1: Xac nhan, 2: Hoan tat',
                    start_time DATETIME NULL,
                    end_time DATETIME NULL,
                    note TEXT NULL,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    FOREIGN KEY (tour_id) REFERENCES tours(id) ON DELETE CASCADE
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
            ";
            try {
                $this->connection->executeStatement($sql);
            } catch (\Throwable $e2) {
                try {
                    $sqlNoFk = "
                        CREATE TABLE IF NOT EXISTS services (
                            id INT AUTO_INCREMENT PRIMARY KEY,
                            tour_id INT NOT NULL,
                            service_types VARCHAR(255) NOT NULL,
                            supplier VARCHAR(255) NOT NULL,
                            quantity INT NOT NULL DEFAULT 1,
                            status TINYINT NOT NULL DEFAULT 0,
                            start_time DATETIME NULL,
                            end_time DATETIME NULL,
                            note TEXT NULL,
                            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
                    ";
                    $this->connection->executeStatement($sqlNoFk);
                } catch (\Throwable $e3) {
                }
            }
        }

        try {
            $countRow = $this->connection->fetchAssociative('SELECT COUNT(*) AS c FROM services');
            $hasData = !empty($countRow) && (int)($countRow['c'] ?? 0) > 0;
            if (!$hasData) {
                $this->seedSampleData();
            }
        } catch (\Throwable $e) {
            try {
                $this->seedSampleData();
            } catch (\Throwable $e2) {
            }
        }
    }

    private function ensureColumnsExist()
    {
        $alterSqls = [
            'departure_id' => "ALTER TABLE services ADD COLUMN departure_id INT NULL AFTER tour_id",
        ];

        foreach ($alterSqls as $col => $sql) {
            try {
                $this->connection->executeQuery("SELECT `{$col}` FROM services LIMIT 1");
            } catch (\Throwable $e) {
                try {
                    $this->connection->executeStatement($sql);
                } catch (\Throwable $e2) {
                }
            }
        }
    }

    private function seedSampleData()
    {
        $tourIds = [];
        try {
            $rows = $this->connection->fetchAllAssociative('SELECT id FROM tours ORDER BY id ASC LIMIT 10');
            foreach ($rows as $r) {
                $tourIds[] = (int)$r['id'];
            }
        } catch (\Throwable $e) {
        }

        if (empty($tourIds)) {
            return;
        }

        $samples = [
            [
                'service_types' => 'Tham quan, Nhà hàng, Vé máy bay, Khách sạn, Xe',
                'supplier'      => 'Công ty Xe Anh Tài',
                'quantity'      => 4,
                'status'        => 1,
                'start_time'    => '2025-12-01 12:22:00',
                'end_time'      => '2025-12-03 12:22:00',
                'note'          => ''
            ],
            [
                'service_types' => 'Nhà hàng, Khách sạn, Xe',
                'supplier'      => 'Công ty Xe Anh Tài',
                'quantity'      => 10,
                'status'        => 0,
                'start_time'    => null,
                'end_time'      => null,
                'note'          => ''
            ],
            [
                'service_types' => 'Xe',
                'supplier'      => 'Công ty Xe Anh Tài',
                'quantity'      => 10,
                'status'        => 2,
                'start_time'    => null,
                'end_time'      => null,
                'note'          => ''
            ]
        ];

        foreach ($samples as $i => $s) {
            $tid = $tourIds[$i % count($tourIds)];
            try {
                $this->connection->insert('services', [
                    'tour_id'       => $tid,
                    'service_types' => $s['service_types'],
                    'supplier'      => $s['supplier'],
                    'quantity'      => $s['quantity'],
                    'status'        => $s['status'],
                    'start_time'    => $s['start_time'],
                    'end_time'      => $s['end_time'],
                    'note'          => $s['note']
                ]);
            } catch (\Throwable $e) {
            }
        }
    }

    public function getAll()
    {
        $stmt = $this->connection->createQueryBuilder();

        $stmt->select(
                's.*',
                't.name AS tour_name',
                'd.group_name AS departure_group_name',
                'd.departure_date AS departure_date',
                'd.return_date AS departure_return_date'
            )
            ->from('services', 's')
            ->leftJoin('s', 'tours', 't', 's.tour_id = t.id')
            ->leftJoin('s', 'departures', 'd', 's.departure_id = d.id')
            ->orderBy('s.id', 'DESC');

        return $stmt->fetchAllAssociative();
    }

    public function findById($id)
    {
        $stmt = $this->connection->createQueryBuilder();

        $stmt->select(
                's.*',
                't.name AS tour_name',
                'd.group_name AS departure_group_name',
                'd.departure_date AS departure_date',
                'd.return_date AS departure_return_date'
            )
            ->from('services', 's')
            ->leftJoin('s', 'tours', 't', 's.tour_id = t.id')
            ->leftJoin('s', 'departures', 'd', 's.departure_id = d.id')
            ->where('s.id = :id')
            ->setParameter('id', $id);

        return $stmt->fetchAssociative();
    }

    public function insert($data)
    {
        return $this->connection->insert('services', [
            'tour_id'       => $data['tour_id'],
            'departure_id'  => !empty($data['departure_id']) ? (int)$data['departure_id'] : null,
            'service_types' => $data['service_types'],
            'supplier'      => $data['supplier'],
            'quantity'      => $data['quantity'],
            'status'        => $data['status'],
            'start_time'    => $data['start_time'] ?: null,
            'end_time'      => $data['end_time'] ?: null,
            'note'          => $data['note']
        ]);
    }

    public function update($id, $data)
    {
        return $this->connection->update('services', [
            'tour_id'       => $data['tour_id'],
            'departure_id'  => !empty($data['departure_id']) ? (int)$data['departure_id'] : null,
            'service_types' => $data['service_types'],
            'supplier'      => $data['supplier'],
            'quantity'      => $data['quantity'],
            'status'        => $data['status'],
            'start_time'    => $data['start_time'] ?: null,
            'end_time'      => $data['end_time'] ?: null,
            'note'          => $data['note']
        ], [
            'id' => $id
        ]);
    }

    public function delete($id)
    {
        return $this->connection->delete(
            'services',
            ['id' => $id]
        );
    }

    public function getTour($id)
    {
        $stmt = $this->connection->createQueryBuilder();

        $stmt->select('t.*')
            ->from('tours', 't')
            ->where('t.id = :id')
            ->setParameter('id', $id);

        return $stmt->fetchAssociative() ?: null;
    }

    public function filter($tourId = null, $departureId = null, $serviceTypes = null, $status = null)
    {
        $stmt = $this->connection->createQueryBuilder();

        $stmt->select(
                's.*',
                't.name AS tour_name',
                'd.group_name AS departure_group_name',
                'd.departure_date AS departure_date',
                'd.return_date AS departure_return_date'
            )
            ->from('services', 's')
            ->leftJoin('s', 'tours', 't', 's.tour_id = t.id')
            ->leftJoin('s', 'departures', 'd', 's.departure_id = d.id');

        if ($tourId) {
            $stmt->where('s.tour_id = :tourId')
                ->setParameter('tourId', $tourId);
        }

        if ($departureId) {
            if ($tourId) {
                $stmt->andWhere('s.departure_id = :departureId');
            } else {
                $stmt->where('s.departure_id = :departureId');
            }
            $stmt->setParameter('departureId', $departureId);
        }

        if ($serviceTypes) {
            if ($tourId || $departureId) {
                $stmt->andWhere('s.service_types LIKE :serviceTypes');
            } else {
                $stmt->where('s.service_types LIKE :serviceTypes');
            }
            $stmt->setParameter('serviceTypes', '%' . $serviceTypes . '%');
        }

        if ($status !== null && $status !== '') {
            if ($tourId || $departureId || $serviceTypes) {
                $stmt->andWhere('s.status = :status');
            } else {
                $stmt->where('s.status = :status');
            }
            $stmt->setParameter('status', $status);
        }

        $stmt->orderBy('s.id', 'DESC');

        return $stmt->fetchAllAssociative();
    }

    /**
     * Lấy các dịch vụ thuộc về 1 chuyến khởi hành (được gắn departure_id).
     */
    public function getByDepartureId($departureId)
    {
        $stmt = $this->connection->createQueryBuilder();

        $stmt->select(
                's.*',
                't.name AS tour_name'
            )
            ->from('services', 's')
            ->leftJoin('s', 'tours', 't', 's.tour_id = t.id')
            ->where('s.departure_id = :departureId')
            ->setParameter('departureId', (int)$departureId)
            ->orderBy('s.id', 'DESC');

        return $stmt->fetchAllAssociative();
    }
}
