<?php
namespace App\Models;

use CodeIgniter\Model;

class EnrollmentModel extends Model {
    protected $table = 'enrollments';
    protected $primaryKey = 'id';
    protected $allowedFields = ['student_id', 'course_id'];

    public function getEnrollmentsByStudent($studentId) {
        return $this->where('student_id', $studentId)->findAll();
    }

    public function getEnrollmentsByCourse($courseId) {
        return $this->where('course_id', $courseId)->findAll();
    }
}