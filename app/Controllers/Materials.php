<?php

namespace App\Controllers;
use App\Models\MaterialModel;
use App\Models\CourseModel;

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
                //Check if the uploaded file is valid, max size 100MB, and allowed file types
                'material_file' => 'uploaded[material_file]|max_size[material_file,102400]|ext_in[material_file,pdf,doc,docx,ppt,pptx,txt,mp4,png,jpg,jpeg]'
            ]);
            if (!$validation->withRequest($this->request)->run()) {
                return redirect()->back()->with('error', $validation->getError('material_file'));
            }
            if ($file->isValid()) {
                $newName = $file->getRandomName();
                $file->move(WRITEPATH . 'materials/uploads', $newName);

                $materialModel->insert([
                    'course_id'  => $course_id,
                    'materialCategoryID' => 1,
                    'file_name'  => $file->getClientName(),
                    'file_path'  => WRITEPATH . 'materials/uploads/' . $newName,
                    'uploaded_at' => date('Y-m-d H:i:s')
                ]);

                return redirect()->to(base_url('/course/' . $course_id . '/modules'))
                                 ->with('success', 'Material uploaded successfully.');
            }

            return redirect()->back()->with('error', 'Failed to upload file.');
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

        if ($material) {
            if (is_file($material['file_path'])) {
                unlink($material['file_path']);
            }

            $materialModel->delete($material_id);
            return redirect()->back()->with('success', 'Material deleted successfully.');
        }

        return redirect()->back()->with('error', 'Material not found.');
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
}