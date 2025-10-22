<?php

namespace App\Models;

use CodeIgniter\Model;

class MasterKegiatanDetailProsesModel extends Model
{
    protected $table            = 'master_kegiatan_detail_proses';
    protected $primaryKey       = 'id_kegiatan_detail_proses';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_kegiatan_detail',
        'nama_kegiatan_detail_proses',
        'satuan',
        'tanggal_mulai',
        'tanggal_selesai',
        'ket',
        'periode',
        'target',
        'persentase',
        'persentase_hari_pertama',
        'target_100_persen',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation Rules
    protected $validationRules = [
        'id_kegiatan_detail'            => 'required|numeric',
        'nama_kegiatan_detail_proses'   => 'required|max_length[255]',
        'satuan'                        => 'permit_empty|max_length[50]',
        'periode'                       => 'permit_empty|max_length[50]',
        'target'                        => 'permit_empty|numeric',
        'persentase'                    => 'permit_empty|decimal',
        'persentase_hari_pertama'       => 'permit_empty|decimal',
        'target_100_persen'             => 'permit_empty|valid_date'
    ];

    protected $validationMessages = [
        'id_kegiatan_detail' => [
            'required' => 'Kegiatan detail harus dipilih',
            'numeric'  => 'Kegiatan detail tidak valid'
        ],
        'nama_kegiatan_detail_proses' => [
            'required'   => 'Nama kegiatan detail proses harus diisi',
            'max_length' => 'Nama kegiatan detail proses maksimal 255 karakter'
        ],
        'satuan' => [
            'max_length' => 'Satuan maksimal 50 karakter'
        ],
        'periode' => [
            'max_length' => 'Periode maksimal 50 karakter'
        ],
        'target' => [
            'numeric' => 'Target harus berupa angka'
        ],
        'persentase' => [
            'decimal' => 'Persentase harus berupa angka desimal'
        ],
        'persentase_hari_pertama' => [
            'decimal' => 'Persentase hari pertama harus berupa angka desimal'
        ],
        'target_100_persen' => [
            'valid_date' => 'Target 100 persen harus berupa tanggal yang valid'
        ]
    ];

    protected $skipValidation       = false;
    protected $cleanValidationRules = true;
    protected $allowCallbacks       = true;
    protected $beforeInsert         = [];
    protected $afterInsert          = [];
    protected $beforeUpdate         = [];
    protected $afterUpdate          = [];
    protected $beforeFind           = [];
    protected $afterFind            = [];
    protected $beforeDelete         = [];
    protected $afterDelete          = [];

    // ====================================================================
    // Custom Methods
    // ====================================================================

    public function getData ()
    {
        return $this->select('
            master_kegiatan_detail_proses.*,
            master_kegiatan_detail.nama_kegiatan_detail,
        ')
        ->join('master_kegiatan_detail', 'master_kegiatan_detail.id_kegiatan_detail = master_kegiatan_detail_proses.id_kegiatan_detail','left')
        ->orderBy('master_kegiatan_detail_proses.id_kegiatan_detail_proses', 'DESC')
        ->findAll();
    }
}