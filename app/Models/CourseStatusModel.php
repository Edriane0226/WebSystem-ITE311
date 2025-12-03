<?php

namespace App\Models;
use CodeIgniter\Model;

class CourseStatusModel extends Model
{
    protected $table      = 'coursestatus';
    protected $primaryKey = 'statusID';
    protected $allowedFields = ['statusName'];

    public function getAllStatuses()
    {
        return $this->findAll();
    }

}