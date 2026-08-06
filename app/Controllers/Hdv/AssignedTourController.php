<?php

namespace App\Controllers\Hdv;

use App\Controller;
use App\Models\StaffAssignment;
use App\Models\Departure;
use App\Models\BookingGuest;
use App\Models\Staff;
use App\Support\Auth;
use App\Support\ResolvesActiveHdv;

class AssignedTourController extends Controller
{
    use ResolvesActiveHdv;

    private $modelAssignment;
    private $modelDeparture;
    private $modelGuest;

    public function __construct()
    {
        $this->modelAssignment = new StaffAssignment();
        $this->modelDeparture = new Departure();
        $this->modelGuest = new BookingGuest();
    }

    public function index()
    {
        $hdvId = $this->resolveActiveHdvId();
        $activeHdv = $this->resolveActiveHdv();
        $allHdv = $this->resolveAllViewableHdv();

        $assignedTours = $this->modelAssignment->getByStaffIdWithDeparture($hdvId, true);

        $db = (new \App\Model())->getConnection();
        foreach ($assignedTours as $idx => $t) {
            $depId = (int) $t['departure_id'];

<<<<<<< HEAD
        // Get detailed assignments matching Quản lý đoàn khách format (theo đoàn departure, k theo booking)
        $sql = "
            SELECT 
                sa.id AS assignment_id,
                sa.role AS hdv_role,
                sa.notes AS assignment_notes,
                d.id AS departure_id,
                d.group_name,
                d.departure_date,
                d.return_date,
                d.meeting_point,
                d.meeting_time,
                d.vehicle,
                d.max_participants,
                d.status AS departure_status,
                t.id AS tour_id,
                t.name AS tour_name,
                tc.name AS category_name,
                COALESCE(
                    (SELECT SUM(b.num_people) FROM bookings b 
                     WHERE b.departure_id = d.id AND b.status = 1),
                    0
                ) AS assigned_people,
                (
                    SELECT COUNT(g.id) FROM booking_guests g 
                    INNER JOIN bookings b ON b.id = g.booking_id 
                    WHERE b.departure_id = d.id
                ) AS total_guests,
                (
                    SELECT COUNT(g.id) FROM booking_guests g 
                    INNER JOIN bookings b ON b.id = g.booking_id 
                    WHERE b.departure_id = d.id AND g.check_in_status = 1
                ) AS checked_in_guests,
                (
                    SELECT COUNT(b.id) FROM bookings b 
                    WHERE b.departure_id = d.id
                ) AS total_bookings
            FROM staff_assignments sa
            INNER JOIN departures d ON d.id = sa.departure_id
            INNER JOIN tours t ON t.id = d.tour_id
            LEFT JOIN tour_categories tc ON tc.id = t.category_id
            WHERE sa.staff_id = :hdv_id
            GROUP BY sa.id, d.id, t.id, tc.id
            ORDER BY d.departure_date DESC
        ";
=======
            $primarySql = "SELECT b.id, b.customer_name FROM bookings b WHERE b.departure_id = :depId ORDER BY b.id ASC LIMIT 1";
            $primary = $db->fetchAssociative($primarySql, ['depId' => $depId]);
            $assignedTours[$idx]['primary_booking_id'] = $primary['id'] ?? null;
            $assignedTours[$idx]['primary_customer_name'] = $primary['customer_name'] ?? null;
>>>>>>> aa059c0a460dbe3ab4b1a8320f08f6d7fe5b043c

            $countSql = "SELECT COUNT(g.id) as c FROM booking_guests g INNER JOIN bookings b ON b.id = g.booking_id WHERE b.departure_id = :depId";
            $countRow = $db->fetchAssociative($countSql, ['depId' => $depId]);
            $assignedTours[$idx]['total_guests'] = (int) ($countRow['c'] ?? 0);
        }

        // If specific detail requested
        $selectedDepartureId = isset($_GET['departure_id']) ? (int)$_GET['departure_id'] : null;
        $selectedTour = null;
        $guests = [];

        if ($selectedDepartureId) {
            foreach ($assignedTours as $tour) {
                if ((int)$tour['departure_id'] === $selectedDepartureId) {
                    $selectedTour = $tour;
                    break;
                }
            }

            if ($selectedTour) {
                $guests = $this->modelGuest->getByDepartureId($selectedDepartureId);
            }
        }

        $title = 'Tour được phân công';

        return view('hdv.assigned-tours.index', compact(
            'title',
            'hdvId',
            'activeHdv',
            'allHdv',
            'assignedTours',
            'selectedDepartureId',
            'selectedTour',
            'guests'
        ));
    }

    public function toggleCheckIn()
    {
        $guestId = isset($_POST['guest_id']) ? (int)$_POST['guest_id'] : 0;
        $departureId = isset($_POST['departure_id']) ? (int)$_POST['departure_id'] : 0;

        if ($guestId > 0) {
            $guest = $this->modelGuest->findById($guestId);
            if ($guest) {
                if ($guest['check_in_status'] == 1) {
                    $this->modelGuest->cancelCheckedIn($guestId);
                    setFlash('success', 'Đã hủy check-in cho khách hàng ' . $guest['full_name']);
                } else {
                    $this->modelGuest->markCheckedIn($guestId);
                    setFlash('success', 'Đã check-in thành công cho ' . $guest['full_name']);
                }
            }
        }

        $redirect = 'hdv/tour-phan-cong';
        if ($departureId) {
            $redirect .= '?departure_id=' . $departureId;
        }
        return redirect($redirect);
    }
}
