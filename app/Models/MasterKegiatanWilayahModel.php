<?php

namespace App\Models;

use CodeIgniter\Model;

class MasterKegiatanWilayahModel extends Model
{
    protected $table = 'kegiatan_wilayah';
    protected $primaryKey = 'id_kegiatan_wilayah';
    protected $allowedFields = [
        'id_kegiatan_detail_proses',
        'id_kabupaten',
        'target_wilayah',
        'keterangan',
        'status'
    ];
    protected $useTimestamps = false;

    /**
     * Get all kegiatan wilayah dengan detail lengkap
     */
    public function getAllWithDetails($kabupatenId = null)
    {
        $builder = $this->db->table('kegiatan_wilayah kw')
            ->select('kw.id_kegiatan_wilayah, kw.target_wilayah, kw.keterangan,
                     mkdp.nama_kegiatan_detail_proses, mkdp.tanggal_mulai, mkdp.tanggal_selesai,
                     mkd.nama_kegiatan_detail, mk.nama_kegiatan, 
                     kab.nama_kabupaten, kab.id_kabupaten')
            ->join('master_kegiatan_detail_proses mkdp', 'kw.id_kegiatan_detail_proses = mkdp.id_kegiatan_detail_proses')
            ->join('master_kegiatan_detail mkd', 'mkdp.id_kegiatan_detail = mkd.id_kegiatan_detail')
            ->join('master_kegiatan mk', 'mkd.id_kegiatan = mk.id_kegiatan')
            ->join('master_kabupaten kab', 'kw.id_kabupaten = kab.id_kabupaten')
            ->orderBy('kab.nama_kabupaten', 'ASC')
            ->orderBy('mkdp.tanggal_mulai', 'DESC');

        if ($kabupatenId) {
            $builder->where('kw.id_kabupaten', $kabupatenId);
        }

        return $builder->get()->getResultArray();
    }

    /**
     * Get kegiatan wilayah by kabupaten (untuk filter dropdown)
     */
    public function getByKabupaten($idKabupaten)
    {
        return $this->db->table('kegiatan_wilayah kw')
            ->select('kw.id_kegiatan_wilayah, kw.target_wilayah, kw.keterangan,
                     mkdp.nama_kegiatan_detail_proses, mkdp.tanggal_mulai, mkdp.tanggal_selesai,
                     mkd.nama_kegiatan_detail, mk.nama_kegiatan')
            ->join('master_kegiatan_detail_proses mkdp', 'kw.id_kegiatan_detail_proses = mkdp.id_kegiatan_detail_proses')
            ->join('master_kegiatan_detail mkd', 'mkdp.id_kegiatan_detail = mkd.id_kegiatan_detail')
            ->join('master_kegiatan mk', 'mkd.id_kegiatan = mk.id_kegiatan')
            ->where('kw.id_kabupaten', $idKabupaten)
            ->orderBy('mkdp.tanggal_mulai', 'DESC')
            ->get()
            ->getResultArray();
    }

    /**
     * Get kegiatan wilayah with admin info
     */
    public function getWithAdminInfo($idKegiatanWilayah)
    {
        return $this->db->table('kegiatan_wilayah kw')
            ->select('kw.*, mkdp.nama_kegiatan_detail_proses, mkdp.tanggal_mulai, mkdp.tanggal_selesai,
                     mkd.nama_kegiatan_detail, mk.nama_kegiatan,
                     kab.nama_kabupaten')
            ->join('master_kegiatan_detail_proses mkdp', 'kw.id_kegiatan_detail_proses = mkdp.id_kegiatan_detail_proses')
            ->join('master_kegiatan_detail mkd', 'mkdp.id_kegiatan_detail = mkd.id_kegiatan_detail')
            ->join('master_kegiatan mk', 'mkd.id_kegiatan = mk.id_kegiatan')
            ->join('master_kabupaten kab', 'kw.id_kabupaten = kab.id_kabupaten')
            ->where('kw.id_kegiatan_wilayah', $idKegiatanWilayah)
            ->get()
            ->getRowArray();
    }
}