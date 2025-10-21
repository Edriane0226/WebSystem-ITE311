<?php

namespace App\Models;
use CodeIgniter\Model;

class NotificationModel extends Model {
    protected $table = 'notifications';
    protected $primaryKey = 'id';
    protected $allowedFields = ['user_id', 'message', 'is_read', 'created_at'];

    public function getUnreadCount($user_id) {
        return $this->where('user_id', $user_id)
                    ->where('is_read', 0)
                    ->countAllResults();
    }

    public function getNotificationsByUser($user_id) {
        return $this->where('user_id', $user_id)
                    ->orderBy('created_at', 'DESC')
                    ->limit(5)
                    ->findAll();
    }

    public function markAsRead($notificationId) {
        return $this->update($notificationId, ['is_read' => 1]);
    }
}