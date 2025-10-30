<?php

namespace App\Models;

use CodeIgniter\Model;

class PCLModel extends Model
{
    protected $table = 'pcl';
    protected $primaryKey = 'id_pcl';
    protected $allowedFields = [
        'sobat_id',
        'id_pml',
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
     * Get PCL by PML
     */
    public function getPCLByPML($idPML)
    {
        return $this->db->table('pcl p')
            ->select('p.*, u.nama_user as nama_pcl, u.email, u.hp')
            ->join('sipantau_user u', 'p.sobat_id = u.sobat_id')
            ->where('p.id_pml', $idPML)
            ->orderBy('u.nama_user', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * Get PCL dengan detail lengkap
     */
    public function getPCLWithDetails($idPCL)
{
    return $this->db->table('pcl p')
        ->select('p.*, 
                 u.nama_user AS nama_pcl, u.email, u.hp, 
                 kab.nama_kabupaten, 
                 pml.target AS target_pml, 
                 u_pml.nama_user AS nama_pml, 
                 kw.id_kegiatan_wilayah, kw.id_kabupaten, 
                 mkdp.nama_kegiatan_detail_proses, 
                 mkdp.tanggal_mulai, mkdp.tanggal_selesai, 
                 mkdp.tanggal_selesai_target, mkdp.persentase_target_awal, 
                 mk.nama_kegiatan')
        ->join('sipantau_user u', 'p.sobat_id = u.sobat_id')
        ->join('master_kabupaten kab', 'u.id_kabupaten = kab.id_kabupaten', 'left')
        ->join('pml', 'p.id_pml = pml.id_pml')
        ->join('sipantau_user u_pml', 'pml.sobat_id = u_pml.sobat_id')
        ->join('kegiatan_wilayah kw', 'pml.id_kegiatan_wilayah = kw.id_kegiatan_wilayah')
        ->join('master_kegiatan_detail_proses mkdp', 'kw.id_kegiatan_detail_proses = mkdp.id_kegiatan_detail_proses')
        ->join('master_kegiatan_detail mkd', 'mkdp.id_kegiatan_detail = mkd.id_kegiatan_detail')
        ->join('master_kegiatan mk', 'mkd.id_kegiatan = mk.id_kegiatan')
        ->where('p.id_pcl', $idPCL)
        ->get()
        ->getRowArray();
}


    /**
     * Get user yang bisa dijadikan PCL (belum di-assign untuk PML tertentu)
     */
    public function getAvailablePCL($idKabupaten, $idPML)
    {
        return $this->db->table('sipantau_user u')
            ->select('u.sobat_id, u.nama_user, u.email, u.hp')
            ->where('u.id_kabupaten', $idKabupaten)
            ->where('u.is_active', 1)
            ->where('u.sobat_id NOT IN (
                SELECT sobat_id FROM pcl 
                WHERE id_pml = ' . $idPML . '
            )')
            ->where('u.sobat_id NOT IN (
                SELECT sobat_id FROM pml
            )')
            ->where('u.sobat_id NOT IN (
                SELECT sobat_id FROM admin_survei_kabupaten
            )')
            ->orderBy('u.nama_user', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * Get progress PCL dengan data pantau progress
     */
    public function getPCLProgress($idPCL)
    {
        return $this->db->table('pcl p')
            ->select('p.*, u.nama_user as nama_pcl,
                     (SELECT MAX(jumlah_realisasi_kumulatif) 
                      FROM pantau_progress 
                      WHERE id_pcl = p.id_pcl) as realisasi_terakhir')
            ->join('sipantau_user u', 'p.sobat_id = u.sobat_id')
            ->where('p.id_pcl', $idPCL)
            ->get()
            ->getRowArray();
    }

    /**
     * Get all PCL by kabupaten untuk monitoring
     */
    public function getAllPCLByKabupaten($idKabupaten)
    {
        return $this->db->table('pcl p')
            ->select('p.*, u.nama_user as nama_pcl,
                     u_pml.nama_user as nama_pml,
                     mkdp.nama_kegiatan_detail_proses,
                     (SELECT MAX(jumlah_realisasi_kumulatif) 
                      FROM pantau_progress 
                      WHERE id_pcl = p.id_pcl) as realisasi_terakhir')
            ->join('sipantau_user u', 'p.sobat_id = u.sobat_id')
            ->join('pml', 'p.id_pml = pml.id_pml')
            ->join('sipantau_user u_pml', 'pml.sobat_id = u_pml.sobat_id')
            ->join('kegiatan_wilayah kw', 'pml.id_kegiatan_wilayah = kw.id_kegiatan_wilayah')
            ->join('master_kegiatan_detail_proses mkdp', 'kw.id_kegiatan_detail_proses = mkdp.id_kegiatan_detail_proses')
            ->where('kw.id_kabupaten', $idKabupaten)
            ->orderBy('p.created_at', 'DESC')
            ->get()
            ->getResultArray();
    }

    // ============================================================
    // METHODS UNTUK GENERATE KURVA S DAN VALIDASI TARGET
    // ============================================================

    /**
     * Get total target PCL yang sudah di-assign ke PML tertentu
     * 
     * @param int $idPML ID PML yang akan dicek
     * @param int|null $excludePCLId ID PCL yang akan dikecualikan (untuk edit)
     * @return int Total target PCL
     */
    public function getTotalTargetByPML($idPML, $excludePCLId = null)
    {
        $builder = $this->db->table('pcl')
            ->selectSum('target', 'total_target')
            ->where('id_pml', $idPML);

        if ($excludePCLId) {
            $builder->where('id_pcl !=', $excludePCLId);
        }

        $result = $builder->get()->getRowArray();
        return (int) ($result['total_target'] ?? 0);
    }

    /**
     * Get sisa target PML yang belum di-assign ke PCL
     * 
     * @param int $idPML ID PML
     * @param int|null $excludePCLId ID PCL yang akan dikecualikan (untuk edit)
     * @return int Sisa target yang tersedia
     */
    public function getSisaTargetPML($idPML, $excludePCLId = null)
    {
        // Get target PML
        $pml = $this->db->table('pml')
            ->select('target')
            ->where('id_pml', $idPML)
            ->get()
            ->getRowArray();

        if (!$pml) {
            return 0;
        }

        $targetPML = (int) $pml['target'];
        $totalTargetPCL = $this->getTotalTargetByPML($idPML, $excludePCLId);

        return max(0, $targetPML - $totalTargetPCL);
    }

    /**
     * Validasi apakah target PCL valid (tidak melebihi sisa target PML)
     * 
     * @param int $idPML ID PML
     * @param int $targetPCL Target yang akan di-assign ke PCL
     * @param int|null $excludePCLId ID PCL yang akan dikecualikan (untuk edit)
     * @return array ['valid' => bool, 'message' => string, 'sisa_target' => int]
     */
    public function validateTargetPCL($idPML, $targetPCL, $excludePCLId = null)
    {
        $sisaTarget = $this->getSisaTargetPML($idPML, $excludePCLId);
        
        if ($targetPCL > $sisaTarget) {
            return [
                'valid' => false,
                'message' => "Target PCL ($targetPCL) melebihi sisa target PML yang tersedia ($sisaTarget)",
                'sisa_target' => $sisaTarget
            ];
        }

        return [
            'valid' => true,
            'message' => 'Target valid',
            'sisa_target' => $sisaTarget
        ];
    }

    /**
     * Get detail kegiatan untuk generate Kurva S
     * 
     * @param int $idPML ID PML
     * @return array|null Detail kegiatan (tanggal mulai, selesai, dll)
     */
    public function getKegiatanDetailForKurva($idPML)
    {
        return $this->db->table('pml')
            ->select('mkdp.tanggal_mulai, mkdp.tanggal_selesai, 
                     mkdp.tanggal_selesai_target, mkdp.persentase_target_awal,
                     kw.id_kegiatan_wilayah')
            ->join('kegiatan_wilayah kw', 'pml.id_kegiatan_wilayah = kw.id_kegiatan_wilayah')
            ->join('master_kegiatan_detail_proses mkdp', 'kw.id_kegiatan_detail_proses = mkdp.id_kegiatan_detail_proses')
            ->where('pml.id_pml', $idPML)
            ->get()
            ->getRowArray();
    }

    /**
     * Delete PCL beserta Kurva Petugas-nya
     * 
     * @param int $idPCL ID PCL yang akan dihapus
     * @return bool Success status
     */
    public function deletePCLWithKurva($idPCL)
    {
        $kurvaModel = new \App\Models\KurvaPetugasModel();
        
        $this->db->transStart();
        
        try {
            // Delete kurva petugas first (jika tidak ada FK CASCADE)
            $kurvaModel->deleteByPCL($idPCL);
            
            // Delete PCL
            $this->delete($idPCL);
            
            $this->db->transComplete();
            
            return $this->db->transStatus();
        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'Error deleting PCL with Kurva: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Update PCL dan regenerate Kurva S jika target berubah
     * 
     * @param int $idPCL ID PCL
     * @param array $data Data yang akan diupdate
     * @return bool Success status
     */
    public function updatePCLWithKurva($idPCL, $data)
    {
        $existingPCL = $this->find($idPCL);
        
        if (!$existingPCL) {
            return false;
        }

        $this->db->transStart();
        
        try {
            // Update PCL data
            $this->update($idPCL, $data);
            
            // Regenerate Kurva S jika target berubah
            if (isset($data['target']) && $data['target'] != $existingPCL['target']) {
                $kurvaModel = new \App\Models\KurvaPetugasModel();
                
                // Delete old kurva
                $kurvaModel->deleteByPCL($idPCL);
                
                // Generate new kurva (akan dipanggil dari controller)
                log_message('info', "Target PCL berubah dari {$existingPCL['target']} ke {$data['target']}, regenerate Kurva S diperlukan");
            }
            
            $this->db->transComplete();
            
            return $this->db->transStatus();
        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'Error updating PCL with Kurva: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Batch insert PCL dengan validasi total target
     * 
     * @param int $idPML ID PML
     * @param array $pclDataArray Array of PCL data
     * @return array ['success' => bool, 'message' => string, 'inserted_ids' => array]
     */
    public function batchInsertPCL($idPML, $pclDataArray)
    {
        // Validasi total target
        $totalTarget = 0;
        foreach ($pclDataArray as $pcl) {
            $totalTarget += (int) $pcl['target'];
        }

        $validation = $this->validateTargetPCL($idPML, $totalTarget);
        
        if (!$validation['valid']) {
            return [
                'success' => false,
                'message' => $validation['message'],
                'inserted_ids' => []
            ];
        }

        $this->db->transStart();
        
        try {
            $insertedIds = [];
            
            foreach ($pclDataArray as $pclData) {
                $pclData['id_pml'] = $idPML;
                $pclData['status_approval'] = 0;
                $pclData['tanggal_approval'] = null;
                
                $insertedId = $this->insert($pclData);
                $insertedIds[] = $insertedId;
            }
            
            $this->db->transComplete();
            
            if ($this->db->transStatus() === false) {
                return [
                    'success' => false,
                    'message' => 'Gagal menyimpan data PCL',
                    'inserted_ids' => []
                ];
            }
            
            return [
                'success' => true,
                'message' => 'Berhasil menyimpan ' . count($insertedIds) . ' PCL',
                'inserted_ids' => $insertedIds
            ];
        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'Error batch insert PCL: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
                'inserted_ids' => []
            ];
        }
    }

    /**
     * Get statistik target vs realisasi PCL
     * 
     * @param int $idPCL ID PCL
     * @return array Statistik target dan realisasi
     */
    public function getTargetStatistics($idPCL)
    {
        $pcl = $this->find($idPCL);
        
        if (!$pcl) {
            return null;
        }

        // Get realisasi terakhir
        $realisasi = $this->db->table('pantau_progress')
            ->select('MAX(jumlah_realisasi_kumulatif) as realisasi_kumulatif')
            ->where('id_pcl', $idPCL)
            ->get()
            ->getRowArray();

        $realisasiKumulatif = (int) ($realisasi['realisasi_kumulatif'] ?? 0);
        $target = (int) $pcl['target'];
        $persentase = $target > 0 ? round(($realisasiKumulatif / $target) * 100, 2) : 0;

        return [
            'id_pcl' => $idPCL,
            'target' => $target,
            'realisasi' => $realisasiKumulatif,
            'sisa' => max(0, $target - $realisasiKumulatif),
            'persentase' => $persentase,
            'status' => $persentase >= 100 ? 'Tercapai' : ($persentase >= 80 ? 'Hampir Tercapai' : 'Dalam Progress')
        ];
    }
}