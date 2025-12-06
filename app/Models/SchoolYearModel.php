<?php

namespace App\Models;

use CodeIgniter\Model;

class SchoolYearModel extends Model
{
    protected $table      = 'schoolYear';
    protected $primaryKey = 'schoolYearID';
    protected $allowedFields = ['schoolYear', 'Semester'];

    public function getAllSchoolYears()
    {
        return $this->select('schoolYear.*, semester.semesterName')
                    ->join('semester', 'semester.semesterID = schoolYear.Semester', 'left')
                    ->orderBy('schoolYear.schoolYear', 'ASC')
                    ->orderBy('schoolYear.Semester', 'ASC')
                    ->findAll();
    }
}