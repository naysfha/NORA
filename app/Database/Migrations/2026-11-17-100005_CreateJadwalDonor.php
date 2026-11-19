<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateJadwalDonor extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_jadwal'     => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'id_fasilitas'  => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'nama_kegiatan' => ['type' => 'VARCHAR', 'constraint' => 150],
            'tanggal'       => ['type' => 'DATE'],
            'waktu'         => ['type' => 'VARCHAR', 'constraint' => 50],
            'lokasi'        => ['type' => 'VARCHAR', 'constraint' => 255],
            'penyelenggara' => ['type' => 'VARCHAR', 'constraint' => 150],
            'status'        => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'Menunggu'], // Menunggu | Dipublikasikan | Ditolak
        ]);
        $this->forge->addKey('id_jadwal', true);
        $this->forge->addKey('id_fasilitas');
        $this->forge->addForeignKey('id_fasilitas', 'fasilitas_kesehatan', 'id_fasilitas', 'CASCADE', 'SET NULL');
        $this->forge->createTable('jadwal_donor');
    }

    public function down()
    {
        $this->forge->dropTable('jadwal_donor', true);
    }
}
