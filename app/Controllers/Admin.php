<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\CourseModel; 
use CodeIgniter\Controller;

Class AdminController extends Controller {

    public function dashboard() {

        $session = session();

        if ($session->get('role') !== 'admin') {
            return redirect()->to('/login');
        }
        
        $session->setFlashdata('success', 'Welcome, Admin!. ' . $session->get('name'));

        return view('reusables/sidebar') . view('admin/dashboard');
    }
}