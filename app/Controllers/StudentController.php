<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\CourseModel;
use App\Models\EnrollmentModel; 
use CodeIgniter\Controller;

Class AdminController extends BaseController {

    public function dashboard() {

        $session = session();

        if (!$session->get('role') == 'student') {
            return redirect()->to('/login');
        }

        $user_id = $session->get('user_id');

        $CourseModel = new CourseModel();
        $EnrollmentModel = new EnrollmentModel();
        
        $courses = $CourseModel->getCourses();
        $enrollments = $EnrollmentModel->getEnrollmentsByStudent($user_id);

        $data = [
            'courses' => $courses,
            'enrollments' => $enrollments
        ];

        return view('student/dashboard', $data);
    }
}