<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AnnouncementSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'title' => 'New Semester Announcement',
                'content' => 'We are excited to start the new semester with you. Stay tuned for upcoming events and important dates.',
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'title' => 'Exam Reminder',
                'content' => 'Please be informed that we have an exam 2morrow.',
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'title' => 'Holiday Notice',
                'content' => 'The institution will be closed next Friday in observance of the holiday.',
                'created_at' => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('announcements')->insertBatch($data);
    }
}
