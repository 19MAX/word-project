<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUnidades extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'unidad_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'materia_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'numero_unidad' => [
                'type' => 'INT',
                'constraint' => 11,
            ],
            'nombre' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ],
            'objetivo' => [
                'type' => 'TEXT',
                'null' => true,
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

        $this->forge->addPrimaryKey('unidad_id');
        $this->forge->addForeignKey('materia_id', 'materias', 'materia_id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('unidades');
    }

    public function down()
    {
        $this->forge->dropTable('unidades');
    }
}
