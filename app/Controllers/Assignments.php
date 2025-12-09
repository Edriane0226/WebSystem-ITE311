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
    $assignments = $this->applyAutoCloseIfNeeded($assignments, $assignmentModel);

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
            'title' => 'required|string|min_length[3]|max_length[255]',
            'assignment_file' => 'uploaded[assignment_file]|max_size[assignment_file,102400]|ext_in[assignment_file,pdf,doc,docx,ppt,pptx,txt,zip,rar]',
            'instructions' => 'permit_empty|string',
            'allowedAttempts' => 'permit_empty|integer|greater_than_equal_to[1]|less_than_equal_to[9]',
            'dueDate' => 'permit_empty',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $file = $this->request->getFile('assignment_file');
        if (!$file || !$file->isValid()) {
            return redirect()->back()->withInput()->with('error', 'Invalid file upload.');
        }

    $title = trim((string) $this->request->getPost('title'));
    $dueDateInput = trim((string) $this->request->getPost('dueDate'));
    $instructions = trim((string) $this->request->getPost('instructions'));
    $autoCloseRequested = $this->request->getPost('autoClose') === '1';

        $publishDate = date('Y-m-d H:i:s');

        $dueDate = $this->normalizeDateTimeInput($dueDateInput, false);
        if ($dueDate === false) {
            return redirect()->back()->withInput()->with('error', 'Invalid due date provided.');
        }

        if ($dueDate && strtotime($dueDate) < strtotime($publishDate)) {
            return redirect()->back()->withInput()->with('error', 'Due date cannot be earlier than the publish date.');
        }

        if ($autoCloseRequested && !$dueDate) {
            return redirect()->back()->withInput()->with('error', 'Auto-close requires a due date.');
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
            'materialCategoryID' => 2, // Assignment category
            'file_name' => $file->getClientName(),
            'file_path' => rtrim($assignmentsDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $newName,
            'uploaded_at' => date('Y-m-d H:i:s'),
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
            'title' => $title,
            'Instructions' => $instructions !== '' ? $instructions : null,
            'allowedAttempts' => $allowedAttempts,
            'publishDate' => $publishDate,
            'dueDate' => $dueDate,
            'isClosed' => 0,
            'autoClose' => $autoCloseRequested ? 1 : 0,
        ]);

        return redirect()->to('/course/' . $courseId . '/assignments')
            ->with('message', 'Assignment uploaded successfully.');
    }

    public function updateAssignment($courseID, $assignmentID)
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('login');
        }

        $role = $session->get('role');
        if (!in_array($role, ['admin', 'teacher'], true)) {
            return redirect()->back()->with('error', 'You are not authorized to edit assignments.');
        }

        $courseId = (int) $courseID;
        $assignmentId = (int) $assignmentID;

        $rules = [
            'title' => 'required|string|min_length[3]|max_length[255]',
            'instructions' => 'permit_empty|string',
            'allowedAttempts' => 'permit_empty|integer|greater_than_equal_to[1]|less_than_equal_to[9]',
            'dueDate' => 'permit_empty',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $assignmentModel = new AssignmentModel();
        $assignment = $assignmentModel->findWithMaterial($assignmentId);
        if (!$assignment || (int) $assignment['courseID'] !== $courseId) {
            return redirect()->back()->with('error', 'Assignment not found for this course.');
        }

    $title = trim((string) $this->request->getPost('title'));
    $instructions = trim((string) $this->request->getPost('instructions'));
    $allowedAttemptsInput = $this->request->getPost('allowedAttempts');
    $autoCloseRequested = $this->request->getPost('autoClose') === '1';
        $allowedAttempts = ($allowedAttemptsInput !== null && $allowedAttemptsInput !== '')
            ? (int) $allowedAttemptsInput
            : null;

        $dueDateInput = trim((string) $this->request->getPost('dueDate'));

        $publishDate = $assignment['publishDate'] ?: date('Y-m-d H:i:s');

        if ($dueDateInput === '') {
            $dueDate = null;
        } else {
            $dueDate = $this->normalizeDateTimeInput($dueDateInput, false);
            if ($dueDate === false) {
                return redirect()->back()->withInput()->with('error', 'Invalid due date provided.');
            }
        }

        if ($dueDate && strtotime($dueDate) < strtotime($publishDate)) {
            return redirect()->back()->withInput()->with('error', 'Due date cannot be earlier than the publish date.');
        }

        if ($autoCloseRequested && !$dueDate) {
            return redirect()->back()->withInput()->with('error', 'Auto-close requires a due date.');
        }

        $materialModel = new MaterialModel();
        $file = $this->request->getFile('assignment_file');
        $materialsDir = WRITEPATH . 'materials/assignments';
        $updatedMaterialId = null;

        if ($file && $file->isValid() && $file->getError() === UPLOAD_ERR_OK) {
            if (!is_dir($materialsDir) && !mkdir($materialsDir, 0755, true)) {
                return redirect()->back()->with('error', 'Failed to prepare storage directory.');
            }

            $newName = $file->getRandomName();
            if (!$file->move($materialsDir, $newName)) {
                return redirect()->back()->with('error', 'Failed to store the assignment file.');
            }

            $materialPath = rtrim($materialsDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $newName;

            if (!empty($assignment['materialIdRef'])) {
                $materialModel->update($assignment['materialIdRef'], [
                    'file_name' => $file->getClientName(),
                    'file_path' => $materialPath,
                    'uploaded_at' => date('Y-m-d H:i:s'),
                ]);
                $updatedMaterialId = (int) $assignment['materialIdRef'];
            } else {
                $updatedMaterialId = $materialModel->insert([
                    'course_id' => $courseId,
                    'materialCategoryID' => 2,
                    'file_name' => $file->getClientName(),
                    'file_path' => $materialPath,
                    'uploaded_at' => date('Y-m-d H:i:s'),
                ], true);
            }
        }

            $assignmentUpdate = [
                'title' => $title,
                'Instructions' => $instructions !== '' ? $instructions : null,
                'allowedAttempts' => $allowedAttempts,
                'publishDate' => $publishDate,
                'dueDate' => $dueDate,
                'autoClose' => $autoCloseRequested ? 1 : 0,
            ];

        if (!empty($assignment['isClosed'])) {
            $dueTimestamp = $dueDate ? strtotime($dueDate) : null;
            if ($dueTimestamp === null || $dueTimestamp > time()) {
                $assignmentUpdate['isClosed'] = 0;
            }
        }

        if ($updatedMaterialId !== null) {
            $assignmentUpdate['materialID'] = $updatedMaterialId;
        }

        $assignmentModel->update($assignmentId, $assignmentUpdate);

        return redirect()->to('/course/' . $courseId . '/assignments')
            ->with('message', 'Assignment updated successfully.');
    }

    public function setSubmissionStatus($courseID, $assignmentID)
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('login');
        }

        $role = $session->get('role');
        if (!in_array($role, ['admin', 'teacher'], true)) {
            return redirect()->back()->with('error', 'You are not authorized to change submission status.');
        }

        $courseId = (int) $courseID;
        $assignmentId = (int) $assignmentID;

        $assignmentModel = new AssignmentModel();
        $assignment = $assignmentModel->find($assignmentId);
        if (!$assignment || (int) $assignment['courseID'] !== $courseId) {
            return redirect()->back()->with('error', 'Assignment not found for this course.');
        }

        $status = $this->request->getPost('status');
        $isClosed = $status === 'closed' ? 1 : 0;

        $assignmentModel->update($assignmentId, ['isClosed' => $isClosed]);

        $message = $isClosed ? 'Submissions have been closed.' : 'Submissions are open again.';

        return redirect()->to('/course/' . $courseId . '/assignments')->with('message', $message);
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

        if (empty($assignment['isClosed']) && $this->shouldAutoClose($assignment)) {
            $assignmentModel->update($assignmentId, ['isClosed' => 1]);
            $assignment['isClosed'] = 1;
        }

        if (!empty($assignment['isClosed'])) {
            return redirect()->back()->with('error', 'Submissions are closed for this assignment.');
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
            'materialCategoryID' => 3,
            'file_name' => $file->getClientName(),
            'file_path' => rtrim($submissionsDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $newName,
            'uploaded_at' => date('Y-m-d H:i:s'),
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

    public function submissionsDetail($courseID, $assignmentID)
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('login');
        }

        $role = $session->get('role');
        $userId = (int) $session->get('userID');
        $isStaff = in_array($role, ['admin', 'teacher'], true);
        $isStudent = ($role === 'student');

        if (!$isStaff && !$isStudent) {
            return redirect()->back()->with('error', 'You are not authorized to view submission details.');
        }

        $courseId = (int) $courseID;
        $assignmentId = (int) $assignmentID;

        $courseModel = new CourseModel();
        $course = $courseModel->getCourseWithDetails($courseId);
        if (!$course) {
            return redirect()->to('/course/search')->with('error', 'Course not found.');
        }

        $assignmentModel = new AssignmentModel();
        $assignment = $assignmentModel->findWithMaterial($assignmentId);
        if (!$assignment || (int) $assignment['courseID'] !== $courseId) {
            return redirect()->to('/course/' . $courseId . '/assignments')->with('error', 'Assignment not found.');
        }

        if ($isStudent) {
            $enrollmentModel = new EnrollmentModel();
            if (!$enrollmentModel->isAlreadyEnrolled($userId, $courseId)) {
                return redirect()->to('/course/search')->with('error', 'You are not enrolled in this course.');
            }
        }

        $submissionModel = new SubmissionModel();
        if ($isStaff) {
            $submissions = $submissionModel->getDetailsByAssignment($assignmentId);

            $uniqueStudentIds = array_map(static function (array $row): int {
                return (int) ($row['userID'] ?? 0);
            }, $submissions);
            $uniqueStudentCount = count(array_unique($uniqueStudentIds));
        } else {
            $submissions = $submissionModel->getDetailsByAssignmentForStudent($assignmentId, $userId);
            $uniqueStudentCount = empty($submissions) ? 0 : 1;
        }

        $latestSubmission = $submissions[0]['submissionDate'] ?? null;

        $teacher = null;
        if (!empty($course['teacherID'])) {
            $teacher = (new UserModel())->find($course['teacherID']);
        }

        $viewData = [
            'course' => $course,
            'assignment' => $assignment,
            'submissions' => $submissions,
            'role' => $role,
            'isStaff' => $isStaff,
            'stats' => [
                'totalSubmissions' => count($submissions),
                'uniqueStudents' => $uniqueStudentCount,
                'latestSubmission' => $latestSubmission,
            ],
            'teacher' => $teacher,
            'currentUserId' => $userId,
        ];

        return view('templates/header', ['role' => $role])
            . view('courses/course/submission_details', $viewData);
    }

    private function applyAutoCloseIfNeeded(array $assignments, AssignmentModel $assignmentModel): array
    {
        $now = time();

        foreach ($assignments as &$assignment) {
            if (!empty($assignment['isClosed'])) {
                continue;
            }

            if (!empty($assignment['autoClose']) && !empty($assignment['dueDate'])) {
                $dueTimestamp = strtotime($assignment['dueDate']);
                if ($dueTimestamp !== false && $dueTimestamp <= $now) {
                    $assignmentModel->update((int) $assignment['AssignmentID'], ['isClosed' => 1]);
                    $assignment['isClosed'] = 1;
                }
            }
        }

        unset($assignment);

        return $assignments;
    }

    private function shouldAutoClose(array $assignment): bool
    {
        if (empty($assignment['autoClose']) || empty($assignment['dueDate'])) {
            return false;
        }

        $dueTimestamp = strtotime($assignment['dueDate']);
        if ($dueTimestamp === false) {
            return false;
        }

        return $dueTimestamp <= time();
    }

    private function normalizeDateTimeInput(?string $input, bool $defaultToNow)
    {
        if ($input === null || $input === '') {
            return $defaultToNow ? date('Y-m-d H:i:s') : null;
        }

        $formats = ['Y-m-d\TH:i', 'Y-m-d H:i', 'Y-m-d H:i:s', DATE_ATOM];

        foreach ($formats as $format) {
            $dt = \DateTimeImmutable::createFromFormat($format, $input);
            if ($dt instanceof \DateTimeImmutable) {
                return $dt->format('Y-m-d H:i:s');
            }
        }

        try {
            $dt = new \DateTimeImmutable($input);
            return $dt->format('Y-m-d H:i:s');
        } catch (\Exception $exception) {
            return false;
        }
    }
}
