<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'sipantau_user';
    protected $primaryKey = 'sobat_id';
    protected $allowedFields = [
        'sobat_id',
        'nama_user',
        'email',
        'hp',
        'id_kabupaten',
        'role', // Sesuai dengan nama kolom di database
        'password',
        'is_active',
        'created_at',
        'updated_at'
    ];
    
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    
    protected $beforeInsert = ['hashPassword'];
    protected $beforeUpdate = ['hashPassword'];

    protected function hashPassword(array $data)
    {
        if (isset($data['data']['password']) && !empty($data['data']['password'])) {
            $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
        }
        return $data;
    }

    /**
     * Get users with details including roles
     */
    public function getUsersWithDetails($search = '', $roleFilter = '')
    {
        $builder = $this->db->table('sipantau_user u')
            ->select('u.*, k.nama_kabupaten')
            ->join('master_kabupaten k', 'u.id_kabupaten = k.id_kabupaten', 'left')
            ->orderBy('u.nama_user', 'ASC');

        if (!empty($search)) {
            $builder->groupStart()
                ->like('u.nama_user', $search)
                ->orLike('u.email', $search)
                ->orLike('u.hp', $search)
                ->groupEnd();
        }

        $users = $builder->get()->getResultArray();

        // Process roles for each user
        $filteredUsers = [];
        foreach ($users as $user) {
            $this->processUserRoles($user);
            
            // Filter by role if needed
            if (!empty($roleFilter)) {
                if (in_array($roleFilter, $user['role_ids'])) {
                    $filteredUsers[] = $user;
                }
            } else {
                $filteredUsers[] = $user;
            }
        }

        return $filteredUsers;
    }

    /**
     * Get user with roles by ID
     */
    public function getUserWithRoles($id)
    {
        $builder = $this->db->table('sipantau_user u')
            ->select('u.*, k.nama_kabupaten')
            ->join('master_kabupaten k', 'u.id_kabupaten = k.id_kabupaten', 'left')
            ->where('u.sobat_id', $id);
        
        $user = $builder->get()->getRowArray();
        
        if ($user) {
            $this->processUserRoles($user);
        }
        
        return $user;
    }

    /**
     * Process user roles - decode JSON and get role names
     */
    private function processUserRoles(&$user)
    {
        $user['role_ids'] = [];
        $user['role_names'] = [];
        $user['roles_display'] = '';

        if (!empty($user['role'])) {
            $roleIds = json_decode($user['role'], true);
            
            if (is_array($roleIds) && !empty($roleIds)) {
                $user['role_ids'] = $roleIds;
                
                // Get role names
                $roleModel = new RoleModel();
                $roles = $roleModel->whereIn('id_roleuser', $roleIds)->findAll();
                
                foreach ($roles as $role) {
                    $user['role_names'][] = $role['roleuser'];
                }
                
                $user['roles_display'] = implode(', ', $user['role_names']);
            }
        }
    }
}