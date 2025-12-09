<?php

namespace App\Models;

use CodeIgniter\Model;

class TimeModel extends Model
{
    protected $table = 'time';
    protected $primaryKey = 'timeID';
    protected $allowedFields = ['timeSlot'];
    protected $returnType = 'array';

    public function getAllSlots()
    {
        return $this->orderBy('timeID')->findAll();
    }
}
