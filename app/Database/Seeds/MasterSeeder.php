<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class MasterSeeder extends Seeder
{
    public function run()
    {
        $this->call('RoleSeeder');
        $this->call('UserSeeder');
        $this->call('CourseStatusSeeder');
        $this->call('TimeSeeder');
        $this->call('SemesterSeeder');
        $this->call('SchoolYearSeeder');
        $this->call('CoursesSeeder');
        $this->call('CourseOfferingSeeder');
        $this->call('EnrollmentStatusSeeder');
    }
}
