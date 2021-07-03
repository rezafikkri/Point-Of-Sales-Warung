<?php namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\UsersModel;

class SignOut extends Controller
{
    public function index()
    {
        $session = session();

        // if sign in
        if($session->has('posw_sign_in_status')) {
            // update last sign in
            $model = new UsersModel;
            $model->update($_SESSION['posw_user_id'], ['last_sign_in' => date('Y-m-d H:i:s')]);

            // destroy session
            $session->destroy();
        }

        return redirect()->to('/');
    }
}
