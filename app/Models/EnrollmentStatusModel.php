<?php

namespace App\Models;

use CodeIgniter\Model;

class EnrollmentStatusModel extends Model
{
    protected $table      = 'enrollmentstatus';
    protected $primaryKey = 'statusID';
    protected $allowedFields = ['statusName'];
    protected $returnType = 'array';

    public function getAllStatuses()
    {
        return $this->orderBy('statusName')->findAll();
    }
}
