<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateBibliografias extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'bibliografia_id' => [
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
            'referencia' => [
                'type' => 'TEXT',
            ],
            'enlace' => [
                'type' => 'VARCHAR',
                'constraint' => '500',
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

        $this->forge->addPrimaryKey('bibliografia_id');
        $this->forge->addForeignKey('materia_id', 'materias', 'materia_id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('bibliografias');
    }

    public function down()
    {
        $this->forge->dropTable('bibliografias');
    }
}
