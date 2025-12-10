<?php

namespace App\Controllers;
use App\Models\MaterialModel;
use App\Models\CourseModel;
use App\Models\EnrollmentModel;
use App\Models\NotificationModel;

class Materials extends BaseController
{
    public function upload($course_id)
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('login');
        }

        $materialModel = new MaterialModel();
        $courseModel   = new CourseModel();

        $course = $courseModel->getCourseWithDetails((int) $course_id);
        if (!$course) {
            return redirect()->back()->with('error', 'Course not found.');
        }

        $materialsByCategory = $materialModel->getMaterialsByCourseGrouped((int) $course_id);

        $role = session()->get('role');
        $canUpload = in_array($role, ['admin', 'teacher'], true);

        // kung POST ang request ug dili student ang role
        if ($this->request->getMethod() === 'POST') {
            if (!$canUpload) {
                return redirect()->back()->with('error', 'You are not allowed to upload materials.');
            }

            $file = $this->request->getFile('material_file');

            $validation =  \Config\Services::validation();
            $validation->setRules([
                'material_file' => [
                    'label' => 'Course material',
                    'rules' => 'uploaded[material_file]|max_size[material_file,102400]|ext_in[material_file,pdf,ppt,pptx]',
                    'errors' => [
                        'uploaded' => 'Please choose a material file before uploading.',
                        'max_size' => 'Course material must not exceed 100MB.',
                        'ext_in' => 'Only PDF and PowerPoint files (PDF, PPT, PPTX) are allowed.',
                    ],
                ],
            ]);

            if (!$validation->withRequest($this->request)->run()) {
                $feedback = $validation->getError('material_file') ?: 'Unable to upload the material because the file did not pass validation.';
                return redirect()->back()->withInput()->with('error', $feedback);
            }

            if ($file && $file->isValid()) {
                $materialsDir = WRITEPATH . 'materials/uploads';
                if (!is_dir($materialsDir) && !mkdir($materialsDir, 0755, true)) {
                    return redirect()->back()->with('error', 'Failed to prepare storage directory.');
                }

                $newName = $file->getRandomName();
                if (!$file->move($materialsDir, $newName)) {
                    return redirect()->back()->with('error', 'Failed to store the uploaded file.');
                }

                $materialModel->insert([
                    'course_id'  => $course_id,
                    'materialCategoryID' => 1,
                    'file_name'  => $file->getClientName(),
                    'file_path'  => rtrim($materialsDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $newName,
                    'uploaded_at' => date('Y-m-d H:i:s')
                ]);

                $uploaderName = session()->get('name') ?: 'Your instructor';
                $courseTitle = $course['courseTitle'] ?? 'your course';
                $materialName = $file->getClientName();
                $this->notifyCourseStudents(
                    (int) $course_id,
                    sprintf('%s uploaded a new module "%s" in %s.', $uploaderName, $materialName, $courseTitle),
                    [(int) session()->get('userID')]
                );

                return redirect()->to(base_url('/course/' . $course_id . '/modules'))
                                 ->with('success', 'Material uploaded successfully.');
            }

            return redirect()->back()->withInput()->with('error', 'The uploaded file is invalid or could not be processed.');
        }

        return view('templates/header', ['role' => $role]) . view('materials/upload', [
            'course'    => $course,
            'course_id' => $course_id,
            'canUpload' => $canUpload,
            'role'      => $role,
            'modules' => $materialsByCategory['modules'],
            'assignments' => $materialsByCategory['assignments'],
            'otherMaterials' => $materialsByCategory['others'],
        ]);
    }

    public function delete($material_id)
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('login');
        }

        if (!in_array(session()->get('role'), ['admin', 'teacher'], true)) {
            return redirect()->back()->with('error', 'You are not allowed to delete materials.');
        }

        $materialModel = new MaterialModel();
        $material = $materialModel->find($material_id);

        if (!$material) {
            return redirect()->back()->with('error', 'Material not found.');
        }

        try {
            if ($materialModel->delete($material_id) === false) {
                $deletionErrors = $materialModel->errors();
                $errorMessage = is_array($deletionErrors) ? implode('; ', array_filter($deletionErrors)) : 'Unknown validation error';

                log_message('error', 'Failed to delete material record with ID {id}: {errors}', [
                    'id'     => $material_id,
                    'errors' => $errorMessage,
                ]);

                return redirect()->back()->with('error', 'Failed to update the material record.');
            }
        } catch (\Throwable $exception) {
            log_message('error', 'Exception deleting material ID {id}: {message}', [
                'id'      => $material_id,
                'message' => $exception->getMessage(),
            ]);

            return redirect()->back()->with('error', 'Failed to delete the material.');
        }

        if (!empty($material['file_path']) && is_file($material['file_path'])) {
            if (!@unlink($material['file_path'])) {
                log_message('warning', 'Failed to delete file for material ID {id} at path {path}', [
                    'id'   => $material_id,
                    'path' => $material['file_path'],
                ]);
            }
        }

        return redirect()->back()->with('success', 'Material deleted successfully.');
    }

    public function download($material_id)
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('login');
        }

        $materialModel = new MaterialModel();
        $material = $materialModel->find($material_id);

        if ($material && file_exists($material['file_path'])) {
            return $this->response->download($material['file_path'], null);
        }

        return redirect()->back()->with('error', 'Material not found.');
    }

    private function notifyCourseStudents(int $courseId, string $message, array $excludeUserIds = []): void
    {
        $enrollmentModel = new EnrollmentModel();
        $studentIds = $enrollmentModel->select('user_id')
            ->where('course_id', $courseId)
            ->whereIn('enrollmentStatus', [
                EnrollmentModel::STATUS_ENROLLED,
                EnrollmentModel::STATUS_PENDING,
            ])
            ->findColumn('user_id');

        if (empty($studentIds)) {
            return;
        }

        $notificationModel = new NotificationModel();
        $excludeLookup = [];
        foreach ($excludeUserIds as $excludeId) {
            $excludeLookup[(int) $excludeId] = true;
        }

        foreach (array_unique(array_map('intval', $studentIds)) as $studentId) {
            if (isset($excludeLookup[$studentId])) {
                continue;
            }
            $notificationModel->createNotification($studentId, $message);
        }
    }
}