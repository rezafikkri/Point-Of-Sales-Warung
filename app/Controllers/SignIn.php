<?php

namespace App\Controllers;

use App\Models\UsersModel;
use CodeIgniter\Config\Factories;

class SignIn extends BaseController
{
    protected $helpers = ['form'];

    public function index()
    {
        if ($this->request->getMethod() === 'post') {
            return $this->signin();
        }

        return view('signin');
    }

    private function signIn()
    {
        $session = session();

        if(!$this->validate([
            'username' => [
                'label' => 'Username',
                'rules' => 'required',
                'errors' => $this->createIndoErrorMessages(['required'])
            ],
            'password' => [
                'label' => 'Password',
                'rules' => 'required',
                'errors' => $this->createIndoErrorMessages(['required'])
            ]
        ])) {
            // set validation error messages to flash session
            $session->setFlashData('errors', $this->addDelimiterMessages($this->validator->getErrors()));
            return redirect()->back()->withInput();
        }

        $userModel = new UsersModel;

        $username = $this->request->getPost('username', FILTER_SANITIZE_STRING);
        $password = $this->request->getPost('password', FILTER_SANITIZE_STRING);
        $userSignIn = $userModel->getUserSignIn($username);

        if($userSignIn !== null) {
            // if sign in success
            if(password_verify($password, $userSignIn['password']) === true) {
                $session->set([
                    'posw_sign_in_status' => true,
                    'posw_user_id' => $userSignIn['user_id'],
                    'posw_user_level' => $userSignIn['level'],
                    'posw_user_full_name' => $userSignIn['full_name']
                ]);

                // if user level is admin
                if($_SESSION['posw_user_level'] === 'admin') {
                    return redirect()->to('/admin');
                }
                return redirect()->to('/kasir');
            }

            // if password is wrong
            $session->setFlashData('errors', $this->addDelimiterMessages(['password' => 'Password salah.']));
            return redirect()->back();
        }

        // if username not found
        $session->setFlashData('errors', $this->addDelimiterMessages(['username' => 'Username tidak ditemukan.']));
        return redirect()->back();
    }
}
