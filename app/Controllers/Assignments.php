<?php

namespace App\Controllers;

use App\Models\AssignmentAttemptModel;
use App\Models\AssignmentModel;
use App\Models\CourseModel;
use App\Models\EnrollmentModel;
use App\Models\MaterialModel;
use App\Models\SubmissionModel;
use App\Models\UserModel;
use CodeIgniter\Controller;

class Assignments extends Controller
{
    public function index($courseID)
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('login');
        }

        $courseId = (int) $courseID;
        $courseModel = new CourseModel();
        $course = $courseModel->getCourseWithDetails($courseId);

        if (!$course) {
            return redirect()->to('/course/search')->with('error', 'Course not found.');
        }

        $role = $session->get('role');
        $userId = (int) $session->get('userID');

        $assignmentModel = new AssignmentModel();
        $assignments = $assignmentModel->getAssignmentsByCourse($courseId);

        $submissionModel = new SubmissionModel();
        $studentSubmissions = [];
        $submissionCounts = [];

        if ($role === 'student') {
            $enrollmentModel = new EnrollmentModel();
            if (!$enrollmentModel->isAlreadyEnrolled($userId, $courseId)) {
                return redirect()->to('/course/search')->with('error', 'You are not enrolled in this course.');
            }
            $studentSubmissions = $submissionModel->getSubmissionsForStudentByCourse($userId, $courseId);
        } else {
            $assignmentIds = array_map(static function (array $row): int {
                return (int) $row['AssignmentID'];
            }, $assignments);
            $submissionCounts = $submissionModel->getCountsByAssignment($assignmentIds);
        }

        $teacher = null;
        if (!empty($course['teacherID'])) {
            $teacher = (new UserModel())->find($course['teacherID']);
        }

        $viewData = [
            'course' => $course,
            'assignments' => $assignments,
            'role' => $role,
            'studentSubmissions' => $studentSubmissions,
            'submissionCounts' => $submissionCounts,
            'teacher' => $teacher,
        ];

        return view('templates/header', ['role' => $role])
            . view('courses/course/assignments', $viewData);
    }

    public function create($courseID)
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('login');
        }

        $role = $session->get('role');
        if (!in_array($role, ['admin', 'teacher'], true)) {
            return redirect()->back()->with('error', 'You are not authorized to create assignments.');
        }

        $courseId = (int) $courseID;
        $courseModel = new CourseModel();
        if (!$courseModel->find($courseId)) {
            return redirect()->to('/course/search')->with('error', 'Course not found.');
        }

        $rules = [
            'assignment_file' => 'uploaded[assignment_file]|max_size[assignment_file,102400]|ext_in[assignment_file,pdf,doc,docx,ppt,pptx,txt,zip,rar]',
            'allowedAttempts' => 'permit_empty|integer|greater_than_equal_to[1]|less_than_equal_to[9]',
            'publishDate' => 'permit_empty|valid_date[Y-m-d]',
            'dueDate' => 'permit_empty|valid_date[Y-m-d]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $file = $this->request->getFile('assignment_file');
        if (!$file || !$file->isValid()) {
            return redirect()->back()->withInput()->with('error', 'Invalid file upload.');
        }

        $publishDate = $this->request->getPost('publishDate');
        $dueDate = $this->request->getPost('dueDate');

        if (!empty($publishDate) && !empty($dueDate)) {
            try {
                $publish = new \DateTimeImmutable($publishDate);
                $due = new \DateTimeImmutable($dueDate);
                if ($due < $publish) {
                    return redirect()->back()->withInput()->with('error', 'Due date cannot be earlier than the publish date.');
                }
            } catch (\Exception $exception) {
                return redirect()->back()->withInput()->with('error', 'Invalid date provided.');
            }
        }

        $assignmentsDir = WRITEPATH . 'materials/assignments';
        if (!is_dir($assignmentsDir) && !mkdir($assignmentsDir, 0755, true)) {
            return redirect()->back()->with('error', 'Failed to prepare storage directory.');
        }

        $newName = $file->getRandomName();
        if (!$file->move($assignmentsDir, $newName)) {
            return redirect()->back()->with('error', 'Failed to store the assignment file.');
        }

        $materialModel = new MaterialModel();
        $materialId = $materialModel->insert([
            'course_id' => $courseId,
            'file_name' => $file->getClientName(),
            'file_path' => rtrim($assignmentsDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $newName,
            'created_at' => date('Y-m-d H:i:s'),
        ], true);

        if (!$materialId) {
            return redirect()->back()->with('error', 'Failed to create assignment resource.');
        }

        $allowedAttemptsInput = $this->request->getPost('allowedAttempts');
        $allowedAttempts = ($allowedAttemptsInput !== null && $allowedAttemptsInput !== '')
            ? (int) $allowedAttemptsInput
            : null;

        $assignmentModel = new AssignmentModel();
        $assignmentModel->insert([
            'courseID' => $courseId,
            'materialID' => $materialId,
            'allowedAttempts' => $allowedAttempts,
            'publishDate' => $publishDate ?: null,
            'dueDate' => $dueDate ?: null,
        ]);

        return redirect()->to('/course/' . $courseId . '/assignments')
            ->with('message', 'Assignment uploaded successfully.');
    }

    public function submit($courseID)
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('login');
        }

        $role = $session->get('role');
        if ($role !== 'student') {
            return redirect()->back()->with('error', 'Only students can upload assignment submissions.');
        }

        $courseId = (int) $courseID;
        $assignmentId = (int) $this->request->getPost('assignment_id');

        if ($assignmentId <= 0) {
            return redirect()->back()->with('error', 'Invalid assignment selected.');
        }

        $assignmentModel = new AssignmentModel();
        $assignment = $assignmentModel->findWithMaterial($assignmentId);
        if (!$assignment || (int) $assignment['courseID'] !== $courseId) {
            return redirect()->back()->with('error', 'Assignment not found for this course.');
        }

        $userId = (int) $session->get('userID');
        $enrollmentModel = new EnrollmentModel();
        if (!$enrollmentModel->isAlreadyEnrolled($userId, $courseId)) {
            return redirect()->back()->with('error', 'You must be enrolled in this course to submit assignments.');
        }

        $rules = [
            'submission_file' => 'uploaded[submission_file]|max_size[submission_file,102400]|ext_in[submission_file,pdf,doc,docx,ppt,pptx,txt,zip,rar]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $submissionModel = new SubmissionModel();
        $existingAttempts = $submissionModel->countSubmissionsForAssignment($assignmentId, $userId);
        $allowedAttempts = $assignment['allowedAttempts'];

        if (!empty($allowedAttempts) && $existingAttempts >= (int) $allowedAttempts) {
            return redirect()->back()->with('error', 'You have reached the maximum number of attempts for this assignment.');
        }

        $file = $this->request->getFile('submission_file');
        if (!$file || !$file->isValid()) {
            return redirect()->back()->with('error', 'Invalid file upload.');
        }

        $submissionsDir = WRITEPATH . 'materials/submissions';
        if (!is_dir($submissionsDir) && !mkdir($submissionsDir, 0755, true)) {
            return redirect()->back()->with('error', 'Failed to prepare storage directory.');
        }

        $newName = $file->getRandomName();
        if (!$file->move($submissionsDir, $newName)) {
            return redirect()->back()->with('error', 'Failed to store the submission file.');
        }

        $materialModel = new MaterialModel();
        $materialId = $materialModel->insert([
            'course_id' => $courseId,
            'file_name' => $file->getClientName(),
            'file_path' => rtrim($submissionsDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $newName,
            'created_at' => date('Y-m-d H:i:s'),
        ], true);

        if (!$materialId) {
            return redirect()->back()->with('error', 'Failed to store submission metadata.');
        }

        $submissionModel->insert([
            'userID' => $userId,
            'AssignmentID' => $assignmentId,
            'materialID' => $materialId,
            'submissionDate' => date('Y-m-d H:i:s'),
        ]);

        $submissionId = $submissionModel->getInsertID();
        if ($submissionId) {
            $attemptModel = new AssignmentAttemptModel();
            $attemptModel->insert([
                'submissionID' => $submissionId,
                'attemptNumber' => $existingAttempts + 1,
                'attemptDate' => date('Y-m-d H:i:s'),
            ]);
        }

        return redirect()->to('/course/' . $courseId . '/assignments')
            ->with('message', 'Assignment submitted successfully.');
    }
}
