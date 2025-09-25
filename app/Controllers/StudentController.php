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
        
        return view('reusables/sidebar') . view('auth/dashboard');
    }
}