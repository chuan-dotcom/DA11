<?php

namespace App\Controllers\Hdv;

use App\Controller;
use App\Models\StaffAssignment;
use App\Models\Departure;
use App\Models\BookingGuest;
use App\Models\Staff;
use App\Support\Auth;
use App\Support\ResolvesActiveHdv;
use App\Models\Tour;
use App\Models\TourLog;

class TourInfoController extends Controller
{
    use ResolvesActiveHdv;

    private $modelAssignment;
    private $modelDeparture;
    private $modelGuest;
    private $modelTour;
    private $modelTourLog;

    public function __construct()
    {
        $this->modelAssignment = new StaffAssignment();
        $this->modelDeparture = new Departure();
        $this->modelGuest = new BookingGuest();
        $this->modelTour = new Tour();
        $this->modelTourLog = new TourLog();
    }

    public function index()
    {
        $hdvId = $this->resolveActiveHdvId();
        $activeHdv = $this->resolveActiveHdv();
        $allHdv = $this->resolveAllViewableHdv();

        $activeTab = $_GET['tab'] ?? 'danh-sach';
        $selectedDepartureId = isset($_GET['departure_id']) ? (int)$_GET['departure_id'] : null;

        $assignedTours = $this->modelAssignment->getByStaffIdWithDeparture($hdvId, true);

        // Categorize into Ongoing (Đang), Upcoming (Sẽ), Completed (Đã)
        $todayTimestamp = strtotime(date('Y-m-d'));
        $ongoingTours = [];
        $upcomingTours = [];
        $completedTours = [];

        foreach ($assignedTours as $item) {
            $startTimestamp = strtotime($item['departure_date']);
            $endTimestamp = strtotime($item['return_date'] ?: $item['departure_date']);
            $depStatus = $item['departure_status'] ?? null;

            if ($depStatus === 'completed') {
                $item['progress_status'] = 'da_tien_hanh';
                $completedTours[] = $item;
            } elseif ($depStatus === 'in_progress') {
                $item['progress_status'] = 'dang_tien_hanh';
                $ongoingTours[] = $item;
            } elseif ($depStatus === 'cancelled') {
                $item['progress_status'] = 'da_tien_hanh';
                $completedTours[] = $item;
            } elseif ($endTimestamp < $todayTimestamp) {
                $item['progress_status'] = 'da_tien_hanh';
                $completedTours[] = $item;
            } elseif ($startTimestamp <= $todayTimestamp && $endTimestamp >= $todayTimestamp) {
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
                        WHERE sa.departure_id = :departure_id AND sa.role = 'driver' AND sa.status NOT IN ('rejected','cancelled')
                        LIMIT 1
                    ";
                    $db = (new \App\Model())->getConnection();
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
