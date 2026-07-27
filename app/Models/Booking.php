<?php

namespace App\Models;

use App\Model;

class Booking extends Model
{
    /**
     * Lấy danh sách tất cả Booking
     */
    public function getAll()
    {
        $stmt = $this->connection->createQueryBuilder();

        $stmt->select(
                'b.*',
                't.name AS tour_name'
            )
            ->from('bookings', 'b')
            ->leftJoin('b', 'tours', 't', 'b.tour_id = t.id')
            ->orderBy('b.id', 'DESC');

        return $stmt->fetchAllAssociative();
    }

    /**
     * Lấy thông tin 1 Booking
     */
    public function findById($id)
    {
        $stmt = $this->connection->createQueryBuilder();

        $stmt->select(
                'b.*',
                't.name AS tour_name',
                't.price'
            )
            ->from('bookings', 'b')
            ->leftJoin('b', 'tours', 't', 'b.tour_id = t.id')
            ->where('b.id = :id')
            ->setParameter('id', $id);

        return $stmt->fetchAssociative();
    }

    /**
     * Lấy thông tin Tour
     * Dùng để lấy giá Tour khi đặt
     */
    public function getTour($id)
    {
        $stmt = $this->connection->createQueryBuilder();

        $stmt->select('t.*')
            ->from('tours', 't')
            ->where('t.id = :id')
            ->setParameter('id', $id);

        return $stmt->fetchAssociative() ?: null;
    }

    /**
     * Thêm Booking
     */
    public function insert($data)
    {
        return $this->connection->insert('bookings', [

            'tour_id'          => $data['tour_id'],

            'customer_name'    => $data['customer_name'],

            'customer_email'   => $data['customer_email'],

            'customer_phone'   => $data['customer_phone'],

            'num_people'       => $data['num_people'],

            'total_price'      => $data['total_price'],

            'booking_date'     => $data['booking_date'],

            'status'           => $data['status'],

            'note'             => $data['note']

        ]);
    }

    /**
     * Cập nhật Booking
     */
    public function update($id, $data)
    {
        return $this->connection->update('bookings', [

            'tour_id'          => $data['tour_id'],

            'customer_name'    => $data['customer_name'],

            'customer_email'   => $data['customer_email'],

            'customer_phone'   => $data['customer_phone'],

            'num_people'       => $data['num_people'],

            'total_price'      => $data['total_price'],

            'booking_date'     => $data['booking_date'],

            'status'           => $data['status'],

            'note'             => $data['note']

        ], [
            'id' => $id
        ]);
    }

    /**
     * Xóa Booking
     */
    public function delete($id)
    {
        return $this->connection->delete(
            'bookings',
            ['id' => $id]
        );
    }

    /**
     * Tổng booking
     */
    public function getTotalBookings()
    {
        $stmt = $this->connection->createQueryBuilder();

        $stmt->select('COUNT(b.id) AS total')
            ->from('bookings', 'b');

        $row = $stmt->fetchAssociative();

        return (int) ($row['total'] ?? 0);
    }

    /**
     * Booking chờ xác nhận
     */
    public function getTotalPendingBookings()
    {
        $stmt = $this->connection->createQueryBuilder();

        $stmt->select('COUNT(b.id) AS total')
            ->from('bookings', 'b')
            ->where('b.status = :status')
            ->setParameter('status', 0);

        $row = $stmt->fetchAssociative();

        return (int) ($row['total'] ?? 0);
    }

    /**
     * Booking đã xác nhận
     */
    public function getTotalCompletedBookings()
    {
        $stmt = $this->connection->createQueryBuilder();

        $stmt->select('COUNT(b.id) AS total')
            ->from('bookings', 'b')
            ->where('b.status = :status')
            ->setParameter('status', 1);

        $row = $stmt->fetchAssociative();

        return (int) ($row['total'] ?? 0);
    }

    /**
     * Doanh thu
     */
    public function getTotalRevenue()
    {
        $stmt = $this->connection->createQueryBuilder();

        $stmt->select('SUM(b.total_price) AS revenue')
            ->from('bookings', 'b')
            ->where('b.status = :status')
            ->setParameter('status', 1);

        $row = $stmt->fetchAssociative();

        return $row['revenue'] !== null ? (float) $row['revenue'] : 0;
    }

    /**
     * Booking theo ngày
     */
    public function getDailyBookingCounts($month, $year)
    {
        $sql = "
        SELECT DAY(booking_date) day,
               COUNT(*) total
        FROM bookings
        WHERE MONTH(booking_date)=?
          AND YEAR(booking_date)=?
        GROUP BY DAY(booking_date)
    ";

        $rows = $this->connection->fetchAllAssociative($sql, [$month,$year]);

        $result=[];

        foreach($rows as $row){
            $result[(int)$row['day']] = (int)$row['total'];
        }

        return $result;
    }

    /**
     * Doanh thu theo ngày
     */
    public function getDailyRevenue($month,$year)
    {
        $sql="
        SELECT DAY(booking_date) day,
               SUM(total_price) revenue
        FROM bookings
        WHERE status=1
          AND MONTH(booking_date)=?
          AND YEAR(booking_date)=?
        GROUP BY DAY(booking_date)
    ";

        $rows=$this->connection->fetchAllAssociative($sql,[$month,$year]);

        $result=[];

        foreach($rows as $row){
            $result[(int)$row['day']] = $row['revenue'] !== null ? (float)$row['revenue'] : 0;
        }

        return $result;
    }

    /**
     * Booking hoàn thành
     */
    public function getCompletedBookingsWithTour()
    {
        $sql="
        SELECT
            b.*,
            t.name tour_name,
            tc.name category_name
        FROM bookings b
        JOIN tours t
            ON b.tour_id=t.id
        JOIN tour_categories tc
            ON t.category_id=tc.id
        WHERE b.status=1
        ORDER BY b.booking_date DESC
    ";

        return $this->connection->fetchAllAssociative($sql);
    }

    /**
     * Doanh thu theo tour
     */
    public function getRevenueByTour()
    {
        $sql="
        SELECT
            t.id,
            t.name,
            COUNT(b.id) total_booking,
            COALESCE(SUM(b.total_price),0) AS revenue
        FROM tours t
        LEFT JOIN bookings b
            ON t.id=b.tour_id
            AND b.status=1
        GROUP BY t.id
        ORDER BY revenue DESC
    ";

        return $this->connection->fetchAllAssociative($sql);
    }

    /**
     * Lấy các booking mới nhất
     */
    public function getLatestBookings($limit = 5)
    {
        $stmt = $this->connection->createQueryBuilder();

        $stmt->select(
                'b.*',
                't.name AS tour_name'
            )
            ->from('bookings', 'b')
            ->leftJoin('b', 'tours', 't', 'b.tour_id=t.id')
            ->orderBy('b.created_at', 'DESC')
            ->setMaxResults($limit);

        return $stmt->fetchAllAssociative();
    }

    /**
     * Trả về id của bản ghi vừa chèn (nếu driver hỗ trợ)
     */
    public function getLastInsertId()
    {
        try {
            return (int) $this->connection->lastInsertId();
        } catch (\Throwable $e) {
            return 0;
        }
    }

    /**
     * Lấy booking theo email khách hàng
     */
    public function getByCustomerEmail($email)
    {
        $stmt = $this->connection->createQueryBuilder();
        $stmt->select('b.*')
            ->from('bookings', 'b')
            ->where('b.customer_email = :email')
            ->setParameter('email', $email)
            ->orderBy('b.id', 'DESC');

        return $stmt->fetchAllAssociative();
    }

    /**
     * Lấy danh sách booking (người tham gia) theo tour ID
     */
    public function getByTourId($tourId)
    {
        $stmt = $this->connection->createQueryBuilder();
        $stmt->select(
                'b.*',
                't.name AS tour_name'
            )
            ->from('bookings', 'b')
            ->leftJoin('b', 'tours', 't', 'b.tour_id = t.id')
            ->where('b.tour_id = :tourId')
            ->setParameter('tourId', $tourId)
            ->orderBy('b.booking_date', 'DESC');

        return $stmt->fetchAllAssociative();
    }

    /**
     * Thống kê số liệu người tham gia theo tour ID
     */
    public function getTourParticipantsStats($tourId)
    {
        $sql = "
            SELECT
                COUNT(b.id) AS total_bookings,
                COALESCE(SUM(b.num_people), 0) AS total_people,
                COALESCE(SUM(CASE WHEN b.status = 1 THEN b.num_people ELSE 0 END), 0) AS confirmed_people,
                COALESCE(SUM(CASE WHEN b.status = 0 THEN b.num_people ELSE 0 END), 0) AS pending_people,
                COALESCE(SUM(CASE WHEN b.status = 1 THEN b.total_price ELSE 0 END), 0) AS confirmed_revenue,
                COALESCE(AVG(b.num_people), 0) AS avg_people_per_booking
            FROM bookings b
            WHERE b.tour_id = ?
        ";

        return $this->connection->fetchAssociative($sql, [$tourId]);
    }

}
