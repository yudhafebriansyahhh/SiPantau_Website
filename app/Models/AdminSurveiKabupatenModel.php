<?php

namespace App\Models;

use CodeIgniter\Model;

class AdminSurveiKabupatenModel extends Model
{
    protected $table = 'admin_survei_kabupaten';
    protected $primaryKey = 'id_admin_kabupaten';
    protected $allowedFields = ['sobat_id'];
    
    // Check apakah user adalah admin kabupaten
    public function isAdminKabupaten($sobatId)
    {
        return $this->where('sobat_id', $sobatId)->first() !== null;
    }
    
    // Get ID admin kabupaten by sobat_id
    public function getAdminKabupatenId($sobatId)
    {
        $admin = $this->where('sobat_id', $sobatId)->first();
        return $admin ? $admin['id_admin_kabupaten'] : null;
    }
    
    // Get kegiatan wilayah yang di-assign ke admin kabupaten ini
    public function getKegiatanWilayahByAdmin($idAdminKabupaten)
    {
        $db = \Config\Database::connect();
        
        return $db->table('kegiatan_wilayah_admin kwa')
            ->select('kw.*, mk.nama_kabupaten')
            ->join('kegiatan_wilayah kw', 'kwa.id_kegiatan_wilayah = kw.id_kegiatan_wilayah')
            ->join('master_kabupaten mk', 'kw.id_kabupaten = mk.id_kabupaten')
            ->where('kwa.id_admin_kabupaten', $idAdminKabupaten)
            ->get()
            ->getResultArray();
    }
}
