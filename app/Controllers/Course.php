<?php

namespace App\Controllers;
use App\Models\EnrollmentModel;

Class Course extends BaseController
{
    public function enroll(){

        if (!session()->get('isLoggedIn')) {
            return redirect()->to('login');
        }

        $enrollmentModel = new EnrollmentModel();
        $user_id = session()->get('userID');
        $course_id = $this->request->getPost('course_id');
        
        if ($enrollmentModel->isAlreadyEnrolled($user_id, $course_id)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Enrolled Naman ka ani nga course.']);
        }
        $data = [
            'user_id' => $user_id,
            'course_id' => $course_id,
            'enrollment_date' => date('Y-m-d'),
        ];
        $enrollmentModel->enrollUser($data);
        return $this->response->setJSON(['success' => true, 'message' => 'Enrolled Naka woaw']);
    }
}