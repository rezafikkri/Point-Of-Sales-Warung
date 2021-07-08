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
}
