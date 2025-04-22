<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMaterias extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'materia_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'usuario_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'nombre' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
            ],
            'descripcion' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'ciclo' => [
                'type' => 'VARCHAR',
                'constraint' => '50',
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

        $this->forge->addPrimaryKey('materia_id');
        $this->forge->addForeignKey('usuario_id', 'users', 'id', 'CASCADE', 'CASCADE'); // Asegúrate que 'users' es tu tabla de usuarios
        $this->forge->createTable('materias');
    }

    public function down()
    {
        $this->forge->dropTable('materias');
    }
}
