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

        $user_id = $session->get('user_id');

        $UserModel = new UserModel();
        $CourseModel = new CourseModel();
        $EnrollmentModel = new EnrollmentModel();

        $students = $UserModel->getUsersByRole('student');
        $courses = $CourseModel->getCourses($user_id);
        //$enrollments = $EnrollmentModel->getEnrollmentsByCourse($courses);

        $data = [
            'students' => $students,
            'courses' => $courses,
            'role' => $session->get('role')
        ];
        
        include  'app\Views\reusables\sideBar.php';
        return view('auth/dashboard', $data);
    }
}