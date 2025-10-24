<?php

namespace App\Models;

use CodeIgniter\Model;

class MasterKegiatanWilayahModel extends Model
{
    protected $table            = 'kegiatan_wilayah';
    protected $primaryKey       = 'id_kegiatan_wilayah';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_kegiatan_wilayah ',
        'id_kegiatan_detail_proses ',
        'id_kab',
        'target_wilayah',
        'keterangan',
        'status'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
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


    public function getData(){
        return $this->select('
           kegiatan_wilayah.*,
           master_kegiatan_detail_proses.nama_kegiatan_detail_proses,
           master_kegiatan_detail_proses.tanggal_mulai,
           master_kegiatan_detail_proses.tanggal_selesai,
           master_kab.nmkab
        ')
        ->join('master_kegiatan_detail_proses', 'master_kegiatan_detail_proses.id_kegiatan_detail_proses = kegiatan_wilayah.id_kegiatan_detail_proses ')
        ->join('master_kab', 'master_kab.idkab = kegiatan_wilayah.id_kab')
        ->orderBy('kegiatan_wilayah.id_kegiatan_wilayah ', 'DESC')
        ->findAll();
    }
}

