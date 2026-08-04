<?php
namespace App\Models;

use App\Model;

class Tour extends Model{
    public function getAll() {
        $stmt=$this->connection->createQueryBuilder();
        $stmt->select('t.*', 'tc.name as category_name')
            ->from('tours', 't')
            ->leftJoin('t', 'tour_categories', 'tc', 'tc.id = t.category_id') 
            ->orderBy('t.id', 'DESC');
        
        return $stmt->fetchAllAssociative();
    }

    public function findByid($id) {
        $stmt=$this->connection->createQueryBuilder();
        $stmt->select('t.*', 'tc.name as category_name')
            ->from('tours', 't')
            ->leftJoin('t', 'tour_categories', 'tc', 'tc.id = t.category_id')
            ->where('t.id = :id')
            ->setParameter('id', $id);
        
        return $stmt->fetchAssociative();
    }

    public function delete($id) {
        $tour = $this->findByid($id);
        if ($tour && $tour['image'] && file_exists($tour['image'])) {
            unlink($tour['image']);
        }
        return $this->connection->delete('tours', ['id'=>$id]);
    }

    public function insert($data) {
        return $this->connection->insert('tours',[
            'name' =>$data['name'],
            'category_id' =>$data['category_id'],
            'price' =>$data['price'],
            'duration' =>$data['duration'],
            'description' =>$data['description'],
            'image' =>$data['image'],
            'status' =>$data['status'],
        ]);
    }

    public function update($id, $data){
        return $this->connection->update('tours',[
            'name' =>$data['name'],
            'category_id' =>$data['category_id'],
            'price' =>$data['price'],
            'duration' =>$data['duration'],
            'description' =>$data['description'],
            'image' =>$data['image'],
            'status' =>$data['status'],
        ], ['id'=>$id]);
    }

    // Các hàm thống kê
    public function getTotalTours() {
        $stmt = $this->connection->createQueryBuilder();
        $stmt->select('COUNT(id) as total_tours')->from('tours');

        return (int) ($stmt->fetchAssociative()['total_tours'] ?? 0);
    }

    public function getTotalActiveTours() {
        $stmt = $this->connection->createQueryBuilder();
        $stmt->select('COUNT(id) as total_active_tours')
            ->from('tours')
            ->where('status = :status')
            ->setParameter('status', 1);

        return (int) ($stmt->fetchAssociative()['total_active_tours'] ?? 0);
    }

    public function getTotalHiddenTours() {
        $stmt = $this->connection->createQueryBuilder();
        $stmt->select('COUNT(id) as total_hidden_tours')
            ->from('tours')
            ->where('status = :status')
            ->setParameter('status', 0);

        return (int) ($stmt->fetchAssociative()['total_hidden_tours'] ?? 0);
    }

    public function getMostExpensiveTour() {
        $stmt = $this->connection->createQueryBuilder();
        $stmt->select('t.*', 'tc.name as category_name')
            ->from('tours', 't')
            ->leftJoin('t', 'tour_categories', 'tc', 'tc.id = t.category_id')
            ->orderBy('t.price', 'DESC')
            ->setMaxResults(1);

        return $stmt->fetchAssociative();
    }

    public function getCheapestTour() {
        $stmt = $this->connection->createQueryBuilder();
        $stmt->select('t.*', 'tc.name as category_name')
            ->from('tours', 't')
            ->leftJoin('t', 'tour_categories', 'tc', 'tc.id = t.category_id')
            ->orderBy('t.price', 'ASC')
            ->setMaxResults(1);

        return $stmt->fetchAssociative();
    }

    public function getTotalPrice() {
        $stmt = $this->connection->createQueryBuilder();
        $stmt->select('SUM(price) as total_price')->from('tours');
        return $stmt->fetchAssociative()['total_price'] ?? 0;
    }

    public function getToursByCategory() {
        $stmt = $this->connection->createQueryBuilder();
        $stmt->select('c.name as category_name', 'COUNT(t.id) as tour_count')
             ->from('tour_categories', 'c')
             ->leftJoin('c', 'tours', 't', 'c.id = t.category_id')
             ->groupBy('c.id');
        return $stmt->fetchAllAssociative();
    }

    public function getLatestTours($limit = 5) {
        $stmt = $this->connection->createQueryBuilder();
        $stmt->select('t.*', 'tc.name as category_name')
            ->from('tours', 't')
            ->leftJoin('t', 'tour_categories', 'tc', 'tc.id = t.category_id')
            ->orderBy('t.id', 'DESC')
            ->setMaxResults((int) $limit);

        return $stmt->fetchAllAssociative();
    }
}