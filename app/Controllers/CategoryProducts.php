<?php

namespace App\Controllers;

use App\Models\CategoryProductsModel;

class CategoryProducts extends BaseController
{
    protected $helpers = ['form', 'active_menu', 'generate_uuid'];

    public function __construct()
    {
        $this->model = new CategoryProductsModel;
        $this->session = session();
    }

    public function index()
    {
        $data['title'] = 'Kategori Produk . POSW';
        $data['page'] = 'kategori_produk';
        $data['categoryProducts'] = $this->model->getCategoryProducts();

        return view('category-products/category_products', $data);
    }

    public function createCategoryProduct()
    {
        $data['title'] = 'Buat Kategori Produk . POSW';
        $data['page'] = 'buat_kategori_produk';

        return view('category-product/create_category_product', $data);
    }

    public function saveCategoryProductToDB()
    {
        if(!$this->validate([
            'category_product_name' => [
                'label' => 'Nama Kategori Produk',
                'rules' => 'required|max_length[20]',
                'errors' => $this->generateIndoErrorMessages(['required','max_length'])
            ]
        ])) {
            // set validation errors message to flash session
            $this->session->setFlashData('form_errors', $this->setDelimiterMessages(
                '<small class="form-message form-message--danger">',
                '</small>',
                $this->validator->getErrors()
            ));

            return redirect()->back()->withInput();
        }

        $this->model->insert([
            'kategori_produk_id' => generate_uuid(),
            'nama_kategori_produk' => $this->request->getPost('category_product_name', FILTER_SANITIZE_STRING),
            'waktu_buat' => date('Y-m-d H:i:s')
        ]);
        return redirect()->to('/admin/kategori_produk');
    }

    public function updateCategoryProduct(string $category_product_id)
    {
        $category_product_id = filter_var($category_product_id, FILTER_SANITIZE_STRING);

        $data['title'] = 'Perbaharui Kategori Produk . POSW';
        $data['page'] = 'perbaharui_kategori_produk';
        $data['category_product_id'] = $category_product_id;
        $data['category_product_db'] = $this->model->findCategoryProduct($category_product_id);

        return view('category-product/update_category_product', $data);
    }

    public function updateCategoryProductInDB()
    {
        if(!$this->validate([
            'category_product_name' => [
                'label' => 'Nama Kategori Produk',
                'rules' => 'required|max_length[20]',
                'errors' => $this->generateIndoErrorMessages(['required','max_length'])
            ]
        ])) {
            // set validation errors message to flash session
            $this->session->setFlashData('form_errors', $this->setDelimiterMessages(
                '<small class="form-message form-message--danger">',
                '</small>',
                $this->validator->getErrors()
            ));
            return redirect()->back();
        }

        // update category product
        $category_product_id = $this->request->getPost('category_product_id', FILTER_SANITIZE_STRING);
        if($this->model->update($category_product_id, [
            'nama_kategori_produk' => $this->request->getPost('category_product_name', FILTER_SANITIZE_STRING),
            'waktu_buat' => date('Y-m-d H:i:s')
        ])) {
            // make success message
            $this->session->setFlashData('form_success', $this->setDelimiterMessages(
                '<div class="alert alert--success mb-3"><span class="alert__icon"></span><p>',
                '</p><a class="alert__close" href="#"></a></div>',
                ['update_category_product' => 'Kategori produk telah diperbaharui.']
            ));
        }
        return redirect()->back();
    }

    public function removeCategoryProductInDB()
    {
        $category_product_id = $this->request->getPost('category_product_id', FILTER_SANITIZE_STRING);
        if($this->model->removeCategoryProduct($category_product_id) > 0) {
            return json_encode([
                'status' => 'success',
                'csrf_value' => csrf_hash()
            ]);
        }

        $error_message = 'Gagal menghapus kategori produk, cek apakah masih ada produk yang terhubung! <a href="https://github.com/rezafikkri/Point-Of-Sales-Warung/wiki/Kategori-Produk#gagal-menghapus-kategori" target="_blank" rel="noreferrer noopener">Pelajari lebih lanjut!</a>';
        return json_encode([
            'status' => 'fail',
            'message' => $error_message,
            'csrf_value' => csrf_hash()
        ]);
    }
}
