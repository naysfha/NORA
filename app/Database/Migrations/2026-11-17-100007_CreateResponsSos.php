<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateResponsSos extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_respons'     => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'id_sos'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'id_pendonor'    => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'pesan'          => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'tanggal_respons' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id_respons', true);
        $this->forge->addUniqueKey(['id_sos', 'id_pendonor']);
        $this->forge->addKey('id_pendonor');
        $this->forge->addForeignKey('id_sos', 'permintaan_sos', 'id_sos', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_pendonor', 'pendonor', 'id_pendonor', 'CASCADE', 'CASCADE');
        $this->forge->createTable('respons_sos');
    }

    public function down()
    {
        $this->forge->dropTable('respons_sos', true);
    }
}
