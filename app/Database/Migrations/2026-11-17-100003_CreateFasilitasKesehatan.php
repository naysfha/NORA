<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateFasilitasKesehatan extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_fasilitas'      => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'id_user'           => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'nama_fasilitas'    => ['type' => 'VARCHAR', 'constraint' => 150],
            'alamat'            => ['type' => 'VARCHAR', 'constraint' => 255],
            'kontak'            => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'status_verifikasi' => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'Menunggu'],
        ]);
        $this->forge->addKey('id_fasilitas', true);
        $this->forge->addKey('id_user');
        $this->forge->addForeignKey('id_user', 'pengguna', 'id_user', 'CASCADE', 'SET NULL');
        $this->forge->createTable('fasilitas_kesehatan');
    }

    public function down()
    {
        $this->forge->dropTable('fasilitas_kesehatan', true);
    }
}
