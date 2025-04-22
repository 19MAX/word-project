<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateObjetivos extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'objetivo_id' => [
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
            'numero_objetivo' => [
                'type' => 'INT',
                'constraint' => 11,
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

        $this->forge->addPrimaryKey('objetivo_id');
        $this->forge->addForeignKey('materia_id', 'materias', 'materia_id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('objetivos');
    }

    public function down()
    {
        $this->forge->dropTable('objetivos');
    }
}
