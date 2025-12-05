<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Models\ShcoolYearModel;
use App\Models\CourseStatusModel;
use App\Models\EnrollmentModel;

class CourseModel extends Model
{
    protected $table      = 'courses';
    protected $primaryKey = 'courseID';
    protected $allowedFields = ['courseCode', 'courseTitle', 'courseDescription', 'schoolYearID', 'teacherID', 'statusID'];

    public function getTeacherIdByCourse($courseId)
    {
        $course = $this->find($courseId);
        return $course ? $course['teacherID'] : null;
    }
    public function getCoursesWithDetails()
    {
        $this->select('courses.*, users.name as teacherName, coursestatus.statusName, schoolYear.schoolYear, courseOfferings.startDate, courseOfferings.endDate, courseOfferings.offeringID');
        $this->join('users', 'courses.teacherID = users.userID', 'left');
        $this->join('coursestatus', 'courses.statusID = coursestatus.statusID', 'left');
        $this->join('schoolYear', 'courses.schoolYearID = schoolYear.schoolYearID', 'left');
        $this->join('courseOfferings', 'courseOfferings.courseID = courses.courseID', 'left');
        $query = $this->get();
        return $query->getResultArray();
    }
    
    public function getCourseWithDetails($courseID)
    {
        $this->select('courses.*, users.name as teacherName, coursestatus.statusName, schoolYear.schoolYear, courseOfferings.startDate, courseOfferings.endDate, courseOfferings.offeringID');
        $this->join('users', 'courses.teacherID = users.userID', 'left');
        $this->join('coursestatus', 'courses.statusID = coursestatus.statusID', 'left');
        $this->join('schoolYear', 'courses.schoolYearID = schoolYear.schoolYearID', 'left');
        $this->join('courseOfferings', 'courseOfferings.courseID = courses.courseID', 'left');
        return $this->where('courses.courseID', $courseID)->first();
    }

    public function getCoursesByTeacher($teacherId)
    {
        return $this->where('teacherID', $teacherId)
                    ->orderBy('courseTitle')
                    ->findAll();
    }

    public function setStatus($courseID, $statusID)
    {
        $this->update($courseID, ['statusID' => $statusID]);
    }

    public function getActiveCoursesCount()
    {
        return $this->where('statusID', 1)->countAllResults();
    }

    // check if the statusID if the same with the course's current statusID
    public function checkCourseStatus($courseID)
    {
        $course = $this->find($courseID);
        return $course ? $course['statusID'] : null;
    }

    public function getAvailableCoursesForStudent($studentId)
    {
        $enrolledCourseIds = (new EnrollmentModel())
            ->where('user_id', $studentId)
            ->select('course_id')
            ->findColumn('course_id') ?? [];

        $builder = $this->builder();
        $builder->select('*');

        if (!empty($enrolledCourseIds)) {
            $builder->whereNotIn('courseID', $enrolledCourseIds);
        }

        return $builder->orderBy('courseTitle')->get()->getResultArray();
    }

    public function getTeacherAvailableCoursesForStudent($teacherId, $studentId)
    {
        $enrolledCourseIds = (new EnrollmentModel())
            ->where('user_id', $studentId)
            ->select('course_id')
            ->findColumn('course_id') ?? [];

        $builder = $this->builder();
        $builder->select('*');
        $builder->where('teacherID', $teacherId);

        if (!empty($enrolledCourseIds)) {
            $builder->whereNotIn('courseID', $enrolledCourseIds);
        }

        return $builder->orderBy('courseTitle')->get()->getResultArray();
    }
}
