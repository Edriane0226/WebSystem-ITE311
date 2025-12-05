<?php

namespace App\Controllers;
use App\Models\EnrollmentModel;
use App\Models\NotificationModel;
use App\Models\CourseModel;
use App\Models\SchoolYearModel;
use App\Models\CourseStatusModel;
use App\Models\CourseOfferingModel;

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
            'enrollmentStatus' => 1,
        ];
        $enrollmentModel->enrollUser($data);

        //Insert Notification after ma enroll para sa student
        $notif = new NotificationModel();
        $notif->createNotification($user_id, 'You have successfully enrolled in a course.');

        
        return $this->response->setJSON(['success' => true, 
                                        'message' => 'Successfully Enrolled!', 
                                        'csrfHash' => csrf_hash()]);
}
    

    // lab 9 Search Server Side
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
    // add ons for lab 9 
    public function details($id)
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('login');
        }

        $courseModel = new CourseModel();
        $userModel = new UserModel();
        
        // get course with all the details 
        $course = $courseModel->getCourseWithDetails($id);
        
        if (!$course) {
            session()->setFlashdata('error', 'Course not found.');
            return redirect()->to('courses');
        }
        
        $teacher = $userModel->find($course['teacherID']);

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
        $courseOfferingModel = new CourseOfferingModel();

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
                'courseCode' => 'required|is_unique[courses.courseCode]|regex_match[/^[a-zA-Z0-9\s]+$/]',
                'courseTitle' => 'required|is_unique[courses.courseTitle|regex_match[/^[a-zA-Z0-9\sñÑ]+$/]',
                'courseDescription' => 'required',
                'teacherID' => 'required',
                'statusID' => 'required',
                'schoolYear' => 'required',
                'startDate' => 'required|valid_date[Y-m-d]',
                'endDate' => 'required|valid_date[Y-m-d]'
            ];
            if (!$this->validate($rules)) {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }

            $startDate = $this->request->getPost('startDate');
            $endDate = $this->request->getPost('endDate');
            $customErrors = [];

            if ($startDate && $endDate) {
                try {
                    $today = new \DateTimeImmutable('today');
                    $start = new \DateTimeImmutable($startDate);
                    $end = new \DateTimeImmutable($endDate);

                    if ($start < $today) {
                        $customErrors['startDate'] = 'Start date cannot be in the past.';
                    }

                    if ($end < $start) {
                        $customErrors['endDate'] = 'End date cannot be earlier than the start date.';
                    }
                } catch (\Exception $e) {
                    $customErrors['startDate'] = 'Invalid date selection.';
                }
            }

            if (!empty($customErrors)) {
                return redirect()->back()->withInput()->with('errors', $customErrors);
            }
            $courseModel = new CourseModel();
            $teacherId = $this->request->getPost('teacherID') ?: null;
            $data = [
                'courseCode' => $this->request->getPost('courseCode'),
                'courseTitle' => $this->request->getPost('courseTitle'),
                'courseDescription' => $this->request->getPost('courseDescription'),
                'teacherID' => $teacherId,
                'statusID' => $this->request->getPost('statusID'),
                'schoolYearID' => $this->request->getPost('schoolYear')
            ];
            $courseModel->insert($data);

            $courseID = $courseModel->getInsertID();
            if ($courseID) {
                $courseOfferingModel->insert([
                    'courseID' => $courseID,
                    'schoolYearID' => $this->request->getPost('schoolYear'),
                    'startDate' => $this->request->getPost('startDate') ?: null,
                    'endDate' => $this->request->getPost('endDate') ?: null,
                ]);
            }

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
        $courseOfferingModel = new CourseOfferingModel();

        if ($this->request->getMethod() === 'POST') {
            $rules = [
                'courseCode' => 'required',
                'courseTitle' => 'required',
                'courseDescription' => 'required',
                'teacherID' => 'required',
                'schoolYearID' => 'required',
                'startDate' => 'required|valid_date[Y-m-d]',
                'endDate' => 'required|valid_date[Y-m-d]'
            ];

            if (!$this->validate($rules)) {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }

            $startDate = $this->request->getPost('startDate');
            $endDate = $this->request->getPost('endDate');
            $customErrors = [];

            if ($startDate && $endDate) {
                try {
                    $today = new \DateTimeImmutable('today');
                    $start = new \DateTimeImmutable($startDate);
                    $end = new \DateTimeImmutable($endDate);

                    if ($start < $today) {
                        $customErrors['startDate'] = 'Start date cannot be in the past.';
                    }

                    if ($end < $start) {
                        $customErrors['endDate'] = 'End date cannot be earlier than the start date.';
                    }
                } catch (\Exception $e) {
                    $customErrors['startDate'] = 'Invalid date selection.';
                }
            }

            if (!empty($customErrors)) {
                return redirect()->back()->withInput()->with('errors', $customErrors);
            }

            $teacherId = $this->request->getPost('teacherID') ?: null;
            $data = [
                'courseCode' => $this->request->getPost('courseCode'),
                'courseTitle' => $this->request->getPost('courseTitle'),
                'courseDescription' => $this->request->getPost('courseDescription'),
                'teacherID' => $teacherId,
                'schoolYearID' => $this->request->getPost('schoolYearID')
            ];
            $courseModel->update($courseID, $data);

                $offeringData = [
                    'courseID' => $courseID,
                    'schoolYearID' => $this->request->getPost('schoolYearID'),
                    'startDate' => $this->request->getPost('startDate'),
                    'endDate' => $this->request->getPost('endDate'),
                ];

                $existingOffering = $courseOfferingModel->findByCourseId((int) $courseID);
                if ($existingOffering) {
                    $offeringData['offeringID'] = $existingOffering['offeringID'];
                }

                $courseOfferingModel->save($offeringData);

            return redirect()->to('/course/manage')->with('message', 'Course updated successfully.');
        }
    }
}