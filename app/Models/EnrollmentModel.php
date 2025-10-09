<?php
namespace App\Models;

use CodeIgniter\Model;

class EnrollmentModel extends Model {
    protected $table = 'enrollments';
    protected $primaryKey = 'id';
    protected $allowedFields = ['user_id', 'course_id', 'enrollment_date'];

    // Insert ug data galing sa controller
    public function enrollUser($data) {
        return $this->insert($data);
    }
    //kwaon niya tanan enrollments ug unsa ang info sa course sa user na naa sa $user_id
    public function getUserEnrollments($user_id) {
        return $this->select('enrollments.*, courses.courseTitle, courses.courseDescription')
                    ->join('courses', 'courses.courseID = enrollments.course_id')
                    ->where('enrollments.user_id', $user_id)
                    ->findAll();
    }
    // check niya if enrolled na ba ang user sa specific course ug i return niya true or false
    public function isAlreadyEnrolled($user_id, $course_id) {
        return (bool) $this->where(['user_id' => $user_id, 'course_id' => $course_id])->first();
    }
}