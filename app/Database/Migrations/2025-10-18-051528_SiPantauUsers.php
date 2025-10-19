<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSipantauUserTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'sobat_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'password' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'nama_user' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'email' => [
                'type'       => 'VARCHAR',
                'constraint' => '150',
                'unique'     => true,
            ],
            'hp' => [
                'type'       => 'VARCHAR',
                'constraint' => '20',
                'null'       => true,
            ],
            'id_role' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'is_active' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
            ],
            'is_admin' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
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

        $this->forge->addKey('sobat_id', true);

        // ✅ Tambahkan relasi foreign key ke tabel sipantau_role
        $this->forge->addForeignKey('id_role', 'sipantau_role', 'id_roleuser', 'CASCADE', 'SET NULL');

        $this->forge->createTable('sipantau_user');
    }

    public function down()
    {
        // Hapus foreign key dulu sebelum drop table
        $this->forge->dropForeignKey('sipantau_user', 'sipantau_user_id_role_foreign');
        $this->forge->dropTable('sipantau_user');
    }
}
