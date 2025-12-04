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
     * ORIGINAL METHOD - TIDAK DIUBAH
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
     * Get PML by Kabupaten dan Admin (hanya kegiatan yang di-assign)
     * NEW METHOD - untuk filter berdasarkan assignment admin
     */
    public function getPMLByKabupatenAndAdmin($idKabupaten, $idAdminKabupaten, $idKegiatanWilayah = null)
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
            ->join('kegiatan_wilayah_admin kwa', 'kw.id_kegiatan_wilayah = kwa.id_kegiatan_wilayah')
            ->join('master_kegiatan_detail_proses mkdp', 'kw.id_kegiatan_detail_proses = mkdp.id_kegiatan_detail_proses')
            ->join('master_kegiatan_detail mkd', 'mkdp.id_kegiatan_detail = mkd.id_kegiatan_detail')
            ->join('master_kegiatan mk', 'mkd.id_kegiatan = mk.id_kegiatan')
            ->where('kw.id_kabupaten', $idKabupaten)
            ->where('kwa.id_admin_kabupaten', $idAdminKabupaten)
            ->orderBy('mkdp.tanggal_mulai', 'DESC')
            ->orderBy('p.created_at', 'DESC');

        if ($idKegiatanWilayah) {
            $builder->where('p.id_kegiatan_wilayah', $idKegiatanWilayah);
        }

        return $builder->get()->getResultArray();
    }

    /**
     * Get PML by Kabupaten dan Admin dengan Pagination
     * NEW METHOD - untuk pagination
     */
    public function getPMLByKabupatenAndAdminPaginated($idKabupaten, $idAdminKabupaten, $idKegiatanWilayah = null, $perPage = 10)
    {
        $builder = $this->select('pml.id_pml, pml.target, pml.sobat_id,
                u.nama_user as nama_pml, u.email,
                kw.id_kegiatan_wilayah,
                mkd.nama_kegiatan_detail,
                mkdp.nama_kegiatan_detail_proses,
                (SELECT COUNT(*) FROM pcl WHERE pcl.id_pml = pml.id_pml) as jumlah_pcl,
                (SELECT COALESCE(SUM(target), 0) FROM pcl WHERE pcl.id_pml = pml.id_pml) as total_target_pcl')
            ->join('sipantau_user u', 'pml.sobat_id = u.sobat_id')
            ->join('kegiatan_wilayah kw', 'pml.id_kegiatan_wilayah = kw.id_kegiatan_wilayah')
            ->join('kegiatan_wilayah_admin kwa', 'kw.id_kegiatan_wilayah = kwa.id_kegiatan_wilayah')
            ->join('master_kegiatan_detail_proses mkdp', 'kw.id_kegiatan_detail_proses = mkdp.id_kegiatan_detail_proses')
            ->join('master_kegiatan_detail mkd', 'mkdp.id_kegiatan_detail = mkd.id_kegiatan_detail')
            ->where('kw.id_kabupaten', $idKabupaten)
            ->where('kwa.id_admin_kabupaten', $idAdminKabupaten)
            ->orderBy('mkdp.tanggal_mulai', 'DESC')
            ->orderBy('pml.created_at', 'DESC');

        if ($idKegiatanWilayah) {
            $builder->where('kw.id_kegiatan_wilayah', $idKegiatanWilayah);
        }

        return $builder->paginate($perPage, 'pml_list');
    }

    /**
     * Get PML by ID dengan detail
     * ORIGINAL METHOD - TIDAK DIUBAH
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
     * ORIGINAL METHOD - TIDAK DIUBAH
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
     * Get Available PML untuk kegiatan tertentu
     * Exclude: admin yang login dan user yang sudah terlibat (PML/PCL/Admin) di kegiatan ini
     * NEW METHOD - dengan filter yang lebih ketat
     */
    public function getAvailablePMLForKegiatan($idKabupaten, $idKegiatanWilayah, $excludeSobatId = null)
    {
        $builder = $this->db->table('sipantau_user u')
            ->select('u.sobat_id, u.nama_user, u.email, u.hp')
            ->where('u.id_kabupaten', $idKabupaten)
            ->where('u.is_active', 1)
            ->orderBy('u.nama_user', 'ASC');

        // Exclude admin yang sedang login
        if ($excludeSobatId) {
            $builder->where('u.sobat_id !=', $excludeSobatId);
        }

        // Exclude user yang sudah menjadi PML di kegiatan ini
        $builder->whereNotIn('u.sobat_id', function ($subquery) use ($idKegiatanWilayah) {
            return $subquery->select('sobat_id')
                ->from('pml')
                ->where('id_kegiatan_wilayah', $idKegiatanWilayah);
        });

        // Exclude user yang sudah menjadi PCL di kegiatan ini
        $builder->whereNotIn('u.sobat_id', function ($subquery) use ($idKegiatanWilayah) {
            return $subquery->select('pcl.sobat_id')
                ->from('pcl')
                ->join('pml', 'pml.id_pml = pcl.id_pml')
                ->where('pml.id_kegiatan_wilayah', $idKegiatanWilayah);
        });

        // Exclude admin kabupaten yang meng-handle kegiatan ini
        $builder->whereNotIn('u.sobat_id', function ($subquery) use ($idKegiatanWilayah) {
            return $subquery->select('ask.sobat_id')
                ->from('admin_survei_kabupaten ask')
                ->join('kegiatan_wilayah_admin kwa', 'kwa.id_admin_kabupaten = ask.id_admin_kabupaten')
                ->where('kwa.id_kegiatan_wilayah', $idKegiatanWilayah);
        });

        return $builder->get()->getResultArray();
    }

    /**
     * Delete PML beserta PCL-nya
     * ORIGINAL METHOD - TIDAK DIUBAH
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