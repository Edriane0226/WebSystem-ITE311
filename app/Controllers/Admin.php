<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\CourseModel; 
use CodeIgniter\Controller;

Class Admin extends Controller {

    public function dashboard() {

        $session = session();
        // Check session if login by admin
        if ($session->get('role') !== 'admin') {
            return redirect()->to('/login');
        }

        $session->setFlashdata('success', 'Welcome, Admin!. ' . $session->get('name'));

        return view('auth/admin_dashboard');
    }
}