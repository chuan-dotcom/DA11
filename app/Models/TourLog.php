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

    public function create(array $data)
    {
        return $this->connection->insert('tour_logs', [
            'departure_id' => (int) $data['departure_id'],
            'title'        => $data['title'],
            'content'      => $data['content'],
            'log_date'     => $data['log_date'],
            'location'     => $data['location'] ?: null,
            'weather'      => $data['weather'] ?: null,
            'mood'         => $data['mood'] ?: null,
            'author_id'    => !empty($data['author_id']) ? (int) $data['author_id'] : null,
            'status'       => 'published',
        ]);
    }

    public function updateLog($id, array $data)
    {
        return $this->connection->update('tour_logs', [
            'title'    => $data['title'],
            'content'  => $data['content'],
            'log_date' => $data['log_date'],
            'location' => $data['location'] ?: null,
            'weather'  => $data['weather'] ?: null,
            'mood'     => $data['mood'] ?: null,
        ], ['id' => (int) $id]);
    }

    public function deleteLog($id)
    {
        return $this->connection->delete('tour_logs', ['id' => (int) $id]);
    }
}
