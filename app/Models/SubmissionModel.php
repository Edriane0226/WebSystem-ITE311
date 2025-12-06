<?php

namespace App\Models;

use CodeIgniter\Model;

class SubmissionModel extends Model
{
    protected $table = 'submissions';
    protected $primaryKey = 'submissionID';
    protected $allowedFields = ['userID', 'AssignmentID', 'materialID', 'submissionDate'];
    protected $returnType = 'array';

    public function getSubmissionsForStudentByCourse(int $userId, int $courseId): array
    {
        $rows = $this->select('submissions.*, materials.file_name AS submissionFile, materials.id AS submissionMaterialId, assignments.courseID')
            ->join('assignments', 'assignments.AssignmentID = submissions.AssignmentID', 'inner')
            ->join('materials', 'materials.id = submissions.materialID', 'left')
            ->where('assignments.courseID', $courseId)
            ->where('submissions.userID', $userId)
            ->orderBy('submissions.submissionDate', 'DESC')
            ->findAll();

        $grouped = [];
        foreach ($rows as $row) {
            $assignmentId = (int) $row['AssignmentID'];
            if (!isset($grouped[$assignmentId])) {
                $grouped[$assignmentId] = [];
            }
            $grouped[$assignmentId][] = $row;
        }

        return $grouped;
    }

    public function countSubmissionsForAssignment(int $assignmentId, int $userId): int
    {
        return $this->where('AssignmentID', $assignmentId)
            ->where('userID', $userId)
            ->countAllResults();
    }

    public function getCountsByAssignment(array $assignmentIds): array
    {
        if (empty($assignmentIds)) {
            return [];
        }

        $rows = $this->select('AssignmentID, COUNT(*) AS submissionCount')
            ->whereIn('AssignmentID', $assignmentIds)
            ->groupBy('AssignmentID')
            ->findAll();

        $mapped = [];
        foreach ($rows as $row) {
            $mapped[(int) $row['AssignmentID']] = (int) $row['submissionCount'];
        }

        return $mapped;
    }
}
