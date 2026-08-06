<?php
namespace App\Controllers\Admin;
   
use App\Controller;
use App\Models\Tour;
use App\Models\TourCategory;
use App\Models\Booking;
use Rakit\Validation\Validator;

class TourController extends Controller{
    private $modelTour;
    private $modelCategory;
    private $modelBooking;
    private $validator;

    public function __construct()
    {
        $this->modelTour = new Tour();
        $this->modelCategory = new TourCategory();
        $this->modelBooking = new Booking();
        $this->validator = new Validator();
    }

    public function index() {
        $title = 'Danh sách tour du lịch';
        $tours = $this->modelTour->getAll();
        return view('admin.tours.index', compact('title', 'tours'));
    }

    public function create() {
        $title = 'Thêm mới tour du lịch';
        $categories = $this->modelCategory->getAll();
        return view('admin.tours.create', compact('title', 'categories'));
    }

    public function store() {
        $data=[
            'name' =>$_POST['name'],
            'category_id' =>$_POST['category_id'],
            'price' =>$_POST['price'],
            'duration' =>$_POST['duration'],
            'location' =>$_POST['location'] ?? null,
            'description' =>$_POST['description'],
            'status' =>$_POST['status'],
        ];

        $rules=[
            'name' =>'required|max:255',
            'category_id' =>'required|integer',
            'price' =>'required|numeric',
            'duration' =>'required',
            'status' =>'required',
        ];

        $errors=$this->validate($this->validator, $data, $rules);
        if (!empty($errors)){
            setFlash('error', reset($errors));
            return redirect('admin/tours/create');
        }

        // Upload ảnh
        $data['image'] = null;
        if (is_upload('image')) {
            $data['image'] = $this->uploadFile($_FILES['image'], 'tours');
        }

        $this->modelTour->insert($data);
        setFlash('success', 'Thêm mới tour du lịch thành công!');
        return  redirect('admin/tours');
    }

    public function edit($id) {
        $title='Cập nhật tour du lịch';
        $categories = $this->modelCategory->getAll();
        $tour=$this->modelTour->findByid($id);
        if(!$tour){
            setFlash('error', 'Tour du lịch không tồn tại');
            redirect('admin/tours');
        }
        return view('admin.tours.edit', compact('title', 'tour', 'categories'));
    }

    public function update($id) {
        $data=[
            'name' =>$_POST['name'],
            'category_id' =>$_POST['category_id'],
            'price' =>$_POST['price'],
            'duration' =>$_POST['duration'],
            'location' =>$_POST['location'] ?? null,
            'description' =>$_POST['description'],
            'status' =>$_POST['status'],
        ];

        $rules=[
            'name' =>'required|max:255',
            'category_id' =>'required|integer',
            'price' =>'required|numeric',
            'duration' =>'required',
            'status' =>'required',
        ];

        $errors=$this->validate($this->validator, $data, $rules);
        if (!empty($errors)){
            setFlash('error', reset($errors));
            return redirect('admin/tours/edit/' . $id);
        }

        // Lấy tour cũ để xử lý ảnh
        $tour = $this->modelTour->findByid($id);
        $data['image'] = $tour['image'];
        
        if (is_upload('image')) {
            // Xóa ảnh cũ nếu có
            if ($tour['image'] && file_exists($tour['image'])) {
                unlink($tour['image']);
            }
            $data['image'] = $this->uploadFile($_FILES['image'], 'tours');
        }

        $this->modelTour->update($id, $data);
        setFlash('success', 'Cập nhật tour du lịch thành công!');
        return  redirect('admin/tours');
    }

    public function delete($id) {
        $tour=$this->modelTour->findByid($id);
        if(!$tour){
            setFlash('error', 'Tour du lịch không tồn tại');
            redirect('admin/tours');
        }
        
        $this->modelTour->delete($id);
        setFlash('success', 'Xóa tour du lịch thành công!');
        return redirect('admin/tours');
    }

    public function show($id){
        $title = 'Chi tiết tour du lịch';
        $tour = $this->modelTour->findByid($id);

        if (!$tour) {
            setFlash('error', 'Tour du lịch không tồn tại');
            return redirect('admin/tours');
        }

        return view('admin.tours.show', compact('title', 'tour'));
    }

    /**
     * Trang công khai khi quét QR — hiện ngay chi tiết tour.
     */
    public function qrShow($id) {
        $tour = $this->modelTour->findByid($id);

        if (!$tour) {
            http_response_code(404);
            echo '<!DOCTYPE html><html><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Không tìm thấy</title></head><body style="font-family:sans-serif;text-align:center;padding:40px;"><h2>Tour không tồn tại</h2></body></html>';
            return;
        }

        $title = $tour['name'];
        return view('admin.tours.qr_show', compact('title', 'tour'));
    }

    /**
     * Xem danh sách người tham gia của một tour
     */
    public function participants($id) {
        $title = 'Danh sách người tham gia tour';
        $tour = $this->modelTour->findByid($id);

        if (!$tour) {
            setFlash('error', 'Tour du lịch không tồn tại');
            return redirect('admin/tours');
        }

        $bookings = $this->modelBooking->getByTourId($id);
        $stats = $this->modelBooking->getTourParticipantsStats($id);

        return view('admin.tours.participants', compact('title', 'tour', 'bookings', 'stats'));
    }
}
