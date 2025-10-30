<?php

namespace App\Models;

use CodeIgniter\Model;

class PMLModel extends Model
{
    protected $table = 'pml';
    protected $primaryKey = 'id_pml';
    protected $allowedFields = [
        'sobat_id',
        'id_kegiatan_wilayah',
        'target',
        'status_approval',
        'tanggal_approval',
        'feedback_admin',
        'created_at',
        'updated_at'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    /**
     * Get all PML dengan detail lengkap untuk admin kabupaten
     */
    public function getPMLByKabupaten($idKabupaten, $idKegiatanWilayah = null)
    {
        $builder = $this->db->table('pml p')
            ->select('p.id_pml, p.target, p.status_approval, p.created_at,
                     u.nama_user as nama_pml, u.email, u.hp,
                     kw.id_kegiatan_wilayah, kw.target_wilayah,
                     mkdp.nama_kegiatan_detail_proses, mkdp.tanggal_mulai, mkdp.tanggal_selesai,
                     mkd.nama_kegiatan_detail,
                     mk.nama_kegiatan,
                     (SELECT COUNT(*) FROM pcl WHERE id_pml = p.id_pml) as jumlah_pcl,
                     (SELECT COALESCE(SUM(target), 0) FROM pcl WHERE id_pml = p.id_pml) as total_target_pcl')
            ->join('sipantau_user u', 'p.sobat_id = u.sobat_id')
            ->join('kegiatan_wilayah kw', 'p.id_kegiatan_wilayah = kw.id_kegiatan_wilayah')
            ->join('master_kegiatan_detail_proses mkdp', 'kw.id_kegiatan_detail_proses = mkdp.id_kegiatan_detail_proses')
            ->join('master_kegiatan_detail mkd', 'mkdp.id_kegiatan_detail = mkd.id_kegiatan_detail')
            ->join('master_kegiatan mk', 'mkd.id_kegiatan = mk.id_kegiatan')
            ->where('kw.id_kabupaten', $idKabupaten)
            ->orderBy('mkdp.tanggal_mulai', 'DESC')
            ->orderBy('p.created_at', 'DESC');

        if ($idKegiatanWilayah) {
            $builder->where('p.id_kegiatan_wilayah', $idKegiatanWilayah);
        }

        return $builder->get()->getResultArray();
    }

    /**
     * Get PML by ID dengan detail
     */
    public function getPMLWithDetails($idPML)
    {
        return $this->db->table('pml p')
            ->select('p.*, u.nama_user as nama_pml, u.email, u.hp,
                     kw.id_kegiatan_wilayah, kw.target_wilayah, kw.id_kabupaten,
                     mkdp.nama_kegiatan_detail_proses, mkdp.tanggal_mulai, mkdp.tanggal_selesai,
                     mk.nama_kegiatan,
                     kab.nama_kabupaten')
            ->join('sipantau_user u', 'p.sobat_id = u.sobat_id')
            ->join('kegiatan_wilayah kw', 'p.id_kegiatan_wilayah = kw.id_kegiatan_wilayah')
            ->join('master_kegiatan_detail_proses mkdp', 'kw.id_kegiatan_detail_proses = mkdp.id_kegiatan_detail_proses')
            ->join('master_kegiatan_detail mkd', 'mkdp.id_kegiatan_detail = mkd.id_kegiatan_detail')
            ->join('master_kegiatan mk', 'mkd.id_kegiatan = mk.id_kegiatan')
            ->join('master_kabupaten kab', 'kw.id_kabupaten = kab.id_kabupaten')
            ->where('p.id_pml', $idPML)
            ->get()
            ->getRowArray();
    }

    /**
     * Get daftar user yang bisa dijadikan PML (belum di-assign)
     */
    public function getAvailablePML($idKabupaten, $idKegiatanWilayah)
    {
        return $this->db->table('sipantau_user u')
            ->select('u.sobat_id, u.nama_user, u.email, u.hp')
            ->where('u.id_kabupaten', $idKabupaten)
            ->where('u.is_active', 1)
            ->where('u.sobat_id NOT IN (
                SELECT sobat_id FROM pml 
                WHERE id_kegiatan_wilayah = ' . $idKegiatanWilayah . '
            )')
            ->where('u.sobat_id NOT IN (
                SELECT sobat_id FROM admin_survei_kabupaten
            )')
            ->orderBy('u.nama_user', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * Delete PML beserta PCL-nya
     */
    public function deletePMLWithPCL($idPML)
    {
        $this->db->transStart();

        // Hapus PCL yang terkait
        $this->db->table('pcl')
            ->where('id_pml', $idPML)
            ->delete();

        // Hapus PML
        $this->delete($idPML);

        $this->db->transComplete();

        return $this->db->transStatus();
    }

    
}