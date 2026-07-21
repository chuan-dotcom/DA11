<?php
namespace App\Models;

use App\Model;

class Booking extends Model {

    /**
     * Tổng số khách hàng (số booking không trùng email).
     */
    public function getTotalCustomers(): int {
        $stmt = $this->connection->createQueryBuilder();
        $stmt->select('COUNT(DISTINCT customer_email) as total')
            ->from('bookings');
        return (int) ($stmt->fetchAssociative()['total'] ?? 0);
    }

    /**
     * Số tour đang mở (status = 0: chờ xử lý).
     */
    public function getTotalPendingBookings(): int {
        $stmt = $this->connection->createQueryBuilder();
        $stmt->select('COUNT(id) as total')
            ->from('bookings')
            ->where('status = :status')
            ->setParameter('status', 0);
        return (int) ($stmt->fetchAssociative()['total'] ?? 0);
    }

    /**
     * Tổng doanh thu từ các booking đã hoàn thành (status = 1).
     */
    public function getTotalRevenue(): int {
        $stmt = $this->connection->createQueryBuilder();
        $stmt->select('SUM(total_price) as revenue')
            ->from('bookings')
            ->where('status = :status')
            ->setParameter('status', 1);
        return (int) ($stmt->fetchAssociative()['revenue'] ?? 0);
    }

    /**
     * Số booking đã hoàn thành.
     */
    public function getTotalCompletedBookings(): int {
        $stmt = $this->connection->createQueryBuilder();
        $stmt->select('COUNT(id) as total')
            ->from('bookings')
            ->where('status = :status')
            ->setParameter('status', 1);
        return (int) ($stmt->fetchAssociative()['total'] ?? 0);
    }

    /**
     * Tổng số booking.
     */
    public function getTotalBookings(): int {
        $stmt = $this->connection->createQueryBuilder();
        $stmt->select('COUNT(id) as total')->from('bookings');
        return (int) ($stmt->fetchAssociative()['total'] ?? 0);
    }

    /**
     * Số booking theo từng ngày trong tháng/năm cho biểu đồ đường.
     * Trả về mảng [ngày => số_booking].
     */
    public function getDailyBookingCounts(int $month, int $year): array {
        $stmt = $this->connection->createQueryBuilder();
        $stmt->select('DAY(booking_date) as day', 'COUNT(id) as cnt')
            ->from('bookings')
            ->where('MONTH(booking_date) = :month')
            ->andWhere('YEAR(booking_date) = :year')
            ->setParameter('month', $month)
            ->setParameter('year', $year)
            ->groupBy('DAY(booking_date)');

        $rows = $stmt->fetchAllAssociative();
        $map = [];
        foreach ($rows as $row) {
            $map[(int) $row['day']] = (int) $row['cnt'];
        }

        return $map;
    }

    /**
     * Doanh thu theo từng ngày trong tháng/năm cho biểu đồ cột.
     * Chỉ tính booking đã hoàn thành (status = 1).
     */
    public function getDailyRevenue(int $month, int $year): array {
        $stmt = $this->connection->createQueryBuilder();
        $stmt->select('DAY(booking_date) as day', 'SUM(total_price) as revenue')
            ->from('bookings')
            ->where('MONTH(booking_date) = :month')
            ->andWhere('YEAR(booking_date) = :year')
            ->andWhere('status = 1')
            ->setParameter('month', $month)
            ->setParameter('year', $year)
            ->groupBy('DAY(booking_date)');

        $rows = $stmt->fetchAllAssociative();
        $map = [];
        foreach ($rows as $row) {
            $map[(int) $row['day']] = (int) $row['revenue'];
        }

        return $map;
    }

    /**
     * Danh sách booking hoàn thành kèm tên tour và danh mục.
     */
    public function getCompletedBookingsWithTour(): array {
        $stmt = $this->connection->createQueryBuilder();
        $stmt->select(
                'b.*',
                't.name as tour_name',
                't.price as tour_price',
                'tc.name as category_name'
            )
            ->from('bookings', 'b')
            ->leftJoin('b', 'tours', 't', 't.id = b.tour_id')
            ->leftJoin('t', 'tour_categories', 'tc', 'tc.id = t.category_id')
            ->where('b.status = 1')
            ->orderBy('b.booking_date', 'DESC');
        return $stmt->fetchAllAssociative();
    }

    /**
     * Thống kê doanh thu theo từng tour.
     * status, duration, price lấy từ bảng tours (không bị GROUP BY ảnh hưởng vì GROUP BY t.id).
     */
    public function getRevenueByTour(): array {
        $stmt = $this->connection->createQueryBuilder();
        $stmt->select(
                't.id as tour_id',
                't.name as tour_name',
                't.duration',
                't.status',
                't.price as unit_price',
                'tc.name as category_name',
                'COUNT(b.id) as booking_count',
                'COALESCE(SUM(b.total_price), 0) as revenue'
            )
            ->from('tours', 't')
            ->leftJoin('t', 'tour_categories', 'tc', 't.category_id = tc.id')
            ->leftJoin('t', 'bookings', 'b', 'b.tour_id = t.id AND b.status = 1')
            ->groupBy('t.id', 't.name', 't.duration', 't.status', 't.price', 'tc.name')
            ->orderBy('revenue', 'DESC');
        return $stmt->fetchAllAssociative();
    }
}
