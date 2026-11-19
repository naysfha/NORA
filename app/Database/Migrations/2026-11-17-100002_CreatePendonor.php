<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePendonor extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_pendonor'       => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'id_user'           => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'golongan_darah'    => ['type' => 'VARCHAR', 'constraint' => 3, 'null' => true],
            'riwayat_donor'     => ['type' => 'TEXT', 'null' => true],
            'lokasi'            => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
            'status_verifikasi' => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'Menunggu'],
        ]);
        $this->forge->addKey('id_pendonor', true);
        $this->forge->addUniqueKey('id_user');
        $this->forge->addForeignKey('id_user', 'pengguna', 'id_user', 'CASCADE', 'CASCADE');
        $this->forge->createTable('pendonor');
    }

    public function down()
    {
        $this->forge->dropTable('pendonor', true);
    }
}
