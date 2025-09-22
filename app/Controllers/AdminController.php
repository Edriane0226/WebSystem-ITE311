<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\CourseModel; 
use CodeIgniter\Controller;

Class AdminController extends BaseController {

    public function dashboard() {

        $session = session();

        if (!$session->get('role') == 'admin') {
            return redirect()->to('/login');
        }

        $UserModel = new UserModel();
        $courseModel = new CourseModel();
        
        $admins = $UserModel->getUsersByRole('admin');
        $teachers = $UserModel->getUsersByRole('teacher');  
        $students = $UserModel->getUsersByRole('student');
        $courses = $courseModel->getCourses();

        $data = [
            'admin' => $admins,
            'teacher' => $teachers,
            'student' => $students,
            'courses' => $courses
        ];

        return view('admin/dashboard', $data);
    }
}