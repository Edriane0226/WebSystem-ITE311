<?php

namespace App\Models;

use CodeIgniter\Model;

class AssignmentAttemptModel extends Model
{
    protected $table = 'assignmentAttempts';
    protected $primaryKey = 'attemptID';
    protected $allowedFields = ['submissionID', 'attemptNumber', 'attemptDate'];
    protected $returnType = 'array';
}
