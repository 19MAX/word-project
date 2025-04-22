<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateResultados extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'resultado_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'objetivo_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'descripcion' => [
                'type' => 'TEXT',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addPrimaryKey('resultado_id');
        $this->forge->addForeignKey('objetivo_id', 'objetivos', 'objetivo_id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('resultados');
    }

    public function down()
    {
        $this->forge->dropTable('resultados');
    }
}
