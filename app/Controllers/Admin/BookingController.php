<?php

namespace App\Controllers\Admin;

use App\Controller;
use App\Models\Booking;
use App\Models\Tour;
use Rakit\Validation\Validator;

class BookingController extends Controller
{
<<<<<<< HEAD
    private $modelBooking;
    private $modelTour;
    private $validator;

    public function __construct()
    {
        $this->modelBooking = new Booking();
        $this->modelTour = new Tour();
=======
    private Booking $bookingModel;
    private Tour $tourModel;
    private Validator $validator;

    public function __construct()
    {
        $this->bookingModel = new Booking();
        $this->tourModel = new Tour();
>>>>>>> aa059c0a460dbe3ab4b1a8320f08f6d7fe5b043c
        $this->validator = new Validator();
    }

    /**
     * Danh sách Booking — đồng bộ pattern với DepartureController
     */
    public function index()
    {
        $pageTitle = 'Quản lý booking';
        $title = 'Danh sách khách hàng đặt tour';

<<<<<<< HEAD
        $tourId = isset($_GET['tour_id']) ? (int)$_GET['tour_id'] : null;
        $status = isset($_GET['status']) ? trim((string)$_GET['status']) : null;
        if ($status === '') {
            $status = null;
        }
        if ($tourId !== null && $tourId <= 0) {
            $tourId = null;
        }

        $tours = $this->modelTour->getAll();

        if ($tourId === null && $status === null) {
            $bookings = $this->modelBooking->getAll();
        } else {
            $bookings = $this->modelBooking->filter($tourId, null, $status);
        }

        return view(
            'admin.bookings.index',
            compact('title', 'bookings', 'tours', 'tourId', 'status')
=======
        $tourId = isset($_GET['tour_id']) ? (int) $_GET['tour_id'] : null;
        $status = isset($_GET['status']) && $_GET['status'] !== '' ? (int) $_GET['status'] : null;
        $fromDate = !empty($_GET['from_date']) ? $_GET['from_date'] : null;
        $toDate = !empty($_GET['to_date']) ? $_GET['to_date'] : null;

        $tours = $this->tourModel->getAll();
        $bookings = $this->bookingModel->getAll($tourId, $status, $fromDate, $toDate);
        $statusCounts = $this->bookingModel->getBookingsByStatus($tourId, $fromDate, $toDate);

        return view(
            'admin.bookings.index',
            compact(
                'pageTitle',
                'title',
                'bookings',
                'tours',
                'tourId',
                'status',
                'fromDate',
                'toDate',
                'statusCounts'
            )
>>>>>>> aa059c0a460dbe3ab4b1a8320f08f6d7fe5b043c
        );
    }

    /**
     * Form thêm Booking
     */
    public function create()
    {
        $pageTitle = 'Quản lý booking';
        $title = 'Thêm Booking';
<<<<<<< HEAD

        $tours = $this->modelTour->getAll();

        $preTourId = isset($_GET['tour_id']) ? (int)$_GET['tour_id'] : 0;

        return view(
            'admin.bookings.create',
            compact('title', 'tours', 'preTourId')
=======
        $tours = $this->tourModel->getAll();
        $this->keepOldInput();

        return view(
            'admin.bookings.create',
            compact('pageTitle', 'title', 'tours')
>>>>>>> aa059c0a460dbe3ab4b1a8320f08f6d7fe5b043c
        );
    }

    /**
     * Lưu Booking
     */
    public function store()
    {
<<<<<<< HEAD
        $data = [

            'tour_id' => $_POST['tour_id'],

            'customer_name' => $_POST['customer_name'],

            'customer_email' => $_POST['customer_email'],

            'customer_phone' => $_POST['customer_phone'],

            'pickup_address' => $_POST['pickup_address'] ?? null,

            'num_people' => $_POST['num_people'],

            'booking_date' => $_POST['booking_date'],

            'status' => $_POST['status'],

            'note' => $_POST['note']

        ];
=======
        $input = $this->normalizeInput([
            'tour_id' => $_POST['tour_id'] ?? null,
            'customer_name' => trim($_POST['customer_name'] ?? ''),
            'customer_email' => trim($_POST['customer_email'] ?? ''),
            'customer_phone' => trim($_POST['customer_phone'] ?? ''),
            'num_people' => $_POST['num_people'] ?? 1,
            'booking_date' => $_POST['booking_date'] ?? date('Y-m-d'),
            'status' => $_POST['status'] ?? '0',
            'note' => $_POST['note'] ?? null,
        ]);
>>>>>>> aa059c0a460dbe3ab4b1a8320f08f6d7fe5b043c

        $rules = [
            'tour_id' => 'required|integer',
            'customer_name' => 'required|max:255',
            'customer_email' => 'required|email',
            'customer_phone' => 'required',
            'num_people' => 'required|integer',
            'booking_date' => 'required',
            'status' => 'required|in:0,1,2',
        ];

        $errors = $this->validate($this->validator, $input, $rules);
        if (!empty($errors)) {
            $this->flashOldInput($input);
            setFlash('error', reset($errors));
            return redirect('admin/bookings/create');
        }

        $tour = $this->bookingModel->getTour($input['tour_id']);
        if (!$tour) {
            $this->flashOldInput($input);
            setFlash('error', 'Tour được chọn không tồn tại');
            return redirect('admin/bookings/create');
        }

<<<<<<< HEAD
        $data['total_price'] = $tour['price'] * (int) $data['num_people'];

        $this->modelBooking->insert($data);

        setFlash(
            'success',
            'Thêm Booking thành công!'
        );

=======
        if ((int) $input['num_people'] <= 0) {
            $this->flashOldInput($input);
            setFlash('error', 'Số người phải lớn hơn 0');
            return redirect('admin/bookings/create');
        }

        $input['total_price'] = (float) ($tour['price'] ?? 0) * (int) $input['num_people'];
        $input['tour_id'] = (int) $input['tour_id'];
        $input['num_people'] = (int) $input['num_people'];

        $this->bookingModel->insert($input);
        setFlash('success', 'Thêm Booking thành công!');
>>>>>>> aa059c0a460dbe3ab4b1a8320f08f6d7fe5b043c
        return redirect('admin/bookings');
    }

    /**
     * Form sửa Booking
     */
    public function edit($id)
    {
        $pageTitle = 'Quản lý booking';
        $title = 'Cập nhật Booking';

        $booking = $this->bookingModel->findById($id);
        if (!$booking) {
            setFlash('error', 'Booking không tồn tại');
            return redirect('admin/bookings');
        }

<<<<<<< HEAD
        $tours = $this->modelTour->getAll();

        return view(
            'admin.bookings.edit',
            compact(
                'title',
                'booking',
                'tours'
            )
=======
        $tours = $this->tourModel->getAll();
        $this->keepOldInput();

        return view(
            'admin.bookings.edit',
            compact('pageTitle', 'title', 'booking', 'tours')
>>>>>>> aa059c0a460dbe3ab4b1a8320f08f6d7fe5b043c
        );
    }

    /**
     * Cập nhật Booking
     */
    public function update($id)
    {
        $booking = $this->bookingModel->findById($id);
        if (!$booking) {
            setFlash('error', 'Booking không tồn tại');
            return redirect('admin/bookings');
        }

<<<<<<< HEAD
        $data = [

            'tour_id' => $_POST['tour_id'],

            'customer_name' => $_POST['customer_name'],

            'customer_email' => $_POST['customer_email'],

            'customer_phone' => $_POST['customer_phone'],

            'pickup_address' => $_POST['pickup_address'] ?? null,

            'num_people' => $_POST['num_people'],

            'booking_date' => $_POST['booking_date'],

            'status' => $_POST['status'],

            'note' => $_POST['note']

        ];
=======
        $input = $this->normalizeInput([
            'tour_id' => $_POST['tour_id'] ?? null,
            'customer_name' => trim($_POST['customer_name'] ?? ''),
            'customer_email' => trim($_POST['customer_email'] ?? ''),
            'customer_phone' => trim($_POST['customer_phone'] ?? ''),
            'num_people' => $_POST['num_people'] ?? 1,
            'booking_date' => $_POST['booking_date'] ?? null,
            'status' => $_POST['status'] ?? '0',
            'note' => $_POST['note'] ?? null,
        ]);
>>>>>>> aa059c0a460dbe3ab4b1a8320f08f6d7fe5b043c

        $rules = [
            'tour_id' => 'required|integer',
            'customer_name' => 'required|max:255',
            'customer_email' => 'required|email',
            'customer_phone' => 'required',
            'num_people' => 'required|integer',
            'booking_date' => 'required',
            'status' => 'required|in:0,1,2',
        ];

        $errors = $this->validate($this->validator, $input, $rules);
        if (!empty($errors)) {
            setFlash('error', reset($errors));
            return redirect('admin/bookings/edit/' . $id);
        }

        $tour = $this->bookingModel->getTour($input['tour_id']);
        if (!$tour) {
            setFlash('error', 'Tour được chọn không tồn tại');
            return redirect('admin/bookings/edit/' . $id);
        }

<<<<<<< HEAD
        $data['total_price'] = $tour['price'] * (int) $data['num_people'];

        $this->modelBooking->update($id, $data);

        setFlash(
            'success',
            'Cập nhật Booking thành công!'
        );

=======
        if ((int) $input['num_people'] <= 0) {
            setFlash('error', 'Số người phải lớn hơn 0');
            return redirect('admin/bookings/edit/' . $id);
        }

        $input['total_price'] = (float) ($tour['price'] ?? 0) * (int) $input['num_people'];
        $input['tour_id'] = (int) $input['tour_id'];
        $input['num_people'] = (int) $input['num_people'];

        $this->bookingModel->update($id, $input);
        setFlash('success', 'Cập nhật Booking thành công!');
>>>>>>> aa059c0a460dbe3ab4b1a8320f08f6d7fe5b043c
        return redirect('admin/bookings');
    }

    /**
     * Xóa Booking
     */
    public function delete($id)
    {
        $booking = $this->bookingModel->findById($id);
        if (!$booking) {
            setFlash('error', 'Booking không tồn tại');
            return redirect('admin/bookings');
        }

        $this->bookingModel->delete($id);
        setFlash('success', 'Xóa Booking thành công!');
        return redirect('admin/bookings');
    }

    /**
     * Chi tiết Booking
     */
    public function show($id)
    {
        $pageTitle = 'Quản lý booking';
        $title = 'Chi tiết Booking';

        $booking = $this->bookingModel->findById($id);
        if (!$booking) {
            setFlash('error', 'Booking không tồn tại');
            return redirect('admin/bookings');
        }

        return view(
            'admin.bookings.show',
            compact('pageTitle', 'title', 'booking')
        );
    }

    private function normalizeInput(array $input): array
    {
        foreach ($input as $key => $value) {
            if (is_string($value)) {
                $input[$key] = $value === '' ? null : preg_replace('/\s+/u', ' ', $value);
            }
        }
        return $input;
    }

    private function flashOldInput(array $input): void
    {
        $safe = [];
        foreach ($input as $key => $value) {
            if (is_string($value) || is_numeric($value)) {
                $safe[$key] = $value;
            }
        }
        $_SESSION['old_input'] = $safe;
    }

    private function keepOldInput(): void
    {
        // keep old_input for one more render for old() helper
    }
}
