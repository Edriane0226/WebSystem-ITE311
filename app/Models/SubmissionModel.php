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

    public function countSubmissionsForAssignment($assignmentId, $userId)
    {
        return $this->where('AssignmentID', $assignmentId)
            ->where('userID', $userId)
            ->countAllResults();
    }

    public function getCountsByAssignment($assignmentIds)
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

    public function getDetailsByAssignment($assignmentId)
    {
        return $this->select('
                submissions.submissionID,
                submissions.userID,
                submissions.AssignmentID,
                submissions.materialID,
                submissions.submissionDate,
                users.name AS studentName,
                users.email AS studentEmail,
                materials.file_name AS submissionFile,
                materials.id AS submissionMaterialId,
                assignmentAttempts.attemptNumber
            ')
            ->join('users', 'users.userID = submissions.userID', 'left')
            ->join('materials', 'materials.id = submissions.materialID', 'left')
            ->join('assignmentAttempts', 'assignmentAttempts.submissionID = submissions.submissionID', 'left')
            ->where('submissions.AssignmentID', $assignmentId)
            ->orderBy('submissions.submissionDate', 'DESC')
            ->findAll();
    }

    public function getDetailsByAssignmentForStudent($assignmentId,$userId)
    {
        return $this->select('
                submissions.submissionID,
                submissions.userID,
                submissions.AssignmentID,
                submissions.materialID,
                submissions.submissionDate,
                users.name AS studentName,
                users.email AS studentEmail,
                materials.file_name AS submissionFile,
                materials.id AS submissionMaterialId,
                assignmentAttempts.attemptNumber
            ')
            ->join('users', 'users.userID = submissions.userID', 'left')
            ->join('materials', 'materials.id = submissions.materialID', 'left')
            ->join('assignmentAttempts', 'assignmentAttempts.submissionID = submissions.submissionID', 'left')
            ->where('submissions.AssignmentID', $assignmentId)
            ->where('submissions.userID', $userId)
            ->orderBy('submissions.submissionDate', 'DESC')
            ->findAll();
    }
}
