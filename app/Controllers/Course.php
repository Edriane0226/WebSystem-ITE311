<?php

namespace App\Controllers;
use App\Models\EnrollmentModel;
use App\Models\NotificationModel;

Class Course extends BaseController
{
    public function enroll(){

        if (!session()->get('isLoggedIn')) {
            return $this->response->setJSON([
                'failed' => true, 
                'message' => 'Please login to enroll.', 
                'csrfHash' => csrf_hash()
            ]);
        }

        $enrollmentModel = new EnrollmentModel();
        $user_id = session()->get('userID');
        $course_id = base64_decode($this->request->getPost('course_id'));
        
        if ($enrollmentModel->isAlreadyEnrolled($user_id, $course_id)) {
            return $this->response->setJSON(['success' => false,
                                             'message' => 'Enrolled Naman ka ani nga course.', 
                                            'csrfHash' => csrf_hash()]);
        }
        $data = [
            'user_id' => $user_id,
            'course_id' => $course_id,
            'enrollment_date' => date('Y-m-d'),
        ];
        $enrollmentModel->enrollUser($data);

        //Insert Notification after ma enroll
        $notif = new NotificationModel();
        $notif->createNotification($user_id, 'You have successfully enrolled in a course.');
        return $this->response->setJSON(['success' => true, 'message' => 'Enrolled Naka woaw']);
    }
}