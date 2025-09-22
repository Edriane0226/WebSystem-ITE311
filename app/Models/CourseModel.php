<?php

namespace App\Models;

use CodeIgniter\Model;

class CourseModel extends Model
{
    protected $table      = 'courses';
    protected $primaryKey = 'courseID';
    protected $allowedFields = ['title', 'description', 'teacherID'];

    // Method to get all courses na ginatudluan sa specific nga teacher
    public function getCourses($user_id) {
        return $this->where('teacherID', $user_id)->findAll();
    }
}
