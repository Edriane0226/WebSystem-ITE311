<?php

namespace App\Controllers;
use App\Models\MaterialModel;
use CodeIgniter\Controller;

class Materials extends Controller
{
    public function upload($course_id)
    {   
        if (session()->get('isLoggedIn') == false || session()->get('role') != 'admin' && session()->get('role') != 'teacher') {
            return redirect()->to('login');
        }

        if ($this->request->getMethod() !== 'POST') {
            return view('materials/upload', ['course_id' => $course_id]);
        }
        $materialModel = new MaterialModel();

        $file = $this->request->getFile('material_file');
        if ($file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName();
            $file->move(WRITEPATH . 'uploads/materials', $newName);

            $data = [
                'course_id' => $course_id,
                'file_name' => $file->getClientName(),
                'file_path' => WRITEPATH . 'uploads/materials/' . $newName,
                'created_at' => date('Y-m-d H:i:s')
            ];

            $materialModel->insertMaterial($data);

            return redirect()->back()->with('success', 'Material uploaded successfully.');
        } else {
            return redirect()->back()->with('error', 'Failed to upload material.');
        }
    }

    public function delete($material_id)
    {
        $materialModel = new MaterialModel();
        $material = $materialModel->find($material_id);

        if ($material) {
            // Delete the file sa folder
            if (file_exists($material['file_path'])) {
                unlink($material['file_path']);
            }

            // Delete record sa database
            $materialModel->delete($material_id);

            return redirect()->back()->with('success', 'Material deleted successfully.');
        } else {
            return redirect()->back()->with('error', 'Material not found.');
        }
    }

    public function download($material_id)
    {
        $materialModel = new MaterialModel();
        $material = $materialModel->find($material_id);

        if ($material && file_exists($material['file_path'])) {
            return $this->response->download($material['file_path'], null);
        } else {
            return redirect()->back()->with('error', 'Material not found.');
        }
    }
}