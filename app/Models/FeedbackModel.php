<?php

namespace App\Models;

use CodeIgniter\Model;

class FeedbackModel extends Model
{
    protected $table = 'sipantau_feedback_app';
    protected $primaryKey = 'id_feedback';
    protected $allowedFields = [
        'sobat_id',
        'feedback',
        'created_at',
        'updated_at'
    ];
    
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    /**
     * Get feedback dengan detail user
     */
    public function getFeedbackWithDetails($filters = [])
    {
        $builder = $this->db->table('sipantau_feedback_app f')
            ->select('f.*, 
                     u.nama_user,
                     u.email,
                     u.hp,
                     k.nama_kabupaten,
                     u.role')
            ->join('sipantau_user u', 'f.sobat_id = u.sobat_id', 'left')
            ->join('master_kabupaten k', 'u.id_kabupaten = k.id_kabupaten', 'left')
            ->orderBy('f.created_at', 'DESC');

        // Apply filters
        if (!empty($filters['sobat_id'])) {
            $builder->where('f.sobat_id', $filters['sobat_id']);
        }
        
        if (!empty($filters['id_kabupaten'])) {
            $builder->where('u.id_kabupaten', $filters['id_kabupaten']);
        }

        if (!empty($filters['search'])) {
            $builder->groupStart()
                ->like('f.feedback', $filters['search'])
                ->orLike('u.nama_user', $filters['search'])
                ->orLike('u.sobat_id', $filters['search'])
                ->groupEnd();
        }

        return $builder->get()->getResultArray();
    }

    /**
     * Get feedback by ID dengan detail lengkap
     */
    public function getFeedbackById($id)
    {
        return $this->db->table('sipantau_feedback_app f')
            ->select('f.*, 
                     u.nama_user,
                     u.email,
                     u.hp,
                     u.sobat_id as sobat_id_user,
                     k.nama_kabupaten,
                     u.role')
            ->join('sipantau_user u', 'f.sobat_id = u.sobat_id', 'left')
            ->join('master_kabupaten k', 'u.id_kabupaten = k.id_kabupaten', 'left')
            ->where('f.id_feedback', $id)
            ->get()
            ->getRowArray();
    }

    /**
     * Get feedback statistics
     */
    public function getFeedbackStats($filters = [])
    {
        $builder = $this->db->table('sipantau_feedback_app f')
            ->join('sipantau_user u', 'f.sobat_id = u.sobat_id', 'left');

        if (!empty($filters['id_kabupaten'])) {
            $builder->where('u.id_kabupaten', $filters['id_kabupaten']);
        }

        $total = $builder->countAllResults(false);
        
        // Feedback per bulan (3 bulan terakhir)
        $perBulan = $this->db->table('sipantau_feedback_app f')
            ->select('DATE_FORMAT(f.created_at, "%Y-%m") as bulan, COUNT(*) as jumlah')
            ->join('sipantau_user u', 'f.sobat_id = u.sobat_id', 'left');
        
        if (!empty($filters['id_kabupaten'])) {
            $perBulan->where('u.id_kabupaten', $filters['id_kabupaten']);
        }
        
        $perBulan = $perBulan
            ->where('f.created_at >=', date('Y-m-d', strtotime('-3 months')))
            ->groupBy('bulan')
            ->orderBy('bulan', 'DESC')
            ->get()
            ->getResultArray();
        
        // Feedback hari ini
        $hariIni = (clone $builder)->where('DATE(f.created_at)', date('Y-m-d'))->countAllResults();
        
        // Feedback minggu ini
        $mingguIni = (clone $builder)->where('YEARWEEK(f.created_at)', date('YW'))->countAllResults();

        return [
            'total' => $total,
            'hari_ini' => $hariIni,
            'minggu_ini' => $mingguIni,
            'per_bulan' => $perBulan
        ];
    }

    /**
     * Get feedback count by user
     */
    public function getFeedbackCountByUser($sobatId)
    {
        return $this->where('sobat_id', $sobatId)->countAllResults();
    }

    /**
     * Get latest feedback for user
     */
    public function getLatestFeedbackByUser($sobatId, $limit = 5)
    {
        return $this->where('sobat_id', $sobatId)
                    ->orderBy('created_at', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }
}