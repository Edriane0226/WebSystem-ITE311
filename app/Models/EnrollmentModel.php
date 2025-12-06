<?php
namespace App\Models;

use CodeIgniter\Model;

class EnrollmentModel extends Model {
    protected $table = 'enrollments';
    protected $primaryKey = 'enrollmentID';
    protected $returnType = 'array';
    protected $allowedFields = ['user_id', 'course_id', 'enrollment_date', 'enrollmentStatus'];

    public const STATUS_ENROLLED = 1;
    public const STATUS_COMPLETED = 2;
    public const STATUS_DROPPED = 3;
    public const STATUS_PENDING = 4;

    // Insert ug data galing sa controller
    public function enrollUser($data) {
        return $this->insert($data);
    }
    //kwaon niya tanan enrollments ug unsa ang info sa course sa user na naa sa $user_id
    public function getUserEnrollments($user_id) {
        return $this->select('enrollments.*, enrollments.enrollment_date AS enrollmentDate, courses.courseTitle, courses.courseDescription, courses.courseCode, courses.schoolYearID, enrollmentstatus.statusName, courseOfferings.startDate, courseOfferings.endDate, courseOfferings.Schedule, time.timeSlot, schoolYear.schoolYear, schoolYear.Semester, semester.semesterName')
                    ->join('courses', 'courses.courseID = enrollments.course_id')
                    ->join('enrollmentstatus', 'enrollmentstatus.statusID = enrollments.enrollmentStatus', 'left')
                    ->join('courseOfferings', 'courseOfferings.courseID = enrollments.course_id', 'left')
                    ->join('schoolYear', 'courses.schoolYearID = schoolYear.schoolYearID', 'left')
                    ->join('semester', 'semester.semesterID = schoolYear.Semester', 'left')
                    ->join('time', 'time.timeID = courseOfferings.Schedule', 'left')
                    ->where('enrollments.user_id', $user_id)
                    ->whereIn('enrollments.enrollmentStatus', [1,2,3,4] )
                    ->orderBy('courses.courseTitle')
                    ->findAll();
    }
    public function getStudentEnrollments($studentId) {
        return $this->select('enrollments.*, enrollments.enrollment_date AS enrollmentDate, courses.courseTitle, courses.courseCode, courses.teacherID, courses.schoolYearID, enrollmentstatus.statusName, courseOfferings.startDate, courseOfferings.endDate, courseOfferings.Schedule, time.timeSlot, schoolYear.schoolYear, schoolYear.Semester, semester.semesterName')
                    ->join('courses', 'courses.courseID = enrollments.course_id')
                    ->join('enrollmentstatus', 'enrollmentstatus.statusID = enrollments.enrollmentStatus', 'left')
                    ->join('courseOfferings', 'courseOfferings.courseID = enrollments.course_id', 'left')
                    ->join('schoolYear', 'courses.schoolYearID = schoolYear.schoolYearID', 'left')
                    ->join('semester', 'semester.semesterID = schoolYear.Semester', 'left')
                    ->join('time', 'time.timeID = courseOfferings.Schedule', 'left')
                    ->where('enrollments.user_id', $studentId)
                    ->orderBy('courses.courseTitle')
                    ->findAll();
    }

    public function getStudentEnrollmentsForTeacher($studentId, $teacherId)
    {
        return $this->select('enrollments.*, enrollments.enrollment_date AS enrollmentDate, courses.courseTitle, courses.courseCode, courses.teacherID, courses.schoolYearID, enrollmentstatus.statusName, courseOfferings.startDate, courseOfferings.endDate, courseOfferings.Schedule, time.timeSlot, schoolYear.schoolYear, schoolYear.Semester, semester.semesterName')
                    ->join('courses', 'courses.courseID = enrollments.course_id')
                    ->join('enrollmentstatus', 'enrollmentstatus.statusID = enrollments.enrollmentStatus', 'left')
                    ->join('courseOfferings', 'courseOfferings.courseID = enrollments.course_id', 'left')
                    ->join('schoolYear', 'courses.schoolYearID = schoolYear.schoolYearID', 'left')
                    ->join('semester', 'semester.semesterID = schoolYear.Semester', 'left')
                    ->join('time', 'time.timeID = courseOfferings.Schedule', 'left')
                    ->where('enrollments.user_id', $studentId)
                    ->where('courses.teacherID', $teacherId)
                    ->orderBy('courses.courseTitle')
                    ->findAll();
    }
    // check niya if enrolled na ba ang user sa specific course ug i return niya true or false
    public function isAlreadyEnrolled($user_id, $course_id) {
        return (bool) $this->where(['user_id' => $user_id, 'course_id' => $course_id])->first();
    }
    public function getEnrollmentById($enrollmentId) {
        return $this->where('enrollmentID', $enrollmentId)->first();
    }
    public function updateEnrollment($enrollmentId, $data) {
        return $this->update($enrollmentId, $data);
    }

    public function getEnrollmentWithCourse($enrollmentId)
    {
        return $this->select('enrollments.*, courses.teacherID')
                    ->join('courses', 'courses.courseID = enrollments.course_id')
                    ->where('enrollments.enrollmentID', $enrollmentId)
                    ->first();
    }

    public function countActiveEnrollments()
    {
        return $this->where('enrollmentStatus', 1)->countAllResults();
    }

    public function updateEnrollmentStatus($enrollmentId, $statusId)
    {
        return $this->update($enrollmentId, ['enrollmentStatus' => $statusId]);
    }
}