<?php

namespace App\Models;

use App\Model;
  
class StaffAssignment extends Model
{
    public function getAll()       
    {
        $stmt = $this->connection->createQueryBuilder();
        $stmt->select('sa.*', 'd.departure_date', 't.name as tour_name', 'h.Hoten as staff_name')
            ->from('staff_assignments', 'sa')
            ->leftJoin('sa', 'departures', 'd', 'd.id = sa.departure_id')
            ->leftJoin('d', 'tours', 't', 't.id = d.tour_id')
            ->leftJoin('sa', 'hdv', 'h', 'h.HDV_id = sa.staff_id')
            ->orderBy('sa.id', 'DESC');

        return $stmt->fetchAllAssociative();
    }

    public function findById($id)
    {
        $stmt = $this->connection->createQueryBuilder();
        $stmt->select('sa.*', 'd.departure_date', 'd.return_date', 'd.status as departure_status', 't.name as tour_name', 'h.Hoten as staff_name', 'h.Lienhe as staff_phone', 'h.Ngonngu as staff_languages')
            ->from('staff_assignments', 'sa')
            ->leftJoin('sa', 'departures', 'd', 'd.id = sa.departure_id')
            ->leftJoin('d', 'tours', 't', 't.id = d.tour_id')
            ->leftJoin('sa', 'hdv', 'h', 'h.HDV_id = sa.staff_id')
            ->where('sa.id = :id')
            ->setParameter('id', $id);

        return $stmt->fetchAssociative();
    }

    public function getByDepartureId($departureId)
    {
        $stmt = $this->connection->createQueryBuilder();
        $stmt->select('sa.*', 'h.Hoten as staff_name', 'h.Lienhe as staff_phone', 'h.Ngonngu as staff_languages', 'h.Kinhnghiem as staff_experience')
            ->from('staff_assignments', 'sa')
            ->leftJoin('sa', 'hdv', 'h', 'h.HDV_id = sa.staff_id')
            ->where('sa.departure_id = :departure_id')
            ->setParameter('departure_id', $departureId)
            ->orderBy('sa.role', 'ASC');

        return $stmt->fetchAllAssociative();
    }

    public function insert($data)
    {
        return $this->connection->insert('staff_assignments', [
            'departure_id' => (int) $data['departure_id'],
            'staff_id'     => (int) $data['staff_id'],
            'role'         => $data['role'] ?? 'other',
            'responsibilities' => $data['responsibilities'] ?? null,
            'notes'        => $data['notes'] ?? null,
            'status'       => $data['status'] ?? 'assigned',
            'assigned_at'  => date('Y-m-d H:i:s'),
        ]);
    }

    public function update($id, $data)
    {
        $updateData = [
            'departure_id'     => (int) $data['departure_id'],
            'staff_id'         => (int) $data['staff_id'],
            'role'             => $data['role'] ?? 'other',
            'responsibilities' => $data['responsibilities'] ?? null,
            'notes'            => $data['notes'] ?? null,
            'status'           => $data['status'] ?? 'assigned',
            'updated_at'       => date('Y-m-d H:i:s'),
        ];

        return $this->connection->update('staff_assignments', $updateData, ['id' => $id]);
    }

    public function delete($id)
    {
        return $this->connection->delete('staff_assignments', ['id' => $id]);
    }

    public function getConflictingAssignments($staffId, $departureDate, $returnDate, $excludeAssignmentId = null)
    {
        $endDate = $returnDate ?: $departureDate;

        $stmt = $this->connection->createQueryBuilder();
        $stmt->select('sa.id', 'sa.departure_id', 'sa.staff_id', 'd.departure_date', 'd.return_date', 't.name as tour_name', 'h.Hoten as staff_name')
            ->from('staff_assignments', 'sa')
            ->innerJoin('sa', 'departures', 'd', 'd.id = sa.departure_id')
            ->leftJoin('d', 'tours', 't', 't.id = d.tour_id')
            ->leftJoin('sa', 'hdv', 'h', 'h.HDV_id = sa.staff_id')
            ->where('sa.staff_id = :staff_id')
            ->andWhere('d.status != :cancelled')
            ->andWhere('sa.status != :rejected')
            ->andWhere('(d.departure_date <= :end_date AND COALESCE(d.return_date, d.departure_date) >= :start_date)')
            ->setParameter('staff_id', $staffId)
            ->setParameter('cancelled', 'cancelled')
            ->setParameter('rejected', 'rejected')
            ->setParameter('start_date', $departureDate)
            ->setParameter('end_date', $endDate);

        if ($excludeAssignmentId) {
            $stmt->andWhere('sa.id != :exclude_id');
            $stmt->setParameter('exclude_id', $excludeAssignmentId);
        }

        $stmt->orderBy('d.departure_date', 'ASC');

        return $stmt->fetchAllAssociative();
    }

    public function checkStaffAvailability($staffId, $departureDate, $returnDate, $excludeAssignmentId = null)
    {
        $conflicts = $this->getConflictingAssignments($staffId, $departureDate, $returnDate, $excludeAssignmentId);
        return count($conflicts) === 0;
    }

    public function getTotalAssignments()
    {
        $stmt = $this->connection->createQueryBuilder();
        $stmt->select('COUNT(id) as total')->from('staff_assignments');
        return (int) ($stmt->fetchAssociative()['total'] ?? 0);
    }

    public function getAvailableStaff($departureId)
    {
        $departureStmt = $this->connection->createQueryBuilder();
        $departureStmt->select('departure_date', 'return_date')
            ->from('departures')
            ->where('id = :id')
            ->setParameter('id', $departureId);
        $departure = $departureStmt->fetchAssociative();

        if (!$departure) {
            return [];
        }

        $assignedStmt = $this->connection->createQueryBuilder();
        $assignedStmt->select('staff_id')
            ->from('staff_assignments')
            ->where('departure_id = :departure_id')
            ->setParameter('departure_id', $departureId);
        $assignedIds = array_column($assignedStmt->fetchAllAssociative(), 'staff_id');

        $staffStmt = $this->connection->createQueryBuilder();
        $staffStmt->select('h.*')
            ->from('hdv', 'h')
            ->where('h.Status = :status')
            ->setParameter('status', 'active');

        if (!empty($assignedIds)) {
            $staffStmt->andWhere('h.HDV_id NOT IN (:assigned_ids)');
            $staffStmt->setParameter('assigned_ids', $assignedIds, \Doctrine\DBAL\ArrayParameterType::INTEGER);
        }

        $staffList = $staffStmt->fetchAllAssociative();

        $available = [];
        foreach ($staffList as $staff) {
            if ($this->checkStaffAvailability($staff['HDV_id'], $departure['departure_date'], $departure['return_date'])) {
                $available[] = $staff;
            }
        }

        return $available;
    }
}

