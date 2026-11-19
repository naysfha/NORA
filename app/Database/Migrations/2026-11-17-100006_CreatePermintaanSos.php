<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePermintaanSos extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_sos'            => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'id_pencari'        => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'golongan_darah'    => ['type' => 'VARCHAR', 'constraint' => 3],
            'jumlah_dibutuhkan' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'lokasi'            => ['type' => 'VARCHAR', 'constraint' => 255],
            'rumah_sakit'       => ['type' => 'VARCHAR', 'constraint' => 150],
            'kontak'            => ['type' => 'VARCHAR', 'constraint' => 30],
            'tingkat_urgensi'   => ['type' => 'VARCHAR', 'constraint' => 20],
            'status'            => ['type' => 'VARCHAR', 'constraint' => 30, 'default' => 'Menunggu Verifikasi'],
            'catatan_admin'     => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'tanggal_pengajuan' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id_sos', true);
        $this->forge->addKey('id_pencari');
        $this->forge->addForeignKey('id_pencari', 'pengguna', 'id_user', 'CASCADE', 'CASCADE');
        $this->forge->createTable('permintaan_sos');
    }

    public function down()
    {
        $this->forge->dropTable('permintaan_sos', true);
    }
}
