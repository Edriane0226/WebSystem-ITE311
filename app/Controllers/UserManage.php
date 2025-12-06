<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\CourseModel;
use App\Models\EnrollmentModel;
use App\Models\EnrollmentStatusModel;
use App\Models\RoleModel;

class UserManage extends BaseController
{
    private const STUDENT_ROLE_ID = 3;
    private const TEACHER_ROLE_ID = 2;

    private function resolveStudentRoleId(): int
    {
        $roleModel = new RoleModel();
        $role = $roleModel->where('role_name', 'student')->first();

        return (int) ($role['roleID'] ?? self::STUDENT_ROLE_ID);
    }

    private function resolveTeacherRoleId(): int
    {
        $roleModel = new RoleModel();
        $role = $roleModel->where('role_name', 'teacher')->first();

        return (int) ($role['roleID'] ?? self::TEACHER_ROLE_ID);
    }

    /**
     * Returns the list of role IDs an admin may assign.
     */
    private function getAssignableRoleIds(): array
    {
        $roles = [
            $this->resolveStudentRoleId(),
            $this->resolveTeacherRoleId(),
        ];

        return array_values(array_unique(array_map('intval', $roles)));
    }

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

            $roleModel = new RoleModel();
            $assignableRoleIds = $this->getAssignableRoleIds();
            $data['roleOptions'] = empty($assignableRoleIds)
                ? []
                : $roleModel->whereIn('roleID', $assignableRoleIds)->orderBy('role_name')->findAll();

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
    public function addStudent()
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return redirect()->to('login');
        }

        $rules = [
            'firstName' => 'required|min_length[2]|max_length[50]',
            'lastName' => 'required|min_length[2]|max_length[50]',
            'email' => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[6]',
            'role' => 'required|integer|is_not_unique[roles.roleID]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $userModel = new UserModel();

        $firstName = trim($this->request->getPost('firstName'));
        $lastName = trim($this->request->getPost('lastName'));
        $displayName = trim($firstName . ' ' . $lastName);

        $roleId = (int) $this->request->getPost('role');
        $roleModel = new RoleModel();
        $assignableRoleIds = $this->getAssignableRoleIds();
        $selectedRole = $roleModel->find($roleId);

        if (!$selectedRole || !in_array((int) $selectedRole['roleID'], $assignableRoleIds, true)) {
            return redirect()->back()->withInput()->with('error', 'Invalid role selection.');
        }

        $userData = [
            'name' => $displayName,
            'email' => trim($this->request->getPost('email')),
            'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'role' => (int) $selectedRole['roleID'],
        ];

        try {
            $userModel->insert($userData);
            return redirect()->to('/students/studentManagement')->with('message', 'Student created successfully.');
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
            'role' => 'permit_empty|integer|is_not_unique[roles.roleID]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->with('errors', $this->validator->getErrors());
        }

        $userModel = new UserModel();
        $roleModel = new RoleModel();
        $assignableRoleIds = $this->getAssignableRoleIds();

        $user = $userModel->find($userID);
        if (!$user) {
            return redirect()->back()->with('error', 'User not found.');
        }

        $currentRole = $roleModel->find((int) ($user['role'] ?? 0));
        $currentRoleName = strtolower($currentRole['role_name'] ?? '');

        $currentRoleId = (int) ($user['role'] ?? 0);
        $isAdminAccount = $currentRoleName === 'admin';

        if (!$isAdminAccount && !in_array($currentRoleId, $assignableRoleIds, true)) {
            return redirect()->back()->with('error', 'Only student and teacher accounts can be modified.');
        }

        $requestedRoleRaw = $this->request->getPost('role');
        $requestedRoleId = $requestedRoleRaw === null || $requestedRoleRaw === '' ? null : (int) $requestedRoleRaw;

        if ($isAdminAccount) {
            $requestedRoleId = $currentRoleId;
        }

        if (!$isAdminAccount) {
            if ($requestedRoleId === null) {
                return redirect()->back()->with('error', 'Role selection is required.');
            }

            if (!in_array($requestedRoleId, $assignableRoleIds, true)) {
                return redirect()->back()->with('error', 'Invalid role selection.');
            }
        }

        $requestedRole = $roleModel->find($requestedRoleId);

        if (!$requestedRole) {
            return redirect()->back()->with('error', 'Invalid role selection.');
        }

        $firstName = trim($this->request->getPost('firstName'));
        $lastName = trim($this->request->getPost('lastName'));
        $updateData = [
            'name' => trim($firstName . ' ' . $lastName),
            'email' => trim($this->request->getPost('email')),
            'role' => (int) $requestedRole['roleID'],
        ];

        try {
            $userModel->update($userID, $updateData);
            return redirect()->to('/students/studentManagement')->with('message', 'User updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update student: ' . $e->getMessage());
        }
    }

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

    $statuses = $statusModel->getAllStatuses();
    $enrolledStatusId = 1;
        $enrolledStatusName = 'Enrolled';
        foreach ($statuses as $status) {
            if ((int) ($status['statusID'] ?? 0) === $enrolledStatusId) {
                $enrolledStatusName = (string) ($status['statusName'] ?? $enrolledStatusName);
                break;
            }
        }

        if ($userRole === 'teacher') {
            $enrolledCourses = $enrollmentModel->getStudentEnrollmentsForTeacher($studentID, $currentUserID);
            $availableCourses = $courseModel->getTeacherAvailableCoursesForStudent($currentUserID, $studentID);

            if (!empty($availableCourses)) {
                foreach ($availableCourses as &$course) {
                    $course['defaultStatusID'] = $enrolledStatusId;
                    $course['defaultStatusName'] = $enrolledStatusName;
                    $course['defaultActionLabel'] = 'Enroll';
                }
                unset($course);
            }
        } elseif ($userRole === 'admin') {
            $enrolledCourses = $enrollmentModel->getStudentEnrollments($studentID);
            $availableCourses = $courseModel->getAvailableCoursesForStudent($studentID);
        } else {
            return $this->response->setJSON(['error' => 'Access denied']);
        }

        return $this->response->setJSON([
            'enrolledCourses' => $enrolledCourses,
            'availableCourses' => $availableCourses,
            'statuses' => $statuses,
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
            'enrollmentStatus' => 1
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

    public function createTeacherEnrollment()
    {
        if (!session()->get('isLoggedIn')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        if (session()->get('role') !== 'teacher') {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied']);
        }

        $teacherId = (int) session()->get('userID');
        $studentId = (int) $this->request->getPost('studentId');
        $courseId = (int) $this->request->getPost('courseId');
        $statusId = (int) $this->request->getPost('statusId');

        if ($studentId <= 0 || $courseId <= 0 || $statusId <= 0) {
            return $this->response->setJSON(['success' => false, 'message' => 'Missing or invalid data']);
        }

        $courseModel = new CourseModel();
        $course = $courseModel->find($courseId);

        if (!$course || (int) ($course['teacherID'] ?? 0) !== $teacherId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Course not found or not assigned to current teacher']);
        }

        $allowedStatuses = [1, 2, 3, 4];

        if (!in_array($statusId, $allowedStatuses, true)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid status selection']);
        }

        $enrollmentModel = new EnrollmentModel();
        $existing = $enrollmentModel
            ->where('user_id', $studentId)
            ->where('course_id', $courseId)
            ->first();

        try {
            if ($existing) {
                $updateData = [
                    'enrollmentStatus' => $statusId,
                ];

                if (empty($existing['enrollment_date'])) {
                    $updateData['enrollment_date'] = date('Y-m-d');
                }

                $enrollmentModel->update((int) $existing['enrollmentID'], $updateData);
            } else {
                if ($statusId === 3) {
                    return $this->response->setJSON(['success' => true, 'message' => 'Enrollment request declined.']);
                }

                $enrollmentModel->insert([
                    'user_id' => $studentId,
                    'course_id' => $courseId,
                    'enrollment_date' => date('Y-m-d'),
                    'enrollmentStatus' => $statusId,
                ]);
            }

            $message = 'Enrollment updated.';
            if ($statusId === 1) {
                $message = 'Enrollment approved.';
            } elseif ($statusId === 3) {
                $message = 'Enrollment declined.';
            } elseif ($statusId === 4) {
                $message = 'Enrollment set to pending.';
            }

            return $this->response->setJSON(['success' => true, 'message' => $message]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to update enrollment: ' . $e->getMessage(),
            ]);
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

        return redirect()->back()->with('error', 'Bulk status updates are not available.');
    }
}