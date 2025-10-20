<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Migration untuk tabel master_desa
 * Menyimpan data desa/kelurahan
 */
class CreateMasterDesaTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_desa' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'id_kec' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'nama_desa' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
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

        $this->forge->addKey('id_desa', true);
        $this->forge->addForeignKey('id_kec', 'master_kec', 'id_kec', 'CASCADE', 'CASCADE');
        $this->forge->createTable('master_desa');
    }

    public function down()
    {
        $this->forge->dropTable('master_desa');
    }
}