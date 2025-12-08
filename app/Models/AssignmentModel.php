<?php

namespace App\Models;

use CodeIgniter\Model;

class AssignmentModel extends Model
{
    protected $table = 'assignments';
    protected $primaryKey = 'AssignmentID';
    protected $allowedFields = ['courseID', 'materialID', 'Instructions', 'allowedAttempts', 'publishDate', 'dueDate'];
    protected $returnType = 'array';

    public function getAssignmentsByCourse(int $courseId): array
    {
        return $this->select('assignments.*, materials.file_name AS materialName, materials.id AS materialIdRef')
            ->join('materials', 'materials.id = assignments.materialID', 'left')
            ->where('assignments.courseID', $courseId)
            ->orderBy('assignments.dueDate', 'ASC')
            ->orderBy('assignments.AssignmentID', 'ASC')
            ->findAll();
    }

    public function findWithMaterial(int $assignmentId): ?array
    {
        return $this->select('assignments.*, materials.file_name AS materialName, materials.file_path AS materialPath')
            ->join('materials', 'materials.id = assignments.materialID', 'left')
            ->where('assignments.AssignmentID', $assignmentId)
            ->first();
    }
}
