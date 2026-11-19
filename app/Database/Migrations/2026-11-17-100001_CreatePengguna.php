<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePengguna extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_user'        => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'nama'           => ['type' => 'VARCHAR', 'constraint' => 100],
            'email'          => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'no_telp'        => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'password'       => ['type' => 'VARCHAR', 'constraint' => 255],
            'role'           => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'pendonor'], // admin | pendonor | pencari | fasilitas
            'tanggal_daftar' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id_user', true);
        $this->forge->addUniqueKey('email');
        $this->forge->addUniqueKey('no_telp');
        $this->forge->createTable('pengguna');
    }

    public function down()
    {
        $this->forge->dropTable('pengguna', true);
    }
}
