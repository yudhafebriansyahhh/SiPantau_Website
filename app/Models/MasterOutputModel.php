<?php

namespace App\Models;

use CodeIgniter\Model;

class MasterOutputModel extends Model
{
    // ====================================================================
    // Properties
    // ====================================================================
    
    protected $table            = 'master_output';
    protected $primaryKey       = 'id_output';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['nama_output', 'fungsi', 'alias'];

    // Timestamps - Disabled karena tidak ada created_at & updated_at
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation Rules
    protected $validationRules = [
        'nama_output' => 'required|max_length[255]',
        'fungsi'      => 'required',
        'alias'       => 'required|max_length[100]'
    ];

    // Validation Messages
    protected $validationMessages = [
        'nama_output' => [
            'required'   => 'Nama output harus diisi',
            'max_length' => 'Nama output maksimal 255 karakter'
        ],
        'fungsi' => [
            'required' => 'Fungsi harus diisi'
        ],
        'alias' => [
            'required'   => 'Alias harus diisi',
            'max_length' => 'Alias maksimal 100 karakter'
        ]
    ];

    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    // ====================================================================
    // Custom Methods
    // ====================================================================

    public function search($keyword)
    {
        return $this->like('nama_output', $keyword)
                    ->orLike('fungsi', $keyword)
                    ->orLike('alias', $keyword)
                    ->orderBy('id_output', 'DESC')
                    ->findAll();
    }

    public function getTotalData()
    {
        return $this->countAll();
    }

    public function getByAlias($alias)
    {
        return $this->where('alias', $alias)->first();
    }

    public function checkDuplicate($field, $value, $excludeId = null)
    {
        $builder = $this->where($field, $value);
        
        if ($excludeId) {
            $builder->where('id_output !=', $excludeId);
        }
        
        return $builder->countAllResults() > 0;
    }

    public function getActiveData()
    {
        return $this->where('status', 1)
                    ->orderBy('id_output', 'DESC')
                    ->findAll();
    }
}