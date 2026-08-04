<?php

namespace App\Models;

use App\Model;

class TourLog extends Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getByDepartureId($departureId)
    {
        $stmt = $this->connection->createQueryBuilder();
        $stmt->select('tl.*')
            ->from('tour_logs', 'tl')
            ->where('tl.departure_id = :departure_id')
            ->setParameter('departure_id', (int) $departureId)
            ->orderBy('tl.log_date', 'ASC')
            ->addOrderBy('tl.id', 'ASC');

        return $stmt->fetchAllAssociative();
    }

    public function findById($id)
    {
        $stmt = $this->connection->createQueryBuilder();
        $stmt->select('tl.*')
            ->from('tour_logs', 'tl')
            ->where('tl.id = :id')
            ->setParameter('id', (int) $id);

        return $stmt->fetchAssociative();
    }
}
