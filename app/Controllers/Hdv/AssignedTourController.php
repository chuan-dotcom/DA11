<?php

namespace App\Controllers\Hdv;

use App\Controller;
use App\Models\StaffAssignment;
use App\Models\Departure;
use App\Models\BookingGuest;
use App\Models\Staff;
use App\Support\Auth;

class AssignedTourController extends Controller
{
    private $modelAssignment;
    private $modelDeparture;
    private $modelGuest;
    private $modelStaff;

    public function __construct()
    {
        $this->modelAssignment = new StaffAssignment();
        $this->modelDeparture = new Departure();
        $this->modelGuest = new BookingGuest();
        $this->modelStaff = new Staff();
    }

    private function getActiveHdvId()
    {
        if (Auth::hasBoundHdv()) {
            $_SESSION['hdv_id'] = (int) (Auth::user()['hdv_id'] ?? 0);
            return $_SESSION['hdv_id'];
        }
        if (isset($_GET['hdv_id']) && (int)$_GET['hdv_id'] > 0) {
            $_SESSION['hdv_id'] = (int)$_GET['hdv_id'];
        }
        if (!isset($_SESSION['hdv_id'])) {
            $allStaff = $this->modelStaff->getAll();
            $_SESSION['hdv_id'] = !empty($allStaff) ? (int)$allStaff[0]['HDV_id'] : 1;
        }
        return $_SESSION['hdv_id'];
    }

    public function index()
    {
        $hdvId = $this->getActiveHdvId();
        $activeHdv = $this->modelStaff->findById($hdvId);
        $allHdv = Auth::canSwitchHdv() ? $this->modelStaff->getAll() : [$activeHdv];

        $db = (new \App\Model())->getConnection();

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

        $assignedTours = $db->fetchAllAssociative($sql, ['hdv_id' => $hdvId]);

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
