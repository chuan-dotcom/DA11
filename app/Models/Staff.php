<?php

namespace App\Models;

use App\Model;
                
class Staff extends Model
{     
    public function getAll()    
    {
        $stmt = $this->connection->createQueryBuilder();
        $stmt->select('h.*')
            ->from('hdv', 'h')
            ->orderBy('h.HDV_id', 'DESC');

        return $stmt->fetchAllAssociative();
    }

    public function findById($id)
    {
        $stmt = $this->connection->createQueryBuilder();
        $stmt->select('h.*')
            ->from('hdv', 'h')
            ->where('h.HDV_id = :id')
            ->setParameter('id', $id);

        return $stmt->fetchAssociative();
    }

    public function insert($data)
    {
        return $this->connection->insert('hdv', [
            'Hoten'            => $data['Hoten'],
            'Ngaysinh'         => !empty($data['Ngaysinh']) ? $data['Ngaysinh'] : null,
            'Gioitinh'         => $data['Gioitinh'] ?? null,
            'Lienhe'           => $data['Lienhe'] ?? null,
            'Ngonngu'          => $data['Ngonngu'] ?? null,
            'Diachi'           => $data['Diachi'] ?? null,
            'chungchiHDV'      => $data['chungchiHDV'] ?? null,
            'Kinhnghiem'       => isset($data['Kinhnghiem']) && $data['Kinhnghiem'] !== '' ? (int)$data['Kinhnghiem'] : 0,
            'Ngaybatdaulam'    => !empty($data['Ngaybatdaulam']) ? $data['Ngaybatdaulam'] : null,
            'Trangthaisuckhoe' => $data['Trangthaisuckhoe'] ?? null,
            'Ghichunoibo'      => $data['Ghichunoibo'] ?? null,
            'Diemdanhgia'      => isset($data['Diemdanhgia']) && $data['Diemdanhgia'] !== '' ? (float)$data['Diemdanhgia'] : 0.0,
            'Nhanxetdanhgia'   => $data['Nhanxetdanhgia'] ?? null,
            'HDV_group_id'     => isset($data['HDV_group_id']) && $data['HDV_group_id'] !== '' ? (int)$data['HDV_group_id'] : null,
            'Status'           => $data['Status'] ?? 'active',
            'created_at'       => date('Y-m-d H:i:s'),
        ]);
    }

    public function update($id, $data)
    {
        $updateData = [
            'Hoten'            => $data['Hoten'],
            'Ngaysinh'         => !empty($data['Ngaysinh']) ? $data['Ngaysinh'] : null,
            'Gioitinh'         => $data['Gioitinh'] ?? null,
            'Lienhe'           => $data['Lienhe'] ?? null,
            'Ngonngu'          => $data['Ngonngu'] ?? null,
            'Diachi'           => $data['Diachi'] ?? null,
            'chungchiHDV'      => $data['chungchiHDV'] ?? null,
            'Kinhnghiem'       => isset($data['Kinhnghiem']) && $data['Kinhnghiem'] !== '' ? (int)$data['Kinhnghiem'] : 0,
            'Ngaybatdaulam'    => !empty($data['Ngaybatdaulam']) ? $data['Ngaybatdaulam'] : null,
            'Trangthaisuckhoe' => $data['Trangthaisuckhoe'] ?? null,
            'Ghichunoibo'      => $data['Ghichunoibo'] ?? null,
            'Diemdanhgia'      => isset($data['Diemdanhgia']) && $data['Diemdanhgia'] !== '' ? (float)$data['Diemdanhgia'] : 0.0,
            'Nhanxetdanhgia'   => $data['Nhanxetdanhgia'] ?? null,
            'HDV_group_id'     => isset($data['HDV_group_id']) && $data['HDV_group_id'] !== '' ? (int)$data['HDV_group_id'] : null,
            'Status'           => $data['Status'] ?? 'active',
            'updated_at'       => date('Y-m-d H:i:s'),
        ];

        return $this->connection->update('hdv', $updateData, ['HDV_id' => $id]);
    }

    public function delete($id)
    {
        return $this->connection->delete('hdv', ['HDV_id' => $id]);
    }

    public function getTotalStaff()
    {
        $stmt = $this->connection->createQueryBuilder();
        $stmt->select('COUNT(HDV_id) as total')->from('hdv');

        return (int) ($stmt->fetchAssociative()['total'] ?? 0);
    }
}
