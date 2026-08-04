<?php

namespace App\Models;

use App\Model;

class Departure extends Model 
{ 
    public function getAll()
    {
        $stmt = $this->connection->createQueryBuilder();
        $stmt->select('d.*', 't.name as tour_name', 't.duration as tour_duration')
            ->from('departures', 'd')
            ->leftJoin('d', 'tours', 't', 't.id = d.tour_id')
            ->orderBy('d.departure_date', 'DESC');

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

    public function getDeparturesByStatus()
    {
        $stmt = $this->connection->createQueryBuilder();
        $stmt->select('status', 'COUNT(id) as count')
            ->from('departures')
            ->groupBy('status');
        return $stmt->fetchAllAssociative();
    }
}

