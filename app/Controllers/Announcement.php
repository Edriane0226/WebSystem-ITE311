<?php

namespace App\Controllers;

use App\Models\AnnouncementModel;
use CodeIgniter\Controller;

Class Announcement extends Controller {

    public function index() {
        if (session()->get('isLoggedIn')) {

            $announceModel = new AnnouncementModel();

            $data['announcements'] = $announceModel->orderBy('created_at', 'DESC')->findAll();

            view('Announcement', $data);
        } else {
            return redirect()->to('/login');
        }
    }
}