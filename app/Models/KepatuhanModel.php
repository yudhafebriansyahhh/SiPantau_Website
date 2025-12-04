<?php

namespace App\Models;

use CodeIgniter\Model;

class KepatuhanModel extends Model
{
    protected $table = 'kepatuhan_summary';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'id_pcl',
        'id_kegiatan_detail_proses',
        'tanggal',
        'jumlah_laporan'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    /**
     * ============================================================
     * MAIN METHODS - Dashboard Kepatuhan
     * ============================================================
     */

    /**
     * Get Statistik Kepatuhan Global
     * Return: total_pcl, patuh, kurang_patuh, tidak_patuh, rata_rata_persentase
     */
    public function getStatistikKepatuhan($idKegiatanDetailProses, $idKegiatanWilayah = 'all')
    {
        $kegiatan = $this->getKegiatanInfo($idKegiatanDetailProses);
        if (!$kegiatan) {
            return $this->getEmptyStats();
        }

        $petugas = $this->getPetugasList($idKegiatanDetailProses, $idKegiatanWilayah);

        $stats = [
            'total_pcl' => count($petugas),
            'patuh' => 0,
            'kurang_patuh' => 0,
            'tidak_patuh' => 0,
            'rata_rata_kepatuhan' => 0,
            'persentase_patuh' => 0,
            'persentase_kurang_patuh' => 0,
            'persentase_tidak_patuh' => 0
        ];

        if (empty($petugas)) {
            return $stats;
        }

        $totalPersentase = 0;

        foreach ($petugas as $pcl) {
            $kepatuhan = $this->hitungKepatuhanPCL(
                $pcl['id_pcl'],
                $idKegiatanDetailProses,
                $kegiatan['tanggal_mulai'],
                $kegiatan['tanggal_selesai']
            );

            if ($kepatuhan['persentase'] >= 80) {
                $stats['patuh']++;
            } elseif ($kepatuhan['persentase'] >= 50) {
                $stats['kurang_patuh']++;
            } else {
                $stats['tidak_patuh']++;
            }

            $totalPersentase += $kepatuhan['persentase'];
        }

        $totalPcl = count($petugas);
        $stats['rata_rata_kepatuhan'] = round($totalPersentase / $totalPcl, 1);
        $stats['persentase_patuh'] = round(($stats['patuh'] / $totalPcl) * 100, 1);
        $stats['persentase_kurang_patuh'] = round(($stats['kurang_patuh'] / $totalPcl) * 100, 1);
        $stats['persentase_tidak_patuh'] = round(($stats['tidak_patuh'] / $totalPcl) * 100, 1);

        return $stats;
    }

    /**
     * Get Kepatuhan Per Kabupaten (untuk Bar Chart Admin Provinsi)
     */
    public function getKepatuhanPerKabupaten($idKegiatanDetailProses)
    {
        $kegiatan = $this->getKegiatanInfo($idKegiatanDetailProses);
        if (!$kegiatan) {
            return [];
        }

        // Get semua kabupaten yang terlibat dalam kegiatan ini
        $kabupatenList = $this->db->query("
            SELECT DISTINCT 
                mk.id_kabupaten,
                mk.nama_kabupaten
            FROM kegiatan_wilayah kw
            JOIN master_kabupaten mk ON kw.id_kabupaten = mk.id_kabupaten
            WHERE kw.id_kegiatan_detail_proses = ?
            ORDER BY mk.nama_kabupaten ASC
        ", [$idKegiatanDetailProses])->getResultArray();

        $result = [];

        foreach ($kabupatenList as $kab) {
            $petugas = $this->getPetugasList($idKegiatanDetailProses, null, $kab['id_kabupaten']);

            if (empty($petugas)) {
                continue;
            }

            $totalPersentase = 0;
            $validPetugas = 0;

            foreach ($petugas as $pcl) {
                $kepatuhan = $this->hitungKepatuhanPCL(
                    $pcl['id_pcl'],
                    $idKegiatanDetailProses,
                    $kegiatan['tanggal_mulai'],
                    $kegiatan['tanggal_selesai']
                );

                $totalPersentase += $kepatuhan['persentase'];
                $validPetugas++;
            }

            $avgPersentase = $validPetugas > 0
                ? round($totalPersentase / $validPetugas, 1)
                : 0;

            $result[] = [
                'id_kabupaten' => $kab['id_kabupaten'],
                'nama_kabupaten' => $kab['nama_kabupaten'],
                'persentase' => $avgPersentase,
                'jumlah_petugas' => $validPetugas,
                'color' => $this->getColorByPersentase($avgPersentase)
            ];
        }

        // Sort by persentase DESC
        usort($result, function ($a, $b) {
            return $b['persentase'] <=> $a['persentase'];
        });

        return $result;
    }

    /**
     * Get Trend Kepatuhan Harian (untuk Line Chart Admin Kabupaten)
     */
    public function getTrendKepatuhanHarian($idKegiatanDetailProses, $idKabupaten)
    {
        $kegiatan = $this->getKegiatanInfo($idKegiatanDetailProses);
        if (!$kegiatan) {
            return [];
        }

        $petugas = $this->getPetugasList($idKegiatanDetailProses, null, $idKabupaten);
        $totalPetugas = count($petugas);

        if ($totalPetugas === 0) {
            return [];
        }

        $tanggalMulai = new \DateTime($kegiatan['tanggal_mulai']);
        $tanggalSelesai = new \DateTime($kegiatan['tanggal_selesai']);
        $today = new \DateTime();

        // Batasi sampai hari ini jika kegiatan masih berjalan
        if ($today < $tanggalSelesai) {
            $tanggalSelesai = $today;
        }

        $result = [];
        $currentDate = clone $tanggalMulai;

        while ($currentDate <= $tanggalSelesai) {
            $dateStr = $currentDate->format('Y-m-d');
            $dayOfWeek = $currentDate->format('N'); // 1 (Monday) to 7 (Sunday)

            // SKIP Sabtu (6) dan Minggu (7)
            if ($dayOfWeek == 6 || $dayOfWeek == 7) {
                $currentDate->modify('+1 day');
                continue;
            }

            // Hitung jumlah PCL yang lapor di tanggal ini
            $jumlahLapor = $this->db->query("
            SELECT COUNT(DISTINCT ks.id_pcl) as total
            FROM kepatuhan_summary ks
            JOIN pcl ON ks.id_pcl = pcl.id_pcl
            JOIN pml ON pcl.id_pml = pml.id_pml
            JOIN kegiatan_wilayah kw ON pml.id_kegiatan_wilayah = kw.id_kegiatan_wilayah
            WHERE ks.id_kegiatan_detail_proses = ?
            AND kw.id_kabupaten = ?
            AND ks.tanggal = ?
        ", [$idKegiatanDetailProses, $idKabupaten, $dateStr])->getRowArray();

            $persentase = round(($jumlahLapor['total'] / $totalPetugas) * 100, 1);

            $result[] = [
                'tanggal' => $dateStr,
                'label' => $currentDate->format('d M'),
                'persentase' => $persentase,
                'jumlah_lapor' => (int) $jumlahLapor['total'],
                'total_pcl' => $totalPetugas // PENTING: Tambahkan ini
            ];

            $currentDate->modify('+1 day');
        }

        return $result;
    }

    /**
     * Get Leaderboard Kepatuhan
     */
    public function getLeaderboardKepatuhan($idKegiatanDetailProses, $idKegiatanWilayah = 'all', $limit = 10)
    {
        $kegiatan = $this->getKegiatanInfo($idKegiatanDetailProses);
        if (!$kegiatan) {
            return [];
        }

        $idKabupaten = ($idKegiatanWilayah === 'all') ? null : $this->getKabupatenFromKegiatanWilayah($idKegiatanWilayah);
        $petugas = $this->getPetugasList($idKegiatanDetailProses, $idKegiatanWilayah, $idKabupaten);

        $leaderboard = [];

        foreach ($petugas as $pcl) {
            $kepatuhan = $this->hitungKepatuhanPCL(
                $pcl['id_pcl'],
                $idKegiatanDetailProses,
                $kegiatan['tanggal_mulai'],
                $kegiatan['tanggal_selesai']
            );

            // Get nama kabupaten
            $kabupatenInfo = $this->db->query("
            SELECT mk.nama_kabupaten
            FROM pcl
            JOIN pml ON pcl.id_pml = pml.id_pml
            JOIN kegiatan_wilayah kw ON pml.id_kegiatan_wilayah = kw.id_kegiatan_wilayah
            JOIN master_kabupaten mk ON kw.id_kabupaten = mk.id_kabupaten
            WHERE pcl.id_pcl = ?
        ", [$pcl['id_pcl']])->getRowArray();

            $leaderboard[] = [
                'id_pcl' => $pcl['id_pcl'],
                'nama_pcl' => $pcl['nama_user'],
                'sobat_id' => $pcl['sobat_id'],
                'nama_kabupaten' => $kabupatenInfo['nama_kabupaten'] ?? '',
                'jumlah_laporan' => $kepatuhan['jumlah_laporan'],
                'total_hari_kerja' => $kepatuhan['total_hari'],
                'persentase_kepatuhan' => $kepatuhan['persentase'],
                'status' => $this->getStatusKepatuhan($kepatuhan['persentase']),
                'badge_color' => $this->getColorByPersentase($kepatuhan['persentase'])
            ];
        }

        usort($leaderboard, function ($a, $b) {
            if ($b['persentase_kepatuhan'] === $a['persentase_kepatuhan']) {
                return $b['jumlah_laporan'] <=> $a['jumlah_laporan'];
            }
            return $b['persentase_kepatuhan'] <=> $a['persentase_kepatuhan'];
        });

        return array_slice($leaderboard, 0, $limit);
    }

    /**
     * Get Daftar Petugas Tidak Patuh (persentase < 50%)
     */
    public function getPetugasTidakPatuh($idKegiatanDetailProses, $idKegiatanWilayah = 'all', $idKabupaten = null)
    {
        $kegiatan = $this->getKegiatanInfo($idKegiatanDetailProses);
        if (!$kegiatan) {
            return [];
        }

        $petugas = $this->getPetugasList($idKegiatanDetailProses, $idKegiatanWilayah, $idKabupaten);

        $tidakPatuh = [];

        foreach ($petugas as $pcl) {
            $kepatuhan = $this->hitungKepatuhanPCL(
                $pcl['id_pcl'],
                $idKegiatanDetailProses,
                $kegiatan['tanggal_mulai'],
                $kegiatan['tanggal_selesai']
            );

            if ($kepatuhan['persentase'] < 50) {
                // Get tanggal laporan terakhir
                $laporanTerakhir = $this->db->query("
                SELECT MAX(tanggal) as terakhir_lapor
                FROM kepatuhan_summary
                WHERE id_pcl = ?
                AND id_kegiatan_detail_proses = ?
            ", [$pcl['id_pcl'], $idKegiatanDetailProses])->getRowArray();

                $terakhirLapor = $laporanTerakhir['terakhir_lapor'] ?? null;
                $hariTidakLapor = 0;

                if ($terakhirLapor) {
                    // Hitung hari kerja sejak terakhir lapor sampai hari ini
                    $lastDate = new \DateTime($terakhirLapor);
                    $today = new \DateTime();
                    $today = min($today, new \DateTime($kegiatan['tanggal_selesai']));

                    $hariTidakLapor = $this->hitungHariKerja($lastDate, $today) - 1; // -1 karena tidak hitung hari terakhir lapor
                } else {
                    // Belum pernah lapor, hitung dari tanggal mulai kegiatan
                    $mulai = new \DateTime($kegiatan['tanggal_mulai']);
                    $today = new \DateTime();
                    $today = min($today, new \DateTime($kegiatan['tanggal_selesai']));

                    $hariTidakLapor = $this->hitungHariKerja($mulai, $today);
                }

                // Get nama kabupaten
                $kabupatenInfo = $this->db->query("
                SELECT mk.nama_kabupaten
                FROM pcl
                JOIN pml ON pcl.id_pml = pml.id_pml
                JOIN kegiatan_wilayah kw ON pml.id_kegiatan_wilayah = kw.id_kegiatan_wilayah
                JOIN master_kabupaten mk ON kw.id_kabupaten = mk.id_kabupaten
                WHERE pcl.id_pcl = ?
            ", [$pcl['id_pcl']])->getRowArray();

                $tidakPatuh[] = [
                    'id_pcl' => $pcl['id_pcl'],
                    'nama_pcl' => $pcl['nama_user'],
                    'sobat_id' => $pcl['sobat_id'],
                    'nama_kabupaten' => $kabupatenInfo['nama_kabupaten'] ?? '',
                    'terakhir_lapor' => $terakhirLapor ? date('d M Y', strtotime($terakhirLapor)) : 'Belum pernah lapor',
                    'hari_tidak_lapor' => $hariTidakLapor,
                    'persentase_kepatuhan' => $kepatuhan['persentase'],
                    'jumlah_laporan' => $kepatuhan['jumlah_laporan'],
                    'total_hari_kerja' => $kepatuhan['total_hari']
                ];
            }
        }

        usort($tidakPatuh, function ($a, $b) {
            return $b['hari_tidak_lapor'] <=> $a['hari_tidak_lapor'];
        });

        return $tidakPatuh;
    }

    /**
     * ============================================================
     * HELPER METHODS
     * ============================================================
     */

    /**
     * Hitung Kepatuhan PCL dalam periode tertentu
     * Menggunakan kepatuhan_summary untuk performa optimal
     */

    private function hitungHariKerja($startDate, $endDate)
    {
        $start = clone $startDate;
        $end = clone $endDate;
        $hariKerja = 0;

        while ($start <= $end) {
            $dayOfWeek = $start->format('N'); // 1 (Monday) to 7 (Sunday)

            // Hanya hitung Senin-Jumat (1-5)
            if ($dayOfWeek >= 1 && $dayOfWeek <= 5) {
                $hariKerja++;
            }

            $start->modify('+1 day');
        }

        return $hariKerja;
    }

    private function hitungKepatuhanPCL($idPCL, $idKegiatanDetailProses, $tanggalMulai, $tanggalSelesai)
    {
        $today = date('Y-m-d');
        $endDate = $tanggalSelesai;

        // Jika kegiatan masih berjalan, gunakan hari ini sebagai batas
        if ($today < $tanggalSelesai) {
            $endDate = $today;
        }

        // Hitung total hari KERJA dalam periode (Senin-Jumat saja)
        $start = new \DateTime($tanggalMulai);
        $end = new \DateTime($endDate);
        $totalHariKerja = $this->hitungHariKerja($start, $end);

        // Hitung jumlah hari PCL melakukan laporan (DISTINCT tanggal dari kepatuhan_summary)
        // HANYA hari kerja (Senin-Jumat)
        $result = $this->db->query("
        SELECT COUNT(DISTINCT ks.tanggal) as jumlah_laporan
        FROM kepatuhan_summary ks
        WHERE ks.id_pcl = ?
        AND ks.id_kegiatan_detail_proses = ?
        AND ks.tanggal BETWEEN ? AND ?
        AND DAYOFWEEK(ks.tanggal) NOT IN (1, 7)
    ", [$idPCL, $idKegiatanDetailProses, $tanggalMulai, $endDate])->getRowArray();

        $jumlahLaporan = (int) ($result['jumlah_laporan'] ?? 0);
        $persentase = $totalHariKerja > 0 ? round(($jumlahLaporan / $totalHariKerja) * 100, 1) : 0;

        return [
            'jumlah_laporan' => $jumlahLaporan,
            'total_hari' => $totalHariKerja, // Ini sekarang total hari KERJA saja
            'persentase' => $persentase
        ];
    }

    /**
     * Get Info Kegiatan (tanggal mulai & selesai)
     */
    private function getKegiatanInfo($idKegiatanDetailProses)
    {
        return $this->db->query("
            SELECT 
                id_kegiatan_detail_proses,
                nama_kegiatan_detail_proses,
                tanggal_mulai, 
                tanggal_selesai
            FROM master_kegiatan_detail_proses
            WHERE id_kegiatan_detail_proses = ?
        ", [$idKegiatanDetailProses])->getRowArray();
    }

    /**
     * Get List PCL yang terlibat dalam kegiatan
     */
    private function getPetugasList($idKegiatanDetailProses, $idKegiatanWilayah = null, $idKabupaten = null)
    {
        $builder = $this->db->table('pcl')
            ->select('pcl.id_pcl, u.nama_user, u.sobat_id, kw.id_kabupaten')
            ->join('sipantau_user u', 'pcl.sobat_id = u.sobat_id')
            ->join('pml', 'pcl.id_pml = pml.id_pml')
            ->join('kegiatan_wilayah kw', 'pml.id_kegiatan_wilayah = kw.id_kegiatan_wilayah')
            ->where('kw.id_kegiatan_detail_proses', $idKegiatanDetailProses)
            ->orderBy('u.nama_user', 'ASC');

        // Filter by kegiatan wilayah jika bukan 'all'
        if ($idKegiatanWilayah && $idKegiatanWilayah !== 'all') {
            $builder->where('kw.id_kegiatan_wilayah', $idKegiatanWilayah);
        }

        // Filter by kabupaten jika ada
        if ($idKabupaten) {
            $builder->where('kw.id_kabupaten', $idKabupaten);
        }

        return $builder->get()->getResultArray();
    }

    /**
     * Get ID Kabupaten dari ID Kegiatan Wilayah
     */
    private function getKabupatenFromKegiatanWilayah($idKegiatanWilayah)
    {
        $result = $this->db->query("
            SELECT id_kabupaten
            FROM kegiatan_wilayah
            WHERE id_kegiatan_wilayah = ?
        ", [$idKegiatanWilayah])->getRowArray();

        return $result['id_kabupaten'] ?? null;
    }

    /**
     * Get Status Kepatuhan berdasarkan persentase
     */
    private function getStatusKepatuhan($persentase)
    {
        if ($persentase >= 80)
            return 'Patuh';
        if ($persentase >= 50)
            return 'Kurang Patuh';
        return 'Tidak Patuh';
    }

    /**
     * Get Color by Persentase
     */
    private function getColorByPersentase($persentase)
    {
        if ($persentase >= 80)
            return '#10b981'; // Green-500
        if ($persentase >= 50)
            return '#f59e0b'; // Yellow-500
        return '#ef4444'; // Red-500
    }

    /**
     * Get Empty Stats (fallback)
     */
    private function getEmptyStats()
    {
        return [
            'total_pcl' => 0,
            'patuh' => 0,
            'kurang_patuh' => 0,
            'tidak_patuh' => 0,
            'rata_rata_persentase' => 0
        ];
    }

    /**
     * ============================================================
     * UTILITY METHODS
     * ============================================================
     */

    /**
     * Rebuild kepatuhan_summary dari sipantau_transaksi
     * Digunakan jika ada data inconsistency
     */
    public function rebuildKepatuhanSummary($idKegiatanDetailProses = null)
    {
        $whereClause = $idKegiatanDetailProses
            ? "WHERE id_kegiatan_detail_proses = {$idKegiatanDetailProses}"
            : "";

        $sql = "
        INSERT INTO kepatuhan_summary (
            id_pcl,
            id_kegiatan_detail_proses,
            tanggal,
            jumlah_laporan,
            created_at,
            updated_at
        )
        SELECT 
            id_pcl,
            id_kegiatan_detail_proses,
            DATE(created_at) as tanggal,
            COUNT(*) as jumlah_laporan,
            MIN(created_at) as created_at,
            MAX(created_at) as updated_at
        FROM sipantau_transaksi
        {$whereClause}
        GROUP BY 
            id_pcl, 
            id_kegiatan_detail_proses, 
            DATE(created_at)
        ON DUPLICATE KEY UPDATE
            jumlah_laporan = VALUES(jumlah_laporan),
            updated_at = VALUES(updated_at)
        ";

        try {
            $this->db->query($sql);
            return [
                'success' => true,
                'message' => 'Kepatuhan summary berhasil di-rebuild',
                'affected_rows' => $this->db->affectedRows()
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get Detail Kepatuhan PCL (untuk debugging/detail view)
     */
    public function getDetailKepatuhanPCL($idPCL, $idKegiatanDetailProses)
    {
        $kegiatan = $this->getKegiatanInfo($idKegiatanDetailProses);
        if (!$kegiatan) {
            return null;
        }

        $kepatuhan = $this->hitungKepatuhanPCL(
            $idPCL,
            $idKegiatanDetailProses,
            $kegiatan['tanggal_mulai'],
            $kegiatan['tanggal_selesai']
        );

        // Get PCL info
        $pclInfo = $this->db->query("
            SELECT 
                pcl.id_pcl,
                u.nama_user,
                u.sobat_id,
                mk.nama_kabupaten
            FROM pcl
            JOIN sipantau_user u ON pcl.sobat_id = u.sobat_id
            JOIN pml ON pcl.id_pml = pml.id_pml
            JOIN kegiatan_wilayah kw ON pml.id_kegiatan_wilayah = kw.id_kegiatan_wilayah
            JOIN master_kabupaten mk ON kw.id_kabupaten = mk.id_kabupaten
            WHERE pcl.id_pcl = ?
            AND kw.id_kegiatan_detail_proses = ?
        ", [$idPCL, $idKegiatanDetailProses])->getRowArray();

        if (!$pclInfo) {
            return null;
        }

        return array_merge($pclInfo, $kepatuhan, [
            'kegiatan' => $kegiatan,
            'status' => $this->getStatusKepatuhan($kepatuhan['persentase']),
            'color' => $this->getColorByPersentase($kepatuhan['persentase'])
        ]);
    }
}