<?php
namespace App\Controllers;
   
use App\Controller;
use App\Models\TourCategory;
use Rakit\Validation\Validator;

class TourCategoryController extends Controller{
    private $modelCategory;
    private $validator;

    public function __construct()
    {
        $this->modelCategory = new TourCategory();
        $this->validator = new Validator();
    }

    public function index() {
        $title = 'Danh sách danh mục tour';
        $categories = $this->modelCategory->getAll();
        return view('tour-categories.index', compact('title', 'categories'));
    }

    public function create() {
        $title = 'Thêm mới danh mục tour';
        return view('tour-categories.create', compact('title'));
    }

    public function store() {
        $data=[
            'name' =>$_POST['name'],
            'description' =>$_POST['description'],
        ];

        $rules=[
            'name' =>'required|max:255',
        ];

        $errors=$this->validate($this->validator, $data, $rules);
        if (!empty($errors)){
            setFlash('error', reset($errors));
            return redirect('admin/tour-categories/create');
        }

        $this->modelCategory->insert($data);
        setFlash('success', 'Thêm mới danh mục tour thành công!');
        return  redirect('admin/tour-categories');
    }

    public function edit($id) {
        $title='Cập nhật danh mục tour';
        $category=$this->modelCategory->findByid($id);
        if(!$category){
            setFlash('error', 'Danh mục tour không tồn tại');
            redirect('admin/tour-categories');
        }
        return view('tour-categories.edit', compact('title', 'category'));
    }

    public function update($id) {
        $data=[
            'name' =>$_POST['name'],
            'description' =>$_POST['description'],
        ];

        $rules=[
            'name' =>'required|max:255',
        ];

        $errors=$this->validate($this->validator, $data, $rules);
        if (!empty($errors)){
            setFlash('error', reset($errors));
            return redirect('admin/tour-categories/edit/' . $id);
        }

        $this->modelCategory->update($id, $data);
        setFlash('success', 'Cập nhật danh mục tour thành công!');
        return  redirect('admin/tour-categories');
    }

    public function delete($id) {
        $category=$this->modelCategory->findByid($id);
        if(!$category){
            setFlash('error', 'Danh mục tour không tồn tại');
            redirect('admin/tour-categories');
        }

        $tourCount = $this->modelCategory->countToursByCategory($id);
        if ($tourCount > 0) {
            setFlash('error', 'Không thể xóa danh mục đang có tour sử dụng');
            return redirect('admin/tour-categories');
        }
        
        $this->modelCategory->delete($id);
        setFlash('success', 'Xóa danh mục tour thành công!');
        return redirect('admin/tour-categories');
    }
}
