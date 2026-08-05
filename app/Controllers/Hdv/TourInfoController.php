<?php

namespace App\Controllers\Hdv;

use App\Controller;
use App\Models\StaffAssignment;
use App\Models\Departure;
use App\Models\BookingGuest;
use App\Models\Staff;
use App\Support\Auth;
use App\Models\Tour;
use App\Models\TourLog;

class TourInfoController extends Controller
{
    private $modelAssignment;
    private $modelDeparture;
    private $modelGuest;
    private $modelStaff;
    private $modelTour;
    private $modelTourLog;

    public function __construct()
    {
        $this->modelAssignment = new StaffAssignment();
        $this->modelDeparture = new Departure();
        $this->modelGuest = new BookingGuest();
        $this->modelStaff = new Staff();
        $this->modelTour = new Tour();
        $this->modelTourLog = new TourLog();
    }

    private function getActiveHdvId()
    {
        if (Auth::isHdv()) {
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
        $allHdv = Auth::isAdmin() ? $this->modelStaff->getAll() : [$activeHdv];

        $activeTab = $_GET['tab'] ?? 'danh-sach';
        $selectedDepartureId = isset($_GET['departure_id']) ? (int)$_GET['departure_id'] : null;

        // Query assigned departures for this HDV
        $db = (new \App\Model())->getConnection();
        
        $sql = "
            SELECT 
                sa.id AS assignment_id,
                sa.role AS hdv_role,
                sa.status AS assignment_status,
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
                t.image AS tour_image,
                t.duration,
                tc.name AS category_name
            FROM staff_assignments sa
            INNER JOIN departures d ON d.id = sa.departure_id
            INNER JOIN tours t ON t.id = d.tour_id
            LEFT JOIN tour_categories tc ON tc.id = t.category_id
            WHERE sa.staff_id = :hdv_id
            ORDER BY d.departure_date DESC
        ";
        
        $assignedTours = $db->fetchAllAssociative($sql, ['hdv_id' => $hdvId]);

        // Categorize into Ongoing (Đang), Upcoming (Sẽ), Completed (Đã)
        $today = date('Y-m-d');
        $ongoingTours = [];
        $upcomingTours = [];
        $completedTours = [];

        foreach ($assignedTours as $item) {
            $startDate = $item['departure_date'];
            $endDate = $item['return_date'] ?: $startDate;

            if ($item['departure_status'] === 'completed' || $endDate < $today) {
                $item['progress_status'] = 'da_tien_hanh';
                $completedTours[] = $item;
            } elseif ($startDate <= $today && $endDate >= $today) {
                $item['progress_status'] = 'dang_tien_hanh';
                $ongoingTours[] = $item;
            } else {
                $item['progress_status'] = 'se_tien_hanh';
                $upcomingTours[] = $item;
            }
        }

        // If a tour detail is requested or defaulting to the first tour
        $currentTourDetail = null;
        $guests = [];
        $driverInfo = null;
        $tourLogs = [];

        if ($activeTab === 'chi-tiet' || $selectedDepartureId) {
            $activeTab = 'chi-tiet';

            if (!$selectedDepartureId && !empty($assignedTours)) {
                $selectedDepartureId = (int)$assignedTours[0]['departure_id'];
            }

            if ($selectedDepartureId) {
                foreach ($assignedTours as $t) {
                    if ((int)$t['departure_id'] === $selectedDepartureId) {
                        $currentTourDetail = $t;
                        break;
                    }
                }

                if (!$currentTourDetail && !empty($assignedTours)) {
                    $currentTourDetail = $assignedTours[0];
                    $selectedDepartureId = (int)$currentTourDetail['departure_id'];
                }

                if ($currentTourDetail) {
                    // Fetch guests for this departure using booking_guests table
                    $guests = $this->modelGuest->getByDepartureId($selectedDepartureId);

                    // Fetch driver assigned for this departure
                    $driverSql = "
                        SELECT h.*, sa.role 
                        FROM staff_assignments sa
                        INNER JOIN hdv h ON h.HDV_id = sa.staff_id
                        WHERE sa.departure_id = :departure_id AND sa.role = 'driver'
                        LIMIT 1
                    ";
                    $driverInfo = $db->fetchAssociative($driverSql, ['departure_id' => $selectedDepartureId]);

                    // Fetch timeline activity logs for this tour departure from the DB model
                    $tourLogs = $this->modelTourLog->getByDepartureId($selectedDepartureId);
                }
            }
        }

        $title = 'Thông tin tour';

        return view('hdv.tour-info.index', compact(
            'title',
            'hdvId',
            'activeHdv',
            'allHdv',
            'activeTab',
            'assignedTours',
            'ongoingTours',
            'upcomingTours',
            'completedTours',
            'selectedDepartureId',
            'currentTourDetail',
            'guests',
            'driverInfo',
            'tourLogs'
        ));
    }
}
