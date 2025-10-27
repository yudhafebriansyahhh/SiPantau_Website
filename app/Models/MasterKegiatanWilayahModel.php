<?php

namespace App\Models;

use CodeIgniter\Model;

class MasterKegiatanWilayahModel extends Model
{
    protected $table = 'kegiatan_wilayah';
    protected $primaryKey = 'id_kegiatan_wilayah';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $protectFields = true;
    protected $allowedFields = [
        'id_kegiatan_wilayah ',
        'id_kegiatan_detail_proses',
        'id_kabupaten',
        'target_wilayah',
        'keterangan',
        'status'
    ];

    public function getData()
    {
        return $this->select('
            kegiatan_wilayah.*,
            master_kegiatan_detail_proses.nama_kegiatan_detail_proses,
            master_kegiatan_detail_proses.tanggal_mulai,
            master_kegiatan_detail_proses.tanggal_selesai,
            master_kabupaten.nama_kabupaten
        ')
        ->join('master_kegiatan_detail_proses', 'master_kegiatan_detail_proses.id_kegiatan_detail_proses = kegiatan_wilayah.id_kegiatan_detail_proses', 'left')
        ->join('master_kabupaten', 'master_kabupaten.id_kabupaten = kegiatan_wilayah.id_kabupaten', 'left')
        ->orderBy('kegiatan_wilayah.id_kegiatan_wilayah', 'DESC')
        ->findAll();
    }
}
