<?php

namespace App\Models;

use CodeIgniter\Model;

class CourseOfferingModel extends Model
{
    protected $table = 'courseOfferings';
    protected $primaryKey = 'offeringID';
    protected $allowedFields = ['courseID', 'schoolYearID', 'startDate', 'endDate', 'Schedule'];
    protected $returnType = 'array';

    public function findByCourseId(int $courseId): ?array
    {
        return $this->where('courseID', $courseId)->first();
    }

    public function hasScheduleConflict(int $schoolYearId, int $timeSlotId, ?int $excludeCourseId = null): bool
    {
        $builder = $this->builder()
            ->where('schoolYearID', $schoolYearId)
            ->where('Schedule', $timeSlotId);

        if ($excludeCourseId !== null) {
            $builder->where('courseID !=', $excludeCourseId);
        }

        return $builder->countAllResults() > 0;
    }
}
