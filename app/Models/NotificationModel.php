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
    
    //nag create ko ug function para mag insert ug notification after ma enroll ang student
    public function createNotification($user_id, $message) {
        $data = [
            'user_id' => $user_id,
            'message' => $message,
            'is_read' => 0,
            'created_at' => date('Y-m-d H:i:s'),
        ];
        return $this->insert($data);
    }

    public function createNotificationsForUsers(array $userIds, string $message): int
    {
        $userIds = array_values(array_unique(array_map('intval', $userIds)));
        if (empty($userIds) || $message === '') {
            return 0;
        }

        $timestamp = date('Y-m-d H:i:s');
        $batch = [];

        foreach ($userIds as $userId) {
            if ($userId <= 0) {
                continue;
            }
            $batch[] = [
                'user_id' => $userId,
                'message' => $message,
                'is_read' => 0,
                'created_at' => $timestamp,
            ];
        }

        if (empty($batch)) {
            return 0;
        }

        return $this->insertBatch($batch);
    }
}