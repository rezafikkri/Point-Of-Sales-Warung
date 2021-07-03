<?php

namespace App\Models;

use CodeIgniter\Model;

class UsersModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'user_id';
    protected $allowedFields = [
        'user_id',
        'full_name',
        'username',
        'level',
        'password',
        'last_sign_in',
        'created_at',
        'updated_at'
    ];
    protected $useAutoIncrement = false;

    public function getUserSignIn(string $username): ? array
    {
        return $this->select('full_name, level, password, user_id')->getWhere(['username' => $username])->getRowArray();
    }

    public function getUsers(): array
    {
        return $this->select('user_id, full_name, level, last_sign_in')->orderBy('full_name', 'ASC')->get()->getResultArray();
    }

    public function findUser(string $userId, string $column): ? array
    {
        return $this->select($column)->getWhere([$this->primaryKey => $userId])->getRowArray();
    }

    public function deleteUser(string $userId): int
    {
        return $this->where('user_id !=', $_SESSION['posw_user_id'])->delete($userId);
    }
}
