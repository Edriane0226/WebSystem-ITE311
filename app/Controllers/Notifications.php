<?php

namespace App\Controllers;
use App\Models\NotificationModel;
use App\Models\RoleModel;
use App\Models\UserModel;
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

    public function send()
    {
        $session = session();
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'admin') {
            return redirect()->to('dashboard')->with('error', 'Access denied.');
        }

        $rules = [
            'message' => 'required|string|min_length[5]|max_length[500]',
            'target' => 'required|in_list[admin,teacher,student,all]',
        ];

        if (!$this->validate($rules)) {
            $errors = (array) $this->validator->getErrors();
            $feedback = reset($errors) ?: 'Unable to send the notification.';
            return redirect()->back()->withInput()->with('error', $feedback);
        }

        $message = trim((string) $this->request->getPost('message'));
        $target = (string) $this->request->getPost('target');

        $userModel = new UserModel();
        $userIds = [];

        if ($target === 'all') {
            $userIds = $userModel->select('userID')->findColumn('userID');
        } else {
            $roleNames = [$target];
            $roleModel = new RoleModel();
            $roleRows = $roleModel->whereIn('role_name', $roleNames)->findAll();

            $resolvedRoleIds = [];
            $fallbackRoleIds = [
                'admin' => 1,
                'teacher' => 2,
                'student' => 3,
            ];

            foreach ($roleRows as $roleRow) {
                if (!isset($roleRow['role_name'], $roleRow['roleID'])) {
                    continue;
                }
                $resolvedRoleIds[strtolower((string) $roleRow['role_name'])] = (int) $roleRow['roleID'];
            }

            $targetKey = strtolower($target);
            $targetRoleIds = [];
            if (isset($resolvedRoleIds[$targetKey])) {
                $targetRoleIds[] = $resolvedRoleIds[$targetKey];
            } elseif (isset($fallbackRoleIds[$targetKey])) {
                $targetRoleIds[] = $fallbackRoleIds[$targetKey];
            }

            if (!empty($targetRoleIds)) {
                $userIds = $userModel->select('userID')
                    ->whereIn('role', array_unique($targetRoleIds))
                    ->findColumn('userID');
            }
        }

        if (empty($userIds)) {
            return redirect()->back()->withInput()->with('error', 'No users matched the selected audience.');
        }

        $notificationModel = new NotificationModel();
        $created = $notificationModel->createNotificationsForUsers($userIds, $message);

        if ($created <= 0) {
            return redirect()->back()->withInput()->with('error', 'Failed to create notifications.');
        }

        return redirect()->to('/dashboard')->with('success', 'Notification sent successfully.');
    }


}