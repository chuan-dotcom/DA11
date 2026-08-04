<?php
   
namespace App\Models;

use App\Model;

class Booking extends Model
{
    public function __construct()
    {
        parent::__construct();
        $this->ensureGuestGroupColumns();
    }

    private function ensureGuestGroupColumns()
    {
        $columns = [
            'departure_id' => "ALTER TABLE bookings ADD COLUMN departure_id INT NULL AFTER tour_id",
            'check_in_status' => "ALTER TABLE bookings ADD COLUMN check_in_status TINYINT(1) NOT NULL DEFAULT 0 AFTER status",
            'checked_in_at' => "ALTER TABLE bookings ADD COLUMN checked_in_at DATETIME NULL AFTER check_in_status",
        ];

        foreach ($columns as $columnName => $sql) {
            try {
                $column = $this->connection->fetchAssociative(
                    'SHOW COLUMNS FROM bookings LIKE ?',
                    [$columnName]
                );

                if (!$column) {
                    $this->connection->executeStatement($sql);
                }
            } catch (\Throwable $e) {
            }
        }

        try {
            $index = $this->connection->fetchAssociative(
                "SHOW INDEX FROM bookings WHERE Key_name = 'idx_bookings_departure_id'"
            );

            if (!$index) {
                $this->connection->executeStatement(
                    'ALTER TABLE bookings ADD INDEX idx_bookings_departure_id (departure_id)'
                );
            }
        } catch (\Throwable $e) {
        }
    }

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
        $sql = "
        SELECT
            t.id AS tour_id,
            t.name AS tour_name,
            t.duration,
            t.price AS unit_price,
            t.status,
            tc.name AS category_name,
            COUNT(b.id) AS booking_count,
            COALESCE(SUM(b.total_price), 0) AS revenue
        FROM tours t
        LEFT JOIN tour_categories tc
            ON t.category_id = tc.id
        LEFT JOIN bookings b
            ON t.id = b.tour_id
            AND b.status = 1
        GROUP BY t.id, t.name, t.duration, t.price, t.status, tc.name
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

    /**
     * Danh sách booking đã được gắn vào một chuyến khởi hành
     */
    public function getByDepartureId($departureId)
    {
        $stmt = $this->connection->createQueryBuilder();

        $stmt->select(
                'b.*',
                't.name AS tour_name',
                'd.departure_date',
                'd.return_date',
                'd.meeting_point',
                'd.meeting_time',
                'd.status AS departure_status'
            )
            ->from('bookings', 'b')
            ->leftJoin('b', 'tours', 't', 'b.tour_id = t.id')
            ->leftJoin('b', 'departures', 'd', 'b.departure_id = d.id')
            ->where('b.departure_id = :departureId')
            ->setParameter('departureId', $departureId)
            ->orderBy('b.check_in_status', 'DESC')
            ->addOrderBy('b.checked_in_at', 'DESC')
            ->addOrderBy('b.id', 'DESC');

        return $stmt->fetchAllAssociative();
    }

    /**
     * Danh sách booking có thể gắn vào đoàn của chuyến khởi hành
     */
    public function getAvailableForDeparture($tourId)
    {
        $stmt = $this->connection->createQueryBuilder();

        $stmt->select(
                'b.*',
                't.name AS tour_name'
            )
            ->from('bookings', 'b')
            ->leftJoin('b', 'tours', 't', 'b.tour_id = t.id')
            ->where('b.tour_id = :tourId')
            ->andWhere('b.status = :status')
            ->andWhere('b.departure_id IS NULL')
            ->setParameter('tourId', $tourId)
            ->setParameter('status', 1)
            ->orderBy('b.booking_date', 'DESC')
            ->addOrderBy('b.id', 'DESC');

        return $stmt->fetchAllAssociative();
    }

    /**
     * Thống kê đoàn khách theo chuyến khởi hành
     */
    public function getAssignedStatsByDepartureId($departureId)
    {
        try {
            $sql = "
                SELECT
                    COUNT(DISTINCT b.id) AS total_bookings,
                    CASE
                        WHEN COUNT(g.id) > 0 THEN COUNT(g.id)
                        ELSE COALESCE(SUM(b.num_people), 0)
                    END AS total_people,
                    CASE
                        WHEN COUNT(g.id) > 0 THEN COALESCE(SUM(CASE WHEN g.check_in_status = 1 THEN 1 ELSE 0 END), 0)
                        ELSE COALESCE(SUM(CASE WHEN b.check_in_status = 1 THEN b.num_people ELSE 0 END), 0)
                    END AS checked_in_people,
                    CASE
                        WHEN COUNT(g.id) > 0 THEN COALESCE(SUM(CASE WHEN g.check_in_status = 0 THEN 1 ELSE 0 END), 0)
                        ELSE COALESCE(SUM(CASE WHEN b.check_in_status = 0 THEN b.num_people ELSE 0 END), 0)
                    END AS pending_check_in_people
                FROM bookings b
                LEFT JOIN booking_guests g
                    ON g.booking_id = b.id
                WHERE b.departure_id = ?
            ";

            return $this->connection->fetchAssociative($sql, [(int) $departureId]);
        } catch (\Throwable $e) {
            $sql = "
                SELECT
                    COUNT(b.id) AS total_bookings,
                    COALESCE(SUM(b.num_people), 0) AS total_people,
                    COALESCE(SUM(CASE WHEN b.check_in_status = 1 THEN b.num_people ELSE 0 END), 0) AS checked_in_people,
                    COALESCE(SUM(CASE WHEN b.check_in_status = 0 THEN b.num_people ELSE 0 END), 0) AS pending_check_in_people
                FROM bookings b
                WHERE b.departure_id = ?
            ";

            return $this->connection->fetchAssociative($sql, [(int) $departureId]);
        }
    }

    /**
     * Tổng số khách đã gắn vào một đoàn
     */
    public function getAssignedPeopleCount($departureId)
    {
        $sql = "
            SELECT COALESCE(SUM(num_people), 0) AS total_people
            FROM bookings
            WHERE departure_id = ?
        ";

        $row = $this->connection->fetchAssociative($sql, [$departureId]);

        return (int) ($row['total_people'] ?? 0);
    }

    /**
     * Gắn booking vào chuyến khởi hành
     */
    public function assignToDeparture($bookingId, $departureId)
    {
        return $this->connection->update('bookings', [
            'departure_id' => $departureId,
            'check_in_status' => 0,
            'checked_in_at' => null,
        ], [
            'id' => $bookingId,
        ]);
    }

    /**
     * Bỏ booking khỏi chuyến khởi hành
     */
    public function removeFromDeparture($bookingId, $departureId)
    {
        return $this->connection->update('bookings', [
            'departure_id' => null,
            'check_in_status' => 0,
            'checked_in_at' => null,
        ], [
            'id' => $bookingId,
            'departure_id' => $departureId,
        ]);
    }

    /**
     * Đánh dấu check-in
     */
    public function markCheckedIn($bookingId, $departureId)
    {
        return $this->connection->update('bookings', [
            'check_in_status' => 1,
            'checked_in_at' => date('Y-m-d H:i:s'),
        ], [
            'id' => $bookingId,
            'departure_id' => $departureId,
        ]);
    }

    /**
     * Hủy trạng thái check-in
     */
    public function cancelCheckedIn($bookingId, $departureId)
    {
        return $this->connection->update('bookings', [
            'check_in_status' => 0,
            'checked_in_at' => null,
        ], [
            'id' => $bookingId,
            'departure_id' => $departureId,
        ]);
    }

}
