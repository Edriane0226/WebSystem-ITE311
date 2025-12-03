<?php

namespace App\Models;

use CodeIgniter\Model;

class SchoolYearModel extends Model
{
    protected $table      = 'schoolYear';
    protected $primaryKey = 'schoolYearID';
    protected $allowedFields = ['schoolYear'];
}