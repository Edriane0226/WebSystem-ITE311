<?php
namespace App\Models;

use CodeIgniter\Model;

class EnrollmentModel extends Model {
    protected $table = 'enrollments';
    protected $primaryKey = 'id';
    protected $allowedFields = ['studentID', 'courseID'];

    public function getEnrollmentsByStudent($studentId) {
        return $this->where('studentID', $studentId)->findAll();
    }

    public function getEnrollmentsByCourse($courseId) {
        return $this->where('courseID', $courseId)->findAll();
    }
}