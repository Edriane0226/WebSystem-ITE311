<?php
namespace App\Controllers;

use App\Models\CourseModel;
use App\Models\EnrollmentModel; 
use CodeIgniter\Controller;

Class StudentController extends Controller {

    public function dashboard() {

        $session = session();

        if ($session->get('role') !== 'student') {
            return redirect()->to('/login');
        }


        $user_id = $session->get('userID');
        $CourseModel = new CourseModel();
        $EnrollmentModel = new EnrollmentModel();
        
        $courses = $CourseModel->getAllCourses();
        $enrollments = $EnrollmentModel->getEnrollmentsByStudent($user_id);

        $data = [
            'courses' => $courses,
            'enrollments' => $enrollments,
            'role' => $session->get('role')
        ];
        
        include  'app\Views\reusables\sideBar.php';
        return view('auth/dashboard', $data);
    }
}