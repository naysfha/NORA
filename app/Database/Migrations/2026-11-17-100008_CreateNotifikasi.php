<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateNotifikasi extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_notifikasi'   => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'id_user'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'jenis_notifikasi' => ['type' => 'VARCHAR', 'constraint' => 30],
            'isi_pesan'       => ['type' => 'VARCHAR', 'constraint' => 500],
            'tautan'          => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
            'tanggal_kirim'   => ['type' => 'DATETIME', 'null' => true],
            'status_baca'     => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
        ]);
        $this->forge->addKey('id_notifikasi', true);
        $this->forge->addKey('id_user');
        $this->forge->addForeignKey('id_user', 'pengguna', 'id_user', 'CASCADE', 'CASCADE');
        $this->forge->createTable('notifikasi');
    }

    public function down()
    {
        $this->forge->dropTable('notifikasi', true);
    }
}
