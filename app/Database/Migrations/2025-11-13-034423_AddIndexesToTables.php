<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddIndexesToTables extends Migration
{
    protected function addIndexIfNotExists(string $table, string $indexName, string $columns)
    {
        $exists = $this->db->query("
            SHOW INDEX FROM {$table} WHERE Key_name = '{$indexName}'
        ")->getResult();

        if (empty($exists)) {
            $this->db->query("CREATE INDEX {$indexName} ON {$table} ({$columns})");
        }
    }

    protected function dropIndexIfExists(string $table, string $indexName)
    {
        $exists = $this->db->query("
            SHOW INDEX FROM {$table} WHERE Key_name = '{$indexName}'
        ")->getResult();

        if (!empty($exists)) {
            $this->db->query("DROP INDEX {$indexName} ON {$table}");
        }
    }

    public function up()
    {
        // sipantau_transaksi
        $this->addIndexIfNotExists('sipantau_transaksi', 'idx_sipantau_transaksi_pcl', 'id_pcl');
        $this->addIndexIfNotExists('sipantau_transaksi', 'idx_sipantau_transaksi_created', 'created_at');
        $this->addIndexIfNotExists('sipantau_transaksi', 'idx_sipantau_transaksi_pcl_created', 'id_pcl, created_at');
        $this->addIndexIfNotExists('sipantau_transaksi', 'idx_sipantau_transaksi_kecamatan', 'id_kecamatan');
        $this->addIndexIfNotExists('sipantau_transaksi', 'idx_sipantau_transaksi_desa', 'id_desa');

        // pantau_progress
        $this->addIndexIfNotExists('pantau_progress', 'idx_pantau_progress_pcl', 'id_pcl');
        $this->addIndexIfNotExists('pantau_progress', 'idx_pantau_progress_created', 'created_at');
        $this->addIndexIfNotExists('pantau_progress', 'idx_pantau_progress_pcl_created', 'id_pcl, created_at');

        // pcl & pml
        $this->addIndexIfNotExists('pcl', 'idx_pcl_pml', 'id_pml');
        $this->addIndexIfNotExists('pml', 'idx_pml_kegiatan_wilayah', 'id_kegiatan_wilayah');
    }

    public function down()
    {
        // sipantau_transaksi
        $this->dropIndexIfExists('sipantau_transaksi', 'idx_sipantau_transaksi_pcl');
        $this->dropIndexIfExists('sipantau_transaksi', 'idx_sipantau_transaksi_created');
        $this->dropIndexIfExists('sipantau_transaksi', 'idx_sipantau_transaksi_pcl_created');
        $this->dropIndexIfExists('sipantau_transaksi', 'idx_sipantau_transaksi_kecamatan');
        $this->dropIndexIfExists('sipantau_transaksi', 'idx_sipantau_transaksi_desa');

        // pantau_progress
        $this->dropIndexIfExists('pantau_progress', 'idx_pantau_progress_pcl');
        $this->dropIndexIfExists('pantau_progress', 'idx_pantau_progress_created');
        $this->dropIndexIfExists('pantau_progress', 'idx_pantau_progress_pcl_created');

        // pcl & pml
        $this->dropIndexIfExists('pcl', 'idx_pcl_pml');
        $this->dropIndexIfExists('pml', 'idx_pml_kegiatan_wilayah');
    }
}
