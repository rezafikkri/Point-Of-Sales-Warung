<?php

namespace App\Controllers;

use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;
use CodeIgniter\Test\FeatureTestCase;

class SignInTest extends FeatureTestCase
{
    use FeatureTestTrait, DatabaseTestTrait;

    protected $refresh = true;
    protected $seed = 'Tests\Support\Database\Seeds\UsersSeeder';
    protected $seedOnce = true;
    protected $basePath = SUPPORTPATH.'Database/';
    protected $namespace = 'Tests\Support';
    protected $migrateOnce = true;

    public function testGetSignIn()
    {
        $result = $this->get('/');
        $result->assertOK();
    }

    public function testGetSignInButSignedInWithAdminAccessRights()
    {
        $result = $this->withSession(['posw_sign_in_status' => true, 'posw_user_level' => 'admin'])
                       ->get('/');
        $result->assertOK();
        $result->assertRedirectTo('/admin');
    }

    public function testGetSignInButSignedInWithCashierAccessRights()
    {
        $result = $this->withSession(['posw_sign_in_status' => true, 'posw_user_level' => 'cashier'])
                       ->get('/');
        $result->assertOK();
        $result->assertRedirectTo('/kasir');
    }

    public function testPostSignInWithAdminAccessRights()
    {
        $result = $this->post('/sign_in', [
            'username' => 'reza',
            'password' => 'reza'
        ]);

        $result->assertOK();
        $result->assertRedirectTo('/admin');
        $result->assertSessionHas('posw_sign_in_status', true);
        $result->assertSessionHas('posw_user_id');
        $result->assertSessionHas('posw_user_level', 'admin');
        $result->assertSessionHas('posw_user_full_name', 'Reza Sariful Fikri');
    }

    public function testPostSignInWithCashierAccessRights()
    {
        $result = $this->post('/sign_in', [
            'username' => 'dian',
            'password' => 'dian'
        ]);

        $result->assertOK();
        $result->assertRedirectTo('/kasir');
        $result->assertSessionHas('posw_sign_in_status', true);
        $result->assertSessionHas('posw_user_id');
        $result->assertSessionHas('posw_user_level', 'cashier');
        $result->assertSessionHas('posw_user_full_name', 'Dian Pranata');
    }

    public function testPostSignInWithInvalidUsername()
    {
        $result = $this->post('/sign_in', [
            'username' => 'adel',
            'password' => 'adel'
        ]);

        $result->assertOK();
        $result->assertRedirectTo('/');
        $result->assertSessionHas(
            'errors',
            [
                'username' => '<small class="form-message form-message--danger">Username tidak ditemukan.</small>'
            ]
        );
    }

    public function testPostSignInWithInvalidPassword()
    {
        $result = $this->post('/sign_in', [
            'username' => 'reza',
            'password' => 'adel'
        ]);

        $result->assertOK();
        $result->assertRedirectTo('/');
        $result->assertSessionHas(
            'errors',
            [
                'password' => '<small class="form-message form-message--danger">Password salah.</small>'
            ]
        );
    }

    public function testPostSignInWithoutSendPasswordButSendUsername()
    {
        $result = $this->post('/sign_in', [
            'username' => 'reza'
        ]);

        $result->assertOK();
        $result->assertRedirectTo('/');
        $result->assertSessionHas(
            'errors',
            [
                'password' => '<small class="form-message form-message--danger">Password tidak boleh kosong!</small>'
            ]
        );
    }

    public function testPostSignInWithoutSendUsernamePassword()
    {
        $result = $this->post('/sign_in');

        $result->assertOK();
        $result->assertRedirectTo('/');
        $result->assertSessionHas(
            'errors',
            [
                'username' => '<small class="form-message form-message--danger">Username tidak boleh kosong!</small>',
                'password' => '<small class="form-message form-message--danger">Password tidak boleh kosong!</small>'
            ]
        );
    }
}
