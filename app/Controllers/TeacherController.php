<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\CourseModel;
use App\Models\EnrollmentModel;
use CodeIgniter\Controller;

Class TeacherController extends Controller {

    public function dashboard() {

        $session = session();

        if ($session->get('role') !== 'teacher') {
            return redirect()->to('/login');
        }

        return view('reusables/sidebar') . view('auth/dashboard');
    }
}