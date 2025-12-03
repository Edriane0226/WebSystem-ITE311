<?php

namespace App\Controllers;
use App\Models\EnrollmentModel;
use App\Models\NotificationModel;
use App\Models\CourseModel;
use App\Models\SchoolYearModel;
use App\Models\CourseStatusModel;

use App\Models\UserModel;

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
    
    public function search()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('login');
        }

        $searchTerm = $this->request->getGet('search_term');

        $courseModel = new CourseModel();
        
        if (!empty($searchTerm)) {
           $courseModel->like('courseTitle', $searchTerm);
           $courseModel->orLike('courseDescription', $searchTerm);
        }

        $courses = $courseModel->findAll();

        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['courses' => $courses]);
        }
        $role = session()->get('role');
        return view('templates/header', ['role' => $role]) . view('courses/index', ['courses' => $courses, 'searchTerm' => $searchTerm]);
    }

    public function details($id)
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('login');
        }

        $courseModel = new CourseModel();
        $userModel = new UserModel();
        $teacherId = $courseModel->getTeacherIdByCourse($id);
        $teacher = $userModel->find($teacherId);
        $course = $courseModel->find($id);

        $data = [
            'course' => $course,
            'teacher' => $teacher,
        ];

        $role = session()->get('role');
        return view('templates/header', ['role' => $role]) . view('courses/details', $data);
    }

    // CRUD Operations for course

    public function createCourse()
    {
        if (!session()->get('isLoggedIn') || session()->get('role') != 'admin') {
            return redirect()->to('login');
        }
        
        $courseModel = new CourseModel();
        $userModel = new UserModel();

        $userRole = session()->get('role');
        $userRoleID = $userModel->getRoleIDByUserID($userRole);
        $teachers = $userModel->getTeacherByRoleID($userRoleID);

        $courseDescriptions = $courseModel->getCoursesWithDetails();


        $activeCourses = $courseModel->getActiveCoursesCount();
        $data = [
            'course' => $courseDescriptions,
            'teachers' => $teachers,
            'activeCourses' => $activeCourses,
            'role' => session()->get('role')
        ];
        if ($this->request->getMethod() === 'POST') {
            $courseModel = new CourseModel();
            $data = [
                'courseCode' => $this->request->getPost('courseCode'),
                'courseTitle' => $this->request->getPost('courseTitle'),
                'courseDescription' => $this->request->getPost('courseDescription'),
                'teacherID' => $this->request->getPost('teacherID'),
                'schoolYearID' => $this->request->getPost('schoolYearID'),
                'statusID' => $this->request->getPost('statusID'),

            ];
            $courseModel->insert($data);

            $message = 'Course created successfully.';
            return view('templates/header') . view('courses/courseManagement', ['message' => $message]);
        }else {
            return view('templates/header', $data) . view('courses/courseManagement');
        }
    }

    public function deleteCourse($id)
    {
        if (!session()->get('isLoggedIn') || session()->get('role') != 'admin') {
            return redirect()->to('login');
        }

        $courseModel = new CourseModel();

        

        return redirect()->to('/courses/manage')->with('message', 'Course deleted successfully.');
    }
}