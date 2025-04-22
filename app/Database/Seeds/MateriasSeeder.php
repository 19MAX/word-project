<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class MateriasSeeder extends Seeder
{
    public function run()
{
    // Obtener el ID de un usuario de prueba (ajusta según tu estructura)
    $usuarioId = 2;

    $data = [
        [
            'usuario_id' => $usuarioId, // NUEVO CAMPO
            'nombre' => 'Matemáticas I',
            'descripcion' => 'Las Matemáticas, presentes en casi todas las actividades humanas...',
            'ciclo' => 'PRIMER CICLO',
            'created_at' => date('Y-m-d H:i:s'),
        ],
        [
            'usuario_id' => $usuarioId, // NUEVO CAMPO
            'nombre' => 'Física I',
            'descripcion' => 'Descripción de la asignatura de Física I...',
            'ciclo' => 'PRIMER CICLO',
            'created_at' => date('Y-m-d H:i:s'),
        ]
    ];

    $this->db->table('materias')->insertBatch($data);
}
}
