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
        $UserModel = new UserModel();
        $CourseModel = new CourseModel();
        
        $admins = $UserModel->getUsersByRole('admin');
        $teachers = $UserModel->getUsersByRole('teacher');  
        $students = $UserModel->getUsersByRole('student');
        $courses = $CourseModel->getAllCourses();

        $data = [
            'admin' => $admins,
            'teacher' => $teachers,
            'student' => $students,
            'courses' => $courses
        ];
        
        return view('admin/dashboard', $data);
    }
}