<?php

namespace App\Controllers;

use App\Models\UsersModel;
use CodeIgniter\Config\Factories;

class SignIn extends BaseController
{
    protected $helpers = ['form'];

    public function index()
    {
        $method = $this->request->getMethod();

        // if method post and error not exists
        if ($method === 'post' && $this->validate([
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
            $username = $this->request->getPost('username', FILTER_SANITIZE_STRING);
            $password = $this->request->getPost('password', FILTER_SANITIZE_STRING);

            return $this->signin();
        }

        // if method = post and error exists
        if ($method === 'post' && $this->hasErrors([
            'username',
            'password'
        ])) {
             // set validation error messages to flash session
            $session->setFlashData('errors', $this->addDelimiterMessages($this->validator->getErrors()));
            return redirect()->back()->withInput();
        }

        return view('signin');
    }

    private function signIn(string $username, string $password)
    {
        $session = session();

        $userModel = new UsersModel;
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
