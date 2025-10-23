<?php

namespace App\Controllers;
use App\Models\MaterialModel;
use App\Models\CourseModel;
use CodeIgniter\Controller;

class Materials extends Controller
{
    public function upload($course_id)
{
    if (!session()->get('isLoggedIn')) {
        return redirect()->to('login');
    }

    $materialModel = new MaterialModel();
    $courseModel   = new CourseModel();

    $courses = $courseModel->findAll();
    //Guba pa get materials by course kay baskin gina select nako iba course gina show lng gyapon niya ang sa iba
    $materials = $materialModel->getMaterialsByCourse($course_id);

    $role = session()->get('role');

    // kung POST ang request ug dili student ang role
    if ($this->request->getMethod() === 'POST' && $role !== 'student') {
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
                'file_name'  => $file->getClientName(),
                'file_path'  => WRITEPATH . 'materials/uploads/' . $newName,
                'created_at' => date('Y-m-d H:i:s')
            ]);

            return redirect()->to(current_url())
                             ->with('success', 'Material uploaded successfully.');
        }

        return redirect()->back()->with('error', 'Failed to upload file.');
    }

    return view('templates/header', ['role' => $role]) . view('materials/upload', [
        'courses'   => $courses,
        'materials' => $materials,
        'course_id' => $course_id
    ]);
}

    public function delete($material_id)
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('login');
        }

        $materialModel = new MaterialModel();
        $material = $materialModel->find($material_id);

        if ($material) {
            if (file_exists($material['file_path'])) {
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