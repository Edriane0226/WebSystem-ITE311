<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\CourseModel;
use App\Models\EnrollmentModel;
use CodeIgniter\Controller;

Class Teacher extends Controller {

    public function dashboard() {
        // Check session if login by teacher
        $session = session();
        if($session->get('role') != 'teacher') {
            return redirect()->to('/login');
        }
        // kung ok ang session mag set ng flashdata tapos diretso na sa teacher_dashboard view
        $session->setFlashdata('success', 'Welcome, Teacher!. ' . $session->get('name'));

        return view('auth/teacher_dashboard');
    }
}