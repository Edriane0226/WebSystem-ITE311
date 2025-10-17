<?php

namespace App\Controllers;

use App\Models\AnnouncementModel;
use CodeIgniter\Controller;

Class Announcement extends Controller {

    public function index() {
        //Any role lang basta naka login
        if (session()->get('isLoggedIn')) {

            //prepare data para sa view
            $announceModel = new AnnouncementModel();
            //order by created_at descending
            $data['announcements'] = $announceModel->orderBy('created_at', 'DESC')->findAll();
            // tapos gipasa ang data sa view
            return view('Announcements', $data);
        } else {
            return redirect()->to('/login');
        }
    }
}