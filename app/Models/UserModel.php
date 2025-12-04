<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Models\EnrollmentModel;

class UserModel extends Model
{
    protected $table      = 'users';
    protected $primaryKey = 'userID';
    protected $allowedFields = ['name', 'email', 'password', 'role', 'created_at', 'updated_at'];
    protected $returnType = 'array';

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getAllUsers()
    {
        return $this->findAll();
    }

    public function getUserRoleName($userID)
    {
        $this->select('roles.role_name');
        $this->join('roles', 'users.role = roles.roleID');
        $this->where('users.userID', $userID);
        $query = $this->get();
        $result = $query->getRow();

        return $result ? $result->role_name : null;
    }

    public function getRoleIDByUserID($userID)
    {
        $user = $this->find($userID);
        return $user ? $user['role'] : null;
    }

    public function getTeacherByRoleID($roleID)
    {
        return $this->where('role', 2)->findAll();
    }

    public function getStudentsWithEnrollmentCount()
    {
        $builder = $this->db->table('users');
        $builder->select("
            users.userID,
            MAX(users.name) AS name,
            MAX(users.email) AS email,
            COUNT(enrollments.enrollmentID) AS enrollment_count
        ");
        $builder->join('enrollments', 'users.userID = enrollments.user_id AND enrollments.enrollmentStatus = ' . EnrollmentModel::STATUS_ENROLLED, 'left');
        $builder->where('users.role', 3);
        $builder->groupBy('users.userID');

        return $builder->get()->getResultArray();
    }

    public function getAllUsersWithRole()
    {
    return $this->select('users.userID, users.name, users.email, users.role, users.created_at, roles.role_name, COUNT(enrollments.enrollmentID) AS enrolledCourses')
                    ->join('roles', 'users.role = roles.roleID', 'left')
                    ->join('enrollments', 'enrollments.user_id = users.userID AND enrollments.enrollmentStatus = ' . EnrollmentModel::STATUS_ENROLLED, 'left')
            ->groupBy('users.userID, users.name, users.email, users.role, users.created_at, roles.role_name')
                    ->orderBy('users.name')
                    ->findAll();
    }

    public function getStudentsByTeacherCourses($teacherId)
    {
    return $this->select('users.userID, users.name, users.email, COUNT(DISTINCT enrollments.course_id) AS enrolledCourses')
                    ->join('enrollments', 'enrollments.user_id = users.userID AND enrollments.enrollmentStatus = ' . EnrollmentModel::STATUS_ENROLLED, 'inner')
                    ->join('courses', 'courses.courseID = enrollments.course_id', 'inner')
                    ->where('courses.teacherID', $teacherId)
                    ->where('users.role', 3)
            ->groupBy('users.userID, users.name, users.email')
                    ->orderBy('users.name')
                    ->findAll();
    }

    public function getStudentsByCourse($courseId)
    {
        return $this->select('users.userID, users.name, users.email, enrollments.enrollmentStatus, enrollmentstatus.statusName')
                    ->join('enrollments', 'enrollments.user_id = users.userID', 'inner')
                    ->join('enrollmentstatus', 'enrollmentstatus.statusID = enrollments.enrollmentStatus', 'left')
                    ->where('enrollments.course_id', $courseId)
                    ->where('users.role', 3)
                    ->orderBy('users.name')
                    ->findAll();
    }

}
