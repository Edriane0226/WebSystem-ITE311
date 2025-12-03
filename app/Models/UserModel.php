<?php

namespace App\Models;

use CodeIgniter\Model;
use app\Models\RoleModel;

class UserModel extends Model
{
    protected $table      = 'users';
    protected $primaryKey = 'userID';
    protected $allowedFields = ['name', 'email', 'password', 'role'];

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
}
