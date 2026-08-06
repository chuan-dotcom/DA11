<?php

namespace App\Controllers\Admin;
  
use App\Controller;
use App\Models\Departure;
use App\Models\Tour;
use App\Models\TourCategory;
use App\Models\StaffAssignment;
use Rakit\Validation\Validator;
use App\Models\Booking;
use App\Models\Service;

class DepartureController extends Controller
{
    private $modelDeparture;
    private $modelTour;
    private $modelCategory;
    private $modelAssignment;
    private $modelBooking;
    private $modelService;
    private $validator;

    public function __construct()
    {
        $this->modelDeparture = new Departure();
        $this->modelTour = new Tour();
        $this->modelCategory = new TourCategory();
        $this->modelAssignment = new StaffAssignment();
        $this->modelBooking = new Booking();
        $this->modelService = new Service();
        $this->validator = new Validator();
    }

    public function index()
    {
        $title = 'Quản lý khởi hành';
        $categoryId = isset($_GET['category_id']) ? (int) $_GET['category_id'] : null;
        $categories = $this->modelCategory->getAll();
        $departures = $this->modelDeparture->getAll($categoryId);
        $statusCounts = $this->modelDeparture->getDeparturesByStatus($categoryId);
        return view('admin.departures.index', compact('title', 'departures', 'statusCounts', 'categories', 'categoryId'));
    }

    public function create()
    {
        $title = 'Thêm chuyến khởi hành mới';
        $tours = $this->modelTour->getAll();
        $bookingSuggestions = $this->modelBooking->getConfirmedWithTourSummary();
        $preTourId = isset($_GET['tour_id']) ? (int)$_GET['tour_id'] : 0;
        if ($preTourId < 0) {
            $preTourId = 0;
        }
        $sourceBookingId = isset($_GET['booking_id']) ? (int)$_GET['booking_id'] : 0;
        $prefillFromBooking = null;
        if ($sourceBookingId > 0) {
            try {
                $b = $this->modelBooking->findById($sourceBookingId);
                if ($b) {
                    $tourName = null;
                    foreach ($tours as $t) {
                        if ((int)$t['id'] === (int)$b['tour_id']) {
                            $tourName = $t['name'];
                            break;
                        }
                    }
                    if ($preTourId <= 0 && (int)$b['tour_id'] > 0) {
                        $preTourId = (int)$b['tour_id'];
                    }
                    $prefillFromBooking = [
                        'id'            => (int)$b['id'],
                        'customer_name' => $b['customer_name'] ?? '',
                        'customer_phone'=> $b['customer_phone'] ?? '',
                        'num_people'    => (int)($b['num_people'] ?? 0),
                        'booking_date'  => $b['booking_date'] ?? null,
                        'pickup_address'=> $b['pickup_address'] ?? null,
                        'tour_id'       => (int)$b['tour_id'],
                        'tour_name'     => $tourName,
                        'departure_id'  => (int)($b['departure_id'] ?? 0),
                    ];
                    if ((int)$prefillFromBooking['departure_id'] > 0) {
                        $prefillFromBooking = null;
                        $sourceBookingId = 0;
                    }
                }
            } catch (\Throwable $e) {
                $prefillFromBooking = null;
                $sourceBookingId = 0;
            }
        }
        return view('admin.departures.create', compact('title', 'tours', 'bookingSuggestions', 'preTourId', 'sourceBookingId', 'prefillFromBooking'));
    }

    public function store()
    {
        $data = [
            'tour_id'         => $_POST['tour_id'],
            'group_name'      => trim($_POST['group_name'] ?? ''),
            'departure_date'  => $_POST['departure_date'],
            'return_date'     => $_POST['return_date'] ?? null,
            'max_participants'=> $_POST['max_participants'] ?? 0,
            'meeting_point'   => $_POST['meeting_point'] ?? null,
            'meeting_time'    => $_POST['meeting_time'] ?? null,
            'vehicle'         => $_POST['vehicle'] ?? null,
            'notes'           => $_POST['notes'] ?? null,
            'status'          => $_POST['status'] ?? 'scheduled',
        ];
        $sourceBookingId = isset($_POST['source_booking_id']) ? (int)$_POST['source_booking_id'] : 0;

        $rules = [
            'tour_id'        => 'required|integer',
            'departure_date' => 'required',
            'group_name'     => 'max:255',
            'max_participants' => 'integer',
            'status'         => 'required',
        ];

        $errors = $this->validate($this->validator, $data, $rules);
        if (!empty($errors)) {
            setFlash('error', reset($errors));
            return redirect('admin/departures/create');
        }

        if (!empty($data['return_date']) && strtotime($data['return_date']) < strtotime($data['departure_date'])) {
            setFlash('error', 'Ngày trở về không thể sớm hơn ngày khởi hành!');
            return redirect('admin/departures/create');
        }

        if ($data['group_name'] === '') {
            $tour = $this->modelTour->findByid((int) $data['tour_id']);
            $data['group_name'] = $tour
                ? ('Đoàn ' . $tour['name'] . ' ' . date('d-m-Y', strtotime($data['departure_date'])))
                : null;
        }

        if ($sourceBookingId > 0) {
            $srcBooking = null;
            try {
                $srcBooking = $this->modelBooking->findById($sourceBookingId);
            } catch (\Throwable $e) {
                $srcBooking = null;
            }
            if (!$srcBooking) {
                $sourceBookingId = 0;
            } elseif ((int)($srcBooking['departure_id'] ?? 0) > 0) {
                setFlash('warning', 'Booking #' . $sourceBookingId . ' đã thuộc đoàn #' . (int)$srcBooking['departure_id'] . ' từ trước; chỉ tạo chuyến mới, không gắn lại.');
                $sourceBookingId = 0;
            } elseif ((int)$srcBooking['tour_id'] !== (int)$data['tour_id']) {
                setFlash('warning', 'Tour trong booking nguồn (#'. $sourceBookingId . ') không khớp với tour được chọn; chỉ tạo chuyến mới, không tự gắn.');
                $sourceBookingId = 0;
            }
        }

        $this->modelDeparture->insert($data);
        $newDepId = (int)$this->modelDeparture->getLastInsertId();
        $msg = 'Thêm chuyến khởi hành thành công!';
        if ($newDepId > 0 && $sourceBookingId > 0) {
            try {
                $this->modelBooking->assignToDeparture($sourceBookingId, $newDepId);
                $msg .= ' (Đã tự gắn Booking #' . $sourceBookingId . ' vào đoàn mới #' . $newDepId . ')';
            } catch (\Throwable $e) {
                $msg .= ' (Lưu ý: không gắn được booking #' . $sourceBookingId . ' vào đoàn, vui lòng gắn thủ công)';
            }
        }
        setFlash('success', $msg);
        if ($newDepId > 0 && $sourceBookingId > 0) {
            return redirect('admin/bookings/show/' . $sourceBookingId);
        }
        if ($newDepId > 0) {
            return redirect('admin/departures/edit/' . $newDepId);
        }
        return redirect('admin/departures');
    }

    public function edit($id)
    {
        $title = 'Cập nhật chuyến khởi hành';
        $departure = $this->modelDeparture->findById($id);
        $tours = $this->modelTour->getAll();

        if (!$departure) {
            setFlash('error', 'Chuyến khởi hành không tồn tại!');
            return redirect('admin/departures');
        }

        $assignments = $this->modelAssignment->getByDepartureId($id);
        $bookingSuggestions = $this->modelBooking->getConfirmedWithTourSummary();
        $services = $this->modelService->getByDepartureId($id);
        $bookings = $this->modelBooking->filter(null, $id, null);

        return view('admin.departures.edit', compact('title', 'departure', 'tours', 'assignments', 'bookingSuggestions', 'services', 'bookings'));
    }

    public function update($id)
    {
        $departure = $this->modelDeparture->findById($id);
        if (!$departure) {
            setFlash('error', 'Chuyến khởi hành không tồn tại!');
            return redirect('admin/departures');
        }

        $data = [
            'tour_id'         => $_POST['tour_id'],
            'group_name'      => trim($_POST['group_name'] ?? ''),
            'departure_date'  => $_POST['departure_date'],
            'return_date'     => $_POST['return_date'] ?? null,
            'max_participants'=> $_POST['max_participants'] ?? 0,
            'meeting_point'   => $_POST['meeting_point'] ?? null,
            'meeting_time'    => $_POST['meeting_time'] ?? null,
            'vehicle'         => $_POST['vehicle'] ?? null,
            'notes'           => $_POST['notes'] ?? null,
            'status'          => $_POST['status'] ?? 'scheduled',
        ];

        $rules = [
            'tour_id'        => 'required|integer',
            'departure_date' => 'required',
            'group_name'     => 'max:255',
            'max_participants' => 'integer',
            'status'         => 'required',
        ];

        $errors = $this->validate($this->validator, $data, $rules);
        if (!empty($errors)) {
            setFlash('error', reset($errors));
            return redirect('admin/departures/edit/' . $id);
        }

        if (!empty($data['return_date']) && strtotime($data['return_date']) < strtotime($data['departure_date'])) {
            setFlash('error', 'Ngày trở về không thể sớm hơn ngày khởi hành!');
            return redirect('admin/departures/edit/' . $id);
        }

        if ($data['group_name'] === '') {
            $tour = $this->modelTour->findByid((int) $data['tour_id']);
            $data['group_name'] = $tour
                ? ('Đoàn ' . $tour['name'] . ' ' . date('d-m-Y', strtotime($data['departure_date'])))
                : ($departure['group_name'] ?? null);
        }

        $assignments = $this->modelAssignment->getByDepartureId($id);
        foreach ($assignments as $assignment) {
            if (!$this->modelAssignment->checkStaffAvailability(
                $assignment['staff_id'],
                $data['departure_date'],
                $data['return_date'],
                $assignment['id']
            )) {
                $conflicts = $this->modelAssignment->getConflictingAssignments(
                    $assignment['staff_id'],
                    $data['departure_date'],
                    $data['return_date'],
                    $assignment['id']
                );
                $conflictSummary = [];
                foreach ($conflicts as $conflict) {
                    $tourName = $conflict['tour_name'] ?? 'chuyến khởi hành';
                    $conflictDate = !empty($conflict['departure_date']) ? date('d/m/Y', strtotime($conflict['departure_date'])) : 'chưa xác định';
                    $conflictSummary[] = $tourName . ' (' . $conflictDate . ')';
                }
                $conflictText = implode(', ', array_slice($conflictSummary, 0, 3));
                if (count($conflictSummary) > 3) {
                    $conflictText .= ', ...';
                }
                setFlash('error', 'Nhân sự ' . ($assignment['staff_name'] ?? 'đã chọn') . ' đã bị trùng lịch với ' . $conflictText . '.');
                return redirect('admin/departures/edit/' . $id);
            }
        }

        $this->modelDeparture->update($id, $data);

        $this->modelDeparture->syncBookingsPickupAddress($id, $data['meeting_point'] ?? null, true);

        setFlash('success', 'Cập nhật chuyến khởi hành thành công!');
        return redirect('admin/departures');
    }

    public function delete($id)
    {
        $departure = $this->modelDeparture->findById($id);
        if (!$departure) {
            setFlash('error', 'Chuyến khởi hành không tồn tại!');
            return redirect('admin/departures');
        }

        $this->modelDeparture->delete($id);
        setFlash('success', 'Xóa chuyến khởi hành thành công!');
        return redirect('admin/departures');
    }

    public function show($id)
    {
        $title = 'Chi tiết chuyến khởi hành';
        $departure = $this->modelDeparture->findById($id);

        if (!$departure) {
            setFlash('error', 'Chuyến khởi hành không tồn tại!');
            return redirect('admin/departures');
        }

        $assignments = $this->modelAssignment->getByDepartureId($id);

        return view('admin.departures.show', compact('title', 'departure', 'assignments'));
    }
}

