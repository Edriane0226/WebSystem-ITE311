<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\CourseModel;
use App\Models\EnrollmentModel;
use App\Models\EnrollmentStatusModel;

class UserManage extends BaseController
{
    public function UserManagement()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('login');
        }

        $userRole = session()->get('role');
            $userID = session()->get('userID');
        
        $userModel = new UserModel();
        $courseModel = new CourseModel();
        $enrollmentModel = new EnrollmentModel();

        $data = [
            'role' => $userRole,
            'activeEnrollments' => $enrollmentModel->countActiveEnrollments(),
        ];

        if ($userRole === 'admin') {
            $users = $userModel->getAllUsersWithRole();
            $data['users'] = $users;

            $studentCount = 0;
            foreach ($users as $user) {
                if (isset($user['role_name']) && strtolower($user['role_name']) === 'student') {
                    $studentCount++;
                }
            }

            $data['studentCount'] = $studentCount;
        } elseif ($userRole === 'teacher') {
            $teacherCourses = $courseModel->getCoursesByTeacher($userID);
            $students = $userModel->getStudentsByTeacherCourses($userID);
            $data['teacherCourses'] = $teacherCourses;
            $data['users'] = $students;
        } else {
            // Students cannot access this page
            return redirect()->to('dashboard')->with('error', 'Access denied.');
        }

        return view('templates/header', ['role' => $userRole]) . 
               view('students/studentManagement', $data);
    }

    // Admin Create new student
    public function save()
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return redirect()->to('login');
        }

        $rules = [
            'firstName' => 'required|min_length[2]|max_length[50]',
            'lastName' => 'required|min_length[2]|max_length[50]',
            'email' => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[6]',
            'status' => 'in_list[active,inactive]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $userModel = new UserModel();
        
        $userData = [
            'firstName' => $this->request->getPost('firstName'),
            'lastName' => $this->request->getPost('lastName'),
            'email' => $this->request->getPost('email'),
            'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'role' => 'student',
            'status' => $this->request->getPost('status') ?? 'active',
            'created_at' => date('Y-m-d H:i:s')
        ];

        try {
            $userModel->insert($userData);
            return redirect()->to('students')->with('message', 'Student created successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Failed to create student: ' . $e->getMessage());
        }
    }

    // Admin only - Update student
    public function update()
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return redirect()->to('login');
        }

        $userID = $this->request->getPost('userID');
        
        $rules = [
            'firstName' => 'required|min_length[2]|max_length[50]',
            'lastName' => 'required|min_length[2]|max_length[50]',
            'email' => "required|valid_email|is_unique[users.email,userID,{$userID}]",
            'status' => 'in_list[active,inactive]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->with('errors', $this->validator->getErrors());
        }

        $userModel = new UserModel();
        
        // Check if user exists and is a student
        $user = $userModel->find($userID);
        if (!$user || $user['role'] !== 'student') {
            return redirect()->back()->with('error', 'Student not found.');
        }

        $updateData = [
            'firstName' => $this->request->getPost('firstName'),
            'lastName' => $this->request->getPost('lastName'),
            'email' => $this->request->getPost('email'),
            'status' => $this->request->getPost('status'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        try {
            $userModel->update($userID, $updateData);
            return redirect()->to('students')->with('message', 'Student updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update student: ' . $e->getMessage());
        }
    }

    // Admin only - Delete student
    // public function delete($userID)
    // {
    //     if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
    //         return redirect()->to('login');
    //     }

    //     $userModel = new UserModel();
    //     $enrollmentModel = new EnrollmentModel();
        
    //     // Check if user exists and is a student
    //     $user = $userModel->find($userID);
    //     if (!$user || $user['role'] !== 'student') {
    //         return redirect()->back()->with('error', 'Student not found.');
    //     }

    //     try {
    //         // First delete all enrollments
    //         $enrollmentModel->where('user_id', $userID)->delete();
            
    //         // Then delete the user
    //         $userModel->delete($userID);
            
    //         return redirect()->to('students')->with('message', 'Student deleted successfully.');
    //     } catch (\Exception $e) {
    //         return redirect()->back()->with('error', 'Failed to delete student: ' . $e->getMessage());
    //     }
    // }

    // Get enrollment data for a student
    public function getEnrollmentData($studentID)
    {
        if (!session()->get('isLoggedIn')) {
            return $this->response->setJSON(['error' => 'Unauthorized']);
        }

        $userRole = session()->get('role');
        $currentUserID = session()->get('userID');

        $courseModel = new CourseModel();
        $enrollmentModel = new EnrollmentModel();
        $statusModel = new EnrollmentStatusModel();

        if ($userRole === 'teacher') {
            // Teachers can see all enrollments but may only edit their own
            $enrolledCourses = $enrollmentModel->getStudentEnrollments($studentID);
            $availableCourses = [];
        } elseif ($userRole === 'admin') {
            $enrolledCourses = $enrollmentModel->getStudentEnrollments($studentID);
            $availableCourses = $courseModel->getAvailableCoursesForStudent($studentID);
        } else {
            return $this->response->setJSON(['error' => 'Access denied']);
        }

        return $this->response->setJSON([
            'enrolledCourses' => $enrolledCourses,
            'availableCourses' => $availableCourses,
            'statuses' => $statusModel->getAllStatuses(),
            'teacherId' => $userRole === 'teacher' ? (int) $currentUserID : null,
        ]);
    }

    // Enroll student in course
    public function enroll()
    {
        if (!session()->get('isLoggedIn')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        $userRole = session()->get('role');
        
        if ($userRole !== 'admin') {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied']);
        }

        $input = json_decode($this->request->getBody(), true);
        $studentId = $input['studentId'] ?? null;
        $courseId = $input['courseId'] ?? null;

        if (!$studentId || !$courseId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Missing required data']);
        }

        $enrollmentModel = new EnrollmentModel();
        // Check if already enrolled
        if ($enrollmentModel->isAlreadyEnrolled($studentId, $courseId)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Student already enrolled in this course']);
        }

        $enrollmentData = [
            'user_id' => $studentId,
            'course_id' => $courseId,
            'enrollment_date' => date('Y-m-d'),
            'enrollmentStatus' => 2
        ];

        try {
            $enrollmentModel->insert($enrollmentData);
            return $this->response->setJSON(['success' => true, 'message' => 'Student enrolled successfully']);
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to enroll student: ' . $e->getMessage()]);
        }
    }

    // Unenroll student from course
    public function unenroll($enrollmentID)
    {
        if (!session()->get('isLoggedIn')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        $userRole = session()->get('role');
        
        if ($userRole !== 'admin') {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied']);
        }

        $enrollmentModel = new EnrollmentModel();
        $courseModel = new CourseModel();

        // Get enrollment details
        $enrollment = $enrollmentModel->find($enrollmentID);
        if (!$enrollment) {
            return $this->response->setJSON(['success' => false, 'message' => 'Enrollment not found']);
        }

        try {
            $enrollmentModel->delete($enrollmentID);
            return $this->response->setJSON(['success' => true, 'message' => 'Student unenrolled successfully']);
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to unenroll student: ' . $e->getMessage()]);
        }
    }

    public function updateEnrollmentStatus()
    {
        if (!session()->get('isLoggedIn')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        $userRole = session()->get('role');
        $currentUserID = session()->get('userID');

        if (!in_array($userRole, ['admin', 'teacher'])) {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied']);
        }

        $enrollmentID = $this->request->getPost('enrollmentId');
        $statusID = $this->request->getPost('statusId');

        if (!$enrollmentID || !$statusID) {
            return $this->response->setJSON(['success' => false, 'message' => 'Missing required data']);
        }

        $enrollmentModel = new EnrollmentModel();
        $enrollment = $enrollmentModel->getEnrollmentWithCourse($enrollmentID);

        if (!$enrollment) {
            return $this->response->setJSON(['success' => false, 'message' => 'Enrollment not found']);
        }

        if ($userRole === 'teacher' && (int) $enrollment['teacherID'] !== (int) $currentUserID) {
            return $this->response->setJSON(['success' => false, 'message' => 'Not authorized for this enrollment']);
        }

        try {
            $enrollmentModel->updateEnrollmentStatus($enrollmentID, $statusID);
            return $this->response->setJSON(['success' => true, 'message' => 'Enrollment status updated']);
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to update enrollment: ' . $e->getMessage()]);
        }
    }

    // Get students by course
    public function getStudentsByCourse($courseID)
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'teacher') {
            return $this->response->setJSON(['error' => 'Unauthorized']);
        }

        $currentUserID = session()->get('userID');
        $courseModel = new CourseModel();
        
        // Check if teacher owns this course
        $course = $courseModel->find($courseID);
        if (!$course || $course['teacherID'] != $currentUserID) {
            return $this->response->setJSON(['error' => 'Access denied']);
        }

        $userModel = new UserModel();
        $students = $userModel->getStudentsByCourse($courseID);

        return $this->response->setJSON(['students' => $students]);
    }

    // Bulk operations (Admin only)
    public function admin()
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return redirect()->to('login');
        }

        $action = $this->request->getPost('action');
        $studentIDs = $this->request->getPost('student_ids');

        if (!$action || !$studentIDs) {
            return redirect()->back()->with('error', 'Invalid action or no students selected');
        }

        $userModel = new UserModel();
        $enrollmentModel = new EnrollmentModel();

        try {
            switch ($action) {
                case 'activate':
                    $userModel->whereIn('userID', $studentIDs)->set(['status' => 'active'])->update();
                    $message = 'Selected students activated successfully';
                    break;
                
                case 'deactivate':
                    $userModel->whereIn('userID', $studentIDs)->set(['status' => 'inactive'])->update();
                    $message = 'Selected students deactivated successfully';
                    break;
                default:
                    return redirect()->back()->with('error', 'Invalid action');
            }

            return redirect()->to('students')->with('message', $message);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to perform action: ' . $e->getMessage());
        }
    }
}