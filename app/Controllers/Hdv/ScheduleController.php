<?php

namespace App\Controllers\Hdv;

use App\Controller;
use App\Models\Staff;
use App\Models\StaffAssignment;
use App\Support\Auth;

class ScheduleController extends Controller
{
    private $modelStaff;
    private $modelAssignment;

    public function __construct()
    {
        $this->modelStaff = new Staff();
        $this->modelAssignment = new StaffAssignment();
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

        // Fetch schedule of tours for this HDV ordered by departure date
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
                d.status AS departure_status,
                t.id AS tour_id,
                t.name AS tour_name,
                tc.name AS category_name,
                t.duration,
                t.description,
                (
                    SELECT COUNT(g.id) 
                    FROM booking_guests g 
                    INNER JOIN bookings b ON b.id = g.booking_id 
                    WHERE b.departure_id = d.id
                ) AS total_guests
            FROM staff_assignments sa
            INNER JOIN departures d ON d.id = sa.departure_id
            INNER JOIN tours t ON t.id = d.tour_id
            LEFT JOIN tour_categories tc ON tc.id = t.category_id
            WHERE sa.staff_id = :hdv_id
            ORDER BY d.departure_date ASC
        ";

        $schedules = $db->fetchAllAssociative($sql, ['hdv_id' => $hdvId]);

        $title = 'Lịch trình tour';

        return view('hdv.schedule.index', compact(
            'title',
            'hdvId',
            'activeHdv',
            'allHdv',
            'schedules'
        ));
    }
}
