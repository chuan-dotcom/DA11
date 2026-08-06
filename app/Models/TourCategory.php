<?php
namespace App\Models;

use App\Model;
    
class TourCategory extends Model{
    public function getAll() {
        $stmt=$this->connection->createQueryBuilder();
        $stmt->select('*')
            ->from('tour_categories');
        
        return $stmt->fetchAllAssociative();
    }    

    public function findByid($id) {
        $stmt=$this->connection->createQueryBuilder();
        $stmt->select('*')
            ->from('tour_categories')
            ->where('id = :id')
            ->setParameter('id', $id);
        
        return $stmt->fetchAssociative();
    }

    public function delete($id) {
        return $this->connection->delete('tour_categories', ['id'=>$id]);
    }

    public function countToursByCategory($id) {
        $stmt = $this->connection->createQueryBuilder();
        $stmt->select('COUNT(id) AS total')
            ->from('tours')
            ->where('category_id = :id')
            ->setParameter('id', $id);

        return (int) ($stmt->fetchAssociative()['total'] ?? 0);
    }

    public function insert($data) {
        return $this->connection->insert('tour_categories',[
            'name' =>$data['name'],
            'description' =>$data['description'],
        ]);
    }

    public function update($id, $data){
        return $this->connection->update('tour_categories',[
            'name' =>$data['name'],
            'description' =>$data['description'],
        ], ['id'=>$id]);
    }

    public function getTotalCategories() {
        return count($this->getAll());
    }
}
