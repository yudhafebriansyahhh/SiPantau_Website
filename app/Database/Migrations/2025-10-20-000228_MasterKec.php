<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;


class CreateMasterKecTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_kec' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'id_kab' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'nama_kec' => [
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

        $this->forge->addKey('id_kec', true);
        $this->forge->addForeignKey('id_kab', 'master_kab', 'id_kab', 'CASCADE', 'CASCADE');
        $this->forge->createTable('master_kec');
    }

    public function down()
    {
        $this->forge->dropTable('master_kec');
    }
}