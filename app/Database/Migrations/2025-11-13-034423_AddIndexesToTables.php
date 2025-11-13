<?php
   
   namespace App\Database\Migrations;
   
   use CodeIgniter\Database\Migration;
   
   class AddIndexesToTables extends Migration
   {
       public function up()
       {
           // sipantau_transaksi indexes
           $this->db->query('CREATE INDEX IF NOT EXISTS idx_sipantau_transaksi_pcl ON sipantau_transaksi(id_pcl)');
           $this->db->query('CREATE INDEX IF NOT EXISTS idx_sipantau_transaksi_created ON sipantau_transaksi(created_at DESC)');
           $this->db->query('CREATE INDEX IF NOT EXISTS idx_sipantau_transaksi_pcl_created ON sipantau_transaksi(id_pcl, created_at DESC)');
           $this->db->query('CREATE INDEX IF NOT EXISTS idx_sipantau_transaksi_kecamatan ON sipantau_transaksi(id_kecamatan)');
           $this->db->query('CREATE INDEX IF NOT EXISTS idx_sipantau_transaksi_desa ON sipantau_transaksi(id_desa)');
           
           // pantau_progress indexes
           $this->db->query('CREATE INDEX IF NOT EXISTS idx_pantau_progress_pcl ON pantau_progress(id_pcl)');
           $this->db->query('CREATE INDEX IF NOT EXISTS idx_pantau_progress_created ON pantau_progress(created_at DESC)');
           $this->db->query('CREATE INDEX IF NOT EXISTS idx_pantau_progress_pcl_created ON pantau_progress(id_pcl, created_at DESC)');
           
           // Other indexes
           $this->db->query('CREATE INDEX IF NOT EXISTS idx_pcl_pml ON pcl(id_pml)');
           $this->db->query('CREATE INDEX IF NOT EXISTS idx_pml_kegiatan_wilayah ON pml(id_kegiatan_wilayah)');
       }
   
       public function down()
       {
           // Drop indexes jika rollback
           $this->db->query('DROP INDEX IF EXISTS idx_sipantau_transaksi_pcl ON sipantau_transaksi');
           $this->db->query('DROP INDEX IF EXISTS idx_sipantau_transaksi_created ON sipantau_transaksi');
           // ... dst untuk semua index
       }
   }