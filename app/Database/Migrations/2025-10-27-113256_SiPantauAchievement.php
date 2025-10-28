<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class SiPantauAchievement extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_achievement' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'sobat_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'total_lapor_aktivitas' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'total_lapor_progress' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'persentase_kepatuhan' => [
                'type' => 'DECIMAL',
                'constraint' => '5,2',
                'unsigned' => true,
            ],
            'kategori_kepatuhan' => [
                'type' => 'ENUM',
                'constraint' => ['tinggi', 'sedang', 'rendah'],
                'null' => true                
            ],
            'persentase_performa' => [
                'type' => 'DECIMAL',
                'constraint' => '5,2',
                'unsigned' => true,
            ],
            'kategori_performa' => [
                'type' => 'ENUM',
                'constraint' => ['sangat bagus', 'bagus', 'tidak bagus'],
                'null' => true                
            ],
        ]);

        $this->forge->addKey('id_achievement', true);
        $this->forge->addForeignKey('sobat_id', 'sipantau_user', 'sobat_id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('sipantau_achievement');
    }

    public function down()
    {
        $this->forge->dropTable('sipantau_achievement');
    }
}
