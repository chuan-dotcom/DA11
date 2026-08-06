<?php
  
namespace App\Controllers\Admin;
 
use App\Controller;
use App\Models\Booking;
use App\Models\Tour;
use App\Models\Departure;
use Rakit\Validation\Validator;

class BookingController extends Controller
{
    private $modelBooking;
    private $modelTour;
    private $modelDeparture;
    private $validator;

    public function __construct()
    {
        $this->modelBooking = new Booking();
        $this->modelTour = new Tour();
        $this->modelDeparture = new Departure();
        $this->validator = new Validator();
    }

    /**
     * Danh sách Booking   
     */
    public function index()
    {
        $title = 'Danh sách khách hàng';

        $tourId = isset($_GET['tour_id']) ? (int)$_GET['tour_id'] : null;
        $departureId = isset($_GET['departure_id']) ? (int)$_GET['departure_id'] : null;
        $status = isset($_GET['status']) ? trim((string)$_GET['status']) : null;
        if ($status === '') {
            $status = null;
        }
        if ($tourId !== null && $tourId <= 0) {
            $tourId = null;
        }
        if ($departureId !== null && $departureId <= 0) {
            $departureId = null;
        }

        $tours = $this->modelTour->getAll();
        $departures = $this->modelDeparture->getAll();

        if ($tourId === null && $departureId === null && $status === null) {
            $bookings = $this->modelBooking->getAll();
        } else {
            $bookings = $this->modelBooking->filter($tourId, $departureId, $status);
        }

        return view(
            'admin.bookings.index',
            compact('title', 'bookings', 'tours', 'departures', 'tourId', 'departureId', 'status')
        );
    }

    /**
     * Form thêm Booking
     */
    public function create()
    {
        $title = 'Thêm Booking';

        $tours = $this->modelTour->getAll();
        $departures = $this->modelDeparture->getAll();

        $preTourId = isset($_GET['tour_id']) ? (int)$_GET['tour_id'] : 0;
        $preDepartureId = isset($_GET['departure_id']) ? (int)$_GET['departure_id'] : 0;

        if ($preDepartureId > 0) {
            try {
                $dep = $this->modelDeparture->findById($preDepartureId);
                if ($dep && (int)$dep['tour_id'] > 0 && $preTourId <= 0) {
                    $preTourId = (int)$dep['tour_id'];
                }
            } catch (\Throwable $e) {
            }
        }

        return view(
            'admin.bookings.create',
            compact('title', 'tours', 'departures', 'preTourId', 'preDepartureId')
        );
    }

    /**
     * Lưu Booking
     */
    public function store()
    {
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

        $departureId = !empty($_POST['departure_id']) ? (int)$_POST['departure_id'] : 0;

        $rules = [

            'tour_id' => 'required|integer',

            'customer_name' => 'required|max:255',

            'customer_email' => 'required|email',

            'customer_phone' => 'required',

            'num_people' => 'required|integer',

            'booking_date' => 'required',

            'status' => 'required'

        ];

        $errors = $this->validate(
            $this->validator,
            $data,
            $rules
        );

        if (!empty($errors)) {

            setFlash('error', reset($errors));

            return redirect('admin/bookings/create');
        }

        $tour = $this->modelBooking->getTour($data['tour_id']);
        if (!$tour) {
            setFlash('error', 'Tour được chọn không tồn tại');
            return redirect('admin/bookings/create');
        }

        if ($departureId > 0) {
            $departure = $this->modelDeparture->findById($departureId);
            if (!$departure) {
                setFlash('error', 'Chuyến khởi hành không tồn tại');
                return redirect('admin/bookings/create');
            }
            if ((int)$departure['tour_id'] !== (int)$data['tour_id']) {
                setFlash('error', 'Chuyến khởi hành không thuộc về tour đã chọn');
                return redirect('admin/bookings/create');
            }
        }

        $data['total_price'] = $tour['price'] * (int) $data['num_people'];

        $this->modelBooking->insert($data);
        $bookingId = (int)$this->modelBooking->getLastInsertId();

        if ($departureId > 0 && $bookingId > 0) {
            $this->modelBooking->assignToDeparture($bookingId, $departureId);
        }

        setFlash(
            'success',
            'Thêm Booking thành công!' . ($departureId > 0 ? ' (Đã gắn vào chuyến khởi hành)' : '')
        );

        if ($departureId > 0) {
            return redirect('admin/bookings?departure_id=' . $departureId);
        }
        return redirect('admin/bookings');
    }

    /**
     * Form sửa
     */
    public function edit($id)
    {
        $title = 'Cập nhật Booking';

        $booking = $this->modelBooking->findById($id);

        if (!$booking) {

            setFlash(
                'error',
                'Booking không tồn tại'
            );

            return redirect('admin/bookings');
        }

        $tours = $this->modelTour->getAll();
        $departures = $this->modelDeparture->getAll();

        return view(
            'admin.bookings.edit',
            compact(
                'title',
                'booking',
                'tours',
                'departures'
            )
        );
    }

    /**
     * Cập nhật Booking
     */
    public function update($id)
    {
        $booking = $this->modelBooking->findById($id);
        if (!$booking) {
            setFlash(
                'error',
                'Booking không tồn tại'
            );
            return redirect('admin/bookings');
        }
        $oldDepartureId = (int)($booking['departure_id'] ?? 0);

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

        $departureId = !empty($_POST['departure_id']) ? (int)$_POST['departure_id'] : 0;

        $rules = [

            'tour_id' => 'required|integer',

            'customer_name' => 'required|max:255',

            'customer_email' => 'required|email',

            'customer_phone' => 'required',

            'num_people' => 'required|integer',

            'booking_date' => 'required',

            'status' => 'required'

        ];

        $errors = $this->validate(
            $this->validator,
            $data,
            $rules
        );

        if (!empty($errors)) {

            setFlash('error', reset($errors));

            return redirect(
                'admin/bookings/edit/' . $id
            );
        }

        $tour = $this->modelBooking->getTour($data['tour_id']);
        if (!$tour) {
            setFlash('error', 'Tour được chọn không tồn tại');
            return redirect('admin/bookings/edit/' . $id);
        }

        if ($departureId > 0) {
            $departure = $this->modelDeparture->findById($departureId);
            if (!$departure) {
                setFlash('error', 'Chuyến khởi hành không tồn tại');
                return redirect('admin/bookings/edit/' . $id);
            }
            if ((int)$departure['tour_id'] !== (int)$data['tour_id']) {
                setFlash('error', 'Chuyến khởi hành không thuộc về tour đã chọn');
                return redirect('admin/bookings/edit/' . $id);
            }
        }

        $data['total_price'] = $tour['price'] * (int) $data['num_people'];

        $this->modelBooking->update($id, $data);

        if ($departureId !== $oldDepartureId) {
            if ($oldDepartureId > 0) {
                try {
                    $this->modelBooking->removeFromDeparture($id, $oldDepartureId);
                } catch (\Throwable $e) {
                }
            }
            if ($departureId > 0) {
                $this->modelBooking->assignToDeparture($id, $departureId);
            }
        }

        setFlash(
            'success',
            'Cập nhật Booking thành công!' . ($departureId > 0 ? ' (Đã cập nhật chuyến khởi hành)' : '')
        );

        if ($departureId > 0) {
            return redirect('admin/bookings?departure_id=' . $departureId);
        }
        return redirect('admin/bookings');
    }

    public function unassignDeparture($id)
    {
        $booking = $this->modelBooking->findById($id);
        if (!$booking) {
            setFlash('error', 'Booking không tồn tại');
            return redirect('admin/bookings');
        }
        $departureId = (int)($booking['departure_id'] ?? 0);
        if ($departureId <= 0) {
            setFlash('warning', 'Booking này chưa thuộc đoàn nào.');
            return redirect('admin/bookings');
        }
        $this->modelBooking->removeFromDeparture($id, $departureId);
        setFlash('success', 'Đã gỡ Booking khỏi chuyến khởi hành.');
        return redirect('admin/bookings?departure_id=' . $departureId);
    }

    /**
     * Xóa Booking
     */
    public function delete($id)
    {
        $booking = $this->modelBooking->findById($id);

        if (!$booking) {

            setFlash(
                'error',
                'Booking không tồn tại'
            );

            return redirect('admin/bookings');
        }

        $this->modelBooking->delete($id);

        setFlash(
            'success',
            'Xóa Booking thành công!'
        );

        return redirect('admin/bookings');
    }

    /**
     * Chi tiết Booking
     */
    public function show($id)
    {
        $title = 'Chi tiết Booking';

        $booking = $this->modelBooking->findById($id);

        if (!$booking) {

            setFlash(
                'error',
                'Booking không tồn tại'
            );

            return redirect('admin/bookings');
        }

        return view(
            'admin.bookings.show',
            compact(
                'title',
                'booking'
            )
        );
    }
}
