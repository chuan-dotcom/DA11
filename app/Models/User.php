<?php

namespace App\Models;

use App\Model;

class User extends Model
{
    public function getAll()
    {
        $stmt = $this->connection->createQueryBuilder();
        $stmt->select('u.*')
            ->from('users', 'u')
            ->orderBy('u.id', 'DESC');

        return $stmt->fetchAllAssociative();
    }

    public function findById($id)
    {
        $stmt = $this->connection->createQueryBuilder();
        $stmt->select('u.*')
            ->from('users', 'u')
            ->where('u.id = :id')
            ->setParameter('id', $id);

        return $stmt->fetchAssociative();
    }

    public function findByEmail($email)
    {
        $stmt = $this->connection->createQueryBuilder();
        $stmt->select('u.*')
            ->from('users', 'u')
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
