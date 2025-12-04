<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Models\ShcoolYearModel;
use App\Models\CourseStatusModel;

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
        $this->select('courses.*, users.name as teacherName, coursestatus.statusName, schoolYear.schoolYear');
        $this->join('users', 'courses.teacherID = users.userID');
        $this->join('coursestatus', 'courses.statusID = coursestatus.statusID');
        $this->join('schoolYear', 'courses.schoolYearID = schoolYear.schoolYearID');
        $query = $this->get();
        return $query->getResultArray();
    }
    
    public function getCourseWithDetails($courseID)
    {
        $this->select('courses.*, users.name as teacherName, coursestatus.statusName, schoolYear.schoolYear');
        $this->join('users', 'courses.teacherID = users.userID');
        $this->join('coursestatus', 'courses.statusID = coursestatus.statusID');
        $this->join('schoolYear', 'courses.schoolYearID = schoolYear.schoolYearID');
        return $this->where('courseID', $courseID)->first();
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
}
