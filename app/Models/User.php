<?php

namespace App\Models;

use App\Model;
     
class User extends Model
{                             
    public function getAll()
    {
        $stmt = $this->connection->createQueryBuilder();
        $stmt->select('u.*', 'h.Hoten AS hdv_name')
            ->from('users', 'u')
            ->leftJoin('u', 'hdv', 'h', 'h.HDV_id = u.hdv_id')
            ->orderBy('u.id', 'DESC');

        return $stmt->fetchAllAssociative();
    }  

    public function findById($id)
    {
        $stmt = $this->connection->createQueryBuilder();
        $stmt->select('u.*', 'h.Hoten AS hdv_name')
            ->from('users', 'u')
            ->leftJoin('u', 'hdv', 'h', 'h.HDV_id = u.hdv_id')
            ->where('u.id = :id')
            ->setParameter('id', $id);

        return $stmt->fetchAssociative();
    }

    public function findByEmail($email)
    {
        $stmt = $this->connection->createQueryBuilder();
        $stmt->select('u.*', 'h.Hoten AS hdv_name')
            ->from('users', 'u')
            ->leftJoin('u', 'hdv', 'h', 'h.HDV_id = u.hdv_id')
            ->where('u.email = :email')
            ->setParameter('email', $email);

        return $stmt->fetchAssociative();
    }

    public function insert($data)
    {
        return $this->connection->insert('users', [
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => password_hash($data['password'], PASSWORD_DEFAULT),
            'phone' => $data['phone'] ?? null,
            'role' => $data['role'] ?? 'user',
            'hdv_id' => !empty($data['hdv_id']) ? (int) $data['hdv_id'] : null,
            'avatar' => $data['avatar'] ?? null,
            'status' => $data['status'] ?? 1,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }

    public function update($id, $data)
    {
        $updateData = [
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'role' => $data['role'] ?? 'user',
            'hdv_id' => !empty($data['hdv_id']) ? (int) $data['hdv_id'] : null,
            'status' => $data['status'] ?? 1,
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        if (!empty($data['password'])) {
            $updateData['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }

        if (isset($data['avatar'])) {
            $updateData['avatar'] = $data['avatar'];
        }

        return $this->connection->update('users', $updateData, ['id' => $id]);
    }

    public function delete($id)
    {
        $user = $this->findById($id);
        if ($user && !empty($user['avatar']) && file_exists($user['avatar'])) {
            unlink($user['avatar']);
        }
        return $this->connection->delete('users', ['id' => $id]);
    }

    public function deleteMultiple(array $ids)
    {
        $ids = array_values(array_filter(array_map('intval', $ids)));
        if (empty($ids)) {
            return 0;
        }

        $idList = implode(',', $ids);
        $users = $this->connection->fetchAllAssociative("SELECT id, avatar FROM users WHERE id IN ($idList)");
        foreach ($users as $user) {
            if (!empty($user['avatar']) && file_exists($user['avatar'])) {
                unlink($user['avatar']);
            }
        }

        return $this->connection->executeStatement("DELETE FROM users WHERE id IN ($idList)");
    }

    public function getTotalUsers()
    {
        $stmt = $this->connection->createQueryBuilder();
        $stmt->select('COUNT(id) as total_users')->from('users');

        return (int) ($stmt->fetchAssociative()['total_users'] ?? 0);
    }

    public function getTotalActiveUsers()
    {
        $stmt = $this->connection->createQueryBuilder();
        $stmt->select('COUNT(id) as total_active_users')
            ->from('users')
            ->where('status = :status')
            ->setParameter('status', 1);

        return (int) ($stmt->fetchAssociative()['total_active_users'] ?? 0);
    }

    public function getTotalAdmins()
    {
        $stmt = $this->connection->createQueryBuilder();
        $stmt->select('COUNT(id) as total_admins')
            ->from('users')
            ->where('role = :role')
            ->setParameter('role', 'admin');

        return (int) ($stmt->fetchAssociative()['total_admins'] ?? 0);
    }

    public function emailExists($email, $excludeId = null)
    {
        $stmt = $this->connection->createQueryBuilder();
        $stmt->select('COUNT(id) as count')
            ->from('users')
            ->where('email = :email')
            ->setParameter('email', $email);

        if ($excludeId !== null) {
            $stmt->andWhere('id != :id')
                ->setParameter('id', $excludeId);
        }

        return (int) ($stmt->fetchAssociative()['count'] ?? 0) > 0;
    }
}
