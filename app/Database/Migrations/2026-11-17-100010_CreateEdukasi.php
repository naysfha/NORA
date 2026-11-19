<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateEdukasi extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_edukasi'     => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'judul'          => ['type' => 'VARCHAR', 'constraint' => 150],
            'kategori'       => ['type' => 'VARCHAR', 'constraint' => 40],
            'ringkasan'      => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'isi'            => ['type' => 'TEXT'],
            'id_admin'       => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'tanggal_dibuat' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id_edukasi', true);
        $this->forge->addKey('id_admin');
        $this->forge->addForeignKey('id_admin', 'pengguna', 'id_user', 'CASCADE', 'SET NULL');
        $this->forge->createTable('edukasi');
    }

    public function down()
    {
        $this->forge->dropTable('edukasi', true);
    }
}
