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
use Rakit\Validation\Validator;

class TourInfoController extends Controller
{
    private $modelAssignment;
    private $modelDeparture;
    private $modelGuest;
    private $modelStaff;
    private $modelTour;
    private $modelTourLog;
    private $validator;

    public function __construct()
    {
        $this->modelAssignment = new StaffAssignment();
        $this->modelDeparture = new Departure();
        $this->modelGuest = new BookingGuest();
        $this->modelStaff = new Staff();
        $this->modelTour = new Tour();
        $this->modelTourLog = new TourLog();
        $this->validator = new Validator();
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
        $todayTimestamp = strtotime(date('Y-m-d'));
        $ongoingTours = [];
        $upcomingTours = [];
        $completedTours = [];

        foreach ($assignedTours as $item) {
            $startTimestamp = strtotime($item['departure_date']);
            $endTimestamp = strtotime($item['return_date'] ?: $item['departure_date']);

            if ($endTimestamp < $todayTimestamp) {
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
        $importantAlerts = [];

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

                    $pendingCheckIn = count(array_filter($guests, function ($guest) {
                        return (int) ($guest['check_in_status'] ?? 0) !== 1;
                    }));
                    if ($pendingCheckIn > 0) {
                        $importantAlerts[] = [
                            'type' => 'warning',
                            'icon' => 'bi-person-x-fill',
                            'title' => $pendingCheckIn . ' khách chưa check-in',
                            'message' => 'Kiểm tra danh sách đoàn khách và thực hiện điểm danh trước khi khởi hành.',
                        ];
                    }

                    if (empty($tourLogs)) {
                        $importantAlerts[] = [
                            'type' => 'info',
                            'icon' => 'bi-clock-history',
                            'title' => 'Chưa có hoạt động timeline',
                            'message' => 'Thêm hoạt động đầu tiên để theo dõi tiến độ thực tế của chuyến tour.',
                        ];
                    }

                    $pendingServices = $db->fetchAllAssociative(
                        'SELECT service_types FROM services WHERE departure_id = :departure_id AND status = 0',
                        ['departure_id' => $selectedDepartureId]
                    );
                    if (!empty($pendingServices)) {
                        $serviceNames = array_filter(array_unique(array_column($pendingServices, 'service_types')));
                        $importantAlerts[] = [
                            'type' => 'warning',
                            'icon' => 'bi-exclamation-circle-fill',
                            'title' => count($pendingServices) . ' dịch vụ chưa xác nhận',
                            'message' => !empty($serviceNames)
                                ? 'Cần xác nhận: ' . implode(', ', $serviceNames) . '.'
                                : 'Kiểm tra và xác nhận các dịch vụ đã đặt cho đoàn.',
                        ];
                    }
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
            'tourLogs',
            'importantAlerts'
        ));
    }

    public function storeTourLog()
    {
        $hdvId = $this->getActiveHdvId();
        $data = $this->tourLogData();
        $departure = $this->assignedDeparture($hdvId, (int) $data['departure_id']);

        if (!$departure) {
            setFlash('error', 'Bạn chỉ có thể cập nhật lịch trình của chuyến được phân công.');
            return redirect('hdv/dashboard?tab=chi-tiet');
        }

        $errors = $this->validate($this->validator, $data, $this->tourLogRules());
        if (!empty($errors)) {
            setFlash('error', reset($errors));
            return redirect('hdv/dashboard?tab=chi-tiet&departure_id=' . (int) $data['departure_id']);
        }

        $dateError = $this->validateLogDate($departure, $data['log_date']);
        if ($dateError) {
            setFlash('error', $dateError);
            return redirect('hdv/dashboard?tab=chi-tiet&departure_id=' . (int) $data['departure_id']);
        }

        $data['author_id'] = $hdvId;
        $this->modelTourLog->create($data);
        setFlash('success', 'Đã thêm hoạt động vào lịch trình tour.');
        return redirect('hdv/dashboard?tab=chi-tiet&departure_id=' . (int) $data['departure_id']);
    }

    public function updateTourLog($id)
    {
        $hdvId = $this->getActiveHdvId();
        $log = $this->modelTourLog->findById($id);
        $data = $this->tourLogData();
        $departureId = (int) ($log['departure_id'] ?? 0);
        $departure = $this->assignedDeparture($hdvId, $departureId);

        if (!$log || !$departure) {
            setFlash('error', 'Hoạt động không tồn tại hoặc bạn không có quyền cập nhật.');
            return redirect('hdv/dashboard?tab=chi-tiet');
        }

        $data['departure_id'] = $departureId;
        $errors = $this->validate($this->validator, $data, $this->tourLogRules());
        if (!empty($errors)) {
            setFlash('error', reset($errors));
            return redirect('hdv/dashboard?tab=chi-tiet&departure_id=' . $departureId);
        }

        $dateError = $this->validateLogDate($departure, $data['log_date']);
        if ($dateError) {
            setFlash('error', $dateError);
            return redirect('hdv/dashboard?tab=chi-tiet&departure_id=' . $departureId);
        }

        $this->modelTourLog->updateLog($id, $data);
        setFlash('success', 'Đã cập nhật hoạt động tour.');
        return redirect('hdv/dashboard?tab=chi-tiet&departure_id=' . $departureId);
    }

    public function deleteTourLog($id)
    {
        $hdvId = $this->getActiveHdvId();
        $log = $this->modelTourLog->findById($id);
        $departureId = (int) ($log['departure_id'] ?? 0);

        if (!$log || !$this->assignedDeparture($hdvId, $departureId)) {
            setFlash('error', 'Hoạt động không tồn tại hoặc bạn không có quyền xóa.');
            return redirect('hdv/dashboard?tab=chi-tiet');
        }

        $this->modelTourLog->deleteLog($id);
        setFlash('success', 'Đã xóa hoạt động khỏi lịch trình tour.');
        return redirect('hdv/dashboard?tab=chi-tiet&departure_id=' . $departureId);
    }

    private function tourLogData()
    {
        return [
            'departure_id' => (int) ($_POST['departure_id'] ?? 0),
            'title'        => trim($_POST['title'] ?? ''),
            'content'      => trim($_POST['content'] ?? ''),
            'log_date'     => trim($_POST['log_date'] ?? ''),
            'location'     => trim($_POST['location'] ?? ''),
            'weather'      => trim($_POST['weather'] ?? ''),
            'mood'         => trim($_POST['mood'] ?? ''),
        ];
    }

    private function tourLogRules()
    {
        return [
            'departure_id' => 'required|integer',
            'title'        => 'required|max:255',
            'content'      => 'required',
            'log_date'     => 'required',
            'location'     => 'max:255',
            'weather'      => 'max:100',
            'mood'         => 'max:50',
        ];
    }

    private function assignedDeparture($hdvId, $departureId)
    {
        $sql = 'SELECT d.* FROM staff_assignments sa INNER JOIN departures d ON d.id = sa.departure_id WHERE sa.staff_id = :hdv_id AND d.id = :departure_id LIMIT 1';
        return (new \App\Model())->getConnection()->fetchAssociative($sql, [
            'hdv_id' => (int) $hdvId,
            'departure_id' => (int) $departureId,
        ]);
    }

    private function validateLogDate(array $departure, $logDate)
    {
        $timestamp = strtotime($logDate);
        $start = strtotime($departure['departure_date'] . ' 00:00:00');
        $end = strtotime(($departure['return_date'] ?: $departure['departure_date']) . ' 23:59:59');

        if (!$timestamp || $timestamp < $start || $timestamp > $end) {
            return 'Thời gian hoạt động phải nằm trong thời gian diễn ra chuyến tour.';
        }

        return null;
    }
}
