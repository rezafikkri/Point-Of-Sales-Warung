<?php

namespace App\Controllers;

use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;
use CodeIgniter\Test\FeatureTestCase;
use App\Models\UsersModel;

class SignOutTest extends FeatureTestCase
{
    use FeatureTestTrait, DatabaseTestTrait;

    protected $refresh = true;
    protected $seed = 'Tests\Support\Database\Seeds\UsersSeeder';
    protected $seedOnce = true;
    protected $basePath = SUPPORTPATH.'Database/';
    protected $namespace = 'Tests\Support';
    protected $migrateOnce = true;

    public function setUp(): void
    {
        parent::setUp();

        $this->model = new UsersModel;
    }

    public function testGetSignOut()
    {
        $userId = '37e46a3d-bd18-440e-a473-daa6ea059b75';
        $values = [
            'posw_sign_in_status' => true,
            'posw_user_id' => $userId
        ];
        $result = $this->withSession($values)->get('/sign_out');

        $result->assertOK();
        $result->assertRedirectTo('/');

        $lastSignIn = $this->model->select('last_sign_in')->getWhere(['user_id' => $userId])->getRowArray()['last_sign_in'];
        $this->assertNotNull($lastSignIn);
    }

    public function testGetSignOutButNotSignInYet()
    {
        $this->get('/sign_out');

        $userId = '920d2c5a-cf6b-4d74-8937-141b243f4141';
        $lastSignIn = $this->model->select('last_sign_in')->getWhere(['user_id' => $userId])->getRowArray()['last_sign_in'];

        $this->assertNull($lastSignIn);
    }
}
