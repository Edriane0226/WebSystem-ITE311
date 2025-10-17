<?php

namespace App\Controllers;

use App\Models\AnnouncementModel;
use CodeIgniter\Controller;

Class AnnouncementController extends Controller {

    public function index() {
        if (session()->get('isLoggedIn')) {

            $announceModel = new AnnouncementModel();

            $data['announcements'] = $announceModel->orderBy('created_at', 'DESC')->findAll();

            return view('reusables/sidebar') . view('announcements/index', $data);
        } else {
            return redirect()->to('/login');
        }
    }
}