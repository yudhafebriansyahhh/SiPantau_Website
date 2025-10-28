<?php

namespace App\Models;

use CodeIgniter\Model;

class KegiatanWilayahAdminModel extends Model
{
    protected $table = 'kegiatan_wilayah_admin';
    protected $primaryKey = 'id';
    protected $allowedFields = ['id_admin_kabupaten', 'id_kegiatan_wilayah'];
    
    // Get admin kabupaten untuk kegiatan wilayah tertentu
    public function getAdminByKegiatanWilayah($idKegiatanWilayah)
    {
        return $this->select('admin_survei_kabupaten.sobat_id, sipantau_user.nama_user, sipantau_user.email')
            ->join('admin_survei_kabupaten', 'admin_survei_kabupaten.id_admin_kabupaten = kegiatan_wilayah_admin.id_admin_kabupaten')
            ->join('sipantau_user', 'sipantau_user.sobat_id = admin_survei_kabupaten.sobat_id')
            ->where('id_kegiatan_wilayah', $idKegiatanWilayah)
            ->findAll();
    }
    
    // Assign admin ke kegiatan wilayah
    public function assignAdmin($idAdminKabupaten, $idKegiatanWilayah)
    {
        // Check if already assigned
        $existing = $this->where([
            'id_admin_kabupaten' => $idAdminKabupaten,
            'id_kegiatan_wilayah' => $idKegiatanWilayah
        ])->first();
        
        if (!$existing) {
            return $this->insert([
                'id_admin_kabupaten' => $idAdminKabupaten,
                'id_kegiatan_wilayah' => $idKegiatanWilayah
            ]);
        }
        
        return false;
    }
}
