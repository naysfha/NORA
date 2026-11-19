<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateLaporan extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_laporan'    => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'jenis_laporan' => ['type' => 'VARCHAR', 'constraint' => 50],
            'periode'       => ['type' => 'VARCHAR', 'constraint' => 30],
            'isi_laporan'   => ['type' => 'TEXT', 'null' => true],
            'id_admin'      => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'tanggal_dibuat' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id_laporan', true);
        $this->forge->addKey('id_admin');
        $this->forge->addForeignKey('id_admin', 'pengguna', 'id_user', 'CASCADE', 'CASCADE');
        $this->forge->createTable('laporan');
    }

    public function down()
    {
        $this->forge->dropTable('laporan', true);
    }
}
