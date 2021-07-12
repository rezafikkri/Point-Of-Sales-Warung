<?php

namespace App\Controllers;

use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;
use CodeIgniter\Test\FeatureTestCase;

class CategoryProductTest extends FeatureTestCase
{
    use FeatureTestTrait, DatabaseTestTrait;

    protected $refresh = true;
    protected $basePath = SUPPORTPATH.'Database/';
    protected $namespace = 'Tests\Support';
    protected $migrateOnce = true;

    // admin sign in session data
    private $adminAccessRights = [
        'posw_sign_in_status' => true,
        'posw_user_id' => '37e46a3d-bd18-440e-a473-daa6ea059b75',
        'posw_user_level' => 'admin',
        'posw_user_full_name' => 'Reza Sariful Fikri'
    ];

    public function testGetCategoryProductButNotSignedIn()
    {
        $result = $this->get('/admin/kategori_produk');
        $result->assertOK();
        $result->assertRedirectTo('/');
    }

    public function testGetCategoryProductHasSignedInButNotAdmin()
    {
        $values = [
            'posw_sign_in_status' => true,
            'posw_user_level' => 'cashier'
        ];
        $result = $this->withSession($values)->get('/admin/kategori_produk');

        $result->assertOK();
        $result->assertRedirectTo('/sign_out');
    }

    public function testGetCategoryProductButNotDataInDb()
    {
        $result = $this->withSession($this->adminAccessRights)->get('/admin/kategori_produk');

        $result->assertOK();
        $result->assertSee('Kategori Produk . POSW', 'title');
        $result->assertSee('Kategori produk tidak ada.', 'td');
    }

    public function testGetCreateCategoryProduct()
    {
        $result = $this->withSession($this->adminAccessRights)->get('/admin/buat_kategori_produk');

        $result->assertOK();
        $result->assertSee('Buat Kategori Produk . POSW', 'title');
    }

    public function testPostCreateCategoryProduct()
    {
        $result = $this->withSession($this->adminAccessRights)->post('/admin/buat_kategori_produk', [
            'category_product_name' => 'Mie ayam'
        ]);

        $result->assertOK();
        $result->assertRedirectTo('/admin/kategori_produk');

        $criteria = [
            'category_product_name' => 'Mie ayam'
        ];
        $this->seeInDatabase('category_products', $criteria);
    }

    /**
     * @depends testPostCreateCategoryProduct
     */
    public function testGetCategoryProductWithDataInDb()
    {
        $result = $this->withSession($this->adminAccessRights)->get('/admin/kategori_produk');

        $result->assertOK();
        $result->assertSee('Kategori Produk . POSW', 'title');
        $result->assertDontSee('Kategori produk tidak ada.', 'td');
    }

    public function testPostCreateCategoryProductButNotSendCategoryProductName()
    {
        $result = $this->withSession($this->adminAccessRights)->post('/admin/buat_kategori_produk', [
            'category_product_name' => ''
        ]);

        $result->assertOK();
        $result->assertRedirect();
        $result->assertSessionHas('errors');
        $this->assertSame(
            $_SESSION['errors']['category_product_name'],
            '<small class="form-message form-message--danger">Nama Kategori Produk tidak boleh kosong!</small>'
        );

        $criteria = [
            'category_product_name' => 'Minyak sanco'
        ];
        $this->dontSeeInDatabase('category_products', $criteria);
    }
}
