<?php

namespace App\Controllers;
use App\Models\NotificationModel;
use CodeIgniter\Controller;

class Notifications extends Controller
{
    public function get(){
        $notifModel = new NotificationModel();
        $user_id = session()->get('userID');
        $notifications = $notifModel->getNotificationsByUser($user_id);
        $unreadCount = $notifModel->getUnreadCount($user_id);

        return $this->response->setJSON([
            'notifications' => $notifications,
            'count' => $unreadCount,
            'csrfHash' => csrf_hash()
        ]);
    }

    public function mark_as_read($id){
        $notifModel = new NotificationModel();
        $notifModel->markAsRead($id);
        return $this->response->setJSON(['success' => true]);
    }


}