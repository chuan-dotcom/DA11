<?php

namespace App\Controllers;

use App\Controller;
use App\Models\Booking;
use App\Models\Tour;
use Rakit\Validation\Validator;

class BookingController extends Controller
{
    private $modelBooking;
    private $modelTour;
    private $validator;

    public function __construct()
    {
        $this->modelBooking = new Booking();
        $this->modelTour = new Tour();
        $this->validator = new Validator();
    }

    /**
     * Danh sách Booking
     */
    public function index()
    {
        $title = 'Danh sách Booking';

        $bookings = $this->modelBooking->getAll();

        return view(
            'bookings.index',
            compact('title', 'bookings')
        );
    }

    /**
     * Form thêm Booking
     */
    public function create()
    {
        $title = 'Thêm Booking';

        $tours = $this->modelTour->getAll();

        return view(
            'bookings.create',
            compact('title', 'tours')
        );
    }

    /**
     * Lưu Booking
     */
    public function store()
    {
        // Lấy Tour
        $tour = $this->modelBooking->getTour($_POST['tour_id']);

        // Tính tổng tiền
        $totalPrice = $tour['price'] * $_POST['num_people'];

        $data = [

            'tour_id' => $_POST['tour_id'],

            'customer_name' => $_POST['customer_name'],

            'customer_email' => $_POST['customer_email'],

            'customer_phone' => $_POST['customer_phone'],

            'num_people' => $_POST['num_people'],

            'total_price' => $totalPrice,

            'booking_date' => $_POST['booking_date'],

            'status' => $_POST['status'],

            'note' => $_POST['note']

        ];

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

        $this->modelBooking->insert($data);

        setFlash(
            'success',
            'Thêm Booking thành công!'
        );

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

        return view(
            'bookings.edit',
            compact(
                'title',
                'booking',
                'tours'
            )
        );
    }

    /**
     * Cập nhật Booking
     */
    public function update($id)
    {
        $tour = $this->modelBooking->getTour($_POST['tour_id']);

        $totalPrice = $tour['price'] * $_POST['num_people'];

        $data = [

            'tour_id' => $_POST['tour_id'],

            'customer_name' => $_POST['customer_name'],

            'customer_email' => $_POST['customer_email'],

            'customer_phone' => $_POST['customer_phone'],

            'num_people' => $_POST['num_people'],

            'total_price' => $totalPrice,

            'booking_date' => $_POST['booking_date'],

            'status' => $_POST['status'],

            'note' => $_POST['note']

        ];

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

        $this->modelBooking->update($id, $data);

        setFlash(
            'success',
            'Cập nhật Booking thành công!'
        );

        return redirect('admin/bookings');
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
            'bookings.show',
            compact(
                'title',
                'booking'
            )
        );
    }
}