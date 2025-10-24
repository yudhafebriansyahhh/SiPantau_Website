<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class KurvaSkab extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_kurva_s_kab' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'id_kegiatan_wilayah' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => false,
            ],
            'tanggal_target' => [
                'type' => 'DATE',
                'null' => false,
            ],
            'target_persen_kumulatif' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
                'null'       => true,
            ],
            'target_harian_absolut' => [
                'type'       => 'INT',
                'null'       => true,
            ],
            'target_kumulatif_absolut' => [
                'type'       => 'INT',
                'null'       => true,
            ],
            'is_hari_kerja' => [
                'type'    => 'BOOLEAN',
                'default' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        // Primary Key
        $this->forge->addKey('id_kurva_s_kab', true);

        // Foreign Key ke master_kegiatan_detail_proses
        $this->forge->addForeignKey(
            'id_kegiatan_wilayah',
            'kegiatan_wilayah',
            'id_kegiatan_wilayah',
            'CASCADE',
            'CASCADE'
        );

        // Buat tabel
        $this->forge->createTable('kurva_s_kab');
    }


    public function down()
    {
        // $this->forge->dropTable('kurva_s_kab');
    }
}
