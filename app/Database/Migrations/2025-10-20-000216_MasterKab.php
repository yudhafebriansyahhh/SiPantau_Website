<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMasterKabTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_kab' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'nama_kab' => [
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

        $this->forge->addKey('id_kab', true);
        $this->forge->createTable('master_kab');
    }

    public function down()
    {
        $this->forge->dropTable('master_kab');
    }
}