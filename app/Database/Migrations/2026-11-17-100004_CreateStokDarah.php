<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateStokDarah extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_stok'           => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'id_fasilitas'      => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'golongan_darah'    => ['type' => 'VARCHAR', 'constraint' => 3],
            'jumlah_kantong'    => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'default' => 0],
            'sumber'            => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'Fasilitas'], // Fasilitas | Admin
            'status_verifikasi' => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'Menunggu'],
            'tanggal_update'    => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id_stok', true);
        $this->forge->addKey('id_fasilitas');
        $this->forge->addForeignKey('id_fasilitas', 'fasilitas_kesehatan', 'id_fasilitas', 'CASCADE', 'CASCADE');
        $this->forge->createTable('stok_darah');
    }

    public function down()
    {
        $this->forge->dropTable('stok_darah', true);
    }
}
