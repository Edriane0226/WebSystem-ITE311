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
            'enrollmentStatus' => 2,
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
        
        // Use the $id parameter to get the specific course
        $course = $courseModel->getCourseWithDetails($id);
        
        if (!$course) {
            session()->setFlashdata('error', 'Course not found.');
            return redirect()->to('courses');
        }
        
        // Get teacher info if assigned
        $teacher = null;
        if (!empty($course['teacherID'])) {
            $teacher = $userModel->find($course['teacherID']);
        }

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
        $schoolYearModel = new SchoolYearModel();
        $courseStatusModel = new CourseStatusModel();

        $userRole = session()->get('role');
        $userRoleID = $userModel->getRoleIDByUserID($userRole);
        $teachers = $userModel->getTeacherByRoleID($userRoleID);

        $courseDescriptions = $courseModel->getCoursesWithDetails();

        $schoolYears = $schoolYearModel->getAllSchoolYears();

        $activeCourses = $courseModel->getActiveCoursesCount();

        $courseStatuses = $courseStatusModel->getAllStatuses();
        
        $data = [
            'course' => $courseDescriptions,
            'teachers' => $teachers,
            'schoolYears' => $schoolYears,
            'activeCourses' => $activeCourses,
            'role' => session()->get('role'),
            'courseStatuses' => $courseStatuses
        ];
        if ($this->request->getMethod() === 'POST') {
            $rules = [
                'courseCode' => 'required|is_unique[courses.courseCode]',
                'courseTitle' => 'required|is_unique[courses.courseTitle]',
                'courseDescription' => 'required',
                'teacherID' => 'required',
                'statusID' => 'required',
                'schoolYear' => 'required',
            ];
            if (!$this->validate($rules)) {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }
            $courseModel = new CourseModel();
            $data = [
                'courseCode' => $this->request->getPost('courseCode'),
                'courseTitle' => $this->request->getPost('courseTitle'),
                'courseDescription' => $this->request->getPost('courseDescription'),
                'teacherID' => $this->request->getPost('teacherID'),
                'statusID' => $this->request->getPost('statusID'),
                'schoolYearID' => $this->request->getPost('schoolYear')
            ];
            $courseModel->insert($data);

            $message = 'Course created successfully.';

            return redirect()->to('/course/manage')->with('message', $message);
        }else {
            return view('templates/header', $data) . view('courses/courseManagement');
        }
    }
    // This act will be like delete but just change the status
    public function setStatus($courseID)
    {
        if (!session()->get('isLoggedIn') || session()->get('role') != 'admin') {
            return redirect()->to('login');
        }
        
        if ($this->request->getMethod() !== 'POST') {
            return redirect()->back();
        }
        
        $statusID = $this->request->getPost('statusID');
        
        $courseModel = new CourseModel();
        
        // Get current course status
        $currentStatusID = $courseModel->checkCourseStatus($courseID);
        
        // Check if the new status is the same as current status
        if ($currentStatusID == $statusID) {
            return redirect()->back()->with('message', 'Course status is already set to the selected status.');
        }
        
        // Update the course status if different sa current stat
        try {
            $updateData = ['statusID' => $statusID];
            $courseModel->update($courseID, $updateData);
            return redirect()->back()->with('message', 'Course status updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update course status: ' . $e->getMessage());
        }
    }

    public function updateCourse($courseID)
    {
        if (!session()->get('isLoggedIn') || session()->get('role') != 'admin') {
            return redirect()->to('login');
        }

        $courseModel = new CourseModel();

        if ($this->request->getMethod() === 'POST') {
            $data = [
                'courseCode' => $this->request->getPost('courseCode'),
                'courseTitle' => $this->request->getPost('courseTitle'),
                'courseDescription' => $this->request->getPost('courseDescription'),
                'teacherID' => $this->request->getPost('teacherID'),
                'schoolYearID' => $this->request->getPost('schoolYearID')
            ];
            $courseModel->update($courseID, $data);

            return redirect()->to('/course/manage')->with('message', 'Course updated successfully.');
        }
    }
}