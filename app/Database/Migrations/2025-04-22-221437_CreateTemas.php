<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTemas extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'tema_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'unidad_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'numero_tema' => [
                'type' => 'INT',
                'constraint' => 11,
            ],
            'nombre' => [
                'type' => 'VARCHAR',
                'constraint' => '200',
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

        $this->forge->addPrimaryKey('tema_id');
        $this->forge->addForeignKey('unidad_id', 'unidades', 'unidad_id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('temas');
    }

    public function down()
    {
        $this->forge->dropTable('temas');
    }
}
